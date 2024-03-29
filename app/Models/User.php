<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail,CanResetPassword
{
    use HasApiTokens, HasFactory, Notifiable,MustVerifyEmailTrait,\Illuminate\Auth\Passwords\CanResetPassword;

    // protected static function boot()
    // {
    //     parent::boot();
    //     Static::creating(function (){
    //         Cache::forget('users');
    //     });
    //     Static::updating(function (){
    //         Cache::forget('users');
    //     });
    //     Static::deleting(function ($user){
    //         Cache::forget('users');
    //     });
    // }

    /**
     * The attributes that are mass assignable
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'user_name',
        'email',
        'email_verified_at',
        'role_id',
        'password',
    ];

    public function requests()
    {
        return $this->hasMany(BloodRequest::class,'user_id','id');
    }
    public function appointments(){
        return $this->hasMany(Appointment::class,'user_id','id');
    }
    public function attachRole($roleName)
    {
        $role = Role::where('name', $roleName)->firstOrFail();
        $this->role_id = $role->id;
        $this->save();
    }

    public function hasPermission($name)
    {
        return $this->role && $this->role->permissions->contains('name', $name);
    }

    public function role(){
        return $this->belongsTo(Role::class);
    }

    /**
     * Summary of profile
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function profile()
    {
        return $this->morphOne(Profile::class, 'user')->withDefault([
            'user_id' => $this->id,
            'user_type' => User::class
        ]);
    }
    
    
    public function deviceTokens(){
        return $this->hasMany(DeviceToken::class);
    }

    // public function routeNotificationForFcm($driver, $notification = null)
    // {
    //     return $this->deviceTokens()->pluck('token')->toArray();
    // }

    public function social(){
        return $this->hasOne(Social::class);
    }

    public function receivedMessages()
    {
        return $this->morphMany(Message::class, 'recipient');
    }

    public function sentMessages()
    {
        return $this->morphMany(Message::class, 'sender');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

}
