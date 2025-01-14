<div>

    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <div class=column>
                <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4 mb-2">
                    Orders Report
                </h4>
                <h6>Count: {{ $orders->total() }} -- {{ number_format($totalWeight / 1000) }}</h6>
            </div>
            
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
                    <li wire:click='openFilteryStatus'
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                        Status
                    </li>
                    <li wire:click='openFilterCreationDate'
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                        Creation Date
                    </li>
                    <li wire:click='openFilterDeliveryDate'
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                        Delivery Date
                    </li>
                    <li wire:click='openFilteryCreator'
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                        Creator
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

        </div>
    </div>


    <div class="card">
        <header class="card-header cust-card-header noborder">
            <iconify-icon wire:loading wire:target="search" class="loading-icon text-lg"
                icon="line-md:loading-twotone-loop"></iconify-icon>
            <input type="text" class="form-control !pl-9 mr-1 basis-1/4" placeholder="Search"
                wire:model.live.debounce.400ms="search">
        </header>

        <header class="card-header cust-card-header noborder">
            <div>
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
                @if ($creation_date_from || $creation_date_to)
                    <span class="badge bg-slate-900 text-white capitalize">
                        <span class="cursor-pointer" wire:click='openFilterCreationDate'>
                            <span class="text-secondary-500 ">Creation Date:</span>&nbsp;
                            {{ $creation_date_from ? 'From ' . \Carbon\Carbon::parse($creation_date_from)->format('l, F j, Y') : '' }}
                            @if ($creation_date_from && $creation_date_to)
                                -
                            @endif
                            {{ $creation_date_to ? 'To ' . \Carbon\Carbon::parse($creation_date_to)->format('l, F j, Y') : '' }}
                        </span>

                        &nbsp;&nbsp;<iconify-icon wire:click="clearFilterCreationDates" icon="material-symbols:close"
                            class="cursor-pointer" width="1.2em" height="1.2em">
                        </iconify-icon>
                    </span>
                @endif
                @if ($delivery_date_from || $delivery_date_to)
                    <span class="badge bg-slate-900 text-white capitalize">
                        <span class="cursor-pointer" wire:click='openFilterDeliveryDate'>
                            <span class="text-secondary-500 ">Delivery Date:</span>&nbsp;
                            {{ $delivery_date_from ? 'From ' . \Carbon\Carbon::parse($delivery_date_from)->format('l, F j, Y') : '' }}
                            @if ($delivery_date_from && $delivery_date_to)
                                -
                            @endif
                            {{ $delivery_date_to ? 'To ' . \Carbon\Carbon::parse($delivery_date_to)->format('l, F j, Y') : '' }}
                        </span>

                        &nbsp;&nbsp;<iconify-icon wire:click="clearFilterDeliveryDates" icon="material-symbols:close"
                            class="cursor-pointer" width="1.2em" height="1.2em">
                        </iconify-icon>
                    </span>
                @endif
                @if ($creator)
                    <span class="badge bg-slate-900 text-white capitalize">
                        <span class="cursor-pointer" wire:click='openFilteryCreator'>
                            <span class="text-secondary-500 ">Creator:</span>&nbsp;
                            {{ $creator->full_name }}

                        </span>

                        &nbsp;&nbsp;<iconify-icon wire:click="clearProperty('creator')" icon="material-symbols:close"
                            class="cursor-pointer" width="1.2em" height="1.2em"></iconify-icon>
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
                                <th scope="col" class="table-th">Delivery</th>
                                <th scope="col" class="table-th">Zone</th>
                                <th scope="col" class="table-th">Customer</th>
                                <th scope="col" class="table-th">Total</th>
                                <th scope="col" class="table-th">KG</th>
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

                                <td class="table-td flex items-center sticky-column bg-slate-100 colomn-shadow even:bg-slate-100 dark:even:bg-slate-700"
                                    style="position: sticky; left: -25px;  z-index: 10;">
                                    <div class="checkbox-area">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" wire:model.live="selectedOrders"
                                                value="{{ $order->id }}" class="hidden" id="select-all">
                                            <span
                                                class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                <img src="{{ asset('assets/images/icon/ck-white.svg') }}"
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

                                    {{ $order->delivery_date->isToday() ? 'Today' : ($order->delivery_date->isYesterday() ? 'Yesterday' : $order->delivery_date->format('D d-m')) }}
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

                                <td class="table-td">
                                    {{ $order->total_weight / 1000 }}
                                </td>

                                <td class="table-td text-start overflow-hidden text-ellipsis whitespace-nowrap">
                                    @if ($order->status === App\Models\Orders\Order::STATUS_NEW)
                                        <span class="badge bg-info-500 text-dark-500 bg-opacity-50 capitalize">
                                            {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                        @if ($order->is_confirmed)
                                            <span class="badge bg-success-500 text-white capitalize rounded-3xl">
                                                Confirmed</span>
                                        @endif
                                    @elseif ($order->status === App\Models\Orders\Order::STATUS_READY)
                                        <span class="badge bg-info-500 text-dark-500 bg-opacity-50 capitalize">
                                            {{ ucwords(str_replace('_', ' ', App\Models\Orders\Order::STATUS_NEW)) }}
                                        </span>
                                        @if ($order->is_confirmed)
                                            <span class="badge bg-success-500 text-white capitalize rounded-3xl">
                                                Confirmed</span>
                                        @endif
                                        <span class="badge bg-[#EAE5FF] text-dark-500 capitalize">

                                            {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                    @elseif ($order->status === App\Models\Orders\Order::STATUS_IN_DELIVERY)
                                        <span class="badge bg-info-500 text-dark-500 bg-opacity-50 capitalize">
                                            {{ ucwords(str_replace('_', ' ', App\Models\Orders\Order::STATUS_NEW)) }}
                                        </span>
                                        @if ($order->is_confirmed)
                                            <span class="badge bg-success-500 text-white capitalize rounded-3xl">
                                                Confirmed</span>
                                        @endif
                                        <span class="badge bg-[#EAE5FF] text-dark-500 capitalize">

                                            {{ ucwords(str_replace('_', ' ', App\Models\Orders\Order::STATUS_READY)) }}
                                        </span>
                                        <span class="badge bg-warning-500 text-dark-500 bg-opacity-50 capitalize">

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
                                    @elseif ($order->isPartlyPaid())
                                        <span class="badge bg-warning-500 text-dark-500 bg-opacity-50 capitalize">
                                            Remaining:
                                            {{ number_format($order->remaining_to_pay, 2) }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning-500 text-dark-500 bg-opacity-50 capitalize">
                                            <iconify-icon icon="octicon:dot-16" width="1.2em"
                                                height="1.2em"></iconify-icon>
                                            Pending
                                        </span>
                                    @endif
                                </td>

                                <td class="table-td text-start overflow-hidden text-ellipsis whitespace-nowrap">
                                    {{ $order->driver ? $order->driver->user->full_name . ' • ' . $order->driver->shift_title : '-' }}
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

    @if ($Edited_creation_date_from_sec)
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
                                Filter by Creation Date
                            </h3>
                            <button wire:click="closeFilterCreationDate" type="button"
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
                            <div class="form-group">
                                <label for="edited_creation_date_from" class="form-label">Creation Date From</label>
                                <input type="date" id="edited_creation_date_from" class="form-control"
                                    wire:model="edited_creation_date_from">
                            </div>

                            <div class="form-group mt-4">
                                <label for="edited_creation_date_to" class="form-label">Creation Date To</label>
                                <input type="date" id="edited_creation_date_to" class="form-control"
                                    wire:model="edited_creation_date_to">
                            </div>
                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="setFilterCreationDate" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="setFilterCreationDate">Submit</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="setFilterCreationDate"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    @endif

    @if ($Edited_delivery_date_from_sec)
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
                                Filter by Delivery Date
                            </h3>
                            <button wire:click="closeFilterDeliveryDate" type="button"
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
                            <div class="form-group">
                                <label for="edited_delivery_date_from" class="form-label">Delivery Date From</label>
                                <input type="date" id="edited_delivery_date_from" class="form-control"
                                    wire:model="edited_delivery_date_from">
                            </div>

                            <div class="form-group mt-4">
                                <label for="edited_delivery_date_to" class="form-label">Delivery Date To</label>
                                <input type="date" id="edited_delivery_date_to" class="form-control"
                                    wire:model="edited_delivery_date_to">
                            </div>
                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="setFilterDeliveryDate" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="setFilterDeliveryDate">Submit</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="setFilterDeliveryDate"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    @endif

    @if ($Edited_creatorId_sec)
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
                                Filter Creator
                            </h3>
                            <button wire:click="closeFilterCreator" type="button"
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
                                    <label for="Edited_creatorId" class="form-label">Creator*</label>
                                    <select name="Edited_creatorId" id="Edited_status"
                                        class="form-control w-full mt-2 @error('Edited_creatorId') !border-danger-500 @enderror"
                                        wire:model="Edited_creatorId" autocomplete="off">
                                        <option value="">Select zone</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">
                                                {{ $user->full_name }}</option>
                                        @endforeach
                                    </select>

                                </div>
                                @error('Edited_creatorId')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="setFilterCreator" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="setFilterCreator">Submit</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="setFilterCreator"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    @endif
</div>
