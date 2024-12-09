<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Driver Shift
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

    <div class="mb-5">
        @if ($deliveryDate)
            <span class="badge bg-slate-900 text-white capitalize">
                <span class="cursor-pointer" wire:click='openFilteryDeliveryDate'>
                    <span class="text-secondary-500 ">Delivery Date:</span>&nbsp;
                    {{ $deliveryDate->isToday()
                        ? 'Today'
                        : ($deliveryDate->isYesterday()
                            ? 'Yesterday'
                            : ($deliveryDate->isTomorrow()
                                ? 'Tomorrow'
                                : $deliveryDate->format('l d-m-Y'))) }}
                </span>
            </span>
        @endif
        @if ($driver)
            <div class="dropdown relative" style="display: contents">
                <span class="badge bg-slate-900 text-white capitalize"
                    @if (auth()->user()->is_driver) type="button"
                    id="secondaryFlatDropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" @endif>
                    <span class="cursor-pointer"
                        @if (!auth()->user()->is_driver) wire:click='openFilteryDriver' @endif>
                        <span class="text-secondary-500 ">Driver:</span>&nbsp;
                        {{ ucwords($driver->user->full_name) }} • {{ $driver->shift_title }}

                    </span>
                </span>
                <ul
                    class=" dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow
                            z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">
                    @foreach (auth()->user()->drivers as $shift)
                        <li wire:click='ChangeDriverShift({{ $shift->id }})'
                            class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                            {{ auth()->user()->full_name }} • <b>{{ $shift->shift_title }}</b>
                            @if ($shift->countOrders())
                                • {{ $shift->countOrders($deliveryDate) }} Orders
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <div>
        <div class="md:flex-1 rounded-md overlay md:col-span-2" style="min-width: 400px;">
            <div class="flex-1 rounded-md col-span-2">
                <div class="card-body flex flex-col justify-center  bg-no-repeat bg-center bg-cover card p-4 active">
                    <div class="card-text flex flex-col justify-between h-full menu-open">

                        @forelse ($orders as $order)
                            <div class="card-body flex flex-col justify-between border rounded-lg h-full menu-open p-0 mb-5"
                                style="border-color:rgb(224, 224, 224)">
                                <div class="grid grid-cols-1 md:grid-cols-8 lg:grid-cols-8">

                                    <div class="p-3 md:col-span-2">
                                        <div class="flex justify-between">
                                            <a href="{{ route('orders.show', $order->id) }}"> <span
                                                    class="hover-underline">
                                                    <b>
                                                        #{{ $order->order_number }} • {{ $order->customer->name }}
                                                    </b>
                                                </span>
                                            </a>

                                            <div class="">
                                                @if ($order->status === App\Models\Orders\Order::STATUS_NEW)
                                                    <span
                                                        class="badge bg-info-500 text-dark-500 bg-opacity-50 capitalize  btn-outline-info"
                                                        style="padding-top: 3px;padding-bottom: 3px">
                                                        {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                                    </span>
                                                @elseif ($order->status === App\Models\Orders\Order::STATUS_IN_DELIVERY)
                                                    <span
                                                        class="badge bg-warning-500 text-dark-500 bg-opacity-50 capitalize  btn-outline-warning"
                                                        style="padding-top: 3px;padding-bottom: 3px">
                                                        {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                                    </span>
                                                @elseif (
                                                    $order->status === App\Models\Orders\Order::STATUS_RETURNED ||
                                                        $order->status === App\Models\Orders\Order::STATUS_CANCELLED)
                                                    <span
                                                        class="badge bg-secondary-500 text-dark-500 bg-opacity-50 capitalize   btn-outline-secondary"
                                                        style="padding-top: 3px;padding-bottom: 3px">
                                                        {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                                    </span>
                                                @elseif ($order->status === App\Models\Orders\Order::STATUS_DONE || $order->status === App\Models\Orders\Order::STATUS_READY)
                                                    <span
                                                        class="badge bg-success-500 text-dark-500 bg-opacity-50 capitalize btn-outline-success"
                                                        style="padding-top: 3px;padding-bottom: 3px">
                                                        {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                                    </span>
                                                @else
                                                    <span
                                                        class="badge bg-secondary-500 text-dark-500 bg-opacity-50 capitalize  btn-outline-secondary"
                                                        style="padding-top: 3px;padding-bottom: 3px">
                                                        {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                                    </span>
                                                @endif
                                            </div>


                                        </div>
                                        <hr class="mt-2">
                                        <div class="mb-2">
                                            @if ($order->note)
                                                <div class="flex justify-between items-end">
                                                    <small>Order Note:</small>
                                                    @can('updateOrderNote', $order)
                                                        <button wire:click='openEditOrderNote({{ $order->id }})'
                                                            class="action-btn mt-2" type="button">
                                                            <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                                                        </button>
                                                    @endcan
                                                </div>
                                                <small class="text-wrap"><b>{{ $order->note }}</b></small>
                                            @else
                                                @can('updateOrderNote', $order)
                                                    <div wire:click='openEditOrderNote({{ $order->id }})'
                                                        class="inline-flex items-center clickable-link">
                                                        <iconify-icon icon="material-symbols:add" width="15"
                                                            height="15"></iconify-icon>
                                                        <small class="">Add Note</small>
                                                    </div>
                                                @else
                                                    <small class="">No notes for this order.</small>
                                                @endcan
                                            @endif
                                        </div>
                                        <hr>
                                        <div>
                                            @if ($order->driver_note)
                                                <div class="flex justify-between items-end">
                                                    <small>Driver Note:</small>
                                                    @can('updateDriverNote', $order)
                                                        <button wire:click='openEditDriverNote({{ $order->id }})'
                                                            class="action-btn  mt-2" type="button">
                                                            <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                                                        </button>
                                                    @endcan
                                                </div>
                                                <small class="text-wrap"><b>{{ $order->driver_note }}</b></small>
                                            @else
                                                @can('updateDriverNote', $order)
                                                    <div wire:click='openEditDriverNote({{ $order->id }})'
                                                        class="inline-flex items-center clickable-link">
                                                        <iconify-icon icon="material-symbols:add" width="15"
                                                            height="15"></iconify-icon>
                                                        <small class="">Add Note</small>
                                                    </div>
                                                @else
                                                    <small class="">No driver notes for this order.</small>
                                                @endcan
                                            @endif
                                        </div>
                                    </div>

                                    <div class="sm:border-l-0 md:border-l p-3 md:col-span-1">

                                        <div class="space-y-1">
                                            <h4 class="text-slate-600 dark:text-slate-200 text-xs font-normal">
                                                Zone
                                            </h4>
                                            <div class="text-sm font-medium text-slate-900 dark:text-white">
                                                {{ $order->zone->name }}

                                            </div>
                                        </div>

                                    </div>


                                    <div class="sm:border-l-0 md:border-l p-3 md:col-span-2">
                                        <div class="space-y-1">
                                            <h4 class="text-slate-600 dark:text-slate-200 text-xs font-normal">
                                                Address
                                            </h4>
                                            <div class="text-sm font-medium text-slate-900 dark:text-white">
                                                {{ $order->shipping_address }}&nbsp; @if ($order->location_url || $order->customer->location_url)
                                                    <a class="clickable-link" target="_blanck"
                                                        href="{{ $order->location_url ?? $order->customer->location_url }}"><iconify-icon
                                                            icon="mdi:location" width="1.2em"
                                                            height="1.2em"></iconify-icon>Location</a>
                                                @endif
                                            </div>
                                            @if ($order->customer_phone || $order->customer->phone)
                                                <div class="text-sm font-medium text-slate-900 dark:text-white">
                                                    {{ $order->customer_phone ?? $order->customer->phone }}&nbsp;
                                                    <a class="clickable-link" target="_blanck"
                                                        href="tel:{{ $order->customer_phone ?? $order->customer->phone }}"></a>
                                                </div>
                                            @endif

                                        </div>
                                    </div>


                                    <div class="sm:border-l-0 md:border-l p-3 md:col-span-3">
                                        <div
                                            class="bg-slate-50 dark:bg-slate-900 rounded p-2 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-5 flex-wrap">

                                            <div class="space-y-1">
                                                <h4 class="text-slate-600 dark:text-slate-200 text-xs font-normal">
                                                    Total Weight
                                                </h4>
                                                <div class=" font-medium text-slate-900 dark:text-white">
                                                    {{ number_format($order->total_weight / 1000, 2) }}
                                                    <small>KG</small>
                                                </div>
                                            </div>

                                            <div class="space-y-1">
                                                <h4 class="text-slate-600 dark:text-slate-200 text-xs font-normal">
                                                    Total Price
                                                </h4>
                                                <div class=" font-medium text-success-500 dark:text-white">
                                                    <b>{{ number_format($order->total_amount, 2) }}</b>
                                                    <small>EGP</small>
                                                </div>
                                            </div>

                                            <div class="space-y-1">
                                                <h4 class="text-slate-600 dark:text-slate-200 text-xs font-normal">
                                                    Price to Collect
                                                </h4>
                                                <div class=" font-medium text-success-500 dark:text-white">
                                                    <b>{{ number_format($order->remaining_to_pay, 2) }}</b>
                                                    <small>EGP</small>

                                                </div>
                                            </div>
                                        </div>


                                        @if (auth()->user()->is_driver)
                                            <div class="grid grid-cols-2 gap-2 mt-2">
                                                <div>
                                                    @if ($order->is_delivered)
                                                        <button wire:click='toggleIsDelivered({{ $order->id }})'
                                                            class="btn inline-flex justify-center btn-success block-btn btn-sm">
                                                            <span class="flex items-center">
                                                                <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2"
                                                                    icon="mdi:truck-check"></iconify-icon>
                                                                <span>Delivered</span>
                                                            </span>
                                                        </button>
                                                    @else
                                                        <button wire:click='toggleIsDelivered({{ $order->id }})'
                                                            class="btn inline-flex justify-center btn-secondary block-btn btn-sm">
                                                            <span class="flex items-center">
                                                                <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2"
                                                                    icon="mdi:truck-remove"></iconify-icon>
                                                                <span>Not Delivered</span>
                                                            </span>
                                                        </button>
                                                    @endif
                                                </div>

                                                <div class="dropdown relative">
                                                    <button
                                                        class="btn flex justify-center w-full btn-outline-dark items-center btn-sm"
                                                        type="button" id="blockDropdownMenuButton2"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        {{ $order->driver_payment_type ? ucwords(str_replace('_', ' ', $order->driver_payment_type)) : 'Not Paid' }}
                                                        <iconify-icon class="text-xl ltr:ml-2 rtl:mr-2"
                                                            icon="ic:round-keyboard-arrow-down"></iconify-icon>
                                                    </button>
                                                    <ul class="dropdown-menu min-w-full absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none"
                                                        style="">
                                                        <li wire:click='setDriverPaymentType({{ $order->id }})'
                                                            class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                                    dark:hover:text-white cursor-pointer">
                                                            None
                                                        </li>
                                                        @foreach ($PAYMENT_METHODS as $PAYMENT_METHOD)
                                                            <li wire:click='setDriverPaymentType({{ $order->id }},"{{ $PAYMENT_METHOD }}")'
                                                                class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                                    dark:hover:text-white cursor-pointer">
                                                                {{ ucwords(str_replace('_', ' ', $PAYMENT_METHOD)) }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        @else
                                            <div class="mt-2">
                                                @if ($order->is_delivered)
                                                    <span
                                                        class="badge bg-success-500 text-white capitalize">Delivered</span>
                                                @else
                                                    <span class="badge bg-secondary-500 text-white capitalize">Not
                                                        Delivered</span>
                                                @endif
                                                @if ($order->driver_payment_type)
                                                    <span class="badge bg-success-500 text-white capitalize">Paid:
                                                        {{ ucwords(str_replace('_', ' ', $order->driver_payment_type)) }}</span>
                                                @else
                                                    <span class="badge bg-secondary-500 text-white capitalize">Not
                                                        Paid</span>
                                                @endif
                                            </div>
                                        @endif





                                    </div>

                                </div>
                            </div>


                        @empty
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
                                        <a href="{{ url('/orders/inventory') }}"
                                            class="btn inline-flex justify-center mx-2 mt-3 btn-primary active btn-sm">View
                                            all orders</a>
                                    </div>
                                </div>
                            </div>
                        @endforelse



                        <div class="card dark active">
                            <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base menu-open">
                                <div
                                    class="bg-slate-50 dark:bg-slate-900 rounded p-4 mt-8 flex gap-5  overflow-x-auto no-wrap">
                                    <div class="space-y-1">
                                        <h4 class="text-slate-600 dark:text-slate-200 text-xs font-normal">
                                            Total Weight
                                        </h4>
                                        <div class="text-sm font-medium text-slate-900 dark:text-white">
                                            {{ number_format($orders->sum('total_weight') / 1000, 3) }}
                                            <small>KG</small>
                                        </div>
                                    </div>
                                    @if (!auth()->user()->is_driver)
                                        <div class="space-y-1">
                                            <h4 class="text-slate-600 dark:text-slate-200 text-xs font-normal">
                                                Total Zones
                                            </h4>
                                            <div class="text-sm font-medium text-slate-900 dark:text-white">
                                                {{ $totalZones }}
                                                <small>Zones</small>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="space-y-1">
                                        <h4 class="text-slate-600 dark:text-slate-200 text-xs font-normal">
                                            Price to Collect
                                        </h4>
                                        <div class="text-sm font-medium text-slate-900 dark:text-white">
                                            {{ number_format($orders->sum('remaining_to_pay'), 2) }}<small>&nbsp;EGP</small>
                                        </div>
                                    </div>

                                    @if (!auth()->user()->is_driver)
                                        <div class="space-y-1">
                                            <h4 class="text-slate-600 dark:text-slate-200 text-xs font-normal">
                                                Total Orders
                                            </h4>
                                            <div class="text-sm font-medium text-slate-900 dark:text-white">
                                                {{ $orders->count() }} <small>(
                                                    {{ $orders->sum(fn($order) => $order->products->sum('quantity')) }}
                                                    Items ) </small>
                                            </div>
                                        </div>
                                    @endif

                                    @foreach ($collectedFromPaymentTypes as $index => $priceCollected)
                                        @if ($priceCollected > 0)
                                            <div class="space-y-1">
                                                <h4 class="text-slate-600 dark:text-slate-200 text-xs font-normal">
                                                    {{ ucwords(str_replace('_', ' ', $index)) }} Collected
                                                </h4>
                                                <div class="text-sm font-medium text-slate-900 dark:text-white">
                                                    {{ number_format($priceCollected, 2) }}<small>&nbsp;EGP</small>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach



                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                                                @if ($ONE_DRIVERS->countOrders())
                                                    • {{ $ONE_DRIVERS->countOrders($deliveryDate) }} Orders
                                                @endif
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
                                    <input name="Edited_deliveryDate" id="Edited_deliveryDate" type="date"
                                        class="form-control w-full mt-2 @error('Edited_deliveryDate') !border-danger-500 @enderror"
                                        wire:model="Edited_deliveryDate" autocomplete="off">

                                </div>
                                @error('Edited_deliveryDate')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>

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

    @if ($editedOrderNoteSec)
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
                                Edit order note
                            </h3>
                            <button wire:click="closeEditOrderNote" type="button"
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
                                    <label for="editedOrderNote" class="form-label">Note</label>
                                    <textarea name="editedOrderNote" id="editedOrderNote"
                                        class="form-control w-full mt-2 @error('editedOrderNote') !border-danger-500 @enderror"
                                        wire:model="editedOrderNote" autocomplete="off" style="min-height: 200px"></textarea>

                                </div>
                                @error('editedOrderNote')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="EditOrderNote" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="EditOrderNote">Submit</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="EditOrderNote"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    @endif

    @if ($editedDriverNoteSec)
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
                                Edit driver note
                            </h3>
                            <button wire:click="closeEditDriverNote" type="button"
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
                                    <label for="editedDriverNote" class="form-label">Note</label>
                                    <textarea name="editedDriverNote" id="editedDriverNote"
                                        class="form-control w-full mt-2 @error('editedDriverNote') !border-danger-500 @enderror"
                                        wire:model="editedDriverNote" autocomplete="off" style="min-height: 200px"></textarea>

                                </div>
                                @error('editedDriverNote')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="EditDriverNote" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="EditDriverNote">Submit</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="EditDriverNote"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    @endif
</div>
