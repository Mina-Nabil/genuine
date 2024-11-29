<div>
    <div class="space-y-5 profile-page mx-auto" style="max-width: 1000px">

        <div class="flex justify-between">
            <h4>
                <b>{{ $customer->name }}</b><iconify-icon class="ml-3" style="position: absolute" wire:loading
                    wire:target="changeSection" icon="svg-spinners:180-ring"></iconify-icon>
            </h4>
            <div class="float-right grid-col-2">
                <button wire:click='showEditCustomerSection'
                    class="btn inline-flex justify-center btn-outline-light btn-sm">Edit
                    info</button>
            </div>
        </div>
        <div class="card-body flex flex-col col-span-2" wire:ignore>
            <div class="card-text h-full">
                <div>
                    <ul class="nav nav-tabs flex flex-col md:flex-row flex-wrap list-none border-b-0 pl-0"
                        id="tabs-tab" role="tablist">
                        <li class="nav-item" role="presentation" wire:click="changeSection('profile')">
                            <a href="#tabs-profile-withIcon"
                                class="nav-link w-full flex items-center font-medium text-sm font-Inter leading-tight capitalize border-x-0 border-t-0 border-b border-transparent px-4 pb-2 my-2 hover:border-transparent focus:border-transparent  @if ($section === 'profile') active @endif dark:text-slate-300"
                                id="tabs-profile-withIcon-tab" data-bs-toggle="pill"
                                data-bs-target="#tabs-profile-withIcon" role="tab"
                                aria-controls="tabs-profile-withIcon" aria-selected="false">
                                <iconify-icon class="mr-1" icon="heroicons-outline:user"></iconify-icon>
                                Profile</a>
                        </li>
                        <li class="nav-item" role="presentation" wire:click="changeSection('followups')">
                            <a href="#tabs-messages-withIcon"
                                class="nav-link w-full flex items-center font-medium text-sm font-Inter leading-tight capitalize border-x-0 border-t-0 border-b border-transparent px-4 pb-2 my-2 hover:border-transparent focus:border-transparent  @if ($section === 'followups') active @endif dark:text-slate-300"
                                id="tabs-messages-withIcon-tab" data-bs-toggle="pill"
                                data-bs-target="#tabs-messages-withIcon" role="tab"
                                aria-controls="tabs-messages-withIcon" aria-selected="false">
                                <iconify-icon class="mr-1" icon="icon-park-outline:cycle-arrow"></iconify-icon>
                                Follow Ups</a>
                        </li>
                        <li class="nav-item" role="presentation" wire:click="changeSection('pets')">
                            <a href="#tabs-messages-withIcon"
                                class="nav-link w-full flex items-center font-medium text-sm font-Inter leading-tight capitalize border-x-0 border-t-0 border-b border-transparent px-4 pb-2 my-2 hover:border-transparent focus:border-transparent  @if ($section === 'pets') active @endif dark:text-slate-300"
                                id="tabs-messages-withIcon-tab" data-bs-toggle="pill"
                                data-bs-target="#tabs-messages-withIcon" role="tab"
                                aria-controls="tabs-messages-withIcon" aria-selected="false">
                                <iconify-icon class="mr-1" icon="mingcute:car-line"></iconify-icon>
                                Pets</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-5 mb-5 text-wrap">

            @if ($section === 'profile')
                <div class="md:flex-1 rounded-md overlay min-w-\[var\(500px\)\] sm:col-span-2"
                    style="min-width: 400px;">


                    <div class="card-body flex flex-col justify-center bg-cover card p-4 ">
                        <div class="card-text flex flex-col justify-between  menu-open">
                            <p>
                                <b>Customer info</b>
                            </p>
                            <br>

                            <p><b>Contact Phone</b></p>
                            <a href="">{{ $customer->phone ?? 'N/A' }}</a>
                            <br>
                        </div>
                    </div>

                    <div class="card-body flex flex-col justify-center bg-cover card p-4 mt-4">
                        <div class="card-text flex flex-col justify-between  menu-open">
                            <p>
                                <b>Location info</b>
                            </p>
                            <br>

                            <p><b>Zone</b></p>
                            <p>{{ $customer->zone->name ?? 'N/A' }}</p>
                            <br>

                            <p><b>Location URL</b></p>
                            <a class="clickable-link" target="_blank"
                                href="{{ $customer->location_url ?? 'N/A' }}">{{ $customer->location_url ?? 'N/A' }}</a>
                            <br>

                            <p><b>Address</b></p>
                            <a href="">{{ $customer->address ?? 'N/A' }}</a>
                            <br>
                        </div>
                    </div>

                    <div class="card-body flex flex-col justify-center bg-cover card p-4 mt-4">
                        <div class="card-text flex flex-col justify-between  menu-open">
                            <p>
                                <b>Last Orders Placed</b>
                            </p>
                            <p wire:click='openSetWeightTarget' class="text-xs font-light text-slate-600 dark:text-slate-300 clickable-link">
                                Monthly weight target: <b>{{ $customer->monthly_weight_target/1000 }}<small>KG</small></b>
                            </p>
                            <br>
                            <div class="card mb-5">

                                <div class="card-body flex flex-col justify-between border rounded-lg h-full menu-open p-0 mb-5 "
                                    style="border-color:rgb(224, 224, 224);">
                                    @forelse ($orders as $order)
                                        <div class="p-3">
                                            <div class="flex justify-between">

                                                <div>
                                                    <div
                                                        class="text-slate-600 pb-2 dark:text-slate-300 overflow-hidden text-ellipsis whitespace-nowrap flex">
                                                        <h6 class="clickable-link mr-3">
                                                            <a href="{{ route('orders.show', $order->id) }}">
                                                                #{{ $order->order_number }}
                                                            </a>
                                                        </h6>

                                                        <div class="mr-2">
                                                            @if ($order->status === App\Models\Orders\Order::STATUS_NEW || $order->status === App\Models\Orders\Order::STATUS_READY)
                                                                <span
                                                                    class="badge bg-info-500 text-dark-500 bg-opacity-50 capitalize">
                                                                    <iconify-icon icon="octicon:dot-16" width="1.2em"
                                                                        height="1.2em"></iconify-icon>
                                                                    {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                                                </span>
                                                            @elseif ($order->status === App\Models\Orders\Order::STATUS_IN_DELIVERY)
                                                                <span
                                                                    class="badge bg-warning-500 text-dark-500 bg-opacity-50 capitalize">
                                                                    <iconify-icon icon="octicon:dot-16" width="1.2em"
                                                                        height="1.2em"></iconify-icon>
                                                                    {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                                                </span>
                                                            @elseif (
                                                                $order->status === App\Models\Orders\Order::STATUS_RETURNED ||
                                                                    $order->status === App\Models\Orders\Order::STATUS_CANCELLED)
                                                                <span
                                                                    class="badge bg-secondary-500 text-dark-500 bg-opacity-50 capitalize">
                                                                    <iconify-icon icon="icon-park-outline:dot"
                                                                        width="1.2em" height="1.2em"></iconify-icon>
                                                                    {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                                                </span>
                                                            @elseif ($order->status === App\Models\Orders\Order::STATUS_DONE)
                                                                <span
                                                                    class="badge bg-success-500 text-dark-500 bg-opacity-50 capitalize">
                                                                    <iconify-icon icon="icon-park-outline:dot"
                                                                        width="1.2em" height="1.2em"></iconify-icon>
                                                                    {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                                                </span>
                                                            @else
                                                                <span
                                                                    class="badge bg-secondary-500 text-dark-500 bg-opacity-50 capitalize">
                                                                    <iconify-icon icon="octicon:dot-16" width="1.2em"
                                                                        height="1.2em"></iconify-icon>
                                                                    {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                                                </span>
                                                            @endif
                                                        </div>

                                                        <div class="flex justify-between">



                                                            @if ($order->is_paid)
                                                                <span
                                                                    class="badge bg-success-500 text-dark-500 bg-opacity-50 capitalize">
                                                                    <iconify-icon icon="icon-park-outline:dot"
                                                                        width="1.2em" height="1.2em"></iconify-icon>
                                                                    Paid
                                                                </span>
                                                            @else
                                                                <div class="flex justify-between mb-2">
                                                                    <div>
                                                                        <span
                                                                            class="badge bg-warning-500 text-dark-500 bg-opacity-50 capitalize">
                                                                            <iconify-icon icon="octicon:dot-16"
                                                                                width="1.2em"
                                                                                height="1.2em"></iconify-icon>
                                                                            Payment pending
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="flex text-sm">
                                                    <p class="ml-3  text-slate-900 dark:text-white">
                                                        {{ number_format($order->total_amount, 2) }}<small>&nbsp;EGP</small>
                                                    </p>
                                                </div>

                                            </div>
                                            <div class=" flex text-sm justify-between">
                                                <span>Delivery date:
                                                    {{ $order->delivery_date->isToday() ? 'Today' : ($order->delivery_date->isYesterday() ? 'Yesterday' : $order->delivery_date->format('l Y-m-d')) }}</span>

                                            </div>
                                        </div>
                                        @if (!$loop->last)
                                            <hr>
                                        @endif
                                    @empty
                                        <div
                                            class="text-slate-600 dark:text-slate-300 overflow-hidden text-ellipsis whitespace-nowrap text-center p-5">
                                            <p class="text-xs">No orders placed for this customer.</p>

                                        </div>
                                    @endforelse

                                </div>
                            </div>
                        </div>
                    </div>



                </div>

                <div>
                    <div class="card no-wrap">
                        <div class="card-body px-6 p-6">
                            <div class="flex justify-between">
                                <div>
                                    <span class="text-xs text-slate-500 dark:text-slate-400 block mb-1">
                                        Balance
                                    </span>
                                    <span class="text-lg font-medium text-slate-900 dark:text-white block">
                                        <small
                                            class="text-light">EGP</small>{{ number_format($customer->balance, 2) }}
                                    </span>
                                </div>
                                @can('updateCustomerBalance', $customer)
                                    <div>
                                        <button wire:click='openAddToBalanceSection' type="button"
                                            class="btn btn-dark btn-sm">
                                            Add Balance
                                        </button>
                                    </div>
                                @endcan
                            </div>
                        </div>
                    </div>


                    <div class="card no-wrap mt-5">
                        <header class="card-header noborder mb-6">
                            <h4 class="card-title">All Payments
                            </h4>
                        </header>
                        <div class="card-body px-6 pb-6">
                            <div class="overflow-x-auto -mx-6 ">
                                <span class=" col-span-8  hidden"></span>
                                <span class="  col-span-4 hidden"></span>
                                <div class="inline-block min-w-full align-middle">
                                    <div class="overflow-hidden ">
                                        <table
                                            class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                            <thead class=" border-t border-slate-100 dark:border-slate-800">
                                                <tr>

                                                    <th scope="col" class=" table-th ">
                                                        Date
                                                    </th>

                                                    <th scope="col" class=" table-th ">
                                                        Method
                                                    </th>

                                                    <th scope="col" class=" table-th ">
                                                        Amount
                                                    </th>

                                                </tr>
                                            </thead>
                                            <tbody
                                                class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">

                                                @forelse ($customer->payments as $payment)
                                                    <tr>
                                                        <td class="table-td ">
                                                            {{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}
                                                            <span
                                                                class="block text-slate-500 text-xs">{{ $payment->createdBy->full_name }}</span>
                                                        </td>
                                                        <td class="table-td ">
                                                            <div class="min-w-[100px]">
                                                                <span class="text-slate-500 dark:text-slate-400">
                                                                    <span
                                                                        class="block text-slate-600 dark:text-slate-300">{{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}</span>
                                                                    @if ($payment->order)
                                                                        <a
                                                                            href="{{ route('orders.show', $payment->order->id) }}">
                                                                            <span
                                                                                class="block text-slate-500 text-xs clickable-link">Order:
                                                                                #{{ $payment->order->order_number }}</span>
                                                                        </a>
                                                                    @endif

                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="table-td ">

                                                            <div class=" text-success-500">
                                                                +<small>EGP</small> {{ $payment->amount }}
                                                            </div>

                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td class="table-td " colspan="3">
                                                            <p class="text-center text-slate-500 p-5">
                                                                No payments for this customer.
                                                            </p>
                                                        </td>
                                                    </tr>
                                                @endforelse

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card no-wrap mt-5">
                        <header class="card-header noborder mb-6">
                            <h4 class="card-title">Balance Transactions
                            </h4>
                        </header>
                        <div class="card-body px-6 pb-6">
                            <div class="overflow-x-auto -mx-6 ">
                                <span class=" col-span-8  hidden"></span>
                                <span class="  col-span-4 hidden"></span>
                                <div class="inline-block min-w-full align-middle">
                                    <div class="overflow-hidden ">
                                        <table
                                            class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                            <thead class=" border-t border-slate-100 dark:border-slate-800">
                                                <tr>

                                                    <th scope="col" class=" table-th ">
                                                        Date
                                                    </th>

                                                    <th scope="col" class=" table-th ">
                                                        Method
                                                    </th>

                                                    <th scope="col" class=" table-th ">
                                                        Amount
                                                    </th>

                                                </tr>
                                            </thead>
                                            <tbody
                                                class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">

                                                @forelse ($customer->transactions as $transaction)
                                                    <tr>
                                                        <td class="table-td ">
                                                            {{ \Carbon\Carbon::parse($transaction->payment_date)->format('Y-m-d') }}
                                                            <span
                                                                class="block text-slate-500 text-xs">{{ $transaction->createdBy->full_name }}</span>
                                                        </td>
                                                        <td class="table-td ">
                                                            <div class="min-w-[100px]">
                                                                <span class="text-slate-500 dark:text-slate-400">
                                                                    <span
                                                                        class="block text-slate-600 dark:text-slate-300">{{ $transaction->description }}</span>
                                                                    @if ($transaction->order)
                                                                        <a
                                                                            href="{{ route('orders.show', $transaction->order->id) }}">
                                                                            <span
                                                                                class="block text-slate-500 text-xs clickable-link">Order:
                                                                                #{{ $transaction->order->order_number }}</span>
                                                                        </a>
                                                                    @endif

                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="table-td ">

                                                            <div class=" text-success-500">
                                                                <small>EGP</small> {{ $transaction->amount }}
                                                            </div>

                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td class="table-td " colspan="3">
                                                            <p class="text-center text-slate-500 p-5">
                                                                No balance transactions for this customer.
                                                            </p>
                                                        </td>
                                                    </tr>
                                                @endforelse

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="md:flex-1 rounded-md overlay min-w-[310px] sm:col-span-2">

                    {{-- note section --}}
                    {{-- <div
                        class="card-body flex flex-col justify-center  bg-no-repeat bg-center bg-cover card p-4 active">
                        <div class="card-text flex flex-col justify-between h-full menu-open">
                            <p>
                                <b>Note</b>
                                <span class="float-right cursor-pointer text-slate-500" wire:click="openEditNote">
                                    <iconify-icon icon="material-symbols:edit-outline"></iconify-icon>
                                </span>
                            </p>
                            <p class="text-wrap">{{ $customer->name }}</p>
                        </div>
                    </div> --}}

                </div>
            @endif

            @if ($section === 'followups')
                <div class="card-body flex flex-col justify-center bg-cover card p-4 mt-5  col-span-2">
                    <div class="card-text flex flex-col justify-between  menu-open">
                        <p>
                            <b>Followups</b>
                        </p>
                        <br>

                        @if ($customer->followups->isEmpty())
                            <p class="text-center m-5 text-primary">No Followups for this customer.</p>
                        @else
                            @foreach ($customer->followups as $followup)
                                <div class="flex items-center ">
                                    <b class="mr-auto">{{ ucfirst($followup->title) }}</b>

                                    <div class="ml-auto">
                                        <div class="relative flex">
                                            <span
                                                class="badge bg-slate-900 text-slate-900 dark:text-slate-200 bg-opacity-30 capitalize ml-auto h-auto">{{ $followup->status }}</span>

                                            <div class="dropdown relative">
                                                <button class="text-xl text-center block w-full " type="button"
                                                    id="tableDropdownMenuButton1" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <iconify-icon
                                                        icon="heroicons-outline:dots-vertical"></iconify-icon>
                                                </button>
                                                <ul
                                                    class=" dropdown-menu min-w-[120px] absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">

                                                    @if ($followup->status === 'new')
                                                        <li>
                                                            <button wire:click="editThisFollowup({{ $followup->id }})"
                                                                class="text-slate-600 dark:text-white block font-Inter font-normal px-4  w-full text-left py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white">
                                                                Edit</button>
                                                        </li>
                                                        <li>
                                                            <button
                                                                wire:click="toggleCallerNote('called',{{ $followup->id }})"
                                                                class="text-slate-600 dark:text-white block font-Inter font-normal px-4  w-full text-left py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white">
                                                                Set as called</button>
                                                        </li>
                                                        <li>
                                                            <button
                                                                wire:click="toggleCallerNote('cancelled',{{ $followup->id }})"
                                                                class="text-slate-600 dark:text-white block font-Inter font-normal px-4  w-full text-left py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white">
                                                                Set as cancelled</button>
                                                        </li>
                                                    @endif
                                                    <li>
                                                        <button wire:click="deleteThisFollowup({{ $followup->id }})"
                                                            class="text-slate-600 dark:text-white block font-Inter text-left font-normal w-full px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white">
                                                            Delete</button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <p><b>Desc:</b> {{ $followup->desc }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 text-right">
                                    {{ $followup->call_time }}</p>
                                <br>
                            @endforeach
                        @endif
                    </div>


                    <button wire:click="OpenAddFollowupSection"
                        class="btn inline-flex justify-center btn-light rounded-[25px] btn-sm float-right">Add
                        Followup</button>
                </div>
            @endif

            @if ($section === 'pets')
                <div class="md:flex-1 rounded-md overlay md:col-span-2" style="min-width: 400px;">
                    <div class="flex-1 rounded-md col-span-2">
                        <div
                            class="card-body flex flex-col justify-center  bg-no-repeat bg-center bg-cover card p-4 active">
                            <div class="card-text flex flex-col justify-between h-full menu-open">
                                <p class="mb-2">
                                    <b>Owned Pets ({{ $customer->pets->count() }})</b>

                                </p>

                                @if ($customer->pets->isEmpty())
                                    <p class="text-center m-5 text-primary">No pets added to this customer.</p>
                                @else
                                    @foreach ($customer->pets as $pet)
                                        <div class="card-body flex flex-col justify-between border rounded-lg h-full menu-open p-0 mb-5"
                                            style="border-color:rgb(224, 224, 224)">
                                            <div class="break-words flex items-center my-1 m-4">

                                                <h3 class="text-base capitalize py-3">
                                                    {{ $pet->name }}
                                                </h3>

                                                <div class="ml-auto">
                                                    <div class="relative">
                                                        <div class="dropdown relative">
                                                            <button class="text-xl text-center block w-full "
                                                                type="button" data-bs-toggle="dropdown"
                                                                aria-expanded="false">
                                                                <iconify-icon
                                                                    icon="heroicons-outline:dots-vertical"></iconify-icon>
                                                            </button>
                                                            <ul
                                                                class=" dropdown-menu min-w-[120px] absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">
                                                                <li>
                                                                <li>
                                                                    <button
                                                                        wire:click="removePet({{ $pet->id }})"
                                                                        class="text-slate-600 dark:text-white block font-Inter text-left font-normal w-full px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white">
                                                                        Remove</button>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <hr><br>
                                            <div class="grid grid-cols-2 mb-4">
                                                <div class="border-r ml-5">
                                                    <p><b>Category</b></p>
                                                    <p class=" flex items-center">
                                                        @if ($pet->category === \App\Models\Pets\Pet::CATEGORY_DOG)
                                                            <iconify-icon icon="mdi:dog" width="1.2em"
                                                                height="1.2em" class="mr-1"></iconify-icon>
                                                        @elseif($pet->category === \App\Models\Pets\Pet::CATEGORY_CAT)
                                                            <iconify-icon icon="mdi:cat" width="1.2em"
                                                                height="1.2em" class="mr-1"></iconify-icon>
                                                        @endif
                                                        {{ ucwords($pet->category) }}
                                                    </p>
                                                </div>
                                                <div class="ml-5">
                                                    <p class="mr-2"><b>Age </b></p>
                                                    <p>
                                                        @php
                                                            $years = \Carbon\Carbon::parse($pet->bdate)
                                                                ->diff(\Carbon\Carbon::now())
                                                                ->format('%y');
                                                            $months = \Carbon\Carbon::parse($pet->bdate)
                                                                ->diff(\Carbon\Carbon::now())
                                                                ->format('%m');
                                                            $days = \Carbon\Carbon::parse($pet->bdate)
                                                                ->diff(\Carbon\Carbon::now())
                                                                ->format('%d');
                                                        @endphp

                                                        @if ($years > 0)
                                                            <span
                                                                class="text-success-500"><b>{{ $years }}</b></span>
                                                            YEAR
                                                        @endif

                                                        @if ($months > 0)
                                                            <span
                                                                class="text-success-500"><b>{{ $months }}</b></span>
                                                            MONTH
                                                        @endif

                                                        @if ($days > 0)
                                                            <span
                                                                class="text-success-500"><b>{{ $days }}</b></span>
                                                            DAY
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                                <button wire:click="showAddPetsSection"
                                    class="btn inline-flex justify-center btn-light rounded-[25px] btn-sm float-right">Add
                                    pet</button>

                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>


    @can('assign', App\Models\Pets\Pet::class)
        @if ($isOpenAddPetsSec)
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
                                    Add pet
                                </h3>
                                <button wire:click="closeAddPetsSection" type="button"
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
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                                        <div class="input-area">
                                            <label for="phone" class="form-label">Category*</label>
                                            <select name="petCategory" id="petCategory"
                                                class="form-control w-full mt-2 @error('petCategory') !border-danger-500 @enderror"
                                                wire:model.live="petCategory" autocomplete="off">
                                                @foreach ($PET_CATEGORIES as $PET_CATEGORY)
                                                    <option value="{{ $PET_CATEGORY }}">
                                                        {{ ucwords(str_replace('_', ' ', $PET_CATEGORY)) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="input-area">
                                            <label for="petType" class="form-label">Type*</label>
                                            <input id="petType" list="petTypes" placeholder="Type to search..."
                                                class="form-control @error('petType') !border-danger-500 @enderror"
                                                wire:model="petType">

                                            <datalist id="petTypes">
                                                @foreach ($PET_TYPES as $PET_TYPE)
                                                    <option value="{{ ucwords($PET_TYPE) }}">
                                                @endforeach
                                            </datalist>

                                        </div>
                                    </div>
                                    @error('petCategory')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror

                                    @error('petType')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="from-group">
                                    <div class="input-area">
                                        <label for="petBdate" class="form-label">Birth Date*</label>
                                        <input id="petBdate" type="date"
                                            class="form-control @error('petBdate') !border-danger-500 @enderror"
                                            wire:model="petBdate" autocomplete="off">
                                    </div>
                                    @error('petBdate')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="from-group">
                                    <div class="input-area">
                                        <label for="petName" class="form-label">Name</label>
                                        <input id="petName" type="text"
                                            class="form-control @error('petName') !border-danger-500 @enderror"
                                            wire:model="petName" autocomplete="off">
                                    </div>
                                    @error('petName')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="from-group">
                                    <div class="input-area">
                                        <label for="petNote" class="form-label">Note</label>
                                        <textarea id="petNote" class="form-control @error('petNote') !border-danger-500 @enderror" wire:model="petNote"></textarea>
                                    </div>
                                    @error('petNote')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>


                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                <button wire:click="addPet" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="addPet">Submit</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="addPet"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endcan

    @can('update', $customer)
        @if ($EditCustomerSection)
            <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
                style="display: block" style="z-index: 999999999999999;" tabindex="-1"
                aria-labelledby="vertically_center" aria-modal="true" role="dialog" style="display: block;">
                <div class="modal-dialog relative w-auto pointer-events-none">
                    <div
                        class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                        <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                                <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                    Edit Customer
                                </h3>
                                <button wire:click="closeEditCustomerSection" type="button"
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
                                        <label for="fullName" class="form-label">Full name</label>
                                        <input id="fullName" type="text"
                                            class="form-control @error('fullName') !border-danger-500 @enderror"
                                            wire:model.lazy="fullName" autocomplete="off">
                                    </div>
                                    @error('fullName')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="from-group">
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                                        <div class="input-area">
                                            <label for="phone" class="form-label">Phone</label>
                                            <input id="phone" type="text"
                                                class="form-control @error('phone') !border-danger-500 @enderror"
                                                wire:model="phone" autocomplete="off">
                                        </div>
                                        <div class="input-area">
                                            <label for="zone_id" class="form-label">Zone</label>
                                            <select name="zone_id" id="zone_id"
                                                class="form-control w-full mt-2 @error('zone_id') !border-danger-500 @enderror"
                                                wire:model="zone_id" autocomplete="off">
                                                <option value="">None</option>
                                                @foreach ($ZONES as $SINGLE_ZONE)
                                                    <option value="{{ $SINGLE_ZONE->id }}">
                                                        {{ ucwords(str_replace('_', ' ', $SINGLE_ZONE->name)) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @error('phone')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror

                                    @error('zone')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>


                                <div class="from-group">
                                    <div class="input-area">
                                        <label for="locationUrl" class="form-label">Location URL</label>
                                        <input id="locationUrl" type="text"
                                            class="form-control @error('locationUrl') !border-danger-500 @enderror"
                                            wire:model="locationUrl" autocomplete="off">
                                    </div>
                                    @error('locationUrl')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="from-group">
                                    <div class="input-area">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea id="address" type="text" class="form-control @error('address') !border-danger-500 @enderror"
                                            wire:model="address" autocomplete="off"></textarea>
                                    </div>
                                    @error('address')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                <button wire:click="editCustomer" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="editCustomer">Submit</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="editCustomer"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endcan

    @can('updateCustomerBalance', $customer)
        @if ($isOpenAddToBalance)
            <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
                style="display: block" style="z-index: 999999999999999;" tabindex="-1"
                aria-labelledby="vertically_center" aria-modal="true" role="dialog" style="display: block;">
                <div class="modal-dialog relative w-auto pointer-events-none">
                    <div
                        class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                        <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                                <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                    Add Balance Payment
                                </h3>
                                <button wire:click="closeAddToBalanceSection" type="button"
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
                                        <label for="AddedAmount" class="form-label">Amount*</label>
                                        <input id="AddedAmount" type="number" min="0"
                                            class="form-control @error('AddedAmount') !border-danger-500 @enderror"
                                            wire:model="AddedAmount" autocomplete="off">
                                    </div>
                                    @error('AddedAmount')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="from-group">

                                    <div class="checkbox-area">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input wire:model.live='AddedIsNowPaymentDate' type="checkbox" class="hidden"
                                                name="checkbox" checked="checked">
                                            <span
                                                class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                <img src="{{ asset('assets/images/icon/ck-white.svg') }}" alt=""
                                                    class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                            <span class="text-slate-500 dark:text-slate-400 text-sm leading-6">is paid
                                                today ?</span>
                                        </label>
                                    </div>
                                    @if (!$AddedIsNowPaymentDate)
                                        <div class="input-area mt-3">
                                            <label for="AddedPaymentDate" class="form-label">Payment Date*</label>
                                            <input id="AddedPaymentDate" type="date"
                                                class="form-control @error('AddedPaymentDate') !border-danger-500 @enderror"
                                                wire:model="AddedPaymentDate" autocomplete="off">
                                        </div>
                                    @endif

                                </div>


                                <div class="from-group">
                                    <div class="input-area">
                                        <label for="AddedPaymentMethod" class="form-label">Payment method*</label>
                                        <select name="AddedPaymentMethod" id="AddedPaymentMethod"
                                            class="form-control w-full mt-2 @error('AddedPaymentMethod') !border-danger-500 @enderror"
                                            wire:model="AddedPaymentMethod" autocomplete="off">
                                            <option value="">Select method</option>
                                            @foreach ($PAYMENT_METHODS as $PAYMENT_METHOD)
                                                <option value="{{ $PAYMENT_METHOD }}">
                                                    {{ ucwords(str_replace('_', ' ', $PAYMENT_METHOD)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('AddedPaymentMethod')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="from-group">
                                    <div class="input-area">
                                        <label for="AddedPaymentNote" class="form-label">Note</label>
                                        <textarea id="AddedPaymentNote" type="text"
                                            class="form-control @error('AddedPaymentNote') !border-danger-500 @enderror" wire:model="AddedPaymentNote"
                                            autocomplete="off"></textarea>
                                    </div>
                                    @error('AddedPaymentNote')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                <button wire:click="addToBalance" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="addToBalance">Submit</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="addToBalance"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endcan

    @if ($deleteFollowupId)
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
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-danger-500">
                            <h3 class="text-base font-medium text-white dark:text-white capitalize">
                                Delete Followup
                            </h3>
                            <button wire:click="dismissDeleteFollowup" type="button"
                                class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center
                                            dark:hover:bg-slate-600 dark:hover:text-white"
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
                            <h6 class="text-base text-slate-900 dark:text-white leading-6">
                                Are you sure ! you Want to delete this followup ?
                            </h6>
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                            <button wire:click="deleteFollowup" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-danger-500">Yes, Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($addFollowupSection)
        {{-- add address section --}}
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog"
            style="display: block;">
            <div class="modal-dialog top-1/2 !-translate-y-1/2 relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Add Follow up
                            </h3>
                            <button wire:click="closeFollowupSection" type="button"
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
                                    <label for="firstName" class="form-label">Title</label>
                                    <input id="lastName" type="text"
                                        class="form-control @error('followupTitle') !border-danger-500 @enderror"
                                        wire:model.defer="followupTitle">
                                </div>
                                @error('followupTitle')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 mt-3">
                                    <div class="input-area">
                                        <label for="firstName" class="form-label">Call Date</label>
                                        <input id="lastName" type="date"
                                            class="form-control @error('followupCallDate') !border-danger-500 @enderror"
                                            wire:model.defer="followupCallDate">
                                    </div>
                                    @error('followupCallDate')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                    <div class="input-area">
                                        <label for="firstName" class="form-label"> Time</label>
                                        <input id="lastName" type="time"
                                            class="form-control @error('followupCallTime') !border-danger-500 @enderror"
                                            wire:model.defer="followupCallTime">
                                    </div>
                                    @error('followupCallTime')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="input-area mt-3">
                                    <label for="firstName" class="form-label">Description</label>
                                    <input id="lastName" type="text"
                                        class="form-control @error('followupDesc') !border-danger-500 @enderror"
                                        wire:model.defer="followupDesc">
                                </div>
                                @error('followupDesc')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror

                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                            <button wire:click="addFollowup" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                Submit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($followupId)
        {{-- add address section --}}
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog"
            style="display: block;">
            <div class="modal-dialog top-1/2 !-translate-y-1/2 relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Edit Follow up
                            </h3>
                            <button wire:click="closeEditFollowup" type="button"
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
                                    <label for="firstName" class="form-label">Title</label>
                                    <input id="lastName" type="text"
                                        class="form-control @error('followupTitle') !border-danger-500 @enderror"
                                        wire:model.defer="followupTitle">
                                </div>
                                @error('followupTitle')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 mt-3">
                                    <div class="input-area">
                                        <label for="firstName" class="form-label">Call Date</label>
                                        <input id="lastName" type="date"
                                            class="form-control @error('followupCallDate') !border-danger-500 @enderror"
                                            wire:model.defer="followupCallDate">
                                    </div>
                                    @error('followupCallDate')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                    <div class="input-area">
                                        <label for="firstName" class="form-label"> Time</label>
                                        <input id="lastName" type="time"
                                            class="form-control @error('followupCallTime') !border-danger-500 @enderror"
                                            wire:model.defer="followupCallTime">
                                    </div>
                                    @error('followupCallTime')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="input-area mt-3">
                                    <label for="firstName" class="form-label">Description</label>
                                    <input id="lastName" type="text"
                                        class="form-control @error('followupDesc') !border-danger-500 @enderror"
                                        wire:model.defer="followupDesc">
                                </div>
                                @error('followupDesc')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror

                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                            <button wire:click="editFollowup" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                Submit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($callerNoteSec)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog"
            style="display: block;">
            <div class="modal-dialog top-1/2 !-translate-y-1/2 relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Caller Note
                            </h3>
                            <button wire:click="toggleCallerNote" type="button"
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
                                    <label for="firstName" class="form-label">Leave a note...</label>
                                    <input id="lastName" type="text"
                                        class="form-control @error('followupTitle') !border-danger-500 @enderror"
                                        wire:model.defer="note">
                                </div>
                                @error('note')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror

                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                            <button wire:click="submitCallerNote" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                Submit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($isOpenSetWeightTarget)
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
                                Monthly Weight Target
                            </h3>
                            <button wire:click="closeSetWeightTarget" type="button"
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
                                    <label for="monthlyWeightTarget" class="form-label">Target Weight (KG)</label>
                                    <input id="monthlyWeightTarget" type="number" min="1"
                                        class="form-control @error('monthlyWeightTarget') !border-danger-500 @enderror"
                                        wire:model="monthlyWeightTarget">
                                </div>
                                @error('monthlyWeightTarget')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror

                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="setWeightTarget" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="setWeightTarget">Submit</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="setWeightTarget"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
