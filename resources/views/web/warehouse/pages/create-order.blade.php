@extends('web.auth.app')
@section('content')
@include('web.warehouse.shared.header')
<main class="dashboard-screen-bg relative">
    <section class="dashboard-title-section bg-white border-b border-gry-50 bg-white">
        <div class="container-fluid">
            <div class="dashboard-inner flex items-center justify-between gap-[10px] flex-wrap py-[10px]">
                <h1 class="h6 text-gry-800">Create Sale</h1>
                <div class="breadcrumb flex items-center gap-[10px]">
                     <a href="{{ route('warehouse.dashboard') }}">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                    <span class="body-14 text-gry-800 bold">Sales & orders</span>
                    <span class="text-gry-300">/</span>
                    <span class="body-14 text-gry-800">Create Order</span>
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard-content py-[15px] md:py-[30px] flex-1">
        <div class="container-fluid">
            <div class="flex flex-wrap gap-[15px] md:gap-[30px]">
                <!-- Left Side - Products -->
                <div class="flex-1">
                    <!-- Search -->
                    <div class="mb-6 relative">
                        <input type="text" placeholder="Search Product" class="pl-[45px] form-control">
                        <button class="absolute left-3 top-1/2 -translate-y-1/2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M11.5 21C16.7467 21 21 16.7467 21 11.5C21 6.25329 16.7467 2 11.5 2C6.25329 2 2 6.25329 2 11.5C2 16.7467 6.25329 21 11.5 21Z"
                                    stroke="#333333" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M22 22L20 20" stroke="#333333" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>

                    <!-- Products Grid -->
                    <div class="grid [grid-template-columns:repeat(auto-fit,minmax(235px,1fr))] gap-[15px]">
                        <div class="white-box text-center flex flex-col gap-[15px] md:gap-[25px]">
                            <h3 class="body-16 semibold">CandyWhirl</h3>
                            <button class="btn btn-outline btn-pink w-full">
                                Add
                            </button>
                        </div>

                        <div class="white-box text-center flex flex-col gap-[15px] md:gap-[25px]">
                            <h3 class="body-16 semibold">Rainbow Twist</h3>
                            <button class="btn btn-outline btn-pink w-full">
                                Add
                            </button>
                        </div>

                        <div class="white-box text-center flex flex-col gap-[15px] md:gap-[25px]">
                            <h3 class="body-16 semibold">Lollipop Mix</h3>
                            <button class="btn btn-outline btn-pink w-full">
                                Add
                            </button>
                        </div>

                        <div class="white-box text-center flex flex-col gap-[15px] md:gap-[25px]">
                            <h3 class="body-16 semibold">Toffee Classic</h3>
                            <button class="btn btn-outline btn-pink w-full">
                                Add
                            </button>
                        </div>

                        <div class="white-box text-center flex flex-col gap-[15px] md:gap-[25px]">
                            <h3 class="body-16 semibold">Toffee Classic</h3>
                            <button class="btn btn-outline btn-pink w-full">
                                Add
                            </button>
                        </div>

                        <div class="white-box text-center flex flex-col gap-[15px] md:gap-[25px]">
                            <h3 class="body-16 semibold">Lollipop Mix</h3>
                            <button class="btn btn-outline btn-pink w-full">
                                Add
                            </button>
                        </div>

                        <div class="white-box text-center flex flex-col gap-[15px] md:gap-[25px]">
                            <h3 class="body-16 semibold">Rainbow Twist</h3>
                            <button class="btn btn-outline btn-pink w-full">
                                Add
                            </button>
                        </div>

                        <div class="white-box text-center flex flex-col gap-[15px] md:gap-[25px]">
                            <h3 class="body-16 semibold">CandyWhirl</h3>
                            <button class="btn btn-outline btn-pink w-full">
                                Add
                            </button>
                        </div>

                        <div class="white-box text-center flex flex-col gap-[15px] md:gap-[25px]">
                            <h3 class="body-16 semibold">CandyWhirl</h3>
                            <button class="btn btn-outline btn-pink w-full">
                                Add
                            </button>
                        </div>

                        <div class="white-box text-center flex flex-col gap-[15px] md:gap-[25px]">
                            <h3 class="body-16 semibold">CandyWhirl</h3>
                            <button class="btn btn-outline btn-pink w-full">
                                Add
                            </button>
                        </div>
                        <div class="white-box text-center flex flex-col gap-[15px] md:gap-[25px]">
                            <h3 class="body-16 semibold">CandyWhirl</h3>
                            <button class="btn btn-outline btn-pink w-full">
                                Add
                            </button>
                        </div>

                        <div class="white-box text-center flex flex-col gap-[15px] md:gap-[25px]">
                            <h3 class="body-16 semibold">Rainbow Twist</h3>
                            <button class="btn btn-outline btn-pink w-full">
                                Add
                            </button>
                        </div>

                        <div class="white-box text-center flex flex-col gap-[15px] md:gap-[25px]">
                            <h3 class="body-16 semibold">Lollipop Mix</h3>
                            <button class="btn btn-outline btn-pink w-full">
                                Add
                            </button>
                        </div>

                        <div class="white-box text-center flex flex-col gap-[15px] md:gap-[25px]">
                            <h3 class="body-16 semibold">Toffee Classic</h3>
                            <button class="btn btn-outline btn-pink w-full">
                                Add
                            </button>
                        </div>

                        <div class="white-box text-center flex flex-col gap-[15px] md:gap-[25px]">
                            <h3 class="body-16 semibold">Toffee Classic</h3>
                            <button class="btn btn-outline btn-pink w-full">
                                Add
                            </button>
                        </div>

                        <div class="white-box text-center flex flex-col gap-[15px] md:gap-[25px]">
                            <h3 class="body-16 semibold">Lollipop Mix</h3>
                            <button class="btn btn-outline btn-pink w-full">
                                Add
                            </button>
                        </div>

                        <div class="white-box text-center flex flex-col gap-[15px] md:gap-[25px]">
                            <h3 class="body-16 semibold">Rainbow Twist</h3>
                            <button class="btn btn-outline btn-pink w-full">
                                Add
                            </button>
                        </div>

                        <div class="white-box text-center flex flex-col gap-[15px] md:gap-[25px]">
                            <h3 class="body-16 semibold">CandyWhirl</h3>
                            <button class="btn btn-outline btn-pink w-full">
                                Add
                            </button>
                        </div>

                        <div class="white-box text-center flex flex-col gap-[15px] md:gap-[25px]">
                            <h3 class="body-16 semibold">CandyWhirl</h3>
                            <button class="btn btn-outline btn-pink w-full">
                                Add
                            </button>
                        </div>

                        <div class="white-box text-center flex flex-col gap-[15px] md:gap-[25px]">
                            <h3 class="body-16 semibold">CandyWhirl</h3>
                            <button class="btn btn-outline btn-pink w-full">
                                Add
                            </button>
                        </div>
                        <div class="white-box text-center flex flex-col gap-[15px] md:gap-[25px]">
                            <h3 class="body-16 semibold">CandyWhirl</h3>
                            <button class="btn btn-outline btn-pink w-full">
                                Add
                            </button>
                        </div>

                        <div class="white-box text-center flex flex-col gap-[15px] md:gap-[25px]">
                            <h3 class="body-16 semibold">Rainbow Twist</h3>
                            <button class="btn btn-outline btn-pink w-full">
                                Add
                            </button>
                        </div>

                        <div class="white-box text-center flex flex-col gap-[15px] md:gap-[25px]">
                            <h3 class="body-16 semibold">Lollipop Mix</h3>
                            <button class="btn btn-outline btn-pink w-full">
                                Add
                            </button>
                        </div>

                        <div class="white-box text-center flex flex-col gap-[15px] md:gap-[25px]">
                            <h3 class="body-16 semibold">Toffee Classic</h3>
                            <button class="btn btn-outline btn-pink w-full">
                                Add
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Order List -->
                <div class="max-w-[648px] w-full white-box sticky top-[110px] h-[fit-content]">
                    <h2 class="body-20 text-gry-800 border-b pb-[10px] semibold mb-[15px]">Order List</h2>

                    <!-- Phone Number -->
                    <div class="flex flex-col gap-[10px] mb-[15px]">
                        <label for="phone" class="body-14 semibold">Phone Number</label>
                        <div class="flex items-center gap-[15px]">
                            <input type="text" placeholder="Enter phone number to search customer" class="form-control">
                            <!-- Open Button -->
                            <button class="btn btn-outline btn-pink gap-[5px]"
                                onclick="document.getElementById('add-customer-modal').showModal()">
                                <img src="images/add.svg" alt="" />
                                Add
                            </button>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="flex justify-between items-center white-box mb-[15px]">
                        <div class="flex flex-col gap-[3px]">
                            <p class="body-16-regular text-gry-900">James Anderson</p>
                            <p class="body-16-regular semibold">+91 45124 451236</p>
                        </div>
                        <button><img src="images/close1.svg" alt="" /></button>
                    </div>
                    <hr class="mb-[15px]" />

                    <!-- Order Details -->
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-[15px]">
                            <div class="flex flex-col gap-[5px]">
                                <h3 class="body-18-semibold text-gry-800">Order Details</h3>
                                <p class="body-16-regular text-gry-900">Items : 3</p>
                            </div>
                            <button class="body-16-semibold text-secondary-500">Clear All</button>
                        </div>
                        <div class="overflow-y-auto whitespace-nowrap">
                            <table class="w-full">
                                <thead class="body-16-semibold text-gry-900 border-b border-[#E6E6E6]">
                                    <tr>
                                        <th class="text-left py-[10px]">Product Name</th>
                                        <th class="py-[10px] text-center px-[10px]">Quantity</th>
                                        <th class="py-[10px] text-right">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="body-14-regular text-gry-500 py-[10px]">Lollipop Mix </td>
                                        <td class="py-[10px] px-[10px]">
                                            <div
                                                class="flex m-auto p-[5px] justify-between items-center border border-primary-500 gap-2 max-w-[125px] rounded-[8px]">
                                                <button
                                                    class="w-[20px] h-[25px] flex items-center justify-center text-primary-500 text-[24px]">-</button>
                                                <span class="body-16-semibold text-primary-500">2</span>
                                                <button
                                                    class="w-[20px] h-[25px] flex items-center justify-center text-primary-500 text-[24px]">+</button>
                                            </div>
                                        </td>
                                        <td class="py-[10px] text-right body-14-regular text-gry-500">$398</td>
                                    </tr>

                                    <tr>
                                        <td class="body-14-regular text-gry-500 py-[10px]">Lollipop Mix </td>
                                        <td class="py-[10px] px-[10px]">
                                            <div
                                                class="flex m-auto p-[5px] justify-between items-center border border-primary-500 gap-2 max-w-[125px] rounded-[8px]">
                                                <button
                                                    class="w-[20px] h-[25px] flex items-center justify-center text-primary-500 text-[24px]">-</button>
                                                <span class="body-16-semibold text-primary-500">2</span>
                                                <button
                                                    class="w-[20px] h-[25px] flex items-center justify-center text-primary-500 text-[24px]">+</button>
                                            </div>
                                        </td>
                                        <td class="py-[10px] text-right body-14-regular text-gry-500">$398</td>
                                    </tr>

                                    <tr>
                                        <td class="body-14-regular text-gry-500 py-[10px]">Lollipop Mix </td>
                                        <td class="py-[10px] px-[10px]">
                                            <div
                                                class="flex m-auto p-[5px] justify-between items-center border border-primary-500 gap-2 max-w-[125px] rounded-[8px]">
                                                <button
                                                    class="w-[20px] h-[25px] flex items-center justify-center text-primary-500 text-[24px]">-</button>
                                                <span class="body-16-semibold text-primary-500">2</span>
                                                <button
                                                    class="w-[20px] h-[25px] flex items-center justify-center text-primary-500 text-[24px]">+</button>
                                            </div>
                                        </td>
                                        <td class="py-[10px] text-right body-14-regular text-gry-500">$398</td>
                                    </tr>
                                </tbody>
                            </table>


                        </div>
                    </div>

                    <!-- Total -->
                    <div
                        class="flex justify-between items-center font-semibold text-lg border-t border-b pt-3 pb-3 mb-[15px]">
                        <span class="body-16-semibold text-gry-900">Total</span>
                        <span class="body-16-semibold text-gry-900">$400</span>
                    </div>

                    <!-- Payment Methods -->
                    <div class="mb-[15px] flex flex-col gap-[10px]">
                        <h3 class="body-18-semibold text-gry-800">Select Payment Method</h3>
                        <div class="flex flex-wrap gap-2">
                            <button class="btn bg-secondary-500">Cash</button>
                            <button class="btn bg-white shadow-md text-[#666666]">Bank Transfer</button>
                            <button class="btn bg-white shadow-md text-[#666666]">Down Payment</button>
                            <button class="btn bg-white shadow-md text-[#666666]">Credit</button>
                        </div>

                        <div
                            class="available-balance-box flex justify-between items-center flex-wrap gap-[10px] bg-secondary-50 border border-secondary-200 p-[10px] rounded-[8px]">
                            <span class="body-14-regular text-gry-900">Available Balance</span>
                            <span class="body-16-semibold text-gry-900">$400</span>
                        </div>

                    </div>

                    <hr class="mb-[15px]" />

                    <!-- Remarks -->
                    <div class="mb-[15px] form-group">
                        <label for="remark">Add Remarks</label>
                        <input type="text" placeholder="Enter remark" class="form-control">
                    </div>

                    <!-- Create Button -->
                    <button class="btn w-full bg-primary-500">
                        Create
                    </button>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Add new customer Modal -->
<dialog id="add-customer-modal" class="modal">
    <div class="modal-box p-[0] relative">
        <!-- Close Button -->
        <form method="dialog">
            <div class="flex items-center justify-between py-[10px] px-[15px] border-b border-gry-50">
                <h2 class="h6">Add new customer</h2>
                <button><img src="images/close1.svg" alt="" /></button>
            </div>
        </form>
        <div class="modal-content p-[15px]">
            <div class="form-group">
                <label>Phone number</label>
                <input type="text" class="form-control" placeholdr="Enter phone number" />
            </div>
            <div class="form-group">
                <label>Customer name</label>
                <input type="text" class="form-control" placeholdr="Enter Name" />
            </div>
            <button class="btn btn-primary w-full">Create</button>
        </div>
    </div>
</dialog>
@endsection