<?php

namespace App\Models;

class Employee extends User
{
    protected $connection = 'mongodb';
    protected $collection = 'users';
    protected $table = 'users';

    protected $fillable = [
        'application_ids',
        'name',
        'email',
        'password',
        'phone',
        'image',
        'role',
        'education',
        'experience',
        'hard_skills',
        'soft_skills',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'application_ids' => 'array',
            'password' => 'hashed',
            'hard_skills' => 'array',
            'soft_skills' => 'array',
        ];
    }

    public static function getRole()
    {
        return 'employee';
    }
}
