@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <section class="py-5 bg-light">
        <div class="container text-center">
            <h1 class="display-3 fw-bold text-dark mb-4">Welcome to {{ config('app.name', 'Laravel') }}!</h1>
            <p class="lead text-muted mb-5">Join our community to create, share, and explore amazing posts.</p>
            <div class="d-flex justify-content-center gap-3">
                @guest
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Login
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-person-plus me-2"></i>Register
                    </a>
                @endguest
                <a href="{{ route('frontend.posts.index') }}" class="btn btn-success btn-lg">
                    <i class="bi bi-file-text me-2"></i>View Posts
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Why Join Us?</h2>
            <div class="row g-4">
                <!-- Feature 1: Create Posts -->
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0 transition-all" style="transition: transform 0.3s;">
                        <div class="card-body text-center">
                            <i class="bi bi-pencil-square display-4 text-success mb-3"></i>
                            <h5 class="card-title fw-bold">Create Posts</h5>
                            <p class="card-text text-muted">Share your ideas and stories with the world.</p>
                        </div>
                    </div>
                </div>
                <!-- Feature 2: Manage Profile -->
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0 transition-all" style="transition: transform 0.3s;">
                        <div class="card-body text-center">
                            <i class="bi bi-person-circle display-4 text-primary mb-3"></i>
                            <h5 class="card-title fw-bold">Manage Profile</h5>
                            <p class="card-text text-muted">Customize your account and keep it secure.</p>
                        </div>
                    </div>
                </div>
                <!-- Feature 3: Explore Community -->
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0 transition-all" style="transition: transform 0.3s;">
                        <div class="card-body text-center">
                            <i class="bi bi-people display-4 text-info mb-3"></i>
                            <h5 class="card-title fw-bold">Explore Community</h5>
                            <p class="card-text text-muted">Connect with others and discover new content.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-4 bg-dark text-white text-center">
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
        </div>
    </footer>

    <style>
        .card:hover {
            transform: translateY(-5px);
        }

        .btn-lg {
            padding: 0.75rem 1.5rem;
        }
    </style>
@endsection
