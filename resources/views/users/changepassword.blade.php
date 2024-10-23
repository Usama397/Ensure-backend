<x-app-layout>

    <x-slot name="header">
        {{ __('Change Password') }}
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
                        <a href="{{ route('dashboard') }}">
                            {{ __('Dashboard') }}
                        </a>
                    </li>
                </ol>
            </nav>
        </div>
    </x-slot>

    <div class="row">
        <div class="col-md-12">

            {!! Form::open(array('route' => 'change.password','method'=>'POST')) !!}

            <div class="box">
                <div class="box-header with-border">
                    <h4 class="box-title">Change Password</h4>
                    <div class="box-controls pull-right">
                        <a class="waves-effect waves-light btn mb-5 bg-gradient-danger"
                           href="{{ route('dashboard') }}">Back</a>
                    </div>
                </div>

                <div class="box-body">

                    <div class="form-group">
                        <label>Current Password</label>
                        {!! Form::password('current-password', array('placeholder' => 'Current Password','class' => 'form-control')) !!}
                    </div>

                    <div class="form-group">
                        <label>New Password</label>
                        {!! Form::password('new-password', array('placeholder' => 'New Password','class' => 'form-control')) !!}
                        <span>The password must be at least 10 characters and contain at least one uppercase character, one number, and one special character.</span>
                    </div>

                    <div class="form-group">
                        <label>Confirm Password</label>
                        {!! Form::password('new-password_confirmation', array('placeholder' => 'Confirm Password','class' => 'form-control')) !!}
                    </div>

                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-rounded btn-primary btn-outline">
                        <i class="ti-save-alt"></i> Change Password
                    </button>
                </div>

            </div>

            {!! Form::close() !!}

        </div>
    </div>

</x-app-layout>>
