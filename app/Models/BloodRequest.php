<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function center()
    {
        return $this->belongsTo(DonationCenter::class,'center_id','id');
    }

    public function donations()
    {
        return $this->belongsToMany(Donation::class, 'blood_request_donation',
         'blood_request_id', 'donation_id')
            ->withPivot('quantity_used');
    }
}
