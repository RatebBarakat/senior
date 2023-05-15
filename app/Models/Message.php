<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Message extends Model
{    
    use HasFactory;
    
    protected $guarded = [];

    public function sender(): MorphTo
    {
        return $this->morphTo('sender');
    }

    // Define the recipient relationship
    public function recipient(): MorphTo
    {
        return $this->morphTo('recipient')->nullable();
    }

    protected $casts = [
        'created_at' => 'date'
    ];
}
