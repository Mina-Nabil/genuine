<div>
    <div>
        <div class="space-y-5 profile-page mx-auto" style="max-width: 1000px">
            <div class="flex justify-between">
                <div>
                    <h5 class=" text-slate-900 dark:text-whiteh5">
                        <b>{{ $order->title }}</b>
                        @if ($order->periodic_option)
                            <span class="badge bg-black-500 text-slate-100 bg-opacity-50 capitalize">
                                <iconify-icon icon="grommet-icons:cycle" width="1.2em" height="1.2em"></iconify-icon>
                                &nbsp;{{ ucwords(str_replace('_', ' ', $order->periodic_option)) }}
                                •
                                @if ($order->periodic_option === App\Models\Orders\PeriodicOrder::PERIODIC_MONTHLY)
                                    Day: {{ $order->order_day }}
                                @else 
                                    {{ ucwords(str_replace('_', ' ',  App\Models\Orders\PeriodicOrder::daysOfWeek[$order->order_day])) }}
                                @endif
                            </span>
                        @endif
                    </h5>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Created
                        {{ $order->created_at->format('F j, Y \a\t g:i a') }}</p>
                </div>


                @can('update', $order)
                    <div class="dropdown relative">
                        <button class="btn inline-flex justify-center btn-dark items-center btn-sm" type="button"
                            id="darkDropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            More actions
                            <iconify-icon class="text-xl ltr:ml-2 rtl:mr-2"
                                icon="ic:round-keyboard-arrow-down"></iconify-icon>
                        </button>
                        <ul
                            class=" dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow
                                            z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">
                            <li wire:click='openAddProductsSec'
                                class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                dark:hover:text-white cursor-pointer">
                                Add Products
                            </li>
                            <li wire:click='openEditPeriodicDetails'
                                class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                dark:hover:text-white cursor-pointer">
                                Update periodic details
                            </li>
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
                                    <div class="card-body flex flex-col justify-between border rounded-lg h-full menu-open p-0 mb-5 "
                                        style="border-color:rgb(224, 224, 224);">
                                        @foreach ($order->products as $orderProduct)
                                            <div class="p-3">
                                                <div class="flex justify-between">

                                                    <div>
                                                        <h6
                                                            class="text-slate-600 pb-2 dark:text-slate-300 overflow-hidden text-ellipsis whitespace-nowrap">
                                                            {{ $orderProduct->product->name }}
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


                                                        <button class="inline-flex justify-center items-center"
                                                            type="button" id="tableDropdownMenuButton2"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            <iconify-icon class="text-xl ltr:ml-2 rtl:mr-2"
                                                                icon="heroicons-outline:dots-vertical"></iconify-icon>
                                                        </button>
                                                        <ul
                                                            class="dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">

                                                            <li wire:click="openEditProduct({{ $orderProduct->id }})">
                                                                <span
                                                                    class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300  last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize  rtl:space-x-reverse">
                                                                    <iconify-icon icon="lucide:edit"></iconify-icon>
                                                                    <span>Edit</span>
                                                                </span>
                                                            </li>

                                                            <li
                                                                wire:click="showConfirmRemoveProduct({{ $orderProduct->id }})">
                                                                <span
                                                                    class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300  last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize  rtl:space-x-reverse">
                                                                    <iconify-icon
                                                                        icon="material-symbols:delete-outline"></iconify-icon>
                                                                    <span>Delete</span>
                                                                </span>
                                                            </li>

                                                        </ul>

                                                    </div>

                                                </div>
                                                <div class=" flex text-sm justify-between">
                                                    <span
                                                        class="badge bg-secondary-500 bg-opacity-30 text-slate-900 dark:text-white rounded-3xl">Weight:
                                                        {{ $orderProduct->product->weight }} gm</span>

                                                </div>
                                                @if ($orderProduct->combo)
                                                    <div class=" flex text-sm justify-between">
                                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                                            Combo:
                                                            {{ $orderProduct->combo->name }}</p>
                                                    </div>
                                                @endif
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

                                <div class="card-body flex flex-col justify-between border rounded-lg h-full menu-open p-0 mb-5 p-2 px-6 overflow-x-auto no-wrap"
                                    style="border-color:rgb(224, 224, 224);">

                                    <table
                                        class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
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
                                                    <b>
                                                        @if ($order->delivery_amount)
                                                            {{ number_format($order->delivery_amount, 2) }}
                                                            <small>&nbsp;EGP</small>
                                                        @else
                                                            <small>Free</small>
                                                        @endif
                                                    </b>
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
                            </div>
                        </div>
                    </div>


                </div>
                <div class="col-span-2">

                    <div class="card">
                        <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base">
                            <div class="items-center p-5">
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
                                        order{{ $order->customer->total_orders > 1 ? 's' : '' }}</p>
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
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                width="24" height="24">
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
                                                    {{ strtoupper(substr($comment->user->username, 0, 1)) }}
                                                </span>
                                            </span>
                                            <span><a href="#">{{ $comment->user->full_name }}</a>
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
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                width="24" height="24">
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
                                                        {{ strtoupper(substr($comment->user->username, 0, 1)) }}
                                                    </span>
                                                </span>
                                                <span><a href="#">{{ $comment->user->full_name }}</a> commented
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
                                                Note: Any changes to the zone may affect the shipping rate for this order
                                                and
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

                                    <div class="from-group">
                                        <div class="input-area">
                                            <label for="locationUrl" class="form-label">location URL</label>
                                            <textarea id="locationUrl" type="text" class="form-control @error('locationUrl') !border-danger-500 @enderror"
                                                wire:model="locationUrl" autocomplete="off"></textarea>
                                        </div>
                                        @error('locationUrl')
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
                                                                <td class="table-td ">
                                                                    {{ $product->weight }}<small>gm</small>
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

                                        <table
                                            class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
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

        @can('update', $order)
            @if ($periodicOrderProduct)
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
                                        Edit products
                                    </h3>
                                    <button wire:click="closeEditProduct" type="button"
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
                                            <tr>
                                                <!-- Product Name Column -->
                                                <td class="table-td imp-p-2">
                                                    <div class="flex-1 text-start">
                                                        <div class="text-start overflow-hidden text-ellipsis whitespace-nowrap"
                                                            style="max-width:200px;">
                                                            <h6
                                                                class="text-slate-600 dark:text-slate-300 overflow-hidden text-ellipsis whitespace-nowrap">
                                                                {{ $periodicOrderProduct->product->name }}
                                                            </h6>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- quantity -->
                                                <td class="table-td imp-p-2">
                                                    <input type="number"
                                                        class="form-control @error('productQuantity') !border-danger-500 @enderror"
                                                        style="max-width: 100px;" wire:model.live='productQuantity'
                                                        min="1">
                                                </td>

                                                <!-- price/item -->
                                                <td class="table-td imp-p-2">
                                                    <input type="number"
                                                        class="form-control @error('productPrice') !border-danger-500 @enderror"
                                                        style="max-width: 100px;" wire:model.live='productPrice'
                                                        min="1">
                                                </td>
                                            </tr>



                                        </tbody>
                                    </table>
                                </div>

                                <!-- Modal footer -->
                                <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                                    <button wire:click="updateProduct" data-bs-dismiss="modal"
                                        class="btn inline-flex justify-center text-white bg-black-500">
                                        <span wire:loading.remove wire:target="updateProduct">Submit</span>
                                        <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                            wire:loading wire:target="updateProduct"
                                            icon="line-md:loading-twotone-loop"></iconify-icon>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
            @endif
        @endcan

    </div>


    @if ($deleteProductId)
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
                                Remove Product
                            </h3>
                            <button wire:click="hideConfirmRemoveProduct" type="button"
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

                            Are you sure you want to remove product ?
                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="deleteProduct" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="deleteProduct">Confirm</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="deleteProduct"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    @endif

    @if ($editPeriodicDetailsSec)
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
                                Edit periodic details
                            </h3>
                            <button wire:click="closeEditPeriodicDetails" type="button"
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
                                        <label for="periodicOption" class="form-label"><b>Periodic option</b></label>
                                        <select name="periodicOption" id="periodicOption"
                                            class="form-control w-full @error('periodicOption') !border-danger-500 @enderror"
                                            wire:model.live="periodicOption" autocomplete="off">
                                            @foreach ($PERIODIC_OPTIONS as $PERIODIC_OPTION)
                                                <option value="{{ $PERIODIC_OPTION }}">
                                                    {{ ucwords(str_replace('_', ' ', $PERIODIC_OPTION)) }}</option>
                                            @endforeach
                                        </select>
                                        @error('periodicOption')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    @if (
                                        $periodicOption === App\Models\Orders\PeriodicOrder::PERIODIC_WEEKLY ||
                                            $periodicOption === App\Models\Orders\PeriodicOrder::PERIODIC_BI_WEEKLY)
                                        <div class="input-area w-full">
                                            <label for="orderDay" class="form-label"><b>Day of week</b></label>
                                            <select name="orderDay" id="orderDay"
                                                class="form-control w-full @error('orderDay') !border-danger-500 @enderror"
                                                wire:model.live="orderDay" autocomplete="off">
                                                @foreach ($daysofweek as $index => $day)
                                                    <option value="{{ $index }}">
                                                        {{ ucwords(str_replace('_', ' ', $day)) }}</option>
                                                @endforeach
                                            </select>
                                            @error('orderDay')
                                                <span
                                                    class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @elseif($periodicOption === App\Models\Orders\PeriodicOrder::PERIODIC_MONTHLY)
                                        <div class="input-area w-full">
                                            <label for="ddate" class="form-label"><b>Day of month</b></label>
                                            <input ty name="orderDay" id="orderDay" type="number" min="1"
                                                max="30"
                                                class="form-control w-full @error('orderDay') !border-danger-500 @enderror"
                                                wire:model.live="orderDay" autocomplete="off">
                                            @error('orderDay')
                                                <span
                                                    class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif


                                </div>

                            </div>

                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="updatePeriodicDetails" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="updatePeriodicDetails">Submit</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="updatePeriodicDetails"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    @endif

</div>
