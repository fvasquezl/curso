<?php
/**
 * Created by PhpStorm.
 * User: fvasquez
 * Date: 1/10/18
 * Time: 04:25 PM
 */

namespace App\Http\ViewComposers;


use App\Models\Profession;
use App\Models\Skill;
use Illuminate\Contracts\View\View;

class UserFieldsComposer
{
    public function compose(View $view)
    {
        $professions = Profession::orderBy('title', 'ASC')->get();
        $skills = Skill::orderBy('name', 'ASC')->get();
        $roles = trans('users.roles');

        $view->with(compact('professions','skills','roles'));
    }
}