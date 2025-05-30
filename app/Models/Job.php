<?php

namespace App\Models;
use MongoDB\Laravel\Eloquent\Model;

class Job extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'jobs';
    protected $table = 'jobs';

    protected $fillable = [
        'employer_id',
        'application_ids',
        'job_role',
        'job_desc',
        'req_skills',
        'salary',
        'employment_type',
        'location',
        'interview_start',
        'interview_end',
        'status'
    ];

    protected $casts = [
        'reqSkills' => 'array',
        'interview_start' => 'array',
        'interview_end' => 'array',
    ];

    public function employer()
    {
        return $this->belongsTo(Employer::class, 'employer_id', '_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, "_id", "application_ids");
    }
}
