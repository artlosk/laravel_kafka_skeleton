@extends('layouts.admin')

@section('admin_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Manage User Roles & Permissions</h3>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="user-roles-permissions-form" action="{{ route('backend.roles.update-user') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="user_id" class="form-label">Select User</label>
                    <select name="user_id" id="user_id" class="form-control select2" custom-select
                        data-url="{{ url('admin/users') }}">
                        <option value="">Select a user</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->getAttribute('id') }}">{{ $user->getAttribute('name') }}
                                ({{ $user->getAttribute('email') }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Roles</label>
                    <select name="roles[]" id="roles" class="form-control select2" custom-select>
                        @foreach ($roles as $role)
                            <option value="{{ $role->getAttribute('name') }}">{{ $role->getAttribute('name') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Permissions</label>
                    <select name="permissions[]" id="permissions" class="form-control select2" custom-select>
                        @foreach ($permissions as $permission)
                            <option value="{{ $permission->getAttribute('name') }}">{{ $permission->getAttribute('name') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update Roles & Permissions</button>
            </form>
        </div>
        <div class="card-footer">
            {{ $users->links() }}
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/backend/roles-manage.js')
    @endpush
@endsection