@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="py-5">
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold text-dark">Welcome, {{ $user->name }}!</h1>
            <p class="lead text-muted">Manage your account and explore your posts from here.</p>
        </div>

        <div class="row g-4">
            <!-- Card: Profile -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 transition-all" style="transition: transform 0.3s;">
                    <div class="card-body text-center">
                        <i class="bi bi-person-circle display-4 text-primary mb-3"></i>
                        <h5 class="card-title fw-bold">Your Profile</h5>
                        <p class="card-text text-muted">Update your name and email address.</p>
                        <a href="{{ route('frontend.profile') }}" class="btn btn-primary mt-3">Edit Profile</a>
                    </div>
                </div>
            </div>

            <!-- Card: Change Password -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 transition-all" style="transition: transform 0.3s;">
                    <div class="card-body text-center">
                        <i class="bi bi-lock-fill display-4 text-danger mb-3"></i>
                        <h5 class="card-title fw-bold">Change Password</h5>
                        <p class="card-text text-muted">Secure your account with a new password.</p>
                        <a href="{{ route('frontend.password') }}" class="btn btn-outline-danger mt-3">Change
                            Password</a>
                    </div>
                </div>
            </div>

            <!-- Card: Posts -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 transition-all" style="transition: transform 0.3s;">
                    <div class="card-body text-center">
                        <i class="bi bi-file-text display-4 text-success mb-3"></i>
                        <h5 class="card-title fw-bold">Your Posts</h5>
                        <p class="card-text text-muted">You have {{ $user->posts()->count() }} post(s).</p>
                        <a href="{{ route('frontend.posts.index') }}" class="btn btn-outline-success mt-3">View
                            Posts</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card:hover {
            transform: translateY(-5px);
        }
    </style>
@endsection
