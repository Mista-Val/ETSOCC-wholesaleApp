@extends('admin.layout')
@section('content')
  @include('admin.header')
  @include('admin.sidebar')
  
  <div id="global-loader">
    <div class="spinner-border text-primary loader-img" role="status">
      <span class="sr-only">Loading...</span>
    </div>
  </div>

  <main id="content">
    @yield('sub_content')
  </main>

  @include('admin.footer')

  {{-- 👇 Add this line to ensure Chart.js and other scripts are loaded --}}
  @stack('scripts')

@endsection
