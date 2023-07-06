<?php

namespace App\Http\Controllers\Admin\Auth;

use Backpack\CRUD\app\Http\Controllers\MyAccountController as BackpackMyAccountController;
use Backpack\CRUD\app\Http\Requests\AccountInfoRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Prologue\Alerts\Facades\Alert;

class MyAccountController extends BackpackMyAccountController
{
    public function getAccountInfoForm(): Factory|\Illuminate\Foundation\Application|View|Application
    {

        $this->data['title'] = trans('backpack::base.my_account');
        $this->data['user'] = $this->guard()->user();
        if (backpack_user()->hasRole('organization')) {
            $this->data['organization'] = backpack_user()->organizations;
        }
        if (backpack_user()->hasRole('donor')) {
            $this->data['donor'] = backpack_user()->donors;
        }
        return view('my_account', $this->data);

    }

    public function postAccountInfoForm(AccountInfoRequest $request): RedirectResponse
    {
        $result = $this->guard()->user()->update([
            'name' => $request->name,
            'phoneNumber' => $request->phoneNumber
        ]);

        if (backpack_user()->hasRole('organization')) {
            $result2 = backpack_user()->organizations()->update([
                'name' => $request->name,
                'phoneNumber' => $request->phoneNumber,
                'email' => $request->email,
                'website' => $request->website,
            ]);


            if ($result2) {
                Alert::success('Organization info updated.')->flash();
            } else {
                Alert::error('Error updating organization info.')->flash();
            }
        }

        if (backpack_user()->hasRole('donor')) {
            $result2 = backpack_user()->donors()->update([
                'name' => $request->name,
                'phoneNumber' => $request->phoneNumber,
                'email' => $request->email,
            ]);

            if ($result2) {
                Alert::success('Donor info updated.')->flash();
            } else {
                Alert::error('Error updating donor info.')->flash();
            }
        }

        if ($result) {
            Alert::success(trans('backpack::base.account_updated'))->flash();
        } else {
            Alert::error(trans('backpack::base.error_saving'))->flash();
        }

        return redirect()->back();
    }
}
