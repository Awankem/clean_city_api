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
        
        $nearbyCount = self::where('id', '!=', $this->id)
            ->whereRaw("ST_Distance_Sphere(location, ST_GeomFromText('POINT({$this->longitude} {$this->latitude})', 4326)) <= ?", [$radius])
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
