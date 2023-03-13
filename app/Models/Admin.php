<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id'
    ];

    public function attachRole($roleName)
    {
        $role = Role::where('name', $roleName)->firstOrFail();
        $this->role_id = $role->id;
        $this->save();
    }

    public function role(){
        return $this->belongsTo(Role::class);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class)->withDefault();
    }

    public function isSuperAdmin(): bool
    {
        return $this->role && $this->role->name == "super-admin";
    }

    public function hasPermission($name)
    {
        return $this->role && $this->role->permissions->contains('name', $name);
    }

    public function scopeNonSuperAdmins($query)
    {
        return $query->where(function($q) {
            $q->whereDoesntHave('role')
                ->orWhereHas('role', function($q) {
                    $q->where('name', '<>', 'super-admin');
                });
        });
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
