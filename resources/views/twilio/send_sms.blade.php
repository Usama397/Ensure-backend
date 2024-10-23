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
                        {{ __('Send SMS') }}
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

                <form class="form" method="POST" action="{{ route('twilio.sendSms')}}"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">

                        <div class="form-group">
                            <label>Number</label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="Number" name="number"
                                       value="{{ old('number') }}">
                            </div>
                            @error('number')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-rounded btn-primary btn-outline">
                            <i class="ti-save-alt"></i> Send
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

