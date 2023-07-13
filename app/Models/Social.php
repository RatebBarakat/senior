<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Laravel\Sanctum\HasApiTokens;

class Social extends Authenticatable
{
    use HasFactory,HasApiTokens,Notifiable;

    protected $table = 'social_users';

    protected $fillable = [
        'name',
        'user_name',
        'email',
        'provider',
        'provider_id',
        'provider_token',
        'user_id'
    ];

    public function hasPermission($name)
    {
        return $this->role && $this->role->permissions->contains('name', $name);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    // public function setProviderTokenAttribute($value){
    //     $this->attributes['provider_token'] = Crypt::encryptString($value);
    // }
    // public function getProviderTokenAttribute($value){
    //     return Crypt::decryptString($value);
    // }
    // public function setProviderIdAttribute($value){
    //     $this->attributes['provider_id'] = Crypt::encryptString($value);
    // }
    // public function getProviderIdAttribute($value)
    // {
    //     return Crypt::decryptString($value);
    // }
}
