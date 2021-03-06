<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Project extends Model
{
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
