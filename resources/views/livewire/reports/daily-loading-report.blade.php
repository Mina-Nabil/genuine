<div>
    <div class="space-y-5 profile-page mx-auto">
        <div class="flex justify-between flex-wrap items-center">
            <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
                <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                    Daily Loading Report
                </h4>
            </div>
        </div>
        <div class="card">
            <header class="card-header cust-card-header noborder pt-0">
                <div class="input-area flex no-wrap">
                    <input id="deliveryDate" type="date"
                        class="form-control @error('deliveryDate') !border-danger-500 @enderror"
                        wire:model.live="deliveryDate" autocomplete="off">
                </div>
            </header>

            <div class="card-body px-6 pb-6  overflow-x-auto">
                <div class=""> <!-- Add this wrapper to allow horizontal scroll -->
                    <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                        <thead class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                            <tr>
                                <th scope="col" class="table-th">Driver</th>
                                <th scope="col" class="table-th">Zone</th>
                                <th scope="col" class="table-th">Orders</th>
                                <th scope="col" class="table-th">Total</th>
                                <th scope="col" class="table-th">Quantity</th>
                                <th scope="col" class="table-th">Cash</th>
                                <th scope="col" class="table-th">Wallet</th>
                                <th scope="col" class="table-th">Bank</th>

                            </tr>
                        </thead>
                        <tbody
                            class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">
                            @foreach ($totals as $t)
                                <tr>
                                    <td class="table-td">
                                        {{ $t->driver_name }}
                                    </td>
                                    <td class="table-td">
                                        {{ $t->name }}
                                    </td>
                                    <td class="table-td">
                                        {{ $t->orders_count }}
                                    </td>
                                    <td class="table-td">
                                        {{ $t->orders_total }}
                                    </td>
                                    <td class="table-td">
                                        {{ $t->quantity_total }}
                                    </td>
                                    <td class="table-td">
                                        {{ $t->total_cash }}
                                    </td>
                                    <td class="table-td">
                                        {{ $t->total_wallet }}
                                    </td>
                                    <td class="table-td">
                                        {{ $t->total_bank }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>


                    @if (!count($totals) || (count($totals) == 1 && $totals[0]->orders_count == 0))
                        <div class="card m-5 p-5">
                            <div class="card-body rounded-md bg-white dark:bg-slate-800">
                                <div class="items-center text-center p-5">
                                    <h2>
                                        <iconify-icon icon="icon-park-outline:search"></iconify-icon>
                                    </h2>
                                    <h2 class="card-title text-slate-900 dark:text-white mb-3">No data with the
                                        applied
                                        filters</h2>
                                    <p class="card-text">Try changing the filters or search terms for this view.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif


                </div>


            </div>
        </div>
    </div>
</div>
