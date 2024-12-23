<div>
    <div class="space-y-5 profile-page mx-auto" style="max-width: 1000px">
        <div class="flex justify-between">
            <div>
                <h5 class=" text-slate-900 dark:text-whiteh5">
                    <b>#{{ $order->order_number }}</b>
                    @if ($order->is_paid)
                        <span class="badge bg-success-500 text-dark-500 bg-opacity-50 capitalize">
                            <iconify-icon icon="icon-park-outline:dot" width="1.2em" height="1.2em"></iconify-icon>
                            Paid
                        </span>
                    @else
                        <span class="badge bg-warning-500 text-dark-500 bg-opacity-50 capitalize">
                            <iconify-icon icon="octicon:dot-16" width="1.2em" height="1.2em"></iconify-icon>
                            Payment pending
                        </span>
                    @endif
                    @if ($order->is_confirmed)
                        <span class="badge bg-success-500 text-white capitalize rounded-3xl">
                            <iconify-icon icon="line-md:check-all" width="1.2em" height="1.2em"></iconify-icon>&nbsp;
                            Confirmed</span>
                    @endif
                    @if ($order->periodic_option)
                        <span class="badge bg-black-500 text-slate-100 bg-opacity-50 capitalize">
                            <iconify-icon icon="grommet-icons:cycle" width="1.2em" height="1.2em"></iconify-icon>
                            &nbsp;{{ ucwords(str_replace('_', ' ', $order->periodic_option)) }}
                        </span>
                    @endif
                </h5>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Created
                    {{ $order->created_at->format('F j, Y \a\t g:i a') }}</p>

                @if ($order->is_whatsapp_sent)
                    <span
                        class="badge bg-success-500 text-success-500 bg-opacity-30 capitalize rounded-3xl"><iconify-icon
                            icon="logos:whatsapp-icon" width="14.89" height="15"></iconify-icon>&nbsp;
                        WhatsApp Message Sent &nbsp;<iconify-icon class="cursor-pointer"
                            wire:click='toggleConfirmRemoveWAmsg' icon="material-symbols:close" width="15"
                            height="15"></iconify-icon>
                    </span>
                @endif
            </div>


            @can('update', $order)
                <div class="dropdown relative">
                    <button class="btn inline-flex justify-center btn-dark items-center btn-sm" type="button"
                        id="darkDropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        More actions
                        <iconify-icon class="text-xl ltr:ml-2 rtl:mr-2" icon="ic:round-keyboard-arrow-down"></iconify-icon>
                    </button>
                    <ul
                        class=" dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow
                                        z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">
                        <li wire:click='openReturnsSection'
                            class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                            dark:hover:text-white cursor-pointer">
                            Return
                        </li>
                        @if ($order->is_new)
                            <li wire:click='openAddProductsSec'
                                class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                            dark:hover:text-white cursor-pointer">
                                Add Products
                            </li>
                            <li wire:click='openCombosSection'
                                class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                            dark:hover:text-white cursor-pointer">
                                Add Combo
                            </li>
                        @endif
                        @if (!$order->is_confirmed)
                            <li wire:click='toggleConfirmation'
                                class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                            dark:hover:text-white cursor-pointer">
                                Set as Confirmed
                            </li>
                        @else
                            <li wire:click='toggleConfirmation'
                                class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                            dark:hover:text-white cursor-pointer">
                                Set as not Confirmed
                            </li>
                        @endif
                        @if ($order->status === 'ready' || $order->status === 'in_delivery')
                            <li wire:click='resetStatus'
                                class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                            dark:hover:text-white cursor-pointer">
                                Reset Status
                            </li>
                        @endif
                        @if ($order->driver)
                            <a wire:click='sendWhatsappMessage' href="{{ $order->generateWhatsAppMessage() }}"
                                target="_blanck">
                                <li
                                    class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                            dark:hover:text-white cursor-pointer">
                                    Send Whatsapp Message
                                </li>
                            </a>
                        @endif
                        @foreach ($NextStatuses as $NextStatus)
                            <li wire:click="setStatus('{{ $NextStatus }}')"
                                class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                            dark:hover:text-white cursor-pointer">
                                Set as {{ ucwords(str_replace('_', ' ', $NextStatus)) }}
                            </li>
                        @endforeach
                        @can('delete', $order)
                            <li wire:click='toggleDelete'
                                class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                            dark:hover:text-white cursor-pointer">
                                Delete Order
                            </li>
                        @endcan
                    </ul>
                </div>
            @endcan
        </div>

        <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-6 md:gap-5 mb-5 text-wrap">
            <div class="col-span-4">

                @if (!$order->products->isEmpty())
                    <div class="card mb-5">
                        <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base">
                            <div class="items-center p-5">
                                @if ($order->status === App\Models\Orders\Order::STATUS_NEW)
                                    <span class="badge bg-info-500 text-dark-500 bg-opacity-50 capitalize">
                                        <iconify-icon icon="octicon:dot-16" width="1.2em"
                                            height="1.2em"></iconify-icon>
                                        {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                    </span>
                                @elseif ($order->status === App\Models\Orders\Order::STATUS_READY)
                                    <span class="badge bg-[#EAE5FF] text-dark-500 capitalize">
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
                                <div class="card-body flex flex-col justify-between border rounded-lg h-full menu-open p-0 mb-5 "
                                    style="border-color:rgb(224, 224, 224);">
                                    @foreach ($order->products as $orderProduct)
                                        <div class="p-3">
                                            <div class="flex justify-between">

                                                <div>
                                                    <h6
                                                        class="text-slate-600 pb-2 dark:text-slate-300 overflow-hidden text-ellipsis whitespace-nowrap">
                                                        {{ $orderProduct->product?->name }}
                                                    </h6>
                                                </div>
                                                <div class="flex text-sm">
                                                    <p class="mr-3  text-slate-900 dark:text-white">
                                                        {{ $orderProduct->price }}<small>&nbsp;EGP</small></p>
                                                    <p class="ml-3 mr-3  text-slate-900 dark:text-white">x
                                                        {{ $orderProduct->quantity }}</p>
                                                    <p class="ml-3  text-slate-900 dark:text-white">
                                                        {{ number_format($orderProduct->price * $orderProduct->quantity, 2) }}<small>&nbsp;EGP</small>
                                                    </p>
                                                </div>

                                            </div>
                                            <div class=" flex text-sm gap-5">
                                                <span
                                                    class="badge bg-secondary-500 bg-opacity-30 text-slate-900 dark:text-white rounded-3xl">Weight:
                                                    {{ $orderProduct->product?->weight }} gm</span>

                                                @if ($orderProduct->combo)
                                                    <span
                                                        class="badge bg-secondary-500 bg-opacity-30 text-slate-900 dark:text-white rounded-3xl">
                                                        {{ $orderProduct->combo->name }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        @if (!$loop->last)
                                            <hr>
                                        @endif
                                    @endforeach

                                </div>

                            </div>
                        </div>
                    </div>
                @endif

                @if (!$order->removedProducts->isEmpty())
                    <div class="card mb-5">
                        <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base">
                            <div class="items-center p-5">
                                <span class="badge bg-danger-500 text-dark-500 bg-opacity-30 capitalize">
                                    <iconify-icon icon="clarity:remove-solid" width="1.2em"
                                        height="1.2em"></iconify-icon>&nbsp;
                                    Removed ({{ $order->removedProducts->count() }})
                                </span>
                                <div class="card-body flex flex-col justify-between border rounded-lg h-full menu-open p-0 mb-5  overflow-x-auto no-wrap"
                                    style="border-color:rgb(224, 224, 224);">
                                    @foreach ($order->removedProducts as $removedProduct)
                                        <div class="p-3">
                                            <div class="flex justify-between">

                                                <div>
                                                    <h6
                                                        class="text-slate-600  pb-2 dark:text-slate-300 overflow-hidden text-ellipsis whitespace-nowrap">
                                                        {{ $removedProduct->product->name }}
                                                    </h6>
                                                </div>
                                                <div class="flex text-sm">
                                                    <p class="mr-3  text-slate-900 dark:text-white">
                                                        {{ $removedProduct->price }}<small>&nbsp;EGP</small></p>
                                                    <p class="ml-3 mr-3  text-slate-900 dark:text-white">x
                                                        {{ $removedProduct->quantity }}</p>
                                                    <p class="ml-3  text-slate-900 dark:text-white">
                                                        {{ number_format($removedProduct->price * $removedProduct->quantity, 2) }}<small>&nbsp;EGP</small>
                                                    </p>
                                                </div>

                                            </div>
                                            <div class=" flex text-sm justify-between">
                                                <p class="text-xs">
                                                    {{ $removedProduct->reason ?? 'No reason set for this return.' }}
                                                </p>

                                            </div>
                                        </div>
                                        @if (!$loop->last)
                                            <hr>
                                        @endif
                                    @endforeach

                                </div>

                            </div>
                        </div>
                    </div>
                @endif

                <div class="card mb-5">
                    <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base">
                        <div class="items-center p-5">
                            @if ($order->is_paid)
                                <span class="badge bg-success-500 text-dark-500 bg-opacity-50 capitalize">
                                    <iconify-icon icon="icon-park-outline:dot" width="1.2em"
                                        height="1.2em"></iconify-icon>
                                    Paid
                                </span>
                            @else
                                <div class="flex justify-between mb-2">
                                    <div>
                                        <span class="badge bg-warning-500 text-dark-500 bg-opacity-50 capitalize">
                                            <iconify-icon icon="octicon:dot-16" width="1.2em"
                                                height="1.2em"></iconify-icon>
                                            Payment pending
                                        </span>
                                    </div>
                                    @if ($order->isOpenToPay())
                                        <div class="flex justify-between mb-2">
                                            @if ($order->remaining_to_pay && $order->customer->balance > 0)
                                                <button wire:click='openPayFromBalance'
                                                    class="btn inline-flex justify-center btn-outline-light btn-sm">
                                                    Pay from balance
                                                </button>
                                            @endif
                                            @if ($order->remaining_to_pay > 0)
                                                <div class="relative mt-2 ml-5">
                                                    <div class="dropdown relative">
                                                        <button class="text-xl text-center block w-full "
                                                            type="button" id="tableDropdownMenuButton1"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            <iconify-icon
                                                                icon="heroicons-outline:dots-vertical"></iconify-icon>
                                                        </button>
                                                        <ul class=" dropdown-menu min-w-[120px] absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none"
                                                            style="min-width: 180px">
                                                            @foreach ($PAYMENT_METHODS as $PAYMENT_METHOD)
                                                                <li wire:click="confirmPayOrder('{{ $PAYMENT_METHOD }}')"
                                                                    class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:text-white hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                                                                    Pay
                                                                    {{ ucwords(str_replace('_', ' ', $PAYMENT_METHOD)) }}
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <div class="card-body flex flex-col justify-between border rounded-lg h-full menu-open p-0 mb-5 p-2 px-6 overflow-x-auto no-wrap"
                                style="border-color:rgb(224, 224, 224);">

                                <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                    <tbody class="bg-white dark:bg-slate-800 ">

                                        <tr>
                                            <td class=" text-xs text-slate-500 dark:text-slate-400">Subtotal</td>
                                            <td
                                                class="hidden md:table-cell text-xs text-slate-500 dark:text-slate-400">
                                                {{ $order->total_items . ' items' }}</td>
                                            <td class="float-right text-dark">
                                                <b>{{ $order->total_items_price ? number_format($order->total_items_price, 2) : '-' }}<small>&nbsp;EGP</small></b>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class=" text-xs text-slate-500 dark:text-slate-400">Shipping &
                                                Delivery</td>
                                            <td
                                                class="hidden md:table-cell text-xs text-slate-500 dark:text-slate-400">
                                                {{ ucwords($order->zone->name) }}</td>
                                            <td class="float-right text-dark">
                                                <b>{{ $order->delivery_amount ? number_format($order->delivery_amount, 2) : 'Free' }}<small>&nbsp;EGP</small></b>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class=" text-xs text-slate-500 dark:text-slate-400">
                                                @if ($order->discount_amount != 0)
                                                    Discount &nbsp;
                                                    <span class="clickable-link" wire:click='openDiscountSection'>
                                                        edit
                                                    </span>
                                                @else
                                                    <span class="clickable-link" wire:click='openDiscountSection'>
                                                        Add Discount
                                                    </span>
                                                @endif

                                            </td>
                                            <td
                                                class="hidden md:table-cell text-xs text-slate-500 dark:text-slate-400">
                                            </td>
                                            <td class="float-right text-dark">
                                                <b>{{ $order->discount_amount != 0 ? '-' . number_format($order->discount_amount, 2) : '-' }}<small>&nbsp;EGP</small></b>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class=" text-xs text-slate-500 dark:text-slate-400"></td>
                                            <td
                                                class="hidden md:table-cell text-xs text-slate-500 dark:text-slate-400">
                                            </td>
                                            <td class="float-right text-dark"></td>
                                        </tr>
                                        <tr>
                                            <td class=" text-xs text-slate-500 dark:text-slate-400"></td>
                                            <td
                                                class="hidden md:table-cell text-xs text-slate-500 dark:text-slate-400">
                                            </td>
                                            <td class="float-right text-dark"></td>
                                        </tr>

                                        <tr class="!pt-5">
                                            <td class=" text-xs text-slate-500 dark:text-slate-400">Total</td>
                                            <td
                                                class="hidden md:table-cell text-xs text-slate-500 dark:text-slate-400">
                                            </td>
                                            <td class="float-right text-dark" style="color: black">
                                                <b>{{ $order->total_amount ? number_format($order->total_amount, 2) : '-' }}<small>&nbsp;EGP</small></b>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>

                            </div>

                            @if ($order->isPartlyPaid())
                                <div class="card-body flex flex-col justify-between border rounded-lg h-full menu-open p-0 mb-5 p-2 px-6 overflow-x-auto no-wrap"
                                    style="border-color:rgb(224, 224, 224);">
                                    <table
                                        class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                        <tbody class="bg-white dark:bg-slate-800 ">
                                            <tr>
                                                <td class=" text-xs text-slate-500 dark:text-slate-400">Remaining to
                                                    pay
                                                </td>
                                                <td
                                                    class="hidden md:table-cell text-xs text-slate-500 dark:text-slate-400">
                                                </td>
                                                <td class="float-right text-dark">
                                                    <b>{{ number_format($order->remaining_to_pay, 2) }}<small>&nbsp;EGP</small></b>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if (!$order->balanceTransactions->isEmpty() || !$order->payments->isEmpty())
                    <div class="card no-wrap mb-5">
                        <div class="card-body px-6 pb-2">
                            <div class="overflow-x-auto -mx-6 ">
                                <span class=" col-span-8  hidden"></span>
                                <span class="  col-span-4 hidden"></span>
                                <div class="inline-block min-w-full align-middle">
                                    <div class="overflow-hidden ">
                                        <table
                                            class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                            <tbody
                                                class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                                @if (!$order->balanceTransactions->isEmpty())
                                                    <tr>
                                                        <td class="table-td  bg-slate-800" colspan="3">
                                                            <span
                                                                class="text-slate-100 flex items-center"><iconify-icon
                                                                    icon="material-symbols:currency-exchange"
                                                                    width="1.2em"
                                                                    height="1.2em"></iconify-icon>&nbsp; Balance
                                                                Transactions</span>

                                                        </td>
                                                    </tr>
                                                    @foreach ($order->balanceTransactions as $balanceTransaction)
                                                        <tr>
                                                            <td class="table-td ">
                                                                {{ \Carbon\Carbon::parse($balanceTransaction->payment_date)->format('l Y-m-d') }}
                                                                <span
                                                                    class="block text-slate-500 text-xs">{{ $balanceTransaction->description }}</span>
                                                            </td>
                                                            <td class="table-td ">

                                                                <div class=" text-success-500">
                                                                    {{ -$balanceTransaction->amount }}
                                                                    <small>EGP</small>
                                                                </div>

                                                            </td>
                                                            <td class="table-td ">
                                                                <span
                                                                    class="block text-slate-500 text-xs">{{ $balanceTransaction->createdBy->full_name }}</span>
                                                            </td>

                                                        </tr>
                                                    @endforeach
                                                @endif
                                                @if (!$order->payments->isEmpty())
                                                    <tr>
                                                        <td class="table-td  bg-slate-800" colspan="3">
                                                            <span
                                                                class="text-slate-100 flex items-center"><iconify-icon
                                                                    icon="material-symbols-light:payments-rounded"
                                                                    width="1.2em"
                                                                    height="1.2em"></iconify-icon>&nbsp;
                                                                Payments</span>

                                                        </td>
                                                    </tr>
                                                    @foreach ($order->payments as $payment)
                                                        <tr>
                                                            <td class="table-td ">
                                                                {{ \Carbon\Carbon::parse($payment->payment_date)->format('l Y-m-d') }}
                                                                <span
                                                                    class="block text-slate-500 text-xs">{{ $payment->note }}</span>
                                                            </td>
                                                            <td class="table-td ">

                                                                <div class=" text-success-500">
                                                                    {{ $payment->amount }} <small>EGP</small>
                                                                    <span
                                                                        class="block text-slate-500 text-xs">{{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}</span>
                                                                </div>

                                                            </td>
                                                            <td class="table-td ">
                                                                <span
                                                                    class="block text-slate-500 text-xs">{{ $payment->createdBy->full_name }}</span>
                                                            </td>

                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif


            </div>
            <div class="col-span-2">

                <div class="card">
                    <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base">
                        <div class="items-center p-5">
                            <div class="input-area w-full mb-5">
                                <div class="flex justify-bewwteen">
                                    <label for="phone" class="form-label"><b>Delivery date</b></label>
                                    <button wire:click='openUpdateDdate' class="action-btn" type="button">
                                        <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                                    </button>
                                </div>
                                <p class="text-xs">
                                    {{ $order->delivery_date ? $order->delivery_date->format('l, F j, Y') : 'No delivery date set for order' }}
                                </p>
                            </div>
                            <div class="input-area w-full">
                                <div class="flex justify-bewwteen">
                                    <label for="phone" class="form-label"><b>Notes</b></label>
                                    <button wire:click='openUpdateNote' class="action-btn" type="button">
                                        <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                                    </button>
                                </div>
                                <p class="text-xs">{{ $order->note ?? 'No notes for order' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-5">
                    <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base">
                        <div class="items-center p-5">
                            <div class="input-area w-full">
                                <label for="phone" class="form-label"><b>Customer</b></label>
                                <p class="text-xs">
                                    <a class="clickable-link"
                                        href="{{ route('customer.show', $order->customer->id) }}">
                                        {{ $order->customer->name }}
                                    </a>
                                </p>
                                <p class="text-xs mt-1">{{ $order->customer->total_orders }}
                                    order{{ $order->customer->total_orders > 1 ? 's' : '' }} @if ($order->customer->balance)
                                        - {{ $order->customer->balance }}EGP in balance
                                    @endif
                                </p>
                            </div>
                            <div class="flex justify-bewwteen mt-5">
                                <label for="phone" class="form-label"><b>Shipping Address</b></label>
                                <button wire:click='openUpdateShippingDetails' class="action-btn" type="button">
                                    <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                                </button>
                            </div>
                            <p class="text-xs">{{ $order->customer_name }}</p>
                            <a class="text-xs clickable-link"
                                href="tel:{{ $order->customer_phone }}">{{ $order->customer_phone }}</a>
                            <p class="text-xs">{{ $order->shipping_address }}</p>
                            <p class="text-xs mt-1">{{ $order->zone->name }}</p>
                        </div>
                    </div>
                </div>

                <div class="card mt-5">
                    <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base">
                        <div class="items-center p-5">
                            <div class="input-area w-full">
                                <div class="flex justify-between">
                                    <label for="phone" class="form-label"><b>Assigned Driver</b></label>
                                    @if ($order->in_house)
                                        <button wire:click='openSetDriverSection' class="action-btn" type="button">
                                            @if ($order->driver)
                                                <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                                            @else
                                                <iconify-icon icon="material-symbols:add" width="1.2em"
                                                    height="1.2em"></iconify-icon>
                                            @endif
                                        </button>
                                        @if ($order->driver)
                                            <button wire:click='showConfirmRemoveDriver' class="action-btn ml-2"
                                                type="button">
                                                <iconify-icon icon="material-symbols-light:delete-outline"
                                                    width="1.2em" height="1.2em"></iconify-icon>
                                            </button>
                                        @endif
                                    @endif
                                </div>

                                @if ($order->driver)
                                    <p class="text-xs">
                                        <a class="clickable-link"
                                            href="{{ route('profile', $order->driver->user_id) }}">
                                            {{ $order->driver->user->full_name }}
                                        </a>
                                        -> {{ $order->driver->shift_title }}
                                    </p>
                                    <a class="text-xs clickable-link"
                                        href="tel:{{ $order->driver->user->phone }} ">{{ $order->driver->user->phone }}
                                    </a>
                                @else
                                    <p class="text-xs text-center p-2">No Driver Assigned</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-6 gap-5 mb-5 text-wrap">
            <div class="col-span-4">
                <div class="mb-5">
                    <h3 class="card-title text-slate-900 dark:text-white">Timeline</h3>
                    <ol class="timeline">
                        <li class="timeline-item">
                            <span class="timeline-item-icon | avatar-icon">
                                <span
                                    class="block w-full h-full object-cover text-center leading-10 text-lg user-initial">
                                    {{ strtoupper(substr(Auth::user()->username, 0, 1)) }}
                                </span>
                            </span>
                            <div class="new-comment">
                                <input type="text" wire:model="addedComment" wire:keydown.enter="addComment"
                                    placeholder="Add a comment..." />
                            </div>
                        </li>

                        @forelse ($comments as $comment)
                            @if ($comment->level === 'info')
                                <li class="timeline-item">
                                    <span class="timeline-item-icon | faded-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24"
                                            height="24">
                                            <path fill="none" d="M0 0h24v24H0z" />
                                            <path fill="currentColor"
                                                d="M12.9 6.858l4.242 4.243L7.242 21H3v-4.243l9.9-9.9zm1.414-1.414l2.121-2.122a1 1 0 0 1 1.414 0l2.829 2.829a1 1 0 0 1 0 1.414l-2.122 2.121-4.242-4.242z" />
                                        </svg>
                                    </span>
                                    <div class="timeline-item-description">
                                        <span class="avatar | small">
                                            <span
                                                class="block w-full h-full object-cover text-center text-lg user-initial"
                                                style="font-size: 12px">
                                                {{ strtoupper(substr($comment->user?->username, 0, 1)) }}
                                            </span>
                                        </span>
                                        <span><a href="#">{{ $comment->user?->full_name }}</a>
                                            {{ $comment->title }}
                                            <time datetime="21-01-2021">
                                                @if ($comment->created_at->isToday())
                                                    Today {{ $comment->created_at->format('h:i A') }}
                                                @elseif($comment->created_at->isYesterday())
                                                    Yesterday {{ $comment->created_at->format('h:i A') }}
                                                @else
                                                    on {{ $comment->created_at->format('M d, Y') }}
                                                @endif
                                            </time></span>
                                    </div>
                                </li>
                            @elseif($comment->level === 'comment')
                                <li class="timeline-item">
                                    <span class="timeline-item-icon | filled-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24"
                                            height="24">
                                            <path fill="none" d="M0 0h24v24H0z" />
                                            <path fill="currentColor"
                                                d="M6.455 19L2 22.5V4a1 1 0 0 1 1-1h18a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H6.455zM7 10v2h2v-2H7zm4 0v2h2v-2h-2zm4 0v2h2v-2h-2z" />
                                        </svg>
                                    </span>

                                    <div class="timeline-item-wrapper w-full">
                                        <div class="timeline-item-description">
                                            <span class="avatar | small">
                                                <span
                                                    class="block w-full h-full object-cover text-center text-lg user-initial"
                                                    style="font-size: 12px">
                                                    {{ strtoupper(substr($comment->user?->username, 0, 1)) }}
                                                </span>
                                            </span>
                                            <span><a href="#">{{ $comment->user?->full_name }}</a> commented
                                                <time datetime="20-01-2021">
                                                    @if ($comment->created_at->isToday())
                                                        Today at {{ $comment->created_at->format('h:m') }}
                                                    @elseif($comment->created_at->isYesterday())
                                                        Yesterday at {{ $comment->created_at->format('h:m') }}
                                                    @else
                                                        on {{ $comment->created_at->format('M d, Y') }}
                                                    @endif
                                                </time>
                                            </span>
                                        </div>
                                        <div class="comment">
                                            <p>{{ $comment->title }}</p>
                                        </div>
                                    </div>

                                </li>
                            @endif
                        @empty
                            <li class="timeline-item">
                                <span class="timeline-item-icon | faded-icon">
                                    <iconify-icon icon="material-symbols:info-outline" width="1.2em"
                                        height="1.2em"></iconify-icon>
                                </span>
                                <div class="timeline-item-description">
                                    <span>No comments added yet.</span>
                                </div>
                            </li>
                        @endforelse
                    </ol>
                    <div class="flex justify-between">
                        @if ($visibleCommentsCount < $order->comments()->count())
                            <button wire:click="loadMore"><small class="clickable-link">See More</small></button>
                        @endif

                        @if ($visibleCommentsCount > 5)
                            <button wire:click="showLess"><small class="clickable-link">Show Less</small></button>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>

    @can('update', $order)
        @if ($updateShippingSec)
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
                                    Edit shipping address
                                </h3>
                                <button wire:click="closeUpdateShippingDetails" type="button"
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
                                            <label for="customerName" class="form-label">Customer Name*</label>
                                            <input id="customerName" type="text"
                                                class="form-control @error('customerName') !border-danger-500 @enderror"
                                                wire:model="customerName" autocomplete="off">
                                        </div>
                                        <div class="input-area">
                                            <label for="customerPhone" class="form-label">Phone*</label>
                                            <input id="customerPhone" type="text"
                                                class="form-control @error('customerPhone') !border-danger-500 @enderror"
                                                wire:model="customerPhone" autocomplete="off">
                                        </div>

                                    </div>
                                    @error('customerName')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                    @error('customerPhone')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="from-group">
                                    <div class="input-area">
                                        <label for="zoneId" class="form-label">Zone*</label>
                                        <select name="zoneId" id="zoneId"
                                            class="form-control w-full mt-2 @error('zoneId') !border-danger-500 @enderror"
                                            wire:model="zoneId" autocomplete="off">
                                            <option value="">Select Zone</option>
                                            @foreach ($zones as $zone)
                                                <option value="{{ $zone->id }}">
                                                    {{ ucwords($zone->name) }}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-gray-500">
                                            Note: Any changes to the zone may affect the shipping rate for this order and
                                            total amount.
                                        </small>
                                    </div>

                                    @error('zoneId')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="from-group">
                                    <div class="input-area">
                                        <label for="shippingAddress" class="form-label">Address*</label>
                                        <textarea id="shippingAddress" type="text"
                                            class="form-control @error('shippingAddress') !border-danger-500 @enderror" wire:model="shippingAddress"
                                            autocomplete="off"></textarea>
                                    </div>
                                    @error('shippingAddress')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>

                            <!-- Modal footer -->
                            <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                                <button wire:click="updateShippingDetails" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="updateShippingDetails">Submit</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="updateShippingDetails"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
        @endif
    @endcan

    @can('update', $order)
        @if ($updateNoteSec)
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
                                    Edit note
                                </h3>
                                <button wire:click="closeUpdateNote" type="button"
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
                                        <label for="note" class="form-label">Note</label>
                                        <textarea id="note" type="text" class="form-control @error('note') !border-danger-500 @enderror"
                                            wire:model="note" autocomplete="off"></textarea>
                                    </div>
                                    @error('note')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>

                            <!-- Modal footer -->
                            <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                                <button wire:click="updateNote" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="updateNote">Submit</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="updateNote"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
        @endif
    @endcan

    @can('pay', $order)
        @if ($isOpenPayFromBalanceSec)
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
                                    Warning
                                </h3>
                                <button wire:click="closePayFromBalance" type="button"
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

                                Are you sure you want to deduct
                                <b>{{ min(number_format($order->total_amount, 2), $order->customer->balance) }}<small>EGP
                                    </small> </b> from the customer's balance of
                                <b>{{ number_format($order->customer->balance, 2) }}<small>EGP </small></b>?
                                The remaining balance will be
                                <b>{{ number_format($order->customer->balance - min(number_format($order->total_amount, 2), $order->customer->balance), 2) }}<small>EGP
                                    </small> </b>

                            </div>

                            <!-- Modal footer -->
                            <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                                <button wire:click="PayFromBalance" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="PayFromBalance">Procceed Transaction</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="PayFromBalance"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
        @endif
    @endcan

    @if ($confirmRemoveDriver)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog"
            style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-danger-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Remove driver
                            </h3>
                            <button wire:click="hideConfirmRemoveDriver" type="button"
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

                            Are you sure you want to remove driver ?
                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="setDriver" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="setDriver">Confirm</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="setDriver"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    @endif

    @can('pay', $order)
        @if ($PAY_BY_PAYMENT_METHOD)
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
                                    Warning
                                </h3>
                                <button wire:click="closeConfirmPayOrder" type="button"
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

                                Are you sure you want to proceed with the payment of
                                <b>{{ number_format($order->remaining_to_pay, 2) }}</b><small>EGP</small> using the
                                <b>{{ ucwords(str_replace('_', ' ', $PAY_BY_PAYMENT_METHOD)) }}</b> method?
                            </div>

                            <!-- Modal footer -->
                            <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                                <button wire:click="PayOrder" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="PayOrder">Procceed Transaction</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="PayOrder"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
        @endif
    @endcan

    @can('update', $order)
        @if ($ddateSection)
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
                                    Edit delivery date
                                </h3>
                                <button wire:click="closeUpdateDdate" type="button"
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
                                        <label for="ddate" class="form-label">Delivery date</label>
                                        <input id="ddate" type="date"
                                            class="form-control @error('ddate') !border-danger-500 @enderror"
                                            wire:model="ddate" autocomplete="off">
                                    </div>
                                    @error('ddate')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>

                            <!-- Modal footer -->
                            <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                                <button wire:click="updateDeliveryDate" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="updateDeliveryDate">Submit</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="updateDeliveryDate"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
        @endif
    @endcan

    @can('returnProducts', $order)
        @if ($returnSection)
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
                                    Return products
                                </h3>
                                <button wire:click="closeReturnsSection" type="button"
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

                                <div class="input-area mb-5">
                                    <label for="reason" class="form-label">Return reason</label>
                                    <select name="reason" id="reason"
                                        class="form-control w-full @error('reason') !border-danger-500 @enderror"
                                        wire:model.live="reason" autocomplete="off">
                                        <option value="">None</option>
                                        @foreach ($removeReasons as $removeReasons)
                                            <option value="{{ $removeReasons }}">
                                                {{ $removeReasons }}</option>
                                        @endforeach
                                    </select>

                                    {{-- <small class="text-gray-500">
                                        Note: Any changes to the zone may affect the shipping rate for this order and
                                        total amount.
                                    </small> --}}
                                </div>
                                @if ($reason === 'Other')
                                    <div class="input-area">
                                        <label for="otherReason" class="form-label">Describe reason</label>
                                        <textarea id="otherReason" class="form-control @error('otherReason') !border-danger-500 @enderror"
                                            wire:model="otherReason" autocomplete="off"> </textarea>
                                    </div>
                                @endif

                                <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                    <thead class="border-t border-slate-100 dark:border-slate-800">
                                        <tr>
                                            <th scope="col" class="table-th imp-p-2">Product</th>
                                            <th scope="col" class="table-th imp-p-2">Return Quantity</th>
                                            <th scope="col" class="table-th imp-p-2">Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                        @foreach ($cancelledProducts as $index => $cancelledProduct)
                                            <tr>
                                                <!-- Product Name Column -->
                                                <td class="table-td imp-p-2">
                                                    <div class="flex-1 text-start">
                                                        <div class="text-start overflow-hidden text-ellipsis whitespace-nowrap"
                                                            style="max-width:200px;">
                                                            <h6
                                                                class="text-slate-600 dark:text-slate-300 overflow-hidden text-ellipsis whitespace-nowrap">
                                                                {{ $cancelledProduct['name'] }}
                                                            </h6>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- Price Input Column -->
                                                <td class="table-td imp-p-2">
                                                    <input type="number"
                                                        class="form-control @error('cancelledProducts.' . $index . '.return_quantity') !border-danger-500 @enderror"
                                                        style="max-width: 100px;"
                                                        wire:model.live='cancelledProducts.{{ $index }}.return_quantity'
                                                        min="0" max="{{ $cancelledProduct['quantity'] }}">
                                                </td>

                                                <!-- Quantity Input Column -->
                                                <td class="table-td imp-p-2">
                                                    / {{ $cancelledProduct['quantity'] }}
                                                </td>

                                                <td class="table-td imp-p-2">
                                                    <div class="checkbox-area">
                                                        <label class="inline-flex items-center cursor-pointer">
                                                            <input
                                                                wire:model='cancelledProducts.{{ $index }}.isReturnToStock'
                                                                type="checkbox" class="hidden" name="checkbox"
                                                                checked="checked">
                                                            <span
                                                                class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                                <img src="{{ asset('assets/images/icon/ck-white.svg') }}"
                                                                    alt=""
                                                                    class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                                            <span
                                                                class="text-slate-500 dark:text-slate-400 text-sm leading-6">
                                                                Return to stock ?</span>
                                                        </label>
                                                    </div>
                                                </td>

                                            </tr>
                                        @endforeach



                                    </tbody>
                                </table>

                                @if ($order->total_paid > 0)
                                    <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                        <tbody
                                            class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                            <tr>
                                                <td class="table-td  bg-slate-800" colspan="3">
                                                    <span class="text-slate-100 flex items-center"><iconify-icon
                                                            icon="material-symbols:currency-exchange" width="1.2em"
                                                            height="1.2em"></iconify-icon>&nbsp; Payments Changes</span>

                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="table-td ">
                                                    <b>{{ number_format($cancelledProductsTotalAmount, 2) }}</b><small>EGP</small>
                                                    <span class="block text-slate-500 text-xs">Amount of retunrs</span>
                                                </td>

                                                <td class="table-td ">
                                                    <b>{{ number_format($order->total_paid, 2) }}</b><small>EGP</small>
                                                    <span class="block text-slate-500 text-xs">Total Paid</span>
                                                </td>

                                                <td class="table-td ">
                                                    <b>{{ number_format(min($cancelledProductsTotalAmount, $order->total_paid), 2) }}</b><small>EGP</small>
                                                    <span class="block text-slate-500 text-xs">Return Amount</span>
                                                </td>

                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="input-area mb-5">
                                        <label for="returnPaymentMehod" class="form-label">Payment return method</label>
                                        <select name="returnPaymentMehod" id="returnPaymentMehod"
                                            class="form-control w-full @error('returnPaymentMehod') !border-danger-500 @enderror"
                                            wire:model.live="returnPaymentMehod" autocomplete="off">
                                            <option value="">Return to customer balance</option>
                                            @foreach ($PAYMENT_METHODS as $PAYMENT_METHOD)
                                                <option value="{{ $PAYMENT_METHOD }}">
                                                    Returned {{ ucwords(str_replace('_', ' ', $PAYMENT_METHOD)) }}</option>
                                            @endforeach

                                        </select>
                                    </div>

                                    <div class="checkbox-area">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input wire:model.live='isReturnShippingAmount' type="checkbox"
                                                class="hidden" name="checkbox" checked="checked">
                                            <span
                                                class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                <img src="{{ asset('assets/images/icon/ck-white.svg') }}" alt=""
                                                    class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                            <span class="text-slate-500 dark:text-slate-400 text-sm leading-6">
                                                Return Shipping Amount ?</span>
                                        </label>
                                    </div>
                                @endif
                            </div>

                            <!-- Modal footer -->
                            <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                                <button wire:click="returnProducts" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="returnProducts">Submit</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="returnProducts"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
        @endif
    @endcan

    @can('update', $order)
        @if ($addProductsSection)
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
                                    Add products
                                </h3>
                                <button wire:click="closeAddProductsSec" type="button"
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

                                <div class="input-area">
                                    <input type="text" placeholder="Search products..."
                                        class="form-control @error('searchAddProducts') !border-danger-500 @enderror"
                                        wire:model.live='searchAddProducts'>
                                </div>

                                <div class="overflow-x-auto -mx-6">
                                    <div class="inline-block min-w-full align-middle">
                                        <div class="overflow-hidden ">
                                            <table
                                                class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                                <tbody
                                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">

                                                    @foreach ($products as $product)
                                                        <tr wire:click='addProductRow({{ $product->id }})'
                                                            class="hover:bg-slate-200 dark:hover:bg-slate-700 cursor-pointer">
                                                            <td class="table-td">
                                                                <h6>
                                                                    <b>{{ $product->name }}</b>
                                                                </h6>
                                                            </td>
                                                            <td class="table-td">
                                                                {{ number_format($product->price, 2) }}<small>EGP</small>
                                                            </td>
                                                            <td class="table-td ">{{ $product->weight }}<small>gm</small>
                                                            </td>
                                                        </tr>
                                                    @endforeach

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                @if (!empty($productsToAdd))
                                    <div class="md:flex justify-between items-center mb-6">
                                        <h5>Added Products</h5>
                                        <span class="text-sm text-slate-600 dark:text-slate-300"></span>
                                    </div>

                                    <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                        <thead class="border-t border-slate-100 dark:border-slate-800">
                                            <tr>
                                                <th scope="col" class="table-th imp-p-2">Product</th>
                                                <th scope="col" class="table-th imp-p-2">Quantity</th>
                                                <th scope="col" class="table-th imp-p-2">Price/item</th>
                                            </tr>
                                        </thead>
                                        <tbody
                                            class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                            @foreach ($productsToAdd as $index => $productToAdd)
                                                <tr class="bg-success-100">
                                                    <!-- Product Name Column -->
                                                    <td class="table-td imp-p-2">
                                                        <div class="flex-1 text-start">
                                                            <div class="text-start overflow-hidden text-ellipsis whitespace-nowrap"
                                                                style="max-width:200px;">
                                                                <h6
                                                                    class="text-slate-600 dark:text-slate-300 overflow-hidden text-ellipsis whitespace-nowrap">
                                                                    {{ $productToAdd['name'] }}
                                                                </h6>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <!-- quantity -->
                                                    <td class="table-td imp-p-2">
                                                        <input type="number"
                                                            class="form-control @error('productsToAdd.' . $index . '.quantity') !border-danger-500 @enderror"
                                                            style="max-width: 100px;"
                                                            wire:model.live='productsToAdd.{{ $index }}.quantity'
                                                            min="1">
                                                    </td>

                                                    <!-- price/item -->
                                                    <td class="table-td imp-p-2">
                                                        <input type="number"
                                                            class="form-control @error('productsToAdd.' . $index . '.price') !border-danger-500 @enderror"
                                                            style="max-width: 100px;"
                                                            wire:model.live='productsToAdd.{{ $index }}.price'
                                                            min="1">
                                                    </td>

                                                    <td class="table-td imp-p-2">
                                                        <button class="action-btn" type="button"
                                                            wire:click='removeProductRow({{ $index }})'>
                                                            <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                        </button>
                                                    </td>

                                                </tr>
                                            @endforeach



                                        </tbody>
                                    </table>
                                @endif
                            </div>

                            <!-- Modal footer -->
                            <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                                <button wire:click="addProducts" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="addProducts">Submit</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="addProducts"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
        @endif
    @endcan

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
                                Set driver
                            </h3>
                            <button wire:click="closeAddProductsSec" type="button"
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

                            <div class="input-area">
                                <input type="text" placeholder="Search drivers..."
                                    class="form-control @error('searchDrivers') !border-danger-500 @enderror"
                                    wire:model.live='searchDrivers'>
                            </div>

                            <div class="overflow-x-auto -mx-6">
                                <div class="inline-block min-w-full align-middle">
                                    <div class="overflow-hidden ">
                                        <table
                                            class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                            <tbody
                                                class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">

                                                @foreach ($drivers as $driver)
                                                    <tr wire:click='setDriver({{ $driver->id }})'
                                                        class="hover:bg-slate-200 dark:hover:bg-slate-700 cursor-pointer">
                                                        <td class="table-td">
                                                            <p>
                                                                <b>{{ $driver->user->full_name }} •
                                                                    {{ $driver->shift_title }}</b>
                                                            </p>
                                                        </td>
                                                        <td class="table-td">
                                                            {{ $driver->car_type }}
                                                        </td>
                                                        <td class="table-td">
                                                            {{ $driver->car_model }}
                                                        </td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    @endif

    @can('delete', $order)
        @if ($isOpenDeleteSection)
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
                                    Delete Order
                                </h3>
                                <button wire:click="toggleDelete" type="button"
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
                                    Are you sure ! you Want to delete this order ?
                                </h6>
                            </div>
                            <!-- Modal footer -->
                            <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                                <button wire:click="deleteCustomer" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-danger-500">
                                    <span wire:loading.remove wire:target="deleteCustomer">Yes, Delete</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="deleteCustomer"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endcan

    @if ($isOpenRemoveWhatsappMsgSection)
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
                                Remove Whatsapp Message
                            </h3>
                            <button wire:click="toggleConfirmRemoveWAmsg" type="button"
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
                                Are you sure ! you Want to remove WhatsApp message mark ?
                            </h6>
                        </div>
                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="sendWhatsappMessage(0)" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-danger-500">
                                <span wire:loading.remove wire:target="sendWhatsappMessage(0)">Yes, Remove</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="sendWhatsappMessage(0)"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($isOpenSelectComboSec)
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
                                Add combo products
                            </h3>
                            <button wire:click="closeCombosSection" type="button"
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
                            <input wire:model.live='combosSearchText' type="text" class="form-control"
                                placeholder="Search combo...">

                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1"><iconify-icon
                                    icon="material-symbols:info-outline" width="1.2em"
                                    height="1.2em"></iconify-icon> Adding this combo will replace any individual
                                products in your selection that match the combo's products.</p>


                            <div class=""> <!-- Add this wrapper to allow horizontal scroll -->
                                <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700 ">
                                    <thead
                                        class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                                        <tr>
                                            <th scope="col"
                                                class="table-th  flex items-center border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700"
                                                style="position: sticky; left: -25px;  z-index: 10;">
                                                Name
                                            </th>

                                            <th scope="col" class="table-th">
                                                Price
                                            </th>

                                            <th scope="col" class="table-th">
                                                Products
                                            </th>

                                            <th scope="col" class="table-th">
                                            </th>

                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">

                                        @foreach ($combos as $combo)
                                            <tr>

                                                <td class="table-td">
                                                    <b>{{ $combo->name }}</b>
                                                </td>

                                                <td class="table-td">
                                                    <b>{{ number_format($combo->total_price) }}</b>&nbsp;<small>EGP</small>
                                                </td>

                                                <td class="table-td">
                                                    <b>{{ $combo->products->count() }}</b>&nbsp;<small>Product{{ $combo->products->count() !== 1 ? 's' : '' }}</small>
                                                </td>

                                                <td class="table-td">
                                                    <button wire:click='selectCombo({{ $combo->id }})'
                                                        class="btn inline-flex justify-center btn-dark btn-sm">
                                                        Select combo
                                                    </button>
                                                </td>

                                            </tr>
                                        @endforeach

                                    </tbody>

                                </table>


                                @if ($combos->isEmpty())
                                    {{-- START: empty filter result --}}
                                    <div class="card m-5 p-5">
                                        <div class="card-body rounded-md bg-white dark:bg-slate-800">
                                            <div class="items-center text-center p-5">
                                                <h2>
                                                    <iconify-icon icon="icon-park-outline:search"></iconify-icon>
                                                </h2>
                                                <h2 class="card-title text-slate-900 dark:text-white mb-3">No
                                                    Combos
                                                    Found!</h2>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- END: empty filter result --}}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    @endif
</div>
