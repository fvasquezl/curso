<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    protected $fillable= ['title'];

    public function profiles()
    {
        return $this->hasMany(UserProfile::class);
    }
}
