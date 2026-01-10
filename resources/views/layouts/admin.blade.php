@extends('adminlte::page')

@section('title', 'Admin Panel')

@section('css')
    @vite(['resources/css/backend/admin.css', 'resources/css/backend/admin-custom.css', 'resources/css/backend/media-gallery.css'])
@stop

@section('content_top_nav_right')
    <li class="nav-item" style="margin-right: 8px;">
        <button class="btn btn-outline-primary btn-sm" onclick="toggleDarkMode()"
                id="dark-mode-toggle-btn" data-dark-mode-toggle="true"
                style="margin: 0; vertical-align: middle;">
            <i class="fas fa-moon"></i> <span class="d-none d-md-inline">Темная тема</span>
        </button>
    </li>
@stop

@section('js')
    <script>
        window.appConfig = {
            routes: {
                mediaIndex: "{{ route('backend.media.index') }}",
                mediaGetByIds: "{{ route('backend.media.getByIds') }}",
                filepondUpload: "{{ route('backend.filepond.upload') }}",
                filepondDelete: "{{ route('backend.filepond.delete') }}"
            }
        };
    </script>
    @vite(['resources/js/backend/admin.js', 'resources/js/backend/admin-media.js'])
@stop

@section('content_header')
    <h1>Admin Dashboard</h1>
@stop

@section('content')
    @yield('admin_content')
@stop
