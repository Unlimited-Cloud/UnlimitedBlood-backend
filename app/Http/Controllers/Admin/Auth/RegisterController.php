<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\Auth\RegisterController as BackpackRegisterController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
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

        $organization_model_fqn = config('auth.providers.organizations.model');
        $organization = new $organization_model_fqn();
        $organizations_table = $organization->getTable();

        return Validator::make($data, [
            'name' => 'required|max:255',
            backpack_authentication_column() => 'required|'.$phoneNumber_validation.'digits:10|unique:'.$organizations_table,
            'personal_name' => 'required|max:255',
            'personal_num' => 'required|numeric|digits:10|unique:'.$users_table.',phoneNumber',
            'email' => 'required|unique:'.$organizations_table,
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

        $organization->create([
            'name' => $data['name'],
            'phoneNumber' => $data['phoneNumber'],
            'email' => $data['email'],
            'address' => $data['address'],
            'latitude' => 30.0444,
            'longitude' => 31.2357,
            'website' => $data['website'],
            'logo' => $data['logo'],
        ]);
        $user->create([
            'name' => $data['personal_name'],
            'phoneNumber' => $data['personal_num'],
            'password' => bcrypt($data['password']),
            'organizationId' => DB::table('organizations')->where('phoneNumber', $data['phoneNumber'])->value('id')
        ]);

        return $user;
    }
}
