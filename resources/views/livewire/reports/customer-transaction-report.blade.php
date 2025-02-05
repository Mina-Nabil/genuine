<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Money Transactions Report
                <iconify-icon class="ml-3" style="position: absolute" wire:loading wire:target="changeSection"
                    icon="svg-spinners:180-ring"></iconify-icon>
            </h4>
        </div>
        <div>
            <button wire:click='openAddTransSec'
                class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1 btn-sm">
                Create payment
            </button>
        </div>

    </div>
    <div class="card-body flex flex-col col-span-2" wire:ignore>
        <div class="card-text h-full">
            <div>
                <ul class="nav nav-tabs flex flex-col md:flex-row flex-wrap list-none border-b-0 pl-0" id="tabs-tab"
                    role="tablist">
                    @foreach ($PAYMENT_METHODS as $PAYMENT_METHOD)
                        <li class="nav-item" role="presentation" wire:click="changeSection('{{ $PAYMENT_METHOD }}')">
                            <a href="#tabs-profile-withIcon"
                                class="nav-link w-full flex items-center font-medium text-sm font-Inter leading-tight capitalize border-x-0 border-t-0 border-b border-transparent px-4 pb-2 my-2 hover:border-transparent focus:border-transparent  @if ($section === $PAYMENT_METHOD) active @endif dark:text-slate-300"
                                id="tabs-profile-withIcon-tab" data-bs-toggle="pill"
                                data-bs-target="#tabs-profile-withIcon" role="tab"
                                aria-controls="tabs-profile-withIcon" aria-selected="false">
                                <iconify-icon class="mr-1" height="1.5em" width="1.5em"
                                    @if ($PAYMENT_METHOD === 'cash') icon="mdi:cash"
                                @elseif ($PAYMENT_METHOD === 'bank_transfer')
                                    icon="mdi:bank-transfer"
                                @elseif ($PAYMENT_METHOD === 'wallet')
                                    icon="mdi:wallet" @endif></iconify-icon>

                                {{ ucwords(str_replace('_', ' ', $PAYMENT_METHOD)) }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <div class="card">
        <header class="card-header cust-card-header noborder">
            <iconify-icon wire:loading wire:target="search" class="loading-icon text-lg"
                icon="line-md:loading-twotone-loop"></iconify-icon>
            <input type="text" class="form-control !pl-9 mr-1 basis-1/4" placeholder="Search"
                wire:model.live.debounce.400ms="search">
        </header>

        <div class="card-body px-6 pb-6  overflow-x-auto">
            <div class=""> <!-- Add this wrapper to allow horizontal scroll -->
                <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                    <thead class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                        <tr>
                            <th scope="col"
                                class="table-th  flex items-center border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700"
                                style="position: sticky; left: -25px;  z-index: 10;">
                                <div class="checkbox-area">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" wire:model.live="selectAll" class="hidden"
                                            id="select-all">
                                        <span
                                            class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                            <img src="assets/images/icon/ck-white.svg" alt=""
                                                class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                    </label>
                                </div>
                                Name
                            </th>
                            <th scope="col" class="table-th">Reference</th>
                            @if ($selectAll)
                                @if ($selectedAllCustomers)
                                    <th colspan="4" class="table-th"><iconify-icon style="vertical-align: top;"
                                            icon="lucide:info" width="1.2em" height="1.2em"></iconify-icon> A
                                        {{ count($selectedCustomers) }} customer selected ..
                                        <span class="clickable-link" wire:click='undoSelectAllCustomers'>Undo</span>
                                    </th>
                                @else
                                    <th colspan="4" class="table-th"><iconify-icon style="vertical-align: top;"
                                            icon="lucide:info" width="1.2em" height="1.2em"></iconify-icon>
                                        {{ count($selectedCustomers) }} customer
                                        selected .. <span class="clickable-link" wire:click='selectAllCustomers'>Select
                                            All Customers</span></th>
                                @endif
                            @else
                                <th scope="col" class="table-th">Amount</th>
                                <th scope="col" class="table-th">Payment Method</th>
                                <th scope="col" class="table-th">Type Balance</th>
                                <th scope="col" class="table-th">Payment Date</th>
                                <th scope="col" class="table-th">Creator</th>
                            @endif

                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">

                        @foreach ($payments as $payment)
                            <tr class="even:bg-slate-100 dark:even:bg-slate-700">
                                @if ($payment->note)
                                    <td class="table-td sticky-column bg-white dark:bg-slate-800 colomn-shadow arabic-font"
                                        style="position: sticky; left: -25px; z-index: 10;" colspan="2">
                                        <div class="flex items-center">
                                            <div class="checkbox-area">
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" wire:model="selectedCustomers"
                                                        value="{{ $payment->id }}" class="hidden" id="select-all">
                                                    <span
                                                        class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                        <img src="assets/images/icon/ck-white.svg" alt=""
                                                            class="h-[10px] w-[10px] block m-auto opacity-0">
                                                    </span>
                                                </label>
                                            </div>
                                            {{ $payment->note }}
                                        </div>
                                    </td>
                                @else
                                    <td class="table-td flex items-center sticky-column bg-white dark:bg-slate-800 colomn-shadow arabic-font"
                                        style="position: sticky; left: -25px;  z-index: 10;">
                                        <div class="checkbox-area">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="checkbox" wire:model="selectedCustomers"
                                                    value="{{ $payment->id }}" class="hidden" id="select-all">
                                                <span
                                                    class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                    <img src="assets/images/icon/ck-white.svg" alt=""
                                                        class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                            </label>
                                        </div>
                                        @if ($payment->customer)
                                            <a href="{{ route('customer.show', $payment->customer->id) }}"> <span
                                                    class="hover-underline">
                                                        {{ $payment->customer->name }}
                                                </span>
                                            </a>
                                        @elseif ($payment->supplier)
                                            <a href="{{ route('invoice.show', $payment->supplier->id) }}"> <span
                                                    class="hover-underline">
                                                    <b>
                                                        {{ $payment->supplier->name }}
                                                    </b>
                                                </span>
                                            </a>
                                        @endif

                                    </td>

                                    <td class="table-td">
                                        @if ($payment->order)
                                            <a href="{{ route('orders.show', $payment->order->id) }}"> <span
                                                    class="hover-underline">
                                                    <b>Order</b> #{{ $payment->order->order_number }}
                                                </span>
                                            </a>
                                        @elseif ($payment->invoice)
                                            <a href="{{ route('invoice.show', $payment->invoice->id) }}"> <span
                                                    class="hover-underline">
                                                    <b>Invoice</b> #{{ $payment->invoice->code }}
                                                </span>
                                            </a>
                                        @endif
                                    </td>
                                @endif

                                <td class="table-td">
                                    <b>{{ number_format($payment->amount, 2) }}</b>
                                </td>

                                <td class="table-td">
                                    {{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}
                                </td>

                                <td class="table-td">
                                    <b>{{ number_format($payment->type_balance, 2) }}</b>
                                </td>



                                <td class="table-td">
                                    {{ $payment->created_at->format('l d/m/Y H:i A') }}
                                </td>

                                <td class="table-td">
                                    {{ $payment->createdBy->full_name }}
                                </td>


                            </tr>
                        @endforeach

                    </tbody>

                </table>


                @if ($payments->isEmpty())
                    {{-- START: empty filter result --}}
                    <div class="card m-5 p-5">
                        <div class="card-body rounded-md bg-white dark:bg-slate-800">
                            <div class="items-center text-center p-5">
                                <h2>
                                    <iconify-icon icon="icon-park-outline:search"></iconify-icon>
                                </h2>
                                <h2 class="card-title text-slate-900 dark:text-white mb-3">No payments with the
                                    applied
                                    filters</h2>
                                <p class="card-text">Try changing the filters or search terms for this view.
                                </p>
                                <a href="{{ url('/report/customers/transactions') }}"
                                    class="btn inline-flex justify-center mx-2 mt-3 btn-primary active btn-sm">View
                                    all payments</a>
                            </div>
                        </div>
                    </div>
                    {{-- END: empty filter result --}}
                @endif
            </div>
        </div>
        <div style="position: sticky ; bottom:0;width:100%; z-index:10;"
            class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
            {{ $payments->links('vendor.livewire.simple-bootstrap') }}
        </div>
    </div>
</div>
