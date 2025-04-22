@extends('layouts.guest')
@section('content')

<style>
    /* Animations */
    .fade-in {
        animation: fadeIn 1s ease forwards;
        opacity: 0;
    }

    @keyframes fadeIn {
        to {
            opacity: 1;
            transform: translateY(0);
        }
        from {
            opacity: 0;
            transform: translateY(20px);
        }
    }

    /* Form styling */
    .form-control {
        background-color: #fff;
        border: 1px solid #ccc;
        padding: 12px 15px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.4);
    }

    .btn-primary {
        background-color: #007bff;
        border: none;
        padding: 12px;
        font-size: 16px;
        border-radius: 8px;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }
</style>
<section class="vh-100" style="background-color: #eee;">
    <div class="container h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-lg-12 col-xl-11">
                <div class="card text-black" style="border-radius: 25px;">
                    <div class="card-body p-md-5">
                        <div class="row justify-content-center">
                            <!-- Right side image (kept) -->
                            <div class="col-md-10 col-lg-6 col-xl-7 d-flex align-items-center order-1 order-lg-2 fade-in">
                                <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.webp"
                                    class="img-fluid" alt="Sample image">
                            </div>

                            <!-- Login Form -->
                            <div class="col-md-10 col-lg-6 col-xl-5 order-2 order-lg-1 fade-in">
                                <div class="text-center mb-4">
                                    <img src="/images/unicorn-removebg-preview.png" alt="Unicorn Logo" class="img-fluid" style="max-height: 100px;">
                                </div>

                                @if (session('status'))
                                    <div class="mb-4 font-medium text-sm text-green-600 text-center">
                                        {{ session('status') }}
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('login') }}">
                                    @csrf

                                    <h4 class="text-center mb-4 fw-bold">Welcome Back</h4>

                                    <!-- Email -->
                                    <div class="mb-4">
                                        <label class="form-label" for="form3Example3">Email address</label>
                                        <input type="email" id="form3Example3" name="email"
                                            class="form-control"
                                            placeholder="Enter your email" />
                                    </div>

                                    <!-- Password -->
                                    <div class="mb-4">
                                        <label class="form-label" for="password">Password</label>
                                        <input id="password" type="password" name="password"
                                            class="form-control"
                                            required autocomplete="current-password" />
                                    </div>

                                    <!-- Remember & Forgot -->
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                                            <label class="form-check-label" for="remember_me">
                                                Remember me
                                            </label>
                                        </div>
                                        @if (Route::has('password.request'))
                                            <a href="{{ route('password.request') }}" class="text-decoration-none">
                                                Forgot your password?
                                            </a>
                                        @endif
                                    </div>

                                    <!-- Validation Errors -->
                                    @if (session('error'))
                                        <div class="alert alert-danger">
                                            {{ session('error') }}
                                        </div>
                                    @endif

                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <!-- Login Button -->
                                    <div class="d-grid mt-4">
                                        <x-button class="btn btn-primary btn-lg">
                                            {{ __('Login') }}
                                        </x-button>
                                    </div>
                                </form>
                            </div>
                            <!-- End Login Form -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('backend.partials.footer')
</section>


    <script>
            // Check for error message in localStorage
            const errorMessage = localStorage.getItem('login_error');

        if (errorMessage) {
            // Display the error message (here it's shown as an alert, but you can customize this)
            alert(errorMessage);

            // Optionally remove the error message from localStorage after displaying it
            localStorage.removeItem('login_error');
        }
    </script>

<script>
    $(document).ready(function () {
        $(".toggle-password").on("click", function () {
            const input = $("#password");
            const icon = $(this);

            // Toggle input type
            const type = input.attr("type") === "password" ? "text" : "password";
            input.attr("type", type);

            // Toggle icon class
            icon.toggleClass("fa-eye fa-eye-slash");
        });
    });
</script>
@endsection

