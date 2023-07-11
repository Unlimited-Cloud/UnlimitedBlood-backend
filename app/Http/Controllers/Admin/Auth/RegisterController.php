<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\Auth\RegisterController as BackpackRegisterController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class RegisterController extends BackpackRegisterController
{
    public function showRegistrationForm(): View|\Illuminate\Foundation\Application|Factory|Response|Application
    {
        return view('register');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     *
     * @return \Illuminate\Validation\Validator
     */
    protected function validator(array $data): \Illuminate\Validation\Validator
    {
        $user_model_fqn = config('backpack.base.user_model_fqn');
        $user = new $user_model_fqn();
        $users_table = $user->getTable();
        $phoneNumber_validation = backpack_authentication_column() == 'phoneNumber' ? 'numeric|' : '';

        return Validator::make($data, [
            'name' => 'required|max:255',
            backpack_authentication_column() => 'required|'.$phoneNumber_validation.'digits:10|unique:'.$users_table,
            'email' => 'required',
            'address' => 'required',
            'website' => 'nullable|url',
            'password' => 'required|min:8|confirmed',
            'logo' => 'nullable|mimes:jpeg,png,jpg,svg|max:2048'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     *
     * @return User
     */
    protected function create(array $data): User
    {
        $user_model_fqn = config('backpack.base.user_model_fqn');
        $user = new $user_model_fqn();

        $organization_model_fqn = config('auth.providers.organizations.model');
        $organization = new $organization_model_fqn();
        $new_user = $user->create([
            'name' => $data['name'],
            backpack_authentication_column() => $data[backpack_authentication_column()],
            'password' => bcrypt($data['password']),
        ]);

        $organization->create([
            'name' => $data['name'],
            'phoneNumber' => $data['phoneNumber'],
            'user_id' => $new_user->id,
            'email' => $data['email'],
            'address' => $data['address'],
            'website' => $data['website'],
            'logo' => $data['logo'],

        ]);
        return $user;
    }
}
