<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportVote extends Model
{
    protected $fillable = ['report_id', 'user_id'];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
