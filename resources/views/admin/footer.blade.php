<!-- Main Footer-->
<div class="main-footer text-center">
  <div class="container">
    <div class="row row-sm">
      <div class="col-md-12">
        <span>{{globalSetting('copyRightText')}}</span>
      </div>
    </div>
  </div>
</div>
    <!--End Footer-->
</div>
<!-- End Page -->

<!-- Back-to-top -->
<a href="#top" id="back-to-top"><i class="fe fe-arrow-up"></i></a>

<!-- Bootstrap js-->
<script src="{{ asset('admin/plugins/bootstrap/js/popper.min.js') }}"></script>
<script src="{{ asset('admin/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
{{-- <script src="{{ asset('admin/js/sweetalert.js') }}"></script> --}}
<!-- Perfect-scrollbar js -->
<script src="{{ asset('admin/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>

<!-- Sidemenu js -->
<script src="{{ asset('admin/plugins/sidemenu/sidemenu.js') }}"></script>
<script type="text/javascript" src="{{asset('admin/js/datepicker.js')}}"></script>
<script type="text/javascript" src="{{asset('admin/js/jquery.maskedinput.js')}}"></script>
<script type="text/javascript" src="{{asset('admin/js/spectrum.js')}}"></script>
<script type="text/javascript" src="{{asset('admin/js/form-elements.js')}}"></script>
<!-- Sidebar js -->
<script src="{{ asset('admin/plugins/sidebar/sidebar.js') }}"></script>

<!-- Select2 js-->
{{-- <script src="{{ asset('admin/plugins/select2/js/select2.min.js') }}"></script> --}}

<!-- Sticky js -->
<script src="{{ asset('admin/js/sticky.js') }}"></script>

<!-- Custom js -->
<script src="{{ asset('admin/js/custom.js') }}"></script>
<script src="{{ asset('admin/js/data-table.js') }}"></script>

<script>

  function checkInputLength(inputField) {

  let maxLength = parseInt(inputField.getAttribute("data-custom"));
  let charCountMsg = inputField.getAttribute("data-error-msg");
  let input = inputField.value;
  let charCountElement = document.getElementById(charCountMsg);
  let remainingChars = maxLength - input.length;
  charCountElement.textContent = input.length + "/" + maxLength + " characters";
  
  if (remainingChars < 0) {
      // If the input exceeds the maximum length, prevent further input
      inputField.value = input.substring(0, maxLength);
      charCountElement.style.color = "red";
      charCountElement.textContent = input.length + "/" + maxLength + " characters limit reached";
  } else {
      charCountElement.style.color = "black";
  }
  }
</script>

<script>
  const toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 8000
  });
</script>
<script>
  $(document).ready(function () {
      $("form").submit(function (e) {
          //stop submitting the form to see the disabled button effect
          e.preventDefault();

          //disable the submit button
          $("button[type='submit']").attr("disabled", true);

          $(this).unbind('submit').submit();
          // return true;
      });
  });
</script>
<script type="text/javascript">
 $(document).ready(function(){

 setTimeout(function(){

  $('.alert-danger').css('display','none');
  $('.alert-success').css('display','none');
  }, 8000);
 })
</script>


</body>
</html>
