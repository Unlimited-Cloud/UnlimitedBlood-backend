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
            'heading'     => 'Welcome ' . backpack_user()->name,
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
    ->to('before_content')
    ->type('alert')
    ->class('alert alert-danger mb-2')
    ->heading('Notice')
    ->content('Use the BloodNepal app for more features like tracking blood pressure, sending blood requests, and more.
    ');
    }
    if (!backpack_user()->hasRole('donor')) {
            Widget::add()
    ->to('after_content')
    ->type('progress')
    ->class('card text-white bg-red mb-2')
    ->value(DB::table('donors')->count())
    ->description('Donors')
    ->progress(DB::table('donors')->count())
    ->hint(100 - DB::table('donors')->count() . ' more donors needed to reach 100');
    }

@endphp

@section('content')

    @if(backpack_user()->hasRole('donor'))
        <div>
            <p> Download it from the Play Store: <a
                    href="https://play.google.com/store/apps/details?id=com.bloodnepal.bloodnepal">BloodNepal</a></p>
            <p> Download it from the IOS Store: <a href="http://github.com/sidtuladhar">BloodNepal</a></p>
        </div>
    @endif

@endsection
