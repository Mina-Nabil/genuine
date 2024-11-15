<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Orders • Inventory
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

    <div>
        <div class="md:flex-1 rounded-md overlay md:col-span-2" style="min-width: 400px;">
            <div class="flex-1 rounded-md col-span-2">
                <div class="card-body flex flex-col justify-center  bg-no-repeat bg-center bg-cover card p-4 active">
                    <div class="card-text flex flex-col justify-between h-full menu-open">

                        @forelse ($orders as $order)
                            <div class="card-body flex flex-col justify-between border rounded-lg h-full menu-open p-0 mb-5"
                                style="border-color:rgb(224, 224, 224)">
                                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-6">

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


                                    </div>

                                    <div class="border-l p-3">

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
                                                                <a href="{{ route('product.show', $orderProduct->product->id) }}"> <span
                                                                        class="hover-underline">
                                                                        <div
                                                                            class="text-start overflow-hidden text-ellipsis whitespace-nowrap" style="max-width: 150px;">
                                                                            <div
                                                                                class="text-sm text-slate-600 dark:text-slate-300 overflow-hidden text-ellipsis whitespace-nowrap">
                                                                                <b>
                                                                                    {{ $orderProduct->product->name }}
                                                                                </b>
                                                                            </div>
                                                                        </div>
                                                                        
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        </td>
                                                        <td class="float-right ml-5"><button
                                                                class="btn inline-flex justify-center btn-outline-secondary btn-sm"
                                                                style="padding-top: 3px;padding-bottom: 3px">Not
                                                                Ready</button></td>

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
                                        test
                                    </div>

                                </div>
                            </div>
                        @empty
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
