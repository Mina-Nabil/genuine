<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Orders • Inventory
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
                    <li wire:click='openFilteryDriver'
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                        Driver
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

    <div class="mb-5">
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
    </div>

    <div>
        <div class="md:flex-1 rounded-md overlay md:col-span-2 mb-5" style="min-width: 400px;">
            <div class="flex-1 rounded-md col-span-2">
                <div class="card-body flex flex-col justify-center  bg-no-repeat bg-center bg-cover card p-4 active">
                    <div class="card-text flex flex-col justify-between h-full menu-open">
                        <span class="text-lg"></span><b>Assigned Drivers on @foreach ($deliveryDate as $sDdate)
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
                        </b>
                        @foreach ($todayShifts as $d)
                            <div class="basicRadio">
                                <label class="flex items-center cursor-pointer">
                                    <input wire:model.live="driverRadio" type="radio" class="hidden" name="driverRadios"
                                        value="{{ $d->id }}" @checked($driver?->id === $d->id)>
                                    <span
                                        class="flex-none bg-white dark:bg-slate-500 rounded-full border inline-flex ltr:mr-2 rtl:ml-2 relative transition-all
                                        duration-150 h-[16px] w-[16px] border-slate-400 dark:border-slate-600 dark:ring-slate-700"></span>
                                    <span @class([
                                        'text-sm',
                                        'leading-6',
                                        'capitalize',
                                        'text-secondary-500' => $driver?->id != $d->id,
                                        'text-primary-500' => $driver?->id == $d->id,
                                    ])>
                                        {{ ucwords($d->user->full_name) }} • {{ $d->shift_title }}
                                    </span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (!$cancelledOrders->isEmpty())
        <div>
            <div class="md:flex-1 rounded-md overlay md:col-span-2" style="min-width: 400px;">
                <div class="flex-1 rounded-md col-span-2">
                    <div
                        class="card-body flex flex-col justify-center  bg-no-repeat bg-center bg-cover card p-4 active">
                        <div class="card-text flex flex-col justify-between h-full menu-open">
                            <p class="text-danger-500 mb-5">
                                <span class="text-lg"></span><b>Cancelled Orders</b><br>
                                <span class="text-xs">* You can restore the ready products from cancelled orders to
                                    remove these orders from the list.</span>
                            </p>
                            @foreach ($cancelledOrders as $order)
                                <div class="card-body flex flex-col justify-between border rounded-lg h-full menu-open p-0 mb-5"
                                    style="border-color:rgb(224, 224, 224)">
                                    <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-4">

                                        <div class="p-3">
                                            <div class="flex">
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
                                                <a href="{{ route('orders.show', $order->id) }}"> <span
                                                        class="hover-underline">
                                                        <b>
                                                            #{{ $order->order_number }} • {{ $order->customer->name }}
                                                        </b>
                                                    </span>
                                                </a>
                                            </div>

                                            <div class="mt-2 flex justify-between">
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
                                                        class="badge bg-secondary-500 text-dark-500 bg-opacity-50 capitalize   btn-outline-danger"
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
                                                <div class="flex items-center text-xs">
                                                    @if ($order->driver)
                                                        <iconify-icon icon="healthicons:truck-driver" width="15"
                                                            height="15"></iconify-icon>
                                                    @endif
                                                    <b>&nbsp;{{ $order->driver?->user->full_name }}</b>
                                                </div>


                                            </div>
                                            @if ($order->status === App\Models\Orders\Order::STATUS_READY)
                                                <button wire:click='setAsInDelivery({{ $order->id }})'
                                                    class="btn inline-flex justify-center btn-primary block-btn mt-2 btn-sm">
                                                    <span class="flex items-center">
                                                        <span>Set as in delivery</span>
                                                    </span>
                                                </button>
                                            @endif




                                        </div>

                                        <div class="border-l p-3 md:col-span-2">

                                            <table
                                                class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                                <tbody class="bg-white dark:bg-slate-800 no-wrap">
                                                    @foreach ($order->products as $orderProduct)
                                                        <tr>
                                                            <td class=" ">
                                                                <div class="flex">
                                                                    <div class="checkbox-area">
                                                                        <label
                                                                            class="inline-flex items-center cursor-pointer">
                                                                            <input type="checkbox"
                                                                                wire:model.live="selectedOrderProducts"
                                                                                value="{{ $orderProduct->id }}"
                                                                                class="hidden" id="select-all">
                                                                            <span
                                                                                class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                                                <img src="{{ asset('assets/images/icon/ck-white.svg') }}"
                                                                                    alt=""
                                                                                    class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                                                        </label>
                                                                    </div>

                                                                    <div
                                                                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xs:mb-2 sm:mb-0">
                                                                        <a
                                                                            href="{{ route('product.show', $orderProduct->product->id) }}">
                                                                            <span class="hover-underline">
                                                                                <div class="text-start overflow-hidden text-ellipsis whitespace-nowrap"
                                                                                    style="max-width: 150px;">
                                                                                    <div
                                                                                        class="text-sm text-slate-600 dark:text-slate-300 overflow-hidden text-ellipsis whitespace-nowrap">
                                                                                        <b>
                                                                                            {{ $orderProduct->product->name }}
                                                                                        </b>
                                                                                    </div>
                                                                                </div>

                                                                            </span>
                                                                        </a>
                                                                        <p class="card-text mx-2">

                                                                            <small>{{ $orderProduct->quantity }} x
                                                                                @if ($orderProduct->product->weight < 1000)
                                                                                    {{ $orderProduct->product->weight }}gm
                                                                                @else
                                                                                    {{ $orderProduct->product->weight / 1000 }}kg
                                                                                @endif

                                                                            </small>

                                                                            {{-- <small>KG</small> --}}
                                                                        </p>
                                                                    </div>

                                                                </div>
                                                            </td>


                                                            @can('update', $order)
                                                                @if ($order->status !== App\Models\Orders\Order::STATUS_READY)
                                                                    <td class="float-right ml-5">
                                                                        @if ($orderProduct->is_ready)
                                                                            <button
                                                                                wire:click='toggleDeletedReady({{ $orderProduct->id }})'
                                                                                class="btn inline-flex justify-center btn-outline-secondary btn-sm"
                                                                                style="padding-top: 3px;padding-bottom: 3px">
                                                                                Click to Restore
                                                                            </button>
                                                                        @else
                                                                            <button
                                                                                wire:click='toggleDeletedReady({{ $orderProduct->id }})'
                                                                                class="btn inline-flex justify-center btn-outline-success btn-sm"
                                                                                style="padding-top: 3px;padding-bottom: 3px">
                                                                                Restored
                                                                            </button>
                                                                        @endif
                                                                    </td>
                                                                @endif
                                                            @endcan

                                                            <td class="float-right">
                                                                <p class="card-text">
                                                                    {{ number_format($orderProduct->quantity * ($orderProduct->product->weight / 1000), 3) }}
                                                                    <small>KG</small>
                                                                </p>
                                                            </td>


                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>


                                        </div>

                                        <div class="border-l p-3">
                                            <div
                                                class="bg-slate-50 dark:bg-slate-900 rounded p-2 flex justify-between flex-wrap">
                                                <div class="space-y-1">
                                                    <h4 class="text-slate-600 dark:text-slate-200 text-xs font-normal">
                                                        Total Weight
                                                    </h4>
                                                    <div class="text-sm font-medium text-slate-900 dark:text-white">
                                                        {{ number_format($order->total_weight / 1000, 2) }}
                                                        <small>KG</small>
                                                    </div>
                                                </div>
                                                <div class="space-y-1">
                                                    <h4 class="text-slate-600 dark:text-slate-200 text-xs font-normal">
                                                        Total Quantity
                                                    </h4>
                                                    <div class="text-sm font-medium text-slate-900 dark:text-white">
                                                        {{ $order->total_items }}
                                                        <small>Product{{ $order->total_items > 1 ? 's' : '' }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <hr>
        </div>
    @endif

    <div>
        <div class="md:flex-1 rounded-md overlay md:col-span-2" style="min-width: 400px;">
            <div class="flex-1 rounded-md col-span-2">
                <div class="card-body flex flex-col justify-center  bg-no-repeat bg-center bg-cover card p-4 active">
                    <div class="card-text flex flex-col justify-between h-full menu-open">

                        <p class="mb-5">
                            <span class="text-lg"></span><b>Active Orders</b><br>
                            <span class="text-xs">* Mark the products as ready to prepare them for processing.</span>
                        </p>

                        @forelse ($orders as $order)
                            <div class="card-body flex flex-col justify-between border rounded-lg h-full menu-open p-0 mb-5"
                                style="border-color:rgb(224, 224, 224)">
                                <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-4">

                                    <div class="p-3">
                                        <div class="flex">
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
                                            <a href="{{ route('orders.show', $order->id) }}"> <span
                                                    class="hover-underline">
                                                    <b>
                                                        #{{ $order->order_number }} • {{ $order->customer->name }}
                                                    </b>
                                                </span>
                                            </a>
                                        </div>

                                        <div class="mt-2 flex justify-between">
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
                                            <div class="flex items-center text-xs">
                                                @if ($order->driver)
                                                    <iconify-icon icon="healthicons:truck-driver" width="15"
                                                        height="15"></iconify-icon>
                                                @endif
                                                <b>&nbsp;{{ $order->driver?->user->full_name }}</b>
                                            </div>


                                        </div>
                                        @if ($order->status === App\Models\Orders\Order::STATUS_READY)
                                            <div class="flex gap-2">
                                                <button wire:click='setAsInDelivery({{ $order->id }})'
                                                    class="btn inline-flex justify-center btn-primary block-btn mt-2 btn-sm">
                                                    <span class="flex items-center">
                                                        <span>Set as in delivery</span>
                                                    </span>
                                                </button>
                                                <button wire:click='resetStatus({{ $order->id }})'
                                                    class="btn inline-flex justify-center btn-outline-warning block-btn mt-2 btn-sm">
                                                    <span class="flex items-center">
                                                        <span>Reset Status</span>
                                                    </span>
                                                </button>
                                            </div>
                                        @endif




                                    </div>

                                    <div class="border-l p-3 md:col-span-2">

                                        <table
                                            class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                            <tbody class="bg-white dark:bg-slate-800 no-wrap">
                                                @can('update', $order)
                                                    @if ($order->status === App\Models\Orders\Order::STATUS_NEW)
                                                        <button wire:click='setAllReady({{ $order->id }})'
                                                            class="btn inline-flex justify-center btn-outline-success btn-sm"
                                                            style="padding-top: 3px;padding-bottom: 3px">
                                                            All Ready
                                                        </button>
                                                    @endif
                                                @endcan

                                                @foreach ($order->products as $orderProduct)
                                                    <tr>
                                                        <td class=" ">
                                                            <div class="flex">
                                                                <div class="checkbox-area">
                                                                    <label
                                                                        class="inline-flex items-center cursor-pointer">
                                                                        <input type="checkbox"
                                                                            wire:model.live="selectedOrderProducts"
                                                                            value="{{ $orderProduct->id }}"
                                                                            class="hidden" id="select-all">
                                                                        <span
                                                                            class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                                            <img src="{{ asset('assets/images/icon/ck-white.svg') }}"
                                                                                alt=""
                                                                                class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                                                    </label>
                                                                </div>

                                                                <div
                                                                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xs:mb-2 sm:mb-0">
                                                                    <a
                                                                        href="{{ route('product.show', $orderProduct->product->id) }}">
                                                                        <span class="hover-underline">
                                                                            <div class="text-start overflow-hidden text-ellipsis whitespace-nowrap"
                                                                                style="max-width: 150px;">
                                                                                <div
                                                                                    class="text-sm text-slate-600 dark:text-slate-300 overflow-hidden text-ellipsis whitespace-nowrap">
                                                                                    <b>
                                                                                        {{ $orderProduct->product->name }}
                                                                                    </b>
                                                                                </div>
                                                                            </div>

                                                                        </span>
                                                                    </a>
                                                                    <p class="card-text mx-2">

                                                                        <small>{{ $orderProduct->quantity }} x
                                                                            @if ($orderProduct->product->weight < 1000)
                                                                                {{ $orderProduct->product->weight }}gm
                                                                            @else
                                                                                {{ $orderProduct->product->weight / 1000 }}kg
                                                                            @endif

                                                                        </small>

                                                                        {{-- <small>KG</small> --}}
                                                                    </p>
                                                                </div>

                                                            </div>
                                                        </td>


                                                        @can('update', $order)
                                                            @if ($order->status === App\Models\Orders\Order::STATUS_NEW)
                                                                <td class="float-right ml-5">
                                                                    @if ($orderProduct->is_ready)
                                                                        <button
                                                                            wire:click='toggleReady({{ $orderProduct->id }})'
                                                                            class="btn inline-flex justify-center btn-outline-success btn-sm"
                                                                            style="padding-top: 3px;padding-bottom: 3px">
                                                                            Ready
                                                                        </button>
                                                                    @else
                                                                        @if ($orderProduct->product->inventory->on_hand - $orderProduct->quantity < 0)
                                                                            <button
                                                                                class="btn inline-flex justify-center btn-outline-danger btn-sm"
                                                                                style="padding-top: 3px;padding-bottom: 3px"
                                                                                disabled>
                                                                                Out of stock
                                                                            </button>
                                                                        @else
                                                                            <button
                                                                                wire:click='toggleReady({{ $orderProduct->id }})'
                                                                                class="btn inline-flex justify-center btn-outline-secondary btn-sm"
                                                                                style="padding-top: 3px;padding-bottom: 3px">
                                                                                Not Ready
                                                                            </button>
                                                                        @endif
                                                                    @endif
                                                                </td>
                                                            @endif
                                                        @endcan

                                                        <td class="float-right">
                                                            <p class="card-text">
                                                                {{ number_format($orderProduct->quantity * ($orderProduct->product->weight / 1000), 3) }}
                                                                <small>KG</small>
                                                            </p>
                                                        </td>


                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>


                                    </div>

                                    <div class="border-l p-3">
                                        <div
                                            class="bg-slate-50 dark:bg-slate-900 rounded p-2 flex justify-between flex-wrap">
                                            <div class="space-y-1">
                                                <h4 class="text-slate-600 dark:text-slate-200 text-xs font-normal">
                                                    No of Bags
                                                </h4>
                                                <div class="text-sm font-medium text-slate-900 dark:text-white">
                                                    <input wire:model.live='noOfBags.{{ $order->id }}' wire:change='updateNoOfBags({{ $order->id }})' id="smallInput" type="number" style="width: 65px;" class="form-control !py-1 !text-xs">
                                                </div>
                                            </div>
                                            <div class="space-y-1">
                                                <h4 class="text-slate-600 dark:text-slate-200 text-xs font-normal">
                                                    Total Quantity
                                                </h4>
                                                <div class="text-sm font-medium text-slate-900 dark:text-white">
                                                    {{ $order->total_items }}
                                                    <small>Product{{ $order->total_items > 1 ? 's' : '' }}</small>
                                                </div>
                                            </div>
                                            <div class="space-y-1">
                                                <h4 class="text-slate-600 dark:text-slate-200 text-xs font-normal">
                                                    Total Price
                                                </h4>
                                                <div class="text-sm font-medium text-slate-900 dark:text-white">
                                                    {{ number_format($order->total_amount, 2) }} <small>EGP</small>

                                                </div>
                                            </div>
                                        </div>
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
                                <div class="bg-slate-50 dark:bg-slate-900 rounded p-4 mt-8 flex gap-5 flex-wrap">
                                    <div class="space-y-1">
                                        <h4 class="text-slate-600 dark:text-slate-200 text-xs font-normal">
                                            Total Weight
                                        </h4>
                                        <div class="text-sm font-medium text-slate-900 dark:text-white">
                                            {{ number_format($orders->sum('total_weight') / 1000, 3) }}
                                            <small>KG</small>
                                        </div>
                                    </div>
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

                                    @if (count($selectedDeliveryDates) > 1)
                                        &nbsp;&nbsp;<iconify-icon wire:click="removeSelectedDate({{ $index }})"
                                            icon="material-symbols:close" class="cursor-pointer" width="1.2em"
                                            height="1.2em"></iconify-icon>
                                    @endif
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

    @if ($resetStatusOrderID)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="dangerModalLabel" aria-modal="true" role="dialog"
            style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding
                                rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-warning-500">
                            <h3 class="text-base font-medium dark:text-white capitalize">
                                Reset order status
                            </h3>
                            <button wire:click="dismissResetStatus" type="button"
                                class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center
                                            dark:hover:bg-slate-600 dark:hover:text-white"
                                data-bs-dismiss="modal">
                                <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10
                                                    11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="p-6 space-y-4">
                            <h6 class="text-base text-slate-900 dark:text-white leading-6">
                                Are you sure ! you Want to reset this Order status ?
                            </h6>
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                            <button wire:click="confirmResetStatus" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center bg-warning-500">Yes,
                                Reset</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
