<?php
/**
 * Created by PhpStorm.
 * User: fvasquez
 * Date: 1/10/18
 * Time: 05:06 PM
 */

namespace App\Http\ViewComponents;


use App\Models\Profession;
use App\Models\Skill;
use App\User;
use Illuminate\Contracts\Support\Htmlable;

class UserFields implements Htmlable
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }


    /**
     * Get content as a string of HTML.
     *
     * @return string
     * @throws \Throwable
     */
    public function toHtml()
    {
        return view('users._fields', [
            'professions'=>Profession::orderBy('title', 'ASC')->get(),
            'skills'=>Skill::orderBy('name', 'ASC')->get(),
            'roles'=>trans('users.roles'),
            'user' => $this->user,
        ]);

    }
}