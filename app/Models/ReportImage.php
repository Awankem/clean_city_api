<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportImage extends Model
{
    protected $fillable = ['report_id', 'image_path'];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if (filter_var($this->image_path, FILTER_VALIDATE_URL)) {
            return $this->image_path;
        }
        return url('storage/' . $this->image_path);
    }

    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
