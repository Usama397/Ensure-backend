<!-- sidebar-->
<section class="sidebar position-relative">
    <div class="user-profile px-10 py-15">
        <div class="d-flex align-items-center">
            <div class="image">
                <img src="{{ asset('themes/webkit/images/avatar/avatar-6.png') }}"
                     class="avatar avatar-lg bg-primary-light" alt="User Image">
            </div>
            <div class="info ml-10">
                <p class="mb-0">Welcome</p>
                <h5 class="mb-0">{{ Auth::user()->name }}</h5>
            </div>
        </div>
    </div>

    <div class="multinav">
        <div class="multinav-scroll" style="height: 100%;">
            <!-- sidebar menu-->
            <ul class="sidebar-menu" data-widget="tree">
                <li class="{{ route('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}">
                        <i class="icon-Layout-4-blocks"><span class="path1"></span><span class="path2"></span></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                @hasanyrole('superadmin')
                <li class="treeview">

                    <a href="javascript:void(0)">
                        <i class="icon-Layout-grid"><span class="path1"></span><span class="path2"></span></i>
                        <span>Management</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-right pull-right treeview"></i>
                        </span>
                    </a>

                    <ul class="treeview-menu">
                        <li class="{{ request()->is('roles') ? 'active' : '' }}">
                            <a href="{{ route('roles.index') }}">
                                <i class="icon-Commit"><span class="path1"></span><span
                                        class="path2"></span></i>
                                {{ 'Roles' }}
                            </a>
                        </li>
                        <li class="{{ request()->is('users') ? 'active' : '' }}">
                            <a href="{{ route('users.index') }}">
                                <i class="icon-Commit"><span class="path1"></span><span
                                        class="path2"></span></i>
                                {{ 'Users' }}
                            </a>
                        </li>
                        {{--<li class="{{ request()->is('twilio') ? 'active' : '' }}">
                            <a href="{{ route('twilio.index') }}">
                                <i class="icon-Commit"><span class="path1"></span><span
                                        class="path2"></span></i>
                                {{ 'Twilio Settings' }}
                            </a>
                        </li>--}}
                    </ul>
                </li>
                @endhasanyrole


            </ul>
        </div>
    </div>
</section>
