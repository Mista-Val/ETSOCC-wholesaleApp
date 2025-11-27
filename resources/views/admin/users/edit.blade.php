@extends('admin.sub_layout')
@section('title', 'Edit User')
@section('sub_content')

<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">

            <div class="page-header d-block">
                <h2 class="main-content-title tx-24 mg-b-5">Edit User</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url("admin/dashboard") }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit User</li>
                </ol>
            </div>

            <div class="row sidemenu-height">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            {!! Form::model($user, ['route' => ['admin.users.update', $user->id], 'method' => 'PUT', 'id' => 'userForm']) !!}

                            <div class="row">
                                {{-- Full Name --}}
                                <div class="form-group col-md-6">
                                    {!! Form::label('fullname', 'Full Name') !!} <span class="text-danger">*</span>
                                    {!! Form::text('fullname', old('fullname', $user->first_name . ' ' . $user->last_name), ['class' => 'form-control', 'placeholder' => 'Enter Full Name']) !!}
                                    <div class="text-danger">{{ $errors->first('fullname') }}</div>
                                </div>

                                {{-- Email --}}
                                <div class="form-group col-md-6">
                                    {!! Form::label('email', 'Email') !!} <span class="text-danger">*</span>
                                    {!! Form::email('email', old('email'), ['class' => 'form-control', 'placeholder' => 'Enter Email']) !!}
                                    <div class="text-danger">{{ $errors->first('email') }}</div>
                                </div>

                                {{-- Role --}}
                                <div class="form-group col-md-6">
                                    {!! Form::label('role', 'Role') !!} <span class="text-danger">*</span>
                                    <div class="select-down-arrow">
                                        <select name="role" id="role" class="form-control" onchange="filterPermissions()">
                                            <option value="">Select Role</option>
                                            @foreach (config('global.roles') as $key => $role)
                                                <option value="{{ $key }}" {{ old('role', $user->role) == $key ? 'selected' : '' }}>
                                                    {{ $role }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="text-danger">{{ $errors->first('role') }}</div>
                                </div>

                                {{-- Status --}}
                                <div class="form-group col-md-6">
                                    {!! Form::label('status', 'Status') !!} <span class="text-danger">*</span>
                                    <div class="select-down-arrow">
                                        {!! Form::select('status', [1 => 'Active', 0 => 'Inactive'], old('status', $user->status), ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="text-danger">{{ $errors->first('status') }}</div>
                                </div>

                                {{-- Password (optional) --}}
                                <div class="form-group col-md-6">
                                    {!! Form::label('password', 'Password') !!}
                                    <div class="position-relative">
                                        <input type="password" 
                                               name="password" 
                                               id="password" 
                                               class="form-control" 
                                               placeholder="Enter Password (leave blank to keep current)" 
                                               autocomplete="off">
                                        <span class="show-password" id="toggle-password" onclick="showPassword(event, 'password')">
                                            <i class="far fa-eye-slash"></i>
                                        </span>
                                    </div>
                                    <div class="text-danger">{{ $errors->first('password') }}</div>
                                </div>
                            
                                {{-- Confirm Password (optional) --}}
                                <div class="form-group col-md-6">
                                    {!! Form::label('password_confirmation', 'Confirm Password') !!}
                                    <div class="position-relative">
                                        <input type="password" 
                                               name="password_confirmation" 
                                               id="password_confirmation" 
                                               class="form-control" 
                                               placeholder="Confirm Password (leave blank to keep current)" 
                                               autocomplete="off">
                                        <span class="show-password" id="toggle-password_confirmation" onclick="showPassword(event, 'password_confirmation')">
                                            <i class="far fa-eye-slash"></i>
                                        </span>
                                    </div>
                                    <div class="text-danger">{{ $errors->first('password_confirmation') }}</div>
                                </div>

                                {{-- Permissions Section --}}
                                <div class="form-group col-md-12" id="permissions-section">
                                    {!! Form::label('permissions', 'Permissions') !!} <span class="text-danger">*</span>
                                    <div class="row" id="permissions-container">
                                        @foreach($permissions as $permission)
                                            <div class="col-md-3 permission-item" data-guard="{{ $permission->guard_name }}">
                                                <label>
                                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                        {{ (is_array(old('permissions')) && in_array($permission->id, old('permissions'))) || 
                                                           (!old('permissions') && $user->permissions->contains('id', $permission->id)) ? 'checked' : '' }}>
                                                    {{ ucfirst($permission->name) }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="text-danger">{{ $errors->first('permissions') }}</div>
                                </div>
                            </div>

                            {{-- Submit Buttons --}}
                            <div class="form-actions mt-3">
                                <button class="btn btn-primary" type="submit">Update</button>
                                <a class="btn btn-secondary ml-3" href="{{ route('admin.users.index') }}">Cancel</a>
                            </div>

                            {!! Form::close() !!}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Store passwords in sessionStorage before form submit
    document.getElementById('userForm').addEventListener('submit', function(e) {
        const role = document.getElementById('role').value;
        const checkedPermissions = document.querySelectorAll('input[name="permissions[]"]:checked');
        
        // Client-side validation for permissions
        if (role && checkedPermissions.length === 0) {
            e.preventDefault();
            alert('Please select at least one permission.');
            
            // Show permissions section if hidden
            const permissionsSection = document.getElementById('permissions-section');
            if (permissionsSection.style.display === 'none') {
                permissionsSection.style.display = 'block';
            }
            
            // Scroll to permissions section
            permissionsSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            return false;
        }

        // Store passwords before submission
        const password = document.getElementById('password').value;
        const passwordConfirmation = document.getElementById('password_confirmation').value;
        
        if (password || passwordConfirmation) {
            sessionStorage.setItem('temp_password', password);
            sessionStorage.setItem('temp_password_confirmation', passwordConfirmation);
        }
    });

    // Restore passwords on page load if there are validation errors
    document.addEventListener('DOMContentLoaded', function() {
        @if($errors->any())
            const storedPassword = sessionStorage.getItem('temp_password');
            const storedPasswordConfirmation = sessionStorage.getItem('temp_password_confirmation');
            
            if (storedPassword) {
                document.getElementById('password').value = storedPassword;
            }
            if (storedPasswordConfirmation) {
                document.getElementById('password_confirmation').value = storedPasswordConfirmation;
            }
        @else
            // Clear sessionStorage if no errors (successful submission or fresh page)
            sessionStorage.removeItem('temp_password');
            sessionStorage.removeItem('temp_password_confirmation');
        @endif
        
        // Filter permissions on page load
        filterPermissions();
    });

    function showPassword(event, id) {
        const passwordField = document.getElementById(id);
        const toggleButton = document.getElementById('toggle-' + id);
        
        if (passwordField.type === "text") {
            passwordField.type = 'password';
            toggleButton.innerHTML = '<i class="far fa-eye-slash"></i>';
        } else {
            passwordField.type = 'text';
            toggleButton.innerHTML = '<i class="far fa-eye"></i>';
        }
    }

    function filterPermissions() {
        const selectedRole = document.getElementById('role').value.toLowerCase();
        const permissionItems = document.querySelectorAll('.permission-item');
        const permissionsSection = document.getElementById('permissions-section');

        if (selectedRole === '') {
            // Hide entire permissions section when no role is selected
            permissionsSection.style.display = 'none';
            // Uncheck all permissions
            permissionItems.forEach(function(item) {
                const checkbox = item.querySelector('input[type="checkbox"]');
                if (checkbox) {
                    checkbox.checked = false;
                }
            });
        } else {
            // Show permissions section
            permissionsSection.style.display = 'block';

            permissionItems.forEach(function(item) {
                const guardName = item.getAttribute('data-guard').toLowerCase();

                // Match if selected role contains the guard name
                if (selectedRole.includes(guardName)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                    // Uncheck hidden permissions
                    const checkbox = item.querySelector('input[type="checkbox"]');
                    if (checkbox) {
                        checkbox.checked = false;
                    }
                }
            });
        }
    }

    // Optional: auto-hide alerts after 4 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll(".alert");
        alerts.forEach(alert => {
            alert.style.transition = "opacity 0.5s";
            alert.style.opacity = "0";
            setTimeout(() => alert.remove(), 500);
        });
    }, 4000);
</script>
@endsection