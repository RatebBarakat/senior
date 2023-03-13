<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function center(){
        return $this->belongsTo(DonationCenter::class);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }
}
