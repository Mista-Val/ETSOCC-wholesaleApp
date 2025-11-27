@extends('web.auth.app')
@section('content')
@include('web.warehouse.shared.header')
<main class="dashboard-screen-bg relative">
   <section class="dashboard-title-section bg-white border-b border-gry-50 bg-white">
      <div class="container-fluid">
         <div class="dashboard-inner flex items-center justify-between gap-[10px] flex-wrap py-[10px]">
            <h1 class="h6 text-gry-800">Received Stock</h1>
            <div class="breadcrumb flex items-center gap-[10px]">
                <a href="{{ route('warehouse.dashboard') }}">
                  <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                     <path d="M8.39173 2.34954L2.61673 6.97453C1.96673 7.4912 1.55006 8.5829 1.69172 9.39956L2.80006 16.0329C3.00006 17.2162 4.13339 18.1745 5.33339 18.1745H14.6667C15.8584 18.1745 17.0001 17.2079 17.2001 16.0329L18.3084 9.39956C18.4417 8.5829 18.0251 7.4912 17.3834 6.97453L11.6084 2.35789C10.7167 1.64122 9.27506 1.64121 8.39173 2.34954Z" stroke="var(--color-gry-800)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                     <path d="M10.0001 12.9167C11.1507 12.9167 12.0834 11.9839 12.0834 10.8333C12.0834 9.68274 11.1507 8.75 10.0001 8.75C8.84949 8.75 7.91675 9.68274 7.91675 10.8333C7.91675 11.9839 8.84949 12.9167 10.0001 12.9167Z" stroke="var(--color-gry-800)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
               </a>
               <span class="text-gry-300">/</span>
               <span class="body-14 text-gry-800 bold">Stock Operations</span>
               <span class="text-gry-300">/</span>
               <span class="body-14 text-gry-800">Received Stock</span>
            </div>
         </div>
      </div>
   </section>

   <section class="dashboard-content py-[15px] md:py-[30px] flex-1">
      <div class="container-fluid">
       <div class="bg-white white-box min-h-[calc(100vh-200px)] flex flex-col items-center justify-center">
        <div class="text-center flex flex-col gap-[20px] justify-center items-center">
         <figure><img src="{{ asset('web/images/stock-icon.svg') }}" alt="stock"/></figure>
         <figcaption class="flex flex-col gap-[15px]">
          <h3 class="h5">Create Receive Stock</h3>  
          <a class="btn btn-primary">Create Receive Stock</a>
         </figcaption>
        </div>   
       </div>   
      </div>
   </section>
</main>
@endsection