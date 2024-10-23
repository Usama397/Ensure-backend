<x-app-layout>
    <x-slot name="header">
        {{ __('Twilio') }}
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
                        {{ __('List') }}
                    </li>
                </ol>
            </nav>
        </div>
    </x-slot>


    <div class="col-md-12">
        <div class="box">
            <div class="box-header">
                <h4 class="box-title">Twilio Settings</h4>
                <div class="box-controls pull-right d-md-flex d-none">
                    <a href="{{ route('twilio.sendTestSms') }}" class="waves-effect waves-light btn mb-5 bg-gradient-success">Send Sms</a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="table-responsive">
                        <table id="data-table" class="table table-striped table-bordered table-hover dataTable"
                               aria-describedby="data-table_info" role="grid">
                            <thead>
                            <tr role="row" class="text-center">
                                <th>Sr#</th>
                                <th>Account SID</th>
                                <th>Auth Token</th>
                                <th>Number</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('page-scripts')

        <script>
            $(function () {
                var table = $('.dataTable').DataTable({
                    searching: false,
                    processing: true,
                    serverSide: true,
                    paging: false,
                    ajax: this.route,
                    columns: [
                        {data: 'id', name: 'id'},
                        {data: 'account_sid', name: 'account_sid'},
                        {data: 'auth_token', name: 'auth_token'},
                        {data: 'number', name: 'number'},
                        {data: 'action', name: 'action', orderable: true},
                    ],
                    createdRow: function (row, data, dataIndex) {
                        // Add the 'text-center' class to center-align the rows
                        $(row).addClass('text-center');
                    }
                });
            });

            $(document).ready(function () {
                @if(session('success'))
                // Create a Bootstrap alert div for success message
                var successAlert = $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                    '{{ session('success') }}' +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span>' +
                    '</button>' +
                    '</div>');

                successAlert.insertBefore('.col-md-12');
                setTimeout(function () {
                    successAlert.alert('close');
                }, 5000);

                @elseif(session('danger'))

                var dangerAlert = $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                    '{{ session('danger') }}' +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span>' +
                    '</button>' +
                    '</div>');
                dangerAlert.insertBefore('.col-md-12');
                setTimeout(function () {
                    dangerAlert.alert('close');
                }, 5000);

                @elseif(session('warning'))

                var warningAlert = $('<div class="alert alert-warning alert-dismissible fade show" role="alert">' +
                    '{{ session('warning') }}' +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span>' +
                    '</button>' +
                    '</div>');

                warningAlert.insertBefore('.col-md-12');
                setTimeout(function () {
                    warningAlert.alert('close');
                }, 5000);

                @endif
            });
        </script>

    @stop

</x-app-layout>

