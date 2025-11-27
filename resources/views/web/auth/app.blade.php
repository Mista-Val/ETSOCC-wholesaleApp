<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Wholesale</title>

  <!-- Iconsax CSS -->
  <link href="https://iconsax.gitlab.io/i/icons.css" rel="stylesheet" />
    <script src="{{ asset('admin/plugins/jquery/jquery.min.js')}}"></script>

  <!-- Tailwind CSS via CDN -->
    <!-- <script src="{{ asset('web/js/tailwind.js') }}"async></script> -->
       <script src="https://cdn.tailwindcss.com"></script>


  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: "#f59e0b",
            secondary: "#fcd34d"
          }
        }
      }
    };
  </script>

  <!-- DaisyUI (must come after Tailwind) -->
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.0/dist/full.css" rel="stylesheet" type="text/css" />

  <!-- Swiper CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

  <!-- Custom CSS (after libraries, to allow overrides) -->
  <link rel="stylesheet" href="{{ asset('web/css/custom.css') }}">
  <link rel="stylesheet" href="{{ asset('web/css/responsive.css') }}">
    <script src="{{ asset('web/js/function.js') }}"></script>

</head>
<body class="min-h-screen">

  {{-- Main Content --}}
  @yield('content')

  {{-- JavaScript Libraries --}}
  <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

  <!-- Iconsax JS -->
  <script src="https://cdn.jsdelivr.net/npm/iconsax-icons"></script>

  <!-- Custom JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr("#customDate", {
        dateFormat: "Y-m-d",
        allowInput: true
    });
</script>
  @stack('scripts')
</body>
</html>
