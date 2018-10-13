<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\Skill;
use App\Http\Forms\UserForm;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;


class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->with('team','skills','profile.profession')
            ->filterBy($request->only(['state','role','search']))
            ->orderBy('created_at','DESC')
            ->paginate();

        $users->appends(request(['search','team']));


        return view('users.index', [
            'users'  => $users,
            'title'  => 'Listado de usuarios',
            'view' => 'index',
            'skills' => Skill::orderBy('name')->get(),
            'checkedSkills' => collect(request('skills')),
        ]);
    }

    public function trashed()
    {
        $users = User::onlyTrashed()->paginate();
        return view('users.index',[
            'users' =>$users,
            'view' => 'trash',
        ]);
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function create()
    {
        return new UserForm('users.create', new User);
    }

    public function store(CreateUserRequest $request)
    {
        $request->createUser();
        return redirect()->route('users.index');
    }

    public function edit(User $user)
    {
        return new UserForm('users.edit', $user);
    }

    public function update(User $user, UpdateUserRequest $request)
    {
        $request->updateUser($user);

        return redirect()->route('users.show', $user);
    }

    public function trash(User $user)
    {
        $user->delete();
        $user->profile()->delete();

        return redirect()->route('users.index');
    }

    public function destroy($id)
    {
        $user = User::onlyTrashed()->where('id',$id)->firstOrFail();

        $user->forceDelete();

        return redirect()->route('users.trashed');
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->where('id',$id)->firstOrFail();

        $user->restore();
        $user->profile()->restore();

        return redirect()->route('users.index');
    }

}
