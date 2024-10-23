<x-app-layout>
    <x-slot name="header">
        {{ __('Dashboard') }}
    </x-slot>
    <x-slot name="bread">
        <div class="d-inline-block align-items-center">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" aria-current="page">
                        <a href="{{ route('dashboard') }}">
                            <i @class('fa fa-dashboard')></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ __('Dashboard') }}
                    </li>
                </ol>
            </nav>
        </div>
    </x-slot>

    <div class="row">
    </div>
</x-app-layout>
