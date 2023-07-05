@extends(backpack_view('blank'))

@php
    if (backpack_theme_config('show_getting_started')) {
        $widgets['before_content'][] = [
            'type'        => 'view',
            'view'        => backpack_view('inc.getting_started'),
        ];
    } else {
        $widgets['before_content'][] = [
            'type'        => 'jumbotron',
            'heading'     => trans('backpack::base.welcome'),
            'content'     => 'Welcome to the admin panel. Please use the menu on the left to navigate.',
            'button_link' => backpack_url('logout'),
            'button_text' => trans('backpack::base.logout'),
        ];
    }
    if (backpack_user()->roles->isEmpty()) {
       Widget::add()
    ->to('after_content')
    ->type('alert')
    ->class('alert alert-danger mb-2')
    ->heading('Notice')
    ->content('Please wait while we verify your account. It may take up to 24 hours.');
    }
    if (backpack_user()->hasRole('donor')) {
         Widget::add()
    ->to('after_content')
    ->type('alert')
    ->class('alert alert-danger mb-2')
    ->heading('Notice')
    ->content('Use the BloodNepal app for more features like tracking blood pressure, sending blood requests, and more.');
    }
@endphp
# section will include links to app and other stuff
@section('content')
    <p> Download it from the Play Store: <a
            href="https://play.google.com/store/apps/details?id=com.bloodnepal.bloodnepal">BloodNepal</a></p>
    <p> Download it from the IOS Store: <a href="http://github.com/sidtuladhar">BloodNepal</a></p>
@endsection
