<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Message extends Model
{
    use HasFactory;

    protected $fillable = ['message', 'from', 'type', 'content_type', 'conversation_id', 'status_id'];

    public function conversation(): HasOne
    {
        return $this->hasOne(Conversation::class);
    }
}
