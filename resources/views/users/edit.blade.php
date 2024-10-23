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
                        {{ __('Edit') }}
                    </li>
                </ol>
            </nav>
        </div>
    </x-slot>

    <div class="row">
        <div class="col-md-12">

            {!! Form::model($user, ['method' => 'PATCH','route' => ['users.update', $user->id]]) !!}

            <div class="box">
                <div class="box-header with-border">
                    <h4 class="box-title">Create New User</h4>
                    <div class="box-controls pull-right">
                        <a class="waves-effect waves-light btn mb-5 bg-gradient-danger"
                           href="{{ route('users.index') }}">Back</a>
                    </div>
                </div>

                <div class="box-body">

                    <div class="form-group">
                        <label>Name</label>
                        {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control')) !!}
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        {!! Form::text('email', null, array('placeholder' => 'Email','class' => 'form-control')) !!}
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        {!! Form::password('password', array('placeholder' => 'Password','class' => 'form-control')) !!}
                    </div>

                    <div class="form-group">
                        <label>Confirm Password</label>
                        {!! Form::password('confirm-password', array('placeholder' => 'Confirm Password','class' => 'form-control')) !!}
                    </div>

                    <div class="form-group">
                        <label>Roles</label>
                        {!! Form::select('roles[]', $roles,$userRole, array('class' => 'form-control','multiple')) !!}
                    </div>

                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-rounded btn-primary btn-outline">
                        <i class="ti-save-alt"></i> Save
                    </button>
                </div>

            </div>

            {!! Form::close() !!}

        </div>
    </div>

</x-app-layout>>
