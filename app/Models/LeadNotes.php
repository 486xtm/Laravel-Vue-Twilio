<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadNotes extends Model
{
    use HasFactory;

    protected $fillable = ['description', 'lead', 'user', 'status'];
    
    public function lead(): HasOne
    {
        return $this->hasOne(Lead::class);
    }

    public function user(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
