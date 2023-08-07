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

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class,'admin_id');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }
    public function scopeComplete($query)
    {
        return $query->where('status', 'complete');
    }
}
