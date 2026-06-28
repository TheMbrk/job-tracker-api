<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $fillable = [
        'company_name',
        'position',
        'job_description',
        'job_url',
        'status',
        'match_score',
        'extracted_skills',
        'watson_analysis',
    ];

    protected $casts = [
        'extracted_skills' => 'array',
        'watson_analysis' => 'array',
    ];
}