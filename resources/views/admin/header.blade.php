<!-- Main Header-->
<div class="main-header side-header sticky">
    <div class="container-fluid">
        <div class="main-header-left">
            <a class="main-header-menu-icon" href="#" id="mainSidebarToggle"><span></span></a>
        </div>
        <div class="main-header-center">
            <div class="responsive-logo">
                <a href="{{ url('admin/dashboard') }}">
                    {{-- <img src="{{ __('app.logo_path')}}" class="mobile-logo" alt="logo"> --}}
                </a>
                <a href="{{ url('admin/dashboard') }}">
                    {{-- <img src="{{ __('app.logo_path')}}" class="mobile-logo-dark" alt="logo"> --}}
                </a>
            </div>

        </div>
        <div class="main-header-right">
            <div class="dropdown header-search">
                <a class="nav-link icon header-search">
                    <i class="fe fe-search header-icons"></i>
                </a>
                <div class="dropdown-menu">
                    <div class="main-form-search p-2">
                        <div class="input-group">
                            <div class="input-group-btn search-panel">

                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <div class="dropdown main-profile-menu">
                {{-- <a class="d-flex" href="">

                @php
                    $user =  Auth::guard()->user();
                @endphp
                 
                    <span class="main-img-user bg-primary" >
                      <img alt="avatar" src="{{ asset('uploads/profile/' . $user->profile_image) }}">
                    </span>
              </a> --}}
                <a class="d-flex" href="#">
                    @php
                        $user = Auth::guard('web')->user();
                    @endphp

                    @if ($user && $user->profile_image)
                        <span class="main-img-user bg-primary">
                            <img alt="avatar" src="{{ asset('uploads/profile/' . $user->profile_image) }}">
                        </span>
                    @endif
                </a>

                <div class="dropdown-menu">
                    <div class="header-navheading">
                        <h6 class="main-notification-title">

                            {{ $user ? ucfirst($user->name) : '' }}

                        </h6>
                        <p class="main-notification-text"></p>
                    </div>
                    <a class="dropdown-item border-top" href="{{ route('admin.profile.changePassword') }}">
                        <i class="fe fe-user"></i> Change Password
                    </a>
                    <a class="dropdown-item border-top" href="{{ route('admin.profile') }}">
                        <i class="fe fe-user"></i> Edit Profile
                    </a>

                    <!-- <a href="{{ route('admin.logout') }}" class="dropdown-item"><i class="fe fe-power"></i>Log Out</a> -->
                    <a href="#" class="dropdown-item logout-button">
                        <i class="fe fe-power"></i> Log Out
                    </a>

                </div>
            </div>

            <button class="navbar-toggler navresponsive-toggler" type="button" data-toggle="collapse"
                data-target="#navbarSupportedContent-4" aria-controls="navbarSupportedContent-4" aria-expanded="false"
                aria-label="Toggle navigation">
                <i class="fe fe-more-vertical header-icons navbar-toggler-icon"></i>
            </button><!-- Navresponsive closed -->
        </div>
    </div>
</div>
<!-- End Main Header-->

<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const logoutButton = document.querySelector('.logout-button');

        if (logoutButton) {
            logoutButton.addEventListener('click', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you really want to log out?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, log out!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('logout-form').submit();
                    }
                });
            });
        }
    });
</script>
