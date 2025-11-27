@extends('web.auth.app')
@section('content')
    @include('web.supervisor.shared.header')
    <main class="dashboard-screen-bg relative">
        <section class="dashboard-title-section bg-white border-b border-gry-50 bg-white">
            <div class="container-fluid">
                <div class="dashboard-inner flex items-center justify-between gap-[10px] flex-wrap py-[10px]">
                    <h1 class="h6 text-gry-800">Create Cash Entry</h1>
                    <div class="breadcrumb flex items-center gap-[10px] flex-wrap">
                        <a>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M8.39173 2.34954L2.61673 6.97453C1.96673 7.4912 1.55006 8.5829 1.69172 9.39956L2.80006 16.0329C3.00006 17.2162 4.13339 18.1745 5.33339 18.1745H14.6667C15.8584 18.1745 17.0001 17.2079 17.2001 16.0329L18.3084 9.39956C18.4417 8.5829 18.0251 7.4912 17.3834 6.97453L11.6084 2.35789C10.7167 1.64122 9.27506 1.64121 8.39173 2.34954Z"
                                    stroke="var(--color-gry-800)" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M10.0001 12.9167C11.1507 12.9167 12.0834 11.9839 12.0834 10.8333C12.0834 9.68274 11.1507 8.75 10.0001 8.75C8.84949 8.75 7.91675 9.68274 7.91675 10.8333C7.91675 11.9839 8.84949 12.9167 10.0001 12.9167Z"
                                    stroke="var(--color-gry-800)" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </a>
                        <span class="text-gry-300">/</span>
                        <span class="body-14 text-gry-800 bold">External Cash</span>
                        <span class="text-gry-300">/</span>
                        <a href="{{ route('supervisor.finalCashDestination') }}" class="body-14 text-gry-800 bold">Final Cash Destination</a>
                        <span class="text-gry-300">/</span>
                        <span class="body-14 text-gry-800">Create Cash Entry</span>
                    </div>
                </div>
            </div>
        </section>
        <section class="dashboard-content py-[15px] md:py-[30px] flex-1">
            <div class="container-fluid">
                <div class="bg-white white-box">
                    @if (session('success'))
                        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                            Please correct the errors below.
                        </div>
                    @endif

                    {{-- CORRECTED: Action route points to the new finalCashStore method --}}
                    <form action="{{ route('supervisor.finalCashStore') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            
                            {{-- FINAL DESTINATION FIELD (Corrected ID, Name, Placeholder, and Error Key) --}}
                             <div class="form-group">
                                <label class="form-label block mb-1" for="final_destination">Final Destination</label>
                                <input type="text" id="final_destination" name="final_destination" value="{{ old('final_destination') }}" placeholder="Enter final destination"
                                    class="form-control w-full border border-gray-300 rounded px-3 py-2" />
                                @error('final_destination')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- CASH HANDLER NAME FIELD (Corrected ID, Name, Placeholder, and Error Key) --}}
                            <div class="form-group">
                                <label class="form-label block mb-1" for="cash_handler_name">Cash Handler Name</label>
                                <input type="text" id="cash_handler_name" name="cash_handler_name" value="{{ old('cash_handler_name') }}" placeholder="Enter cash handler name"
                                    class="form-control w-full border border-gray-300 rounded px-3 py-2" />
                                @error('cash_handler_name')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>


                            {{-- RESPONSIBLE PERSON FIELD (Corrected ID, Name, Placeholder, and Error Key) --}}
                              <div class="form-group">
                                <label class="form-label block mb-1" for="responsible_person">Responsible Person</label>
                                <input type="text" id="responsible_person" name="responsible_person" value="{{ old('responsible_person') }}" placeholder="Enter responsible person"
                                    class="form-control w-full border border-gray-300 rounded px-3 py-2" />
                                @error('responsible_person')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- AMOUNT FIELD (Corrected Placeholder and Error Key) --}}
                             <div class="form-group">
                                <label class="form-label block mb-1" for="amount">Amount</label>
                                <input type="text" id="amount" name="amount" value="{{ old('amount') }}" placeholder="Enter amount (e.g., 100.00)"
                                    class="form-control w-full border border-gray-300 rounded px-3 py-2" />
                                @error('amount')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                        </div>

                        {{-- REMARKS FIELD (Error Key remains correct) --}}
                        <div class="form-group mt-4">
                            <label class="form-label block mb-1" for="remarks">Remarks</label>
                            <textarea id="remarks" name="remarks" rows="4" placeholder="Enter optional remarks"
                                class="form-control w-full border border-gray-300 rounded px-3 py-2">{{ old('remarks') }}</textarea>
                            @error('remarks')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Submit Button --}}
                        <div class="mt-6">
                            <button type="submit"
                                class="bg-pink-500 text-white py-2 px-6 rounded hover:bg-pink-600 transition-all">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>
@endsection