<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Orders
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            @can('create', App\Models\Orders\Order::class)
                <a href="{{ route('orders.create') }}">
                    <button class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1 btn-sm">
                        Create order
                    </button>
                </a>
            @endcan
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
                                            <img src="{{ asset('assets/images/icon/ck-white.svg') }}" alt=""
                                                class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                    </label>
                                </div>
                                Order
                            </th>
                            @if ($selectAll)
                                @if ($selectedAllOrders)
                                    <th colspan="8" class="table-th"><iconify-icon style="vertical-align: top;"
                                            icon="lucide:info" width="1.2em" height="1.2em"></iconify-icon> A
                                        {{ count($selectedOrders) }} order selected ..
                                        <span class="clickable-link" wire:click='undoSelectAllOrders'>Undo</span>
                                    </th>
                                @else
                                    <th colspan="8" class="table-th"><iconify-icon style="vertical-align: top;"
                                            icon="lucide:info" width="1.2em" height="1.2em"></iconify-icon>
                                        {{ count($selectedOrders) }} order
                                        selected .. <span class="clickable-link" wire:click='selectAllOrders'>Select
                                            All Orders</span></th>
                                @endif
                            @else
                                <th scope="col" class="table-th">Customer</th>
                                <th scope="col" class="table-th">Date</th>
                                <th scope="col" class="table-th">Total</th>
                                <th scope="col" class="table-th">Status</th>
                                <th scope="col" class="table-th">Items</th>
                                <th scope="col" class="table-th">Phone</th>
                                <th scope="col" class="table-th">Zone</th>
                                <th scope="col" class="table-th">Driver</th>
                            @endif

                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">

                        @foreach ($orders as $order)
                            <tr>
                                <td class="table-td flex items-center sticky-column bg-white dark:bg-slate-800 colomn-shadow"
                                    style="position: sticky; left: -25px;  z-index: 10;">
                                    <div class="checkbox-area">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" wire:model="selectedOrders"
                                                value="{{ $order->id }}" class="hidden" id="select-all">
                                            <span
                                                class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                <img src="{{ asset('assets/images/icon/ck-white.svg') }}" alt=""
                                                    class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                        </label>
                                    </div>
                                    <a href=""> <span class="hover-underline">
                                            <b>
                                                #{{ $order->order_number }}
                                            </b>
                                        </span>
                                    </a>

                                </td>


                                <td class="table-td">
                                    {{ $order->customer->name }}
                                </td>

                                <td class="table-td">
                                    {{ $order->created_at->isToday() ? 'Today at ' . $order->created_at->format('g:i a') : ($order->created_at->isYesterday() ? 'Yesterday at ' . $order->created_at->format('g:i a') : $order->created_at->format('Y-m-d g:i a')) }}
                                </td>

                                <td class="table-td">
                                    {{ $order->total_amount }}<small>&nbsp;EGP</small>
                                </td>

                                <td class="table-td text-start overflow-hidden text-ellipsis whitespace-nowrap">
                                    @if ($order->status === App\Models\Orders\Order::STATUS_NEW || $order->status === App\Models\Orders\Order::STATUS_READY)
                                        <span class="badge bg-info-500 text-dark-500 bg-opacity-50 capitalize">
                                            <iconify-icon icon="octicon:dot-16" width="1.2em"
                                                height="1.2em"></iconify-icon>
                                            {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                    @elseif ($order->status === App\Models\Orders\Order::STATUS_IN_DELIVERY)
                                        <span class="badge bg-warning-500 text-dark-500 bg-opacity-50 capitalize">
                                            <iconify-icon icon="octicon:dot-16" width="1.2em"
                                                height="1.2em"></iconify-icon>
                                            {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                    @elseif (
                                        $order->status === App\Models\Orders\Order::STATUS_RETURNED ||
                                            $order->status === App\Models\Orders\Order::STATUS_CANCELLED)
                                        <span class="badge bg-secondary-500 text-dark-500 bg-opacity-50 capitalize">
                                            <iconify-icon icon="icon-park-outline:dot" width="1.2em"
                                                height="1.2em"></iconify-icon>
                                            {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                    @elseif ($order->status === App\Models\Orders\Order::STATUS_DONE)
                                        <span class="badge bg-success-500 text-dark-500 bg-opacity-50 capitalize">
                                            <iconify-icon icon="icon-park-outline:dot" width="1.2em"
                                                height="1.2em"></iconify-icon>
                                            {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-500 text-dark-500 bg-opacity-50 capitalize">
                                            <iconify-icon icon="octicon:dot-16" width="1.2em"
                                                height="1.2em"></iconify-icon>
                                            {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                    @endif
                                    @if ($order->is_paid)
                                        <span class="badge bg-success-500 text-dark-500 bg-opacity-50 capitalize">
                                            <iconify-icon icon="icon-park-outline:dot" width="1.2em"
                                                height="1.2em"></iconify-icon>
                                            Paid
                                        </span>
                                    @else
                                        <span class="badge bg-warning-500 text-dark-500 bg-opacity-50 capitalize">
                                            <iconify-icon icon="octicon:dot-16" width="1.2em"
                                                height="1.2em"></iconify-icon>
                                            Payment pending
                                        </span>
                                    @endif
                                </td>

                                <td class="table-td">
                                    {{ $order->total_quantity }} item{{ $order->total_quantity > 1 ? 's' : '' }}
                                </td>

                                <td class="table-td">
                                    {{ $order->customer_phone }}
                                </td>

                                <td class="table-td">
                                    {{ $order->zone->name }}
                                </td>

                                <td class="table-td text-start overflow-hidden text-ellipsis whitespace-nowrap">
                                    {{ $order->driver ? $order->driver->user->full_name : '-' }}
                                </td>


                            </tr>
                        @endforeach

                    </tbody>

                </table>


                @if ($orders->isEmpty())
                    {{-- START: empty filter result --}}
                    <div class="card m-5 p-5">
                        <div class="card-body rounded-md bg-white dark:bg-slate-800">
                            <div class="items-center text-center p-5">
                                <h2>
                                    <iconify-icon icon="icon-park-outline:search"></iconify-icon>
                                </h2>
                                <h2 class="card-title text-slate-900 dark:text-white mb-3">No orders with the
                                    applied
                                    filters</h2>
                                <p class="card-text">Try changing the filters or search terms for this view.
                                </p>
                                <a href="{{ url('/orders') }}"
                                    class="btn inline-flex justify-center mx-2 mt-3 btn-primary active btn-sm">View
                                    all orders</a>
                            </div>
                        </div>
                    </div>
                    {{-- END: empty filter result --}}
                @endif
            </div>
        </div>
        <div style="position: sticky ; bottom:0;width:100%; z-index:10;"
            class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
            {{ $orders->links('vendor.livewire.simple-bootstrap') }}
        </div>

    </div>

</div>
