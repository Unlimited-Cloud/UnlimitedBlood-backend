{{-- This file is used for menu items by any Backpack v6 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i
            class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
@if(backpack_user()->can('donors-access'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('donor') }}"><i class="nav-icon la la-heartbeat"></i>
            Donors</a></li>
@endif
@if(backpack_user()->can('glossary-access'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('glossary') }}"><i class="nav-icon la la-book"></i>
            Glossary</a></li>
@endif
@if(backpack_user()->can('organizations-access'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('organizations') }}"><i
                class="nav-icon la la-group"></i>
            Organizations</a></li>
@endif
@if(backpack_user()->can('inventory-access'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('inventory') }}"><i
                class="nav-icon la la-boxes"></i>
            Inventory</a></li>
@endif
@if(backpack_user()->can('donations-access'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('donations') }}"><i
                class="nav-icon la la-hand-holding-heart"></i> Donations</a></li>
@endif
@if(backpack_user()->can('requests-access'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('requests') }}"><i
                class="nav-icon la la-hands-helping"></i> Requests</a></li>
@endif
@if(backpack_user()->can('camps-access'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('camps') }}"><i
                class="nav-icon la la-campground"></i>
            Camps</a></li>
@endif
@if(backpack_user()->can('organizers-access'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('organizers') }}"><i
                class="nav-icon la la-gavel"></i>
            Organizers</a></li>
@endif
@if(backpack_user()->can('user-access'))
    <li class="nav-item nav-dropdown">

        <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-users"></i> Authentication</a>
        <ul class="nav-dropdown-items">

            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i
                        class="nav-icon la la-user"></i>
                    <span>Users</span></a></li>

            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('role') }}"><i
                        class="nav-icon la la-id-badge"></i> <span>Roles</span></a></li>
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('permission') }}"><i
                        class="nav-icon la la-key"></i> <span>Permissions</span></a></li>

        </ul>
    </li>
@endif
