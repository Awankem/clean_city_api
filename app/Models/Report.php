<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'assigned_to',
        'description',
        'latitude',
        'longitude',
        'location',
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
