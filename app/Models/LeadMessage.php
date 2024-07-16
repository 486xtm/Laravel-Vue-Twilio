<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadMessage extends Model
{
    use HasFactory;

    protected $fillable = ['message'];

    public function lead(): HasOne
    {
        return $this->hasOne(Lead::class);
    }
}
