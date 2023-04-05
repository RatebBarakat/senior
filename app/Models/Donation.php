<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Model\User;

class Donation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function center()
    {
        return $this->belongsTo(DonationCenter::class,'center_id ','id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id ','id');
    }
}
