<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $fillable = ['title','description','start_date','end_date'];

    public function centers()
    {
        return $this->belongsToMany(DonationCenter::class,'event-donation-centers');
    }
}
