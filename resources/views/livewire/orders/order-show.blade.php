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
                    @if ($order->periodic_option)
                        <span class="badge bg-black-500 text-slate-100 bg-opacity-50 capitalize">
                            <iconify-icon icon="grommet-icons:cycle" width="1.2em" height="1.2em"></iconify-icon>
                            &nbsp;{{ ucwords(str_replace('_', ' ', $order->periodic_option)) }}
                        </span>
                    @endif
                </h5>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Created
                    {{ $order->created_at->format('F j, Y \a\t g:i a') }}</p>
            </div>


            @can('update', $order)
                <div>
                    <button class="btn inline-flex justify-center btn-secondary btn-sm"
                        wire:click='openEditSection'>Edit</button>
                    <button class="btn inline-flex justify-center btn-secondary btn-sm"
                        wire:click='openEditSection'>Edit</button>
                    <button class="btn inline-flex justify-center btn-secondary btn-sm"
                        wire:click='openEditSection'>Edit</button>
                </div>
            @endcan
        </div>

        <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-6 md:gap-5 mb-5 text-wrap">
            <div class="col-span-4">

                <div class="card mb-5">
                    <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base">
                        <div class="items-center p-5">
                            @if ($order->status === App\Models\Orders\Order::STATUS_NEW || $order->status === App\Models\Orders\Order::STATUS_READY)
                                <span class="badge bg-info-500 text-dark-500 bg-opacity-50 capitalize">
                                    <iconify-icon icon="octicon:dot-16" width="1.2em" height="1.2em"></iconify-icon>
                                    {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                </span>
                            @elseif ($order->status === App\Models\Orders\Order::STATUS_IN_DELIVERY)
                                <span class="badge bg-warning-500 text-dark-500 bg-opacity-50 capitalize">
                                    <iconify-icon icon="octicon:dot-16" width="1.2em" height="1.2em"></iconify-icon>
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
                                    <iconify-icon icon="octicon:dot-16" width="1.2em" height="1.2em"></iconify-icon>
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
                                                    class="text-slate-600 dark:text-slate-300 overflow-hidden text-ellipsis whitespace-nowrap">
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
                                            </div>

                                        </div>
                                        <div class=" flex text-sm justify-between">
                                            <span
                                                class="badge bg-secondary-500 bg-opacity-30 text-slate-900 dark:text-white rounded-3xl">Weight:
                                                {{ $orderProduct->product->weight }} gm</span>

                                        </div>
                                        @if ($orderProduct->combo)
                                            <div class=" flex text-sm justify-between">
                                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Combo:
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
                                <span class="badge bg-warning-500 text-dark-500 bg-opacity-50 capitalize">
                                    <iconify-icon icon="octicon:dot-16" width="1.2em" height="1.2em"></iconify-icon>
                                    Payment pending
                                </span>
                            @endif

                            <div class="card-body flex flex-col justify-between border rounded-lg h-full menu-open p-0 mb-5 p-2 px-6"
                                style="border-color:rgb(224, 224, 224);">

                                <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                    <tbody class="bg-white dark:bg-slate-800 ">

                                        <tr>
                                            <td class=" text-xs text-slate-500 dark:text-slate-400">Subtotal</td>
                                            <td class=" text-xs text-slate-500 dark:text-slate-400">
                                                {{ $order->total_items . ' items' }}</td>
                                            <td class="float-right text-dark">
                                                <b>{{ $order->total_items_price ? number_format($order->total_items_price, 2) : '-' }}<small>&nbsp;EGP</small></b>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class=" text-xs text-slate-500 dark:text-slate-400">Shipping &
                                                Delivery</td>
                                            <td class=" text-xs text-slate-500 dark:text-slate-400">
                                                {{ ucwords($order->zone->name) }}</td>
                                            <td class="float-right text-dark">
                                                <b>{{ $order->delivery_amount ? number_format($order->delivery_amount, 2) : 'Free' }}<small>&nbsp;EGP</small></b>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class=" text-xs text-slate-500 dark:text-slate-400">
                                                @if ($order->discount_amount)
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
                                            <td class=" text-xs text-slate-500 dark:text-slate-400"></td>
                                            <td class="float-right text-dark">
                                                <b>{{ $order->discount_amount ? '-' . number_format($order->discount_amount, 2) : '-' }}<small>&nbsp;EGP</small></b>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class=" text-xs text-slate-500 dark:text-slate-400"></td>
                                            <td class=" text-xs text-slate-500 dark:text-slate-400"></td>
                                            <td class="float-right text-dark"></td>
                                        </tr>
                                        <tr>
                                            <td class=" text-xs text-slate-500 dark:text-slate-400"></td>
                                            <td class=" text-xs text-slate-500 dark:text-slate-400"></td>
                                            <td class="float-right text-dark"></td>
                                        </tr>

                                        <tr class="!pt-5">
                                            <td class=" text-xs text-slate-500 dark:text-slate-400">Total</td>
                                            <td class=" text-xs text-slate-500 dark:text-slate-400"></td>
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
                                <label for="phone" class="form-label"><b>Notes</b></label>
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
                                    <a class="clickable-link" href="{{ route('customer.show',$order->customer->id) }}">
                                        {{ $order->customer->name }}
                                    </a>
                                </p>
                                <p class="text-xs mt-1">{{ $order->customer->total_orders }} order{{ ($order->customer->total_orders > 1)  ? 's' : '' }}</p>
                            </div>
                            <label for="phone" class="form-label mt-5"><b>Shipping Address</b></label>
                            <p class="text-xs">{{ $order->customer_name }}</p>
                            <a class="text-xs clickable-link" href="tel:{{ $order->customer_phone }}">{{ $order->customer_phone }}</a>
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
                                <span class="block w-full h-full object-cover text-center leading-10 text-lg user-initial">
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
                                            <span class="block w-full h-full object-cover text-center text-lg user-initial"
                                                style="font-size: 12px">
                                                {{ strtoupper(substr($comment->user->username, 0, 1)) }}
                                            </span>
                                        </span>
                                        <span><a href="#">{{ $comment->user->full_name }}</a> {{ $comment->title }}
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
                                                    {{ strtoupper(substr($comment->user->username, 0, 1)) }}
                                                </span>
                                            </span>
                                            <span><a href="#">{{ $comment->user->full_name }}</a> commented <time
                                                    datetime="20-01-2021">
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
</div>
