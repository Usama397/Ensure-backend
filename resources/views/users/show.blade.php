<x-app-layout>

    <x-slot name="header">
        {{ __('User Management') }}
    </x-slot>

    <x-slot name="bread">
        <div class="d-inline-block align-items-center">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" aria-current="page">
                        <a href="{{ route('users.index') }}">
                            <i @class('fa fa-users')></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item" aria-current="page">
                        <a href="{{ route('users.index') }}">
                            {{ __('Users') }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ __('Profile') }}
                    </li>
                </ol>
            </nav>
        </div>
    </x-slot>

    <div class="row">
        <div class="col-md-7">

            <div class="box box-widget widget-user">
                <!-- Add the bg color to the header using any of the bg-* classes -->
                <div class="widget-user-header bg-black"
                     style="background: url({{ asset('themes/webkit/images/full/10.jpg') }}) center center;">
                    <h3 class="widget-user-username">{{ $user->name }}</h3>
                </div>
                <div class="widget-user-image">
                    <img class="rounded-circle" src="{{ asset('themes/webkit/images/avatar/avatar-6.png') }}"
                         alt="User Avatar">
                </div>
                <div class="box-footer">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="description-block">
                                <h5 class="description-header">{{ $user->name }}</h5>
                                <span class="description-text">Name</span>
                            </div>
                            <!-- /.description-block -->
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-6 bl-1">
                            <div class="description-block">
                                <h5 class="description-header">Email</h5>
                                <span class="description-text">{{ $user->email }}</span>
                            </div>
                            <!-- /.description-block -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
            </div>

        </div>

        <div class="col-md-5">

            <div class="box">
                <div class="box-body">
                    <div class="flexbox align-items-baseline mb-20">
                        <h6 class="text-uppercase ls-2">Roles</h6>
                        <small>Assigned Roles</small>
                    </div>
                    <div class="gap-items-2 gap-y">
                        @if(!empty($user->getRoleNames()))
                            @foreach($user->getRoleNames() as $v)
                                <span class="badge badge-pill badge-warning">{{ $v }}</span>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
