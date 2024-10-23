<x-guest-layout>
    <div class="row align-items-center justify-content-md-center h-p100">

        <div class="col-12">
            <div class="row justify-content-center no-gutters">
                <div class="col-lg-5 col-md-5 col-12">
                    <div class="bg-white rounded30 shadow-lg">
                        <div class="content-top-agile p-20 pb-0">
                            <h2 class="text-primary">UPS Management</h2>
                            <p class="mb-0">Welcome</p>
                        </div>
                        <div class="p-40">
                            <!-- Session Status -->
                            <x-auth-session-status class="mb-4" :status="session('status')"/>

                            <!-- Validation Errors -->
                            <x-auth-validation-errors class="mb-4" :errors="$errors"/>

                            <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- Email Address -->
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-transparent">
                                                <i class="ti-user"></i>
                                            </span>
                                        </div>
                                        <input id="email" type="text" class="form-control pl-15 bg-transparent"
                                               name="email" required placeholder="Email">
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-transparent">
                                                <i class="ti-lock"></i>
                                            </span>
                                        </div>
                                        <input id="password" type="password" class="form-control pl-15 bg-transparent"
                                               name="password" required placeholder="Password">
                                    </div>
                                </div>

                                <!-- Remember Me -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="checkbox">
                                            <input type="checkbox" id="remember_me" name="remember">
                                            <label for="basic_checkbox_1">{{ __('Remember me') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.col -->
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-danger mt-10">{{ __('Log in') }}</button>
                                </div>
                                <!-- /.col -->

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-guest-layout>
