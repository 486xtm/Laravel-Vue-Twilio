<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormCampaign extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'description', 'facebook_form_id', 'campaign_id', 'status_id'];
}
