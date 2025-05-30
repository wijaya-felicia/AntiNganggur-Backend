<?php

namespace App\Models;
use MongoDB\Laravel\Eloquent\Model;

class Application extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'applications';
    protected $table = 'applications';

    protected $fillable = [
        'employeeIds',
        'jobId',
    ];

    public function Employee()
    {
        return $this->belongsTo(Employee::class, "employeeIds");
    }

    public function Job()
    {
        return $this->belongsTo(Job::class, "jobId");
    }
}
