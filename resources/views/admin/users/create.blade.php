@extends('admin.sub_layout')
@section('title', 'Add User')
@section('sub_content')

<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">
            <div class="page-header d-block">
                <h2 class="main-content-title tx-24 mg-b-5">Add User</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url("admin/dashboard") }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add User</li>
                </ol>
            </div>

            <div class="row sidemenu-height">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">

                            {!! Form::open(['route' => 'admin.users.store', 'method' => 'POST', 'id' => 'userForm']) !!}

                            {{-- Row 1 --}}
                            <div class="row">
                                {{-- Full Name --}}
                                <div class="form-group col-md-6">
                                    {!! Form::label('fullname', 'Full Name') !!} <span class="text-danger">*</span>
                                    {!! Form::text('fullname', old('fullname'), ['class' => 'form-control', 'placeholder' => 'Enter Full Name','autocomplete' => 'off']) !!}
                                    <div class="text-danger">{{ $errors->first('fullname') }}</div>
                                </div>

                                {{-- Email --}}
                                <div class="form-group col-md-6">
                                    {!! Form::label('email', 'Email') !!} <span class="text-danger">*</span>
                                    {!! Form::email('email', old('email'), ['class' => 'form-control', 'placeholder' => 'Enter Email','autocomplete' => 'off']) !!}
                                    <div class="text-danger">{{ $errors->first('email') }}</div>
                                </div>
                            </div>

                            {{-- Row 2 --}}
                            <div class="row">
                                {{-- Password Field --}}
                                <div class="form-group col-md-6">
                                    {!! Form::label('password', 'Password') !!} <span class="text-danger">*</span>
                                    <div class="position-relative">
                                        <input type="password" 
                                               name="password" 
                                               id="password" 
                                               class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" 
                                               placeholder="Password" 
                                               autocomplete="off">
                                        <span class="show-password" id="toggle-password" onclick="showPassword(event, 'password')">
                                            <i class="far fa-eye-slash"></i>
                                        </span>
                                    </div>
                                    @error('password')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Confirm Password Field --}}
                                <div class="form-group col-md-6">
                                    {!! Form::label('password_confirmation', 'Confirm Password') !!} <span class="text-danger">*</span>
                                    <div class="position-relative">
                                        <input type="password" 
                                               name="password_confirmation" 
                                               id="password_confirmation" 
                                               class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" 
                                               placeholder="Confirm Password" 
                                               autocomplete="off">
                                        <span class="show-password" id="toggle-password_confirmation" onclick="showPassword(event, 'password_confirmation')">
                                            <i class="far fa-eye-slash"></i>
                                        </span>
                                    </div>
                                    @error('password_confirmation')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Row 3 --}}
                            <div class="row">
                                {{-- Role --}}
                                <div class="form-group col-md-6">
                                    {!! Form::label('role', 'Role') !!} <span class="text-danger">*</span>
                                    <div class="select-down-arrow">
                                        <select name="role" id="role" class="form-control" onchange="filterPermissions()">
                                            <option value="">Select Role</option>
                                            @foreach (config('global.roles') as $key => $role)
                                            <option value="{{ $key }}" {{ old('role') == $key ? 'selected' : '' }}>
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
                                        {!! Form::select('status', [1 => 'Active', 0 => 'Inactive'], old('status', 1), ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="text-danger">{{ $errors->first('status') }}</div>
                                </div>

                                {{-- Permissions Section --}}
                                <div class="form-group col-md-12" id="permissions-section">
                                    {!! Form::label('permissions', 'Permissions') !!} <span class="text-danger">*</span>
                                    <div class="row" id="permissions-container">
                                        @foreach($permissions as $permission)
                                        <div class="col-md-3 permission-item" data-guard="{{ $permission->guard_name }}">
                                            <label>
                                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                    {{ (is_array(old('permissions')) && in_array($permission->id, old('permissions'))) ? 'checked' : '' }}>
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
                                <button class="btn btn-primary" type="submit">Submit</button>
                                <button class="btn btn-secondary ml-3" type="button" onclick="resetForm()">Reset</button>
                            </div>

                            {!! Form::close() !!}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CSS FIX for Overlapping Validation Icon --}}
<style>
    /* * This CSS targets the validation icon (the cross) added by your theme's 
     * error styles and hides it for the password fields, preventing it from 
     * overlapping the eye icon.
     */
    .form-group .position-relative input.is-invalid {
        /* Remove the background image (the cross/exclamation icon) */
        background-image: none !important;
        /* Restore default padding so text doesn't flow under the eye icon */
        padding-right: 15px !important; 
    }
</style>

<script>
    // Store passwords in sessionStorage before form submit
    document.getElementById('userForm').addEventListener('submit', function() {
        const password = document.getElementById('password').value;
        const passwordConfirmation = document.getElementById('password_confirmation').value;
        
        sessionStorage.setItem('temp_password', password);
        sessionStorage.setItem('temp_password_confirmation', passwordConfirmation);
    });

    // Restore passwords and fix eye icon on page load if there are validation errors
    document.addEventListener('DOMContentLoaded', function() {
        const passwordField = document.getElementById('password');
        const passwordConfirmationField = document.getElementById('password_confirmation');
        const togglePassword = document.getElementById('toggle-password');
        const togglePasswordConfirmation = document.getElementById('toggle-password_confirmation');

        @if($errors->any())
            const storedPassword = sessionStorage.getItem('temp_password');
            const storedPasswordConfirmation = sessionStorage.getItem('temp_password_confirmation');
            
            if (storedPassword) {
                passwordField.value = storedPassword;
            }
            if (storedPasswordConfirmation) {
                passwordConfirmationField.value = storedPasswordConfirmation;
            }

            // FIX 1: Ensure input type is 'password' and eye icon is 'show' (eye-slash) on page load after error
            if (passwordField && passwordField.type !== 'password') {
                passwordField.type = 'password';
            }
            if (togglePassword) {
                togglePassword.innerHTML = '<i class="far fa-eye-slash"></i>';
            }

            if (passwordConfirmationField && passwordConfirmationField.type !== 'password') {
                passwordConfirmationField.type = 'password';
            }
            if (togglePasswordConfirmation) {
                togglePasswordConfirmation.innerHTML = '<i class="far fa-eye-slash"></i>';
            }

        @else
            // Clear sessionStorage if no errors (successful submission or fresh page)
            sessionStorage.removeItem('temp_password');
            sessionStorage.removeItem('temp_password_confirmation');
        @endif
        
        // Filter permissions on page load
        filterPermissions();
    });

    function resetForm() {
        // Reset the form
        document.getElementById('userForm').reset();
        
        // Clear sessionStorage
        sessionStorage.removeItem('temp_password');
        sessionStorage.removeItem('temp_password_confirmation');
        
        // Clear all error messages
        document.querySelectorAll('.text-danger').forEach(function(element) {
            element.textContent = '';
        });
        
        // Reset password field types back to password
        document.getElementById('password').type = 'password';
        document.getElementById('password_confirmation').type = 'password';
        
        // Reset eye icons
        document.getElementById('toggle-password').innerHTML = '<i class="far fa-eye-slash"></i>';
        document.getElementById('toggle-password_confirmation').innerHTML = '<i class="far fa-eye-slash"></i>';
        
        // Hide permissions section
        document.getElementById('permissions-section').style.display = 'none';
    }

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

    // Optional: auto-hide alerts after 4 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll(".alert");
        alerts.forEach(alert => {
            alert.style.transition = "opacity 0.5s";
            alert.style.opacity = "0";
            setTimeout(() => alert.remove(), 500);
        });
    }, 4000);

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

            // Filter permissions based on guard contained in role
            permissionItems.forEach(function(item) {
                const guardName = item.getAttribute('data-guard').toLowerCase();

                // Check if the selected role contains the guard name
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
</script>
@endsection