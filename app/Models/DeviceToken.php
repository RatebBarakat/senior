<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (is_null($model->user_id) && is_null($model->admin_id) && is_null($model->social_id)) {
                throw new \Exception('At least one of user_id or admin_id or social_id must be set.');
            }

            if ((!is_null($model->user_id) && is_null($model->admin_id) && is_null($model->social_id)) ||
                (is_null($model->user_id) && !is_null($model->admin_id) && is_null($model->social_id)) ||
                (is_null($model->user_id) && is_null($model->admin_id) && !is_null($model->social_id))) {
                // Only one foreign key is filled, which is valid
            } else {
                throw new \Exception('Exactly one of user_id or admin_id or social_id must be set.');
            }

        });

    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
