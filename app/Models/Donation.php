<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function center()
    {
        return $this->belongsTo(DonationCenter::class,'center_id ','id');
    }
}
