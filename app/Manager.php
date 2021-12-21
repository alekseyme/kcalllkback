<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Manager extends Model
{
    protected $fillable = [
        'name', 'email', 'phone'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
