<!DOCTYPE html>
<html lang="en">

<head>

  {{-- <title>@yield('title')</title> --}}
  <title>{{globalSetting('title')}}</title>

  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
  <meta name="description" content="{{ __('app.name') }}">
  <meta name="author" content="{{ __('app.name') }}">
  <meta name="keywords" content="{{ __('app.name') }}">
  <meta name="csrf-token" id="csrf-token" content="{{ csrf_token() }}">

  <!-- Favicon -->
  @if(globalSetting('favicon'))
      <link rel="icon" type="image/x-icon" id="favicon" href="{{ globalSetting('favicon') }}">
  @else
      <link rel="icon" type="image/x-icon" id="favicon"  href="{{ asset('admin/img/favicon.ico') }}">
  @endif
  <!-- Bootstrap css-->
  <link href="{{ asset('admin/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" />

  <!-- Icons css-->
  <link href="{{ asset('admin/plugins/web-fonts/icons.css')}}" rel="stylesheet" />
  <link href="{{ asset('admin/plugins/web-fonts/font-awesome/font-awesome.min.css')}}" rel="stylesheet">
  <link href="{{ asset('admin/plugins/web-fonts/plugin.css')}}" rel="stylesheet" />

  <!-- Internal Summernote css-->
  {{-- <link rel="stylesheet" href="{{ asset('admin/plugins/summernote/summernote-bs4.css')}}"> --}}

  <!-- Style css-->
  <link href="{{ asset('admin/css/style.css')}}" rel="stylesheet">
  <link href="{{ asset('admin/css/skins.css')}}" rel="stylesheet">
  <link href="{{ asset('admin/css/dark-style.css')}}" rel="stylesheet">
  <link href="{{ asset('admin/css/colors/default.css')}}" rel="stylesheet">
  <link href="{{ asset('admin/css/custom.css')}}" rel="stylesheet">

  <!-- Select2 css -->
  {{-- <link href="{{ asset('admin/plugins/select2/css/select2.min.css')}}" rel="stylesheet"> --}}

  <!-- Jquery js-->
  <script src="{{ asset('admin/plugins/jquery/jquery.min.js')}}"></script>

  <!-- Sidemenu css-->
  <link href="{{ asset('admin/css/sidemenu/sidemenu.css')}}" rel="stylesheet">

  {{-- ckediter --}}
  {{-- <script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script> --}}
  <script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>

  <!-- DataTable -->
  <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
  <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  @stack("styles")
  @livewireStyles
</head>
<body class="main-body leftmenu">

  <main>
    @yield('content')
  </main>

  <script>
    const editorElement = document.querySelector('#editor');
    if(editorElement){
        ClassicEditor
          .create(document.querySelector('#editor'), {
              ckfinder: {
                  uploadUrl: '{{ route("admin.upload.image") }}'
              },
              height: 200,
          })
          .then(editor => {
              editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
                  return {
                      upload: () => {
                          return loader.file.then(file => {
                              const data = new FormData();
                              data.append('upload', file);
                              data.append('_token', '{{ csrf_token() }}');
    
                              return fetch('{{ route("admin.upload.image") }}', {
                                  method: 'POST',
                                  body: data
                              })
                              .then(response => response.json())
                              .then(response => {
                                  if (response.url) {
                                      return { default: response.url };
                                  }
                                  throw new Error('Image upload failed.');
                              });
                          });
                      }
                  };
              };
          })
          .catch(error => {
              console.error(error);
          });
    }

    // Show Selected file
    function loadFile(event,id) {
      var image = document.getElementById(id);
      image.src = URL.createObjectURL(event.target.files[0]);
      $('#'+id).attr('style', 'display: block !important');
    }

    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
  </script>
  @livewireScripts
</body>
</html>
