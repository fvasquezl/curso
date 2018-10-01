<?php

namespace App\Http\Requests;

use App\Role;
use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'bio' => 'required',
            'twitter' => ['nullable','present','url'],
            'role' => ['nullable', Rule::in(Role::getList())],
            'profession_id' => [
                'nullable','present',
                Rule::exists('professions','id')
                ->whereNull('deleted_at')
                ],
            'skills' => [
                'array',
                Rule::exists('skills','id'),
            ]
        ];
    }

    public function createUser()
    {
        DB::transaction( function (){
            $user = new User([
                'name' => $this->name,
                'email' => $this->email,
                'password' => bcrypt($this->password),
            ]);

            $user->role= $this->role ?? 'user';

            $user->save();

            $user->profile()->create([
                'bio' => $this->bio,
                'twitter' => $this->twitter,
                'profession_id' => $this->profession_id
            ]);

            $user->skills()->attach($this->skills);

        });
    }
}
