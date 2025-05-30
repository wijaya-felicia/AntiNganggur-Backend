<?php

namespace App\Models;

class Employer extends User
{
    protected $connection = 'mongodb';
    protected $collection = 'users';
    protected $table = 'users';

    protected $fillable = [
        'job_ids',
        'name',
        'email',
        'password',
        'phone',
        'image',
        'role',
        'npwp',
        'address',
        'deed_of_establishment',
        'NIB',
        'website',
        'social',
        'token'
    ];

    protected $hidden = [
        'password'
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'jobIds' => 'array',
        ];
    }
    public static function getRole()
    {
        return 'employer';
    }

    public function jobs()
    {
        return $this->hasMany(Job::class, '_id', 'jobIds');
    }
}
