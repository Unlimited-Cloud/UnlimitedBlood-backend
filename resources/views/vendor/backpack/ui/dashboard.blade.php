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
@endphp

@section('content')

@endsection
