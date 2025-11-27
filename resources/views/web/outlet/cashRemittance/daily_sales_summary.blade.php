@extends('web.auth.app')
@section('content')
@include('web.outlet.shared.header')
<main class="dashboard-screen-bg relative">
   <section class="dashboard-title-section bg-white border-b border-gry-50 bg-white">
      <div class="container-fluid">
         <div class="dashboard-inner flex items-center justify-between gap-[10px] flex-wrap py-[10px]">
            <h1 class="h6 text-gry-800">Daily Sales Summary</h1>
            <div class="breadcrumb flex items-center gap-[10px]">
               <a>
                  <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                     <path d="M8.39173 2.34954L2.61673 6.97453C1.96673 7.4912 1.55006 8.5829 1.69172 9.39956L2.80006 16.0329C3.00006 17.2162 4.13339 18.1745 5.33339 18.1745H14.6667C15.8584 18.1745 17.0001 17.2079 17.2001 16.0329L18.3084 9.39956C18.4417 8.5829 18.0251 7.4912 17.3834 6.97453L11.6084 2.35789C10.7167 1.64122 9.27506 1.64121 8.39173 2.34954Z" stroke="var(--color-gry-800)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                     <path d="M10.0001 12.9167C11.1507 12.9167 12.0834 11.9839 12.0834 10.8333C12.0834 9.68274 11.1507 8.75 10.0001 8.75C8.84949 8.75 7.91675 9.68274 7.91675 10.8333C7.91675 11.9839 8.84949 12.9167 10.0001 12.9167Z" stroke="var(--color-gry-800)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
               </a>
               <span class="text-gry-300">/</span>
               <span class="body-14 text-gry-800">Cash Handling</span>
               <span class="text-gry-300">/</span>
               <span class="body-14 text-gry-800">Daily Sales Summary</span>
            </div>
         </div>
      </div>
   </section>
   <section class="dashboard-content py-[15px] md:py-[30px]">
      <div class="container-fluid">
         <div class="space-y-6">
            <!-- Today's Overview -->
            <div class="today-overview">
               <!-- <h2 class="h6 mb-3">Today's Overview</h2> -->
               <div class="grid gap-4 md:grid-cols-3 lg:grid-cols-3">
                  <div class="bg-white rounded-[16px] shadow-[0px_1px_78px_rgba(0,0,0,0.06)] p-4 flex flex-col">
                     <span class="body-18 semibold text-gry-800">Total Cash Sales</span>
                     <div class="mt-2 flex items-center gap-[10px] flex-wrap">
                        <div class="flex-1 flex flex-col gap-[5px]">   
                           <!-- <span class="semibold text-gry-400">Amount</span>    -->
                           <span class="h5 text-gry-800">${{$totalCashSale}}</span>
                        </div>
                        <img src="{{ asset('web/images/d-icon1.svg') }}" alt="icon" class="w-[45px] basis-[45px]"/>
                     </div>
                  </div>
                  <div class="bg-white rounded-[16px] shadow-[0px_1px_78px_rgba(0,0,0,0.06)] p-4 flex flex-col">
                     <span class="body-18 semibold text-gry-800">Total Credit</span>
                     <div class="mt-2 flex items-center gap-[10px] flex-wrap">
                        <div class="flex-1 flex flex-col gap-[5px]">   
                           <!-- <span class="semibold text-gry-400">Payments</span>    -->
                           <span class="h5 text-gry-800">${{$totalCredit}}</span>
                        </div>
                        <img src="{{ asset('web/images/d-icon2.svg') }}" alt="icon" class="w-[45px] basis-[45px]"/>
                     </div>
                  </div>
                  <div class="bg-white rounded-[16px] shadow-[0px_1px_78px_rgba(0,0,0,0.06)] p-4 flex flex-col">
                     <span class="body-18 semibold text-gry-800">Total Down Payments</span>
                     <div class="mt-2 flex items-center gap-[10px] flex-wrap">
                        <div class="flex-1 flex flex-col gap-[5px]">   
                           <!-- <span class="semibold text-gry-400">Debt</span>    -->
                              <span class="h5 text-gry-800">${{$totalDownPayment}}</span>
                        </div>
                        <img src="{{ asset('web/images/d-icon3.svg') }}" alt="icon" class="w-[45px] basis-[45px]"/>
                     </div>
                  </div>

                  <div class="bg-white rounded-[16px] shadow-[0px_1px_78px_rgba(0,0,0,0.06)] p-4 flex flex-col">
                     <span class="body-18 semibold text-gry-800">Total Bank Transfer</span>
                     <div class="mt-2 flex items-center gap-[10px] flex-wrap">
                        <div class="flex-1 flex flex-col gap-[5px]">   
                           <!-- <span class="semibold text-gry-400">Debt</span>    -->
                              <span class="h5 text-gry-800">${{$totalBankTransfer}}</span>
                        </div>
                        <img src="{{ asset('web/images/d-icon3.svg') }}" alt="icon" class="w-[45px] basis-[45px]"/>
                     </div>
                  </div>
               
               </div>
            </div>
          
         </div>
      </div>
   </section>
</main>

@endsection