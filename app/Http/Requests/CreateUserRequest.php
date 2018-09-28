<?php

namespace App\Http\Requests;

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
            'profession_id' => [
                'nullable','present',
                Rule::exists('professions','id')
                ->whereNull('deleted_at')
                ]
        ];
    }

    public function createUser()
    {
        DB::transaction( function (){
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => bcrypt($this->password),
            ]);

            $user->profile()->create([
                'bio' => $this->bio,
                'twitter' => $this->twitter,
                'profession_id' => $this->profession_id
            ]);
        });
    }
}
