<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Report;
use App\Models\ReportImage;
use App\Models\ReportVote;
use App\Models\StatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ReportController extends Controller
{
    /**
     * Get all active categories.
     */
    public function categories()
    {
        return response()->json(Category::where('is_active', true)->get());
    }

    /**
     * Submit a new report.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photos' => 'nullable|array|min:1',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120', // 5MB limit
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            return DB::transaction(function () use ($request) {
                // 0. Geofencing check (Example for a specific city boundary)
                // Roughly checking if within a box - adjust to your city's bounds
                $lat = (float) $request->latitude;
                $lng = (float) $request->longitude;
                // Example bounds (replace with your city): 
                // Latitude: 5.4 - 5.8, Longitude: -0.3 - -0.1
                if ($lat < 5.4 || $lat > 5.8 || $lng < -0.3 || $lng > -0.1) {
                    // return response()->json(['message' => 'Reports must be within the city boundary.'], 400);
                    // For now we just log it, but you can uncomment the above to enforce it strictly.
                    \Log::info("Report outside city bounds: Lat {$lat}, Lng {$lng}");
                }

                $isPgSql = DB::getDriverName() === 'pgsql';
                $pointSql = $isPgSql 
                    ? "ST_GeomFromText('POINT({$request->longitude} {$request->latitude})', 4326)" 
                    : "ST_GeomFromText('POINT({$request->longitude} {$request->latitude})', 4326)";

                $report = Report::create([
                    'user_id'     => $request->user()->id,
                    'category_id' => $request->category_id,
                    'title'       => $request->title ?? ('Waste Report #' . time()),
                    'description' => $request->description,
                    'latitude'    => $request->latitude,
                    'longitude'   => $request->longitude,
                    'location_name' => $request->location_name ?? null,
                    'location'    => DB::raw($pointSql),
                    'status'      => 'pending',
                ]);

                // Bind coordinates to the raw SQL
                if ($isPgSql) {
                    $report->where('id', $report->id)->update([
                        'location' => DB::raw("ST_GeomFromText('POINT(' || {$request->longitude} || ' ' || {$request->latitude} || ')', 4326)")
                    ]);
                }


                // 2. Handle Photo Uploads (Cloudinary or Local)
                if ($request->hasFile('photos')) {
                    foreach ($request->file('photos') as $photo) {
                        if (config('cloudinary.cloud_url')) {
                            // Upload to Cloudinary
                            $uploadedFileUrl = Cloudinary::upload($photo->getRealPath(), [
                                'folder' => 'clean_city/reports',
                                'transformation' => [
                                    'width' => 1200,
                                    'height' => 1200,
                                    'crop' => 'limit',
                                    'quality' => 'auto',
                                    'fetch_format' => 'auto'
                                ]
                            ])->getSecurePath();
                            
                            $path = $uploadedFileUrl;
                        } else {
                            // Fallback to Local Storage
                            $filename = 'report_' . time() . '_' . uniqid() . '.jpg';
                            $path = 'reports/' . $filename;
                            $this->saveOptimizedImage($photo->getRealPath(), storage_path('app/public/' . $path));
                        }

                        ReportImage::create([
                            'report_id' => $report->id,
                            'image_path' => $path,
                        ]);
                    }
                }

                // 3. Create initial status history
                StatusHistory::create([
                    'report_id' => $report->id,
                    'changed_by' => $request->user()->id,
                    'old_status' => 'pending',
                    'new_status' => 'pending',
                    'note' => 'Report submitted successfully.',
                ]);

                // 4. Initial Priority Calculation
                $this->updatePriorityScore($report);

                $report->refresh();

                // 5. Dispatch Real-time Event
                event(new \App\Events\ReportSubmitted($report));

                return response()->json($report->load('images', 'category', 'statusHistory'), 201);

            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to submit report', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get reports for the map view (with basic clustering/filtering).
     */
    public function index(Request $request)
    {
        $reports = Report::with(['category', 'images'])
            ->whereIn('status', ['pending', 'in_progress'])
            ->get()
            ->map(fn($r) => $this->appendImageUrls($r));

        return response()->json($reports);
    }

    /**
     * Upvote an existing report.
     */
    public function upvote(Request $request, $id)
    {
        $user = $request->user();
        $report = Report::findOrFail($id);

        // Check if user already voted
        $exists = ReportVote::where('report_id', $id)->where('user_id', $user->id)->exists();

        if ($exists) {
            return response()->json(['message' => 'You have already upvoted this report'], 400);
        }

        ReportVote::create([
            'report_id' => $id,
            'user_id' => $user->id
        ]);

        $this->updatePriorityScore($report);

        return response()->json(['message' => 'Report upvoted successfully', 'priority_score' => $report->priority_score]);
    }

    /**
     * Logic to update the priority score based on proximity and votes.
     * Weights: 2 for each vote, 1 for each nearby report within 500m.
     */
    protected function updatePriorityScore(Report $report)
    {
        // 1. Count votes
        $votesCount = ReportVote::where('report_id', $report->id)->count();

        // 2. Count nearby reports within 500 meters
        $isPgSql = DB::getDriverName() === 'pgsql';
        $distanceFunc = $isPgSql ? 'ST_DistanceSphere' : 'ST_Distance_Sphere';
        $pointFunc = $isPgSql ? "ST_GeomFromText('POINT({$request->longitude} {$request->latitude})', 4326)" : "ST_GeomFromText('POINT({$request->longitude} {$request->latitude})', 4326)";

        $nearbyCount = DB::table('reports')
            ->where('id', '!=', $report->id)
            ->whereRaw("$distanceFunc(location, $pointFunc) <= 500", [
                $report->longitude,
                $report->latitude
            ])
            ->count();

        // 3. Calculate new score
        $newScore = ($votesCount * 2) + $nearbyCount;

        $report->update(['priority_score' => $newScore]);
    }

    /**
     * Get report details for tracking.
     */
    public function show($id)
    {
        $report = Report::with(['category', 'images', 'statusHistory.changedBy'])->findOrFail($id);
        return response()->json($this->appendImageUrls($report));
    }

    /**
     * Get all reports submitted by the authenticated user.
     */
    public function userReports(Request $request)
    {
        $reports = Report::with(['category', 'images', 'statusHistory'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($r) => $this->appendImageUrls($r));

        return response()->json($reports);
    }

    /**
     * Append full storage URLs to image objects so Flutter can render them directly.
     */
    protected function appendImageUrls(Report $report): Report
    {
        $report->images->transform(function ($image) {
            if (filter_var($image->image_path, FILTER_VALIDATE_URL)) {
                $image->image_url = $image->image_path;
            } else {
                $image->image_url = url('storage/' . $image->image_path);
            }
            return $image;
        });
        return $report;
    }

    /**
     * Optimized image saving using pure PHP GD library.
     */
    protected function saveOptimizedImage($sourcePath, $destinationPath)
    {
        list($width, $height, $type) = getimagesize($sourcePath);
        
        $maxSize = 1200;
        $newWidth = $width;
        $newHeight = $height;

        if ($width > $maxSize || $height > $maxSize) {
            if ($width > $height) {
                $newWidth = $maxSize;
                $newHeight = floor($height * ($maxSize / $width));
            } else {
                $newHeight = $maxSize;
                $newWidth = floor($width * ($maxSize / $height));
            }
        }

        $image = null;
        switch ($type) {
            case IMAGETYPE_JPEG: $image = imagecreatefromjpeg($sourcePath); break;
            case IMAGETYPE_PNG: $image = imagecreatefrompng($sourcePath); break;
            case IMAGETYPE_GIF: $image = imagecreatefromgif($sourcePath); break;
        }

        if ($image) {
            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            
            // Handle transparency for PNGs
            if ($type == IMAGETYPE_PNG) {
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
            }

            imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            
            // Ensure directory exists
            if (!file_exists(dirname($destinationPath))) {
                mkdir(dirname($destinationPath), 0755, true);
            }

            // Save as high-quality JPG
            imagejpeg($newImage, $destinationPath, 80);
            
            imagedestroy($image);
            imagedestroy($newImage);
        }
    }
}



