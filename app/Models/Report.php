<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'assigned_to',
        'description',
        'latitude',
        'longitude',
        'location',
        'location_name',   // Human-readable address from Mapbox reverse geocoding
        'status',
        'priority_score'
    ];

    protected $casts = [
        'location' => 'string', // Will be handled by raw spatial queries or custom casts later
    ];

    protected $hidden = [
        'location',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate priority based on Chapter 3.6.1:
     * (Nearby Reports within 500m) + (Upvotes * 2)
     */
    public function calculatePriorityScore()
    {
        $radius = 500; // 500 meters as per documentation
        $isPgSql = \Illuminate\Support\Facades\DB::getDriverName() === 'pgsql';
        $distanceFunc = $isPgSql ? 'ST_DistanceSphere' : 'ST_Distance_Sphere';
        $pointSql = $isPgSql 
            ? "ST_GeomFromText('POINT(' || ? || ' ' || ? || ')', 4326)" 
            : "ST_GeomFromText(CONCAT('POINT(', ?, ' ', ?, ')'), 4326)";

        $nearbyCount = self::where('id', '!=', $this->id)
            ->whereRaw("$distanceFunc(location, $pointSql) <= ?", [
                $this->longitude,
                $this->latitude,
                $radius
            ])
            ->count();

        $upvotes = $this->votes()->count();

        $this->priority_score = $nearbyCount + ($upvotes * 2);
        $this->save();
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function images()
    {
        return $this->hasMany(ReportImage::class);
    }

    public function votes()
    {
        return $this->hasMany(ReportVote::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(StatusHistory::class);
    }
}
