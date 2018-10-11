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
        //
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
        return $this->hasOne(UserProfile::class)->withDefault([
            'bio' => 'Programador'
        ]);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'user_skill');
    }

    public function isAdmim()
    {
        return $this->role === 'admin';
    }

    public function scopeSearch($query)
    {
        $query->when(request('team'),function($query,$team){
        if($team === 'with_team'){
            $query->has('team');
        }elseif ($team === 'without_team'){
            $query->doesntHave('team');
        }
        })
        ->when(request('search'),function($query,$search){
            $query->where(function($query) use ($search){
                $query->where('name','like',"%{$search}%")
                    ->orWhere('email','like',"%{$search}%")
                    ->orWhereHas('team',function ($query) use ($search){
                        $query->where('name','like',"%{$search}%");
                    });
            });
        });
    }

}
