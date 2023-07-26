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
     * @param array $data
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
            backpack_authentication_column() => 'required|' . $phoneNumber_validation . 'digits:10|unique:' . $users_table . ',phoneNumber',
            'password' => 'required|min:8|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     *
     * @return User
     */
    protected function create(array $data): User
    {
        $user_model_fqn = config('backpack.base.user_model_fqn');
        $user = new $user_model_fqn();

        $unverified = $user->create([
            'name' => $data['name'],
            'phoneNumber' => $data['phoneNumber'],
            'password' => bcrypt($data['password']),
        ]);
        $unverified->assignRole('unverified');

        return $user;
    }
}
