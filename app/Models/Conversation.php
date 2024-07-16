<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'from', 'read', 'status_id'];


    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }
}
