<?php

namespace App;

use App\Models\Profession;
use App\Models\Skill;
use App\Models\UserProfile;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use SoftDeletes;
    use Notifiable;

    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'active' => 'bool',
    ];

    public static function findByEmail($email)
    {
        return static::where(compact('email'))->first();
    }


    public function isAdmin()
    {
        return $this->isAdmin();
    }

    public function team()
    {
        return $this->belongsTo(Team::class)->withDefault();
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class)->withDefault();
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'user_skill');
    }

    public function isAdmim()
    {
        return $this->role === 'admin';
    }


    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return;

        };
        $query->where('name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhereHas('team', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });
    }

    public function scopeByState($query, $state)
    {
        if ($state == 'active') {
            return $query->where('active', true);
        }
        if ($state == 'inactive') {
            return $query->where('active', false);
        }
    }

    public function setStateAttribute($value)
    {
        $this->attributes['active'] = $value =='active';
    }

    public function getStateAttribute()
    {
        if($this->active !== null){
            return $this->active ? 'active' : 'inactive';
        }
    }

}
