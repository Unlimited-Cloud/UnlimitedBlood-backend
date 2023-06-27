{{-- This file is used to store sidebar items, inside the Backpack admin panel --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i
            class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

<li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-user"></i> Users</a>
</li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('donor') }}"><i class="nav-icon la la-heartbeat"></i>
        Donors</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('glossary') }}"><i class="nav-icon la la-book"></i>
        Glossary</a></li>

<li class="nav-item"><a class="nav-link" href="{{ backpack_url('donations') }}"><i
            class="nav-icon la la-hand-holding-heart"></i> Donations</a></li>

<li class="nav-item"><a class="nav-link" href="{{ backpack_url('organizations') }}"><i class="nav-icon la la-group"></i>
        Organizations</a></li>

<li class="nav-item"><a class="nav-link" href="{{ backpack_url('inventory') }}"><i class="nav-icon la la-boxes"></i>
        Inventories</a></li>

<li class="nav-item"><a class="nav-link" href="{{ backpack_url('camps') }}"><i class="nav-icon la la-campground"></i>
        Camps</a></li>

<li class="nav-item"><a class="nav-link" href="{{ backpack_url('camp-donors') }}"><i
            class="nav-icon la la-user-plus"></i> Camp donors</a></li>

<li class="nav-item"><a class="nav-link" href="{{ backpack_url('requests') }}"><i
            class="nav-icon la la-hands-helping"></i> Requests</a></li>
