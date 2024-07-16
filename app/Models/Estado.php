<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Estado extends Model
{
    use HasFactory;

    protected $fillable = ['nome'];

    public function cidade(): BelongsTo
    {
        return $this->belongsTo(Cidade::class);
    }
}
