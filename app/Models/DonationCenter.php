<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationCenter extends Model
{
    use HasFactory;

    protected $fillable = ['name','location_id','admin_id'];

    public function location(){
        return $this->belongsTo(Location::class);
    }

    // public function CenterAdmin(){
    //     return $this->hasOne(Admin::class)->withDefault();
    // }

    public function admin()//return admin of center
    {
        return $this->hasOne(Admin::class, 'id', 'admin_id');
    }
    public function bloodRequests()
    {
        return $this->hasMany(BloodRequest::class, 'center_id');
    }
    
    public function appointments()
    {
        return $this->hasMany(Appointment::class,'center_id');
    }

    public function donations()
    {
        return $this->hasMany(Donation::class,'center_id');
    }

    public function employees()
    {
        return $this->hasMany(Admin::class,'center_id')
            ->whereHas('role', function ($query) {
                $query->where('name', 'center-employee');
        });
    }
}
