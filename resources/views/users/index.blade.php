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
                        {{ __('List') }}
                    </li>
                </ol>
            </nav>
        </div>
    </x-slot>

    <div class="row">
        <div class="col-md-9">

            <div class="box">
                <div class="media-list media-list-divided media-list-hover">

                    @foreach ($data as $key => $user)
                        <div class="media align-items-center">
                            <span class="badge badge-dot badge-danger"></span>
                            <a class="avatar avatar-lg status-success" href="#">
                                <img src="{{ asset('themes/webkit/images/avatar/avatar-6.png') }}" alt="...">
                            </a>

                            <div class="media-body">
                                <p>
                                    <a href="{{ route('users.show',$user->id) }}"><strong>{{ $user->name }}</strong></a>
                                    <small class="sidetitle">{{ $user->id }}</small>
                                </p>
                                <p>{{ $user->email }}</p>
                                <p>
                                    @if(!empty($user->getRoleNames()))
                                        @foreach($user->getRoleNames() as $v)
                                            {{ $v }}
                                        @endforeach
                                    @endif
                                </p>
                            </div>

                            <div class="media-right gap-items">
                                <a class="media-action lead" href="{{ route('users.show',$user->id) }}"
                                   data-toggle="tooltip" title=""
                                   data-original-title="View"><i class="fa fa-square"></i></a>

                                <a class="media-action lead" href="{{ route('users.edit',$user->id) }}"
                                   data-toggle="tooltip" title=""
                                   data-original-title="Edit"><i class="fa fa-edit"></i></a>

                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-4">
            <div class="box no-shadow">
                <div class="box-body">
                    <a class="btn btn-outline btn-success mb-5 d-flex justify-content-between" href="javascript:void(0)">Total Users <span class="pull-right">{{ $data->count() }}</span></a>
                    <a class="btn btn-danger mt-10 d-flex justify-content-between" href="{{ route('users.index') }}">Users</a>
                    <a class="btn btn-danger mt-10 d-flex justify-content-between" href="{{ route('users.create') }}">Create New User</a>
                    <a class="btn btn-primary mt-10 d-flex justify-content-between" href="{{ route('roles.index') }}">Roles Listing</a>
                    <a class="btn btn-primary mt-10 d-flex justify-content-between" href="{{ route('roles.create') }}">Create New Role</a>
                </div>
            </div>
        </div>

    </div>

    {!! $data->render() !!}

    @section('page-scripts')

        <script>
            $(document).ready(function () {
                @if(session('success'))
                var successAlert = $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                    '{{ session('success') }}' +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span>' +
                    '</button>' +
                    '</div>');

                successAlert.insertBefore('.row');
                setTimeout(function () {
                    successAlert.alert('close');
                }, 5000);
                @endif
            });
        </script>

    @stop

</x-app-layout>
