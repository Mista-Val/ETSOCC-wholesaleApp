@extends('web.auth.app')

@section('content')
    @include('web.warehouse.shared.header')

    <main class="dashboard-screen-bg relative">
        <!-- Page Title & Breadcrumb -->
        <section class="dashboard-title-section bg-white border-b border-gry-50">
            <div class="container-fluid">
                <div class="dashboard-inner flex items-center justify-between gap-[10px] flex-wrap py-[10px]">
                    <h1 class="h6 text-gry-800">Create Record Expenses</h1>
                    <div class="breadcrumb flex items-center gap-[10px] flex-wrap">
                        <a href="{{ route('warehouse.dashboard') }}">
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
                        <span class="body-14 text-gry-800 bold">Cash Handling</span>
                        <span class="text-gry-300">/</span>
                        <a href="{{ route('warehouse.recordExpenses') }}" class="body-14 text-gry-800 bold">Record
                            Expenses</a>
                        <span class="text-gry-300">/</span>
                        <span class="body-14 text-gry-800">Create Record Expenses</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Form Content -->
        <section class="dashboard-content py-[15px] md:py-[30px] flex-1">
            <div class="container-fluid">
                <div class="bg-white white-box p-6 rounded shadow-sm">

                    <form action="{{ route('warehouse.recordExpenses-store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label>Receiver's Name</label>
                                <select class="form-control" name="receiver_id">
                                    <option value="">Select Receiver's Name</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('receiver_id') == $user->id ? 'selected' : '' }}>
                                            {{ ucwords($user->name) }}({{ucwords($user->role)}})
                                        </option>
                                    @endforeach
                                </select>
                                @error('receiver_id')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label block mb-1">Expenses Amount</label>
                                <input type="text" name="amount" placeholder="Enter Amount"
                                    class="form-control w-full border border-gray-300 rounded px-3 py-2" />
                                @error('amount')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label block mb-1">Purpose</label>
                                <input type="text" name="purpose" placeholder="Enter purpose"
                                    class="form-control w-full border border-gray-300 rounded px-3 py-2" />
                                @error('purpose')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group mt-4">
                            <label class="form-label block mb-1">Remark</label>
                            <textarea name="remark" rows="4" placeholder="Enter Remark"
                                class="form-control w-full border border-gray-300 rounded px-3 py-2"></textarea>
                            @error('remark')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
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
