<?php

namespace App;

use App\Models\Profession;
use App\Models\Skill;
use App\Models\UserProfile;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'is_admin' => 'boolean'
    ];

    public static function findByEmail($email)
    {
        return static::where(compact('email'))->first();
    }

    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }

    public function isAdmin()
    {
        return $this->isAdmin();
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class,'user_skill');
    }

}
