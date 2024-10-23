<x-app-layout>
    <x-slot name="header">
        {{ __('Dashboard') }}
    </x-slot>
    <x-slot name="bread">
        <div class="d-inline-block align-items-center">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" aria-current="page">
                        <a href="{{ route('twilio.index') }}">
                            <i @class('fa fa-gear')></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item" aria-current="page">
                        <a href="{{ route('twilio.index') }}">
                            {{ __('Twilio Setting') }}
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
        <div class="col-xl-12 col-12">
            <div class="box">
                <div class="box-header with-border">
                    <h4 class="box-title">Twilio Settings</h4>
                </div>

                <form class="form" method="POST" action="{{ route('twilio.edit', ['id' => $data->id])}}"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <div class="form-group">
                            <label>Account SID</label>
                            <div class="input-group mb-3">
                                <input type="hidden" name="id" value="{{ !empty($data->id) ? $data->id : '' }}"/>
                                <input type="text" class="form-control" placeholder="Account SID" name="account_sid"
                                       value="{{ !empty($data->account_sid) ? $data->account_sid : '' }}">
                            </div>
                            @error('account_sid')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Auth Token</label>
                            <div class="input-group mb-3">
                                <input type="hidden" name="id" value="{{ !empty($data->id) ? $data->id : '' }}"/>
                                <input type="text" class="form-control" placeholder="Auth Token" name="auth_token"
                                       value="{{ !empty($data->auth_token) ? $data->auth_token : '' }}">
                            </div>
                            @error('auth_token')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Number</label>
                            <div class="input-group mb-3">
                                <input type="hidden" name="id" value="{{ !empty($data->id) ? $data->id : '' }}"/>
                                <input type="text" class="form-control" placeholder="Number" name="number"
                                       value="{{ !empty($data->number) ? $data->number : '' }}">
                            </div>
                            @error('number')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="box-footer">

                        <button type="submit" class="btn btn-rounded btn-primary btn-outline">
                            <i class="ti-save-alt"></i> Update
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    @section('page-scripts')
        <script>

        </script>
    @stop

</x-app-layout>

