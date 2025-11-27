<!-- <i class="iconsax text-white text-[28px]" icon-name="menu-broken"></i> -->
@extends('web.auth.app')
@section('content')
<main class="welcome-screen-bg bg-white relative overflow-hidden">
   <div class="left-shape-box absolute left-[-100px] bottom-0"><img src="{{ asset('web/images/left-shape.svg') }}" alt="Welcome Background" class="object-cover w-full h-full" /></div>
   <div class="right-shape-box absolute right-[-100px] top-0"><img src="{{ asset('web/images/right-shape.svg') }}" alt="Welcome Background" class="object-cover w-full h-full" /></div>
   <!-- <div class="user-select-bg h-screen w-screen absolute inset-0"></div> -->
   <div class="content-box relative z-10 h-screen overflow-auto flex p-[15px]">
      <div class="content-box-inner m-auto max-w-[968px] w-full flex flex-col gap-[15px] md:gap-[30px]">
         <div class="logo flex justify-center">
            <a href="javascript:void(0)"><img src="{{ asset('web/images/logo.svg') }}" alt="logo"/></a> 
         </div>
         <div class="select-user-box flex flex-col gap-[15px] md:gap-[30px]">
            <h1 class="h6 text-color-gry-900 text-white text-center">
               Select User Type
            </h1>

            <ul class="select-user-list flex flex-wrap justify-center gap-[15px]">
               <li class="flex-1">
                  <a href="#" data-role="warehouse-manager" class="role-option bg-white active rounded-[16px] border border-1 border-disabled-gry-100 flex flex-col gap-[15px] p-[20px] md:p-[40px] justify-center items-center">
                     <figure class="w-[65px] h-[65px] flex justify-center items-center">
                        <img src="{{ asset('web/images/icon1.svg') }}" alt="User Icon" class="w-full h-full object-contain" />
                     </figure>
                     <figcaption class="text-center h6 text-black">Ware House</figcaption>
                  </a>
               </li>
               <li class="flex-1">
                  <a href="#" data-role="outlet-manager" class="role-option bg-white rounded-[16px] border border-1 border-disabled-gry-100 flex flex-col gap-[15px] p-[20px] md:p-[40px] justify-center items-center">
                     <figure class="w-[65px] h-[65px] flex justify-center items-center">
                        <img src="{{ asset('web/images/icon2.svg') }}" alt="User Icon" class="w-full h-full object-contain" />
                     </figure>
                     <figcaption class="text-center h6 text-black">Outlet</figcaption>
                  </a>
               </li>
               <li class="flex-1">
                  <a href="#" data-role="supervisor" class="role-option bg-white rounded-[16px] border border-1 border-disabled-gry-100 flex flex-col gap-[15px] p-[20px] md:p-[40px] justify-center items-center">
                     <figure class="w-[65px] h-[65px] flex justify-center items-center">
                        <img src="{{ asset('web/images/icon3.svg') }}" alt="User Icon" class="w-full h-full object-contain" />
                     </figure>
                     <figcaption class="text-center h6 text-black">Supervisor</figcaption>
                  </a>
               </li>
            </ul>

            <div class="continue-button flex justify-center">
               <a href="javascript:void(0)" class="btn btn-primary min-w-[178px]" onclick="triggerContinue()">Continue</a>
            </div>
         </div>
      </div>
   </div>
</main>

<script>

   $(document).ready(function(){
      sessionStorage.removeItem('selectedUserRole');
      $(".role-option").click(function(e){
         e.preventDefault();
         const role = $(this).attr('data-role');
         $('.role-option').removeClass('active');
         $(this).addClass('active');
         sessionStorage.setItem('selectedUserRole', role);
      });
   });
   function triggerContinue(){
      const role = sessionStorage.getItem('selectedUserRole') || "warehouse-manager";
      let url = "{{ route('login') }}"; 

      window.location.href = `${url}?role=${role}`;
      // window.location.href=`/login?role=${role}`;
   }
</script>
@endsection