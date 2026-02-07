<div>


    @if (count($selectedOrders) > 0)
        <div class="dropdup relative select-action-btns-container">
            <button class="btn inline-flex justify-center btn-dark items-center  no-wrap" type="button"
                id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 50px;">
                Bulk actions
            </button>
            <ul class="dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none"
                style="">
                @if ($AvailableToSetDriver)
                    <li wire:click='openSetDriver'
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                        Set Driver
                    </li>
                    <li wire:click='openSetDeliveryDate'
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                        Set delivery date
                    </li>
                @endif
                <li wire:click='setBulkAsConfirmed'
                    class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                    Set as confirmed
                </li>
                <li wire:click='setBulkAsNotConfirmed'
                    class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                    Set as not confirmed
                </li>
                @if ($AvailableToPay)
                    @foreach ($PAYMENT_METHODS as $PAYMENT_METHOD)
                        <li wire:click='openPayOrders("{{ $PAYMENT_METHOD }}")'
                            class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                            Pay {{ ucwords(str_replace('_', ' ', $PAYMENT_METHOD)) }}
                        </li>
                    @endforeach

                @endif
                @foreach ($availableBulkStatuses as $availableBulkStatus)
                    @if (
                        !(
                            $availableBulkStatus == App\Models\Orders\Order::STATUS_READY ||
                            $availableBulkStatus == App\Models\Orders\Order::STATUS_IN_DELIVERY
                        ) || auth()->user()->can('updateInventoryInfo', App\Models\Orders\Order::class))
                        <li wire:click="setBulkStatus('{{ $availableBulkStatus }}')"
                            class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                            Set as {{ ucwords(str_replace('_', ' ', $availableBulkStatus)) }}
                        </li>
                    @endif
                @endforeach
                @if (in_array('in_delivery', $availableBulkStatuses) || in_array('done', $availableBulkStatuses))
                    <li wire:click='resetStatuses'
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                        Reset Orders Status
                    </li>
                @endif
            </ul>
        </div>
    @endif

    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Active Orders 
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            <div class="dropdown relative">
                <button class="btn inline-flex justify-center btn-dark items-center btn-sm" type="button"
                    id="darkDropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    Filter
                    <iconify-icon class="text-xl ltr:ml-2 rtl:mr-2" icon="ic:round-keyboard-arrow-down"></iconify-icon>
                </button>
                <ul
                    class=" dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow
                            z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">
                    <li wire:click='openFilteryDeliveryDate'
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                        Delivery Date
                    </li>
                    <li wire:click='openFilteryStatus'
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                        Status
                    </li>
                    <li wire:click='openFilteryDriver'
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                        Driver
                    </li>
                    <li wire:click='openZoneSec'
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                        Zone
                    </li>
                </ul>
            </div>

            @can('create', App\Models\Orders\Order::class)
                <a href="{{ route('orders.create') }}">
                    <button
                        class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1 btn-sm">
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

        @if ($driver)
            <header class="dark-card-header noborder bg-dark">
                <div class="space-y-1">
                    <h4 class="text-slate-400 dark:text-slate-200 text-xs font-normal">
                        Driver Weight Limit
                    </h4>
                    <div class="text-sm font-medium text-white dark:text-slate-900">
                        {{ $totalWeight > 0 ? number_format($totalWeight / 1000, 3) : 0 }} /
                        {{ number_format($driver->weight_limit / 1000, 3) }} KG
                    </div>
                    <div class=" text-xs font-normal">
                        @if ($driver->weight_limit)
                            @if (($totalWeight / $driver->weight_limit) * 100 <= 50)
                                <span
                                    class="text-danger-500">({{ number_format(($totalWeight / $driver->weight_limit) * 100, 0) }}%)
                                    In-sufficient</span>
                            @elseif (($totalWeight / $driver->weight_limit) * 100 > 50 && ($totalWeight / $driver->weight_limit) * 100 <= 70)
                                <span class="text-warning-500">
                                    ({{ number_format(($totalWeight / $driver->weight_limit) * 100, 0) }}%) Nearly
                                    Sufficient</span>
                            @elseif (($totalWeight / $driver->weight_limit) * 100 > 70 && ($totalWeight / $driver->weight_limit) * 100 <= 100)
                                <span
                                    class="text-success-500">({{ number_format(($totalWeight / $driver->weight_limit) * 100, 0) }}%)
                                    Sufficient</span>
                            @elseif (($totalWeight / $driver->weight_limit) * 100 > 100)
                                <span
                                    class="text-danger-500">({{ number_format(($totalWeight / $driver->weight_limit) * 100, 0) }}%)
                                    Overload</span>
                            @endif
                        @endif

                    </div>
                </div>
                <div class="space-y-1">
                    <h4 class="text-slate-400 dark:text-slate-200 text-xs font-normal">
                        Total Orders Limit
                    </h4>
                    <div class="text-sm font-medium text-white dark:text-slate-900">
                        {{ $ordersCount ?? 0 }} / {{ $driver->order_quantity_limit ?? 'No Limit' }} Orders
                    </div>
                    <div class=" text-xs font-normal">
                        @if ($driver->order_quantity_limit)
                            @if (($ordersCount / $driver->order_quantity_limit) * 100 <= 50)
                                <span
                                    class="text-danger-500">({{ number_format(($ordersCount / $driver->order_quantity_limit) * 100, 0) }}%)
                                    In-sufficient</span>
                            @elseif (
                                ($ordersCount / $driver->order_quantity_limit) * 100 > 50 &&
                                    ($ordersCount / $driver->order_quantity_limit) * 100 <= 70)
                                <span class="text-warning-500">
                                    ({{ number_format(($ordersCount / $driver->order_quantity_limit) * 100, 0) }}%)
                                    Nearly
                                    Sufficient</span>
                            @elseif (
                                ($ordersCount / $driver->order_quantity_limit) * 100 > 70 &&
                                    ($ordersCount / $driver->order_quantity_limit) * 100 <= 100)
                                <span
                                    class="text-success-500">({{ number_format(($ordersCount / $driver->order_quantity_limit) * 100, 0) }}%)
                                    Sufficient</span>
                            @elseif (($ordersCount / $driver->order_quantity_limit) * 100 > 100)
                                <span
                                    class="text-danger-500">({{ number_format(($ordersCount / $driver->weight_limit) * 100, 0) }}%)
                                    Overload</span>
                            @endif
                        @endif

                    </div>
                </div>
                <div class="space-y-1">
                    <h4 class="text-slate-400 dark:text-slate-200 text-xs font-normal">
                        Total Zones
                    </h4>
                    <div class="text-sm font-medium text-white dark:text-slate-900">
                        {{ $totalZones }}
                    </div>
                </div>
                <div class="space-y-1">
                    <h4 class="text-slate-400 dark:text-slate-200 text-xs font-normal">
                        Amount to Collect
                    </h4>
                    <div class="text-sm font-medium text-white dark:text-slate-900">
                        {{ number_format($orders->sum('remaining_to_pay'), 2) }}
                    </div>
                </div>
            </header>
        @endif

        <header class="card-header cust-card-header noborder">
            <div>
                @if ($deliveryDate)
                    <span class="badge bg-slate-900 text-white capitalize">
                        <span class="cursor-pointer" wire:click='openFilteryDeliveryDate'>
                            <span class="text-secondary-500 ">Delivery Date:</span>
                            @foreach ($deliveryDate as $sDdate)
                                &nbsp;
                                {{ $sDdate->isToday()
                                    ? 'Today'
                                    : ($sDdate->isYesterday()
                                        ? 'Yesterday'
                                        : ($sDdate->isTomorrow()
                                            ? 'Tomorrow'
                                            : $sDdate->format('l d-m-Y'))) }}
                                @if (!$loop->last)
                                    ,
                                @endif
                            @endforeach
                        </span>

                        &nbsp;&nbsp;<iconify-icon wire:click="clearDeliveryDate(closed)" icon="material-symbols:close"
                            class="cursor-pointer" width="1.2em" height="1.2em"></iconify-icon>
                    </span>
                @endif
                @if ($status)
                    <span class="badge bg-slate-900 text-white capitalize">
                        <span class="cursor-pointer" wire:click='openFilteryStatus'>
                            <span class="text-secondary-500 ">Status:</span>&nbsp;
                            {{ ucwords(str_replace('_', ' ', $status)) }}

                        </span>

                        &nbsp;&nbsp;<iconify-icon wire:click="clearProperty('status')" icon="material-symbols:close"
                            class="cursor-pointer" width="1.2em" height="1.2em"></iconify-icon>
                    </span>
                @endif
                @if ($driver)
                    <span class="badge bg-slate-900 text-white capitalize">
                        <span class="cursor-pointer" wire:click='openFilteryDriver'>
                            <span class="text-secondary-500 ">Driver:</span>&nbsp;
                            {{ ucwords($driver->user->full_name) }} • {{ $driver->shift_title }}

                        </span>

                        &nbsp;&nbsp;<iconify-icon wire:click="clearProperty('driver')" icon="material-symbols:close"
                            class="cursor-pointer" width="1.2em" height="1.2em"></iconify-icon>
                    </span>
                @endif
                @if (count($selectedZonesNames))
                    <span wire:click='openZoneSec' class="badge bg-slate-900 text-white capitalize" type="button"
                        id="secondaryFlatDropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="cursor-pointer">
                            <span class="text-secondary-500 ">Zone:</span>&nbsp;
                            @foreach ($selectedZonesNames as $zz)
                                {{ $zz }},
                            @endforeach

                        </span>
                    </span>
                @endif
            </div>
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
                                            <img src="{{ asset("assets/images/icon/ck-white.svg") }}" alt=""
                                                class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                    </label>
                                </div>
                                Order
                            </th>
                            @if (count($selectedOrders))
                                <th colspan="8" class="table-th"><iconify-icon style="vertical-align: top;"
                                        icon="lucide:info" width="1.2em" height="1.2em"></iconify-icon>
                                    {{ count($selectedOrders) }} order
                                    selected .. <span class="clickable-link" wire:click='unselectAllOrders'>Unselect
                                        All Orders</span></th>
                            @else
                                <th scope="col" class="table-th">Delivery</th>
                                <th scope="col" class="table-th">Zone</th>
                                <th scope="col" class="table-th">Customer</th>
                                <th scope="col" class="table-th">Total</th>
                                <th scope="col" class="table-th">Status</th>
                                <th scope="col" class="table-th">Payment</th>
                                <th scope="col" class="table-th">Driver</th>
                                <th scope="col" class="table-th">Creator</th>
                            @endif

                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">

                        @foreach ($orders as $order)
                            <tr class="even:bg-slate-100 dark:even:bg-slate-700">

                                <td class="table-td flex items-center sticky-column bg-slate-100 colomn-shadow"
                                    style="position: sticky; left: -25px;  z-index: 10;">
                                    <div class="checkbox-area">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" wire:model.live="selectedOrders"
                                                value="{{ $order->id }}" class="hidden" id="select-all">
                                            <span
                                                class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                <img src="{{ asset("assets/images/icon/ck-white.svg") }}"
                                                    alt=""
                                                    class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                        </label>
                                    </div>
                                    <a href="{{ route('orders.show', $order->id) }}" target="_blanck"> <span
                                            class="hover-underline">
                                            <b>
                                                #{{ $order->order_number }}
                                            </b>
                                        </span>
                                    </a>

                                </td>

                                <td class="table-td">
                                    @if (
                                        ($order->delivery_date?->isPast() && !$order->delivery_date?->isToday()) ||
                                            ($order->delivery_date?->isToday() && !$order->is_confirmed))
                                        <span
                                            class="h-[6px] w-[6px] bg-danger-500 rounded-full inline-block ring-4 ring-opacity-30 ring-danger-500"
                                            style="vertical-align: middle;"></span> &nbsp;
                                    @endif

                                    {{ $order->delivery_date?->isToday() ? 'Today' : ($order->delivery_date?->isYesterday() ? 'Yesterday' : $order->delivery_date?->format('D d-m')) }}
                                </td>

                                <td class="table-td">
                                    {{ $order->zone->name }}
                                </td>

                                <td class="table-td">
                                    {{ $order->customer->name }}
                                </td>


                                <td class="table-td">
                                    {{ $order->total_amount }}
                                </td>

                                <td class="table-td text-start overflow-hidden text-ellipsis whitespace-nowrap">
                                    @if ($order->status === App\Models\Orders\Order::STATUS_NEW)
                                        @if ($order->is_confirmed)
                                            <span class="badge bg-success-500 text-white capitalize rounded-3xl">
                                                Confirmed</span>
                                        @endif
                                    @elseif ($order->status === App\Models\Orders\Order::STATUS_READY)
                                        @if ($order->is_confirmed)
                                            <span class="badge bg-success-500 text-white capitalize rounded-3xl">
                                                Confirmed</span>
                                        @endif
                                        <span class="badge bg-[#EAE5FF] text-dark-500 capitalize">

                                            {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                    @elseif ($order->status === App\Models\Orders\Order::STATUS_IN_DELIVERY)
                                        @if ($order->is_confirmed)
                                            <span class="badge bg-success-500 text-white capitalize rounded-3xl">
                                                Confirmed</span>
                                        @endif
                                        <span class="badge bg-[#EAE5FF] text-dark-500 capitalize">

                                            {{ ucwords(str_replace('_', ' ', App\Models\Orders\Order::STATUS_READY)) }}
                                        </span>
                                        <span class="badge bg-primary-500 text-dark-500 bg-opacity-50 capitalize">

                                            {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                    @elseif (
                                        $order->status === App\Models\Orders\Order::STATUS_RETURNED ||
                                            $order->status === App\Models\Orders\Order::STATUS_CANCELLED)
                                        <span class="badge bg-secondary-500 text-dark-500 bg-opacity-50 capitalize">

                                            {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                    @elseif ($order->status === App\Models\Orders\Order::STATUS_DONE)
                                        <span class="badge bg-success-500 text-dark-500 bg-opacity-50 capitalize">

                                            {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-500 text-dark-500 bg-opacity-50 capitalize">

                                            {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                        @if ($order->is_confirmed)
                                            <span class="badge bg-success-500 text-white capitalize rounded-3xl">
                                                Confirmed</span>
                                        @endif
                                    @endif
                                </td>

                                <td class="table-td">
                                    @if ($order->is_paid)
                                        <span class="badge bg-success-500 text-dark-500 bg-opacity-50 capitalize">
                                            <iconify-icon icon="icon-park-outline:dot" width="1.2em"
                                                height="1.2em"></iconify-icon>
                                            Paid
                                        </span>
                                        {{-- @elseif ($order->isPartlyPaid())
                                        <span class="badge bg-warning-500 text-dark-500 bg-opacity-50 capitalize">
                                            Remaining:
                                            {{ number_format($order->remaining_to_pay, 2) }}
                                        </span> --}}
                                    @else
                                        <span class="badge bg-warning-500 text-dark-500 bg-opacity-50 capitalize">
                                            <iconify-icon icon="octicon:dot-16" width="1.2em"
                                                height="1.2em"></iconify-icon>
                                            Pending
                                        </span>
                                    @endif
                                </td>

                                <td class="table-td text-start overflow-hidden text-ellipsis whitespace-nowrap">
                                    @if ($order->is_delivered)
                                        <span
                                            class="h-[6px] w-[6px] bg-success-500 rounded-full inline-block ring-4 ring-opacity-30 ring-success-500"
                                            style="vertical-align: middle;"></span> &nbsp;
                                    @endif
                                    {{ $order->driver ? $order->driver->shift_title : '-' }}
                                    {{ $order->driver && $order->driver_payment_type ? ' • ' . paymentMethodInArabic($order->driver_payment_type) : '' }}
                                </td>

                                <td class="table-td text-start overflow-hidden text-ellipsis whitespace-nowrap">
                                    {{ $order->creator->full_name }}
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


    {{-- @can('create', App\Models\Products\Product::class) --}}
    @if ($setDriverSection)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog"
            style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Set Driver
                            </h3>
                            <button wire:click="closeSetDriver" type="button"
                                class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                data-bs-dismiss="modal">
                                <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>

                        <!-- Modal body -->
                        <div class="p-6 space-y-4">

                            <div class="from-group">
                                <div class="input-area">
                                    <label for="driverId" class="form-label">Driver*</label>
                                    <select name="category_id" id="driverId"
                                        class="form-control w-full mt-2 @error('driverId') !border-danger-500 @enderror"
                                        wire:model="driverId" autocomplete="off">
                                        <option value="">Select driver</option>
                                        @foreach ($drivers as $driver)
                                            <option value="{{ $driver->id }}">
                                                {{ $driver->user->full_name }} • {{ $driver->shift_title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('driverId')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="setDriver" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="setDriver">Submit</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="setDriver"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    @endif
    {{-- @endcan --}}

    @if ($setDeliveryDateSection)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog"
            style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Set delivery date
                            </h3>
                            <button wire:click="closeSetDeliveryDate" type="button"
                                class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                data-bs-dismiss="modal">
                                <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>

                        <!-- Modal body -->
                        <div class="p-6 space-y-4">

                            <div class="from-group">
                                <div class="input-area">
                                    <label for="driverId" class="form-label">Delivery date*</label>
                                    <input name="bulkDeliveryDate" id="bulkDeliveryDate" type="date"
                                        class="form-control w-full mt-2 @error('bulkDeliveryDate') !border-danger-500 @enderror"
                                        wire:model="bulkDeliveryDate" autocomplete="off">

                                </div>
                                @error('bulkDeliveryDate')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="setDeliveryDate" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="setDeliveryDate">Submit</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="setDeliveryDate"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    @endif


    @if ($isOpenPayAlert)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog"
            style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-warning-500">
                            <h3 class="text-xl font-medium text-black dark:text-white capitalize">
                                Payment Warning
                            </h3>
                            <button wire:click="closePayOrders" type="button"
                                class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                data-bs-dismiss="modal">
                                <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>

                        <!-- Modal body -->
                        <div class="p-6 space-y-4">

                            Are you sure you want to pay
                            {{-- <b>{{ number_format($order->total_amount, 2) }}<small>EGP  </small> </b>  --}}
                            {{ ucwords(str_replace('_', ' ', $isOpenPayAlert)) }}

                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="ProcceedBulkPayment" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="ProcceedBulkPayment">Procceed Payment</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="ProcceedBulkPayment"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    @endif

    @if ($Edited_deliveryDate_sec)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog"
            style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Filter delivery date
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="removeSelectedDate,Edited_deliveryDate"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </h3>
                            <button wire:click="closeFilteryDeliveryDate" type="button"
                                class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                data-bs-dismiss="modal">
                                <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>

                        <!-- Modal body -->
                        <div class="p-6 space-y-4">

                            <div class="from-group">
                                <div class="input-area">
                                    <label for="Edited_deliveryDate" class="form-label">Delivery date*</label>
                                    <p class="text-gray-600 text-xs mb-2">
                                        *You can select multiple dates by clicking on the date. Once done, click
                                        "Submit" to apply the filter.
                                    </p>
                                    <input name="Edited_deliveryDate" id="Edited_deliveryDate" type="date"
                                        class="form-control w-full mt-2 @error('Edited_deliveryDate') !border-danger-500 @enderror"
                                        wire:model.live="Edited_deliveryDate" autocomplete="off">
                                </div>
                                @error('Edited_deliveryDate')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>

                            @foreach ($selectedDeliveryDates as $index => $date)
                                <span class="badge bg-slate-900 text-white capitalize">
                                    <span class="cursor-pointer">
                                        {{ $date->isToday()
                                            ? 'Today'
                                            : ($date->isYesterday()
                                                ? 'Yesterday'
                                                : ($date->isTomorrow()
                                                    ? 'Tomorrow'
                                                    : $date->format('l d-m-Y'))) }}
                                    </span>

                                    &nbsp;&nbsp;<iconify-icon wire:click="removeSelectedDate({{ $index }})"
                                        icon="material-symbols:close" class="cursor-pointer" width="1.2em"
                                        height="1.2em"></iconify-icon>
                                </span>
                            @endforeach

                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="setFilteryDeliveryDate" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="setFilteryDeliveryDate">Submit</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="setFilteryDeliveryDate"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    @endif

    @if ($Edited_status_sec)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog"
            style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Filter Status
                            </h3>
                            <button wire:click="closeFilteryStatus" type="button"
                                class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                data-bs-dismiss="modal">
                                <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>

                        <!-- Modal body -->
                        <div class="p-6 space-y-4">

                            <div class="from-group">
                                <div class="input-area">
                                    <label for="Edited_status" class="form-label">Status*</label>
                                    <select name="category_id" id="Edited_status"
                                        class="form-control w-full mt-2 @error('Edited_status') !border-danger-500 @enderror"
                                        wire:model="Edited_status" autocomplete="off">
                                        <option value="">Select status</option>
                                        @foreach ($STATUSES as $ONE_STATUSES)
                                            <option value="{{ $ONE_STATUSES }}">
                                                {{ ucwords(str_replace('_', ' ', $ONE_STATUSES)) }}</option>
                                        @endforeach
                                    </select>

                                </div>
                                @error('Edited_status')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="setFilterStatus" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="setFilterStatus">Submit</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="setFilterStatus"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    @endif

    @if ($Edited_driverId_sec)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog"
            style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Filter Driver
                            </h3>
                            <button wire:click="closeFilteryDriver" type="button"
                                class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                data-bs-dismiss="modal">
                                <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>

                        <!-- Modal body -->
                        <div class="p-6 space-y-4">

                            <div class="from-group">
                                <div class="input-area">
                                    <label for="Edited_driverId" class="form-label">Driver*</label>
                                    <select name="Edited_driverId" id="Edited_status"
                                        class="form-control w-full mt-2 @error('Edited_driverId') !border-danger-500 @enderror"
                                        wire:model="Edited_driverId" autocomplete="off">
                                        <option value="">Select driver</option>
                                        @foreach ($DRIVERS as $ONE_DRIVERS)
                                            <option value="{{ $ONE_DRIVERS->id }}">
                                                {{ $ONE_DRIVERS->user->full_name }} • {{ $ONE_DRIVERS->shift_title }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>
                                @error('Edited_driverId')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="setFilterDriver" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="setFilterDriver">Submit</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="setFilterDriver"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    @endif

    @if ($Edited_Zone_sec)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog"
            style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Filter by Zones
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="removeSelectedZone,Edited_Zone"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </h3>
                            <button wire:click="closeZoneSec" type="button"
                                class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                data-bs-dismiss="modal">
                                <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>

                        <!-- Modal body -->
                        <div class="p-6 space-y-4">

                            <div class="from-group">
                                <div class="input-area">
                                    <label for="Edited_Zone" class="form-label">Zone</label>
                                    <select
                                        class="form-control w-full mt-2 @error('Edited_Zone') !border-danger-500 @enderror"
                                        wire:model.live="Edited_Zone" autocomplete="off">
                                        <option selected readonly>Select Zones</option>
                                        @foreach ($saved_zones as $z)
                                            <option value="{{ $z->id }}">
                                                {{ $z->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('Edited_Zone')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>

                            @foreach ($selectedZonesNames as $index => $zone)
                                <span class="badge bg-slate-900 text-white capitalize">
                                    <span class="cursor-pointer">
                                        {{ $zone }}
                                    </span>

                                    &nbsp;&nbsp;<iconify-icon wire:click="removeSelectedZone({{ $index }})"
                                        icon="material-symbols:close" class="cursor-pointer" width="1.2em"
                                        height="1.2em"></iconify-icon>
                                </span>
                            @endforeach

                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="setZones" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="setZones">Submit</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="setZones"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    @endif
</div>
