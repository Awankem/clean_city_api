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
            'description' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photos' => 'required|array|min:1',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120', // 5MB limit
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            return DB::transaction(function () use ($request) {
                // 1. Create the Report record
                $report = Report::create([
                    'user_id' => $request->user()->id,
                    'category_id' => $request->category_id,
                    'description' => $request->description,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    // Use ST_SRID to ensure coordinates are mapped correctly for spatial queries
                    'location' => DB::raw("ST_GeomFromText('POINT({$request->longitude} {$request->latitude})', 4326)"),

                    'status' => 'pending',
                ]);

                // 2. Handle Photo Uploads
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('reports', 'public');
                    ReportImage::create([
                        'report_id' => $report->id,
                        'image_path' => $path,
                    ]);
                }

                // 3. Create initial status history
                StatusHistory::create([
                    'report_id' => $report->id,
                    'changed_by' => $request->user()->id,
                    'old_status' => null,
                    'new_status' => 'pending',
                    'note' => 'Report submitted successfully.',
                ]);

                // 4. Initial Priority Calculation
                $this->updatePriorityScore($report);

                $report->refresh();

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
            ->get();

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

        // 2. Count nearby reports within 500 meters using ST_Distance_Sphere
        $nearbyCount = DB::table('reports')
            ->where('id', '!=', $report->id)
            ->whereRaw("ST_Distance_Sphere(location, ST_GeomFromText(CONCAT('POINT(', ?, ' ', ?, ')'), 4326)) <= 500", [
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
        return response()->json($report);
    }

    /**
     * Get all reports submitted by the authenticated user.
     */
    public function userReports(Request $request)
    {
        $reports = Report::with(['category', 'images', 'statusHistory'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($reports);
    }
}
