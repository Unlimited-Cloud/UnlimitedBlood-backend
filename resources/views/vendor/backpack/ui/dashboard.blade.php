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
    ->content('Use the UnlimitedBlood app for more features like tracking blood pressure, sending blood requests, and more.
    ');
    }
    if (backpack_user()->hasRole('admin')) {
            Widget::add()
    ->to('after_content')
    ->type('progress')
    ->class('card text-white bg-red mb-2')
    ->value(DB::table('donors')->count())
    ->description('Donors')
    ->progress(DB::table('donors')->count())
    ->hint(100 - DB::table('donors')->count() . ' more donors needed to reach 100');
    }

    if (backpack_user()->hasRole('organizer')) {
        $totalCamps = DB::table('camps')->count();
        $userOrganizationCamps = DB::table('camps')
        ->where('organizationId', backpack_user()->organizationId)->count();
        $totalRequests = DB::table('requests')->count();
        $userOrganizationRequests = DB::table('requests')
        ->where('fulfilled_by', backpack_user()->organizationId)->count();
        [
        'type'    => 'div',
        'class'   => 'row',
        'content' => [ // widgets here
        [Widget::make()
    ->group('after_content')
    ->type('progress')
    ->class('card text-white bg-red mb-2')
    ->value(DB::table('requests')->where('fulfilled_by', backpack_user()->organizationId)->count())
    ->description('Requests')
    ->progress($userOrganizationRequests / $totalRequests * 100)
    ->hint(number_format($userOrganizationRequests / $totalRequests * 100, 2) . '% of requests fulfilled by your organization.')],
        [Widget::make()
    ->group('after_content')
    ->type('progress')
    ->class('card text-white bg-red mb-2')
    ->value(DB::table('camps')->where('organizationId', backpack_user()->organizationId)->count())
    ->description('Camps')
    ->progress($userOrganizationCamps / $totalCamps * 100)
    ->hint(number_format($userOrganizationCamps / $totalCamps * 100, 2) . '% of camps organized by your organization.')
        ],
        [ 'type' => 'card', 'content' => ['body' => 'Three'] ],
        ]];


    }

@endphp

@section('content')

    @if(backpack_user()->hasRole('donor'))
        <div>
            <p> Download it from the Play Store: <a
                    href="https://play.google.com/store/apps/details?id=com.UnlimitedBlood.UnlimitedBlood">UnlimitedBlood</a>
            </p>
            <p> Download it from the IOS Store: <a href="http://github.com/sidtuladhar">UnlimitedBlood</a></p>
        </div>
    @endif

@endsection
