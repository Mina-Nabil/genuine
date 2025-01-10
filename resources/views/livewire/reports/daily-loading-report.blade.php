<div>
    @section('head_content')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    @endsection

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
                    <input type="text" class="form-control w-auto d-inline-block cursor-pointer" style="width:auto"
                        name="datetimes" id="reportrange" />
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
                                <th scope="col" class="table-th">KGs</th>
                                <th scope="col" class="table-th">Cash</th>
                                <th scope="col" class="table-th">Wallet</th>
                                <th scope="col" class="table-th">Bank</th>

                            </tr>
                        </thead>
                        <tbody
                            class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">
                            @foreach ($totals as $index => $t)
                                <tr class="even:bg-slate-100 dark:even:bg-slate-700">
                                    <td
                                        class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                        {{ $t->shift_title ?? 'N/A' }}
                                    </td>
                                    <td
                                        class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                        {{ $t->name }}
                                    </td>
                                    <td
                                        class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                        {{ $t->orders_count }}
                                    </td>
                                    <td
                                        class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                        {{ $t->orders_total }}
                                    </td>
                                    <td
                                        class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                        {{ number_format($t->kgs_total) }}
                                    </td>
                                    <td
                                        class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                        {{ $t->total_cash }}
                                    </td>
                                    <td
                                        class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                        {{ $t->total_wallet }}
                                    </td>
                                    <td
                                        class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                        {{ $t->total_bank }}
                                    </td>
                                </tr>

                                @php
                                    $driverRecords = $totals->where('user_id', $t->user_id);
                                    $isLastRecord = $driverRecords->keys()->last() === $index;
                                    $hasMultipleRecords = $driverRecords->count() > 1;
                                @endphp

                                @if ($isLastRecord && $hasMultipleRecords)
                                    <tr class="bg-slate-900">
                                        <td
                                            class="px-2 border border-slate-100 dark:bg-slate-800 dark:border-slate-700 text-slate-100 text-center">
                                            {{ $t->first_name.' '.$t->last_name }}
                                        </td>

                                        <td
                                            class="px-2 border border-slate-100 dark:bg-slate-800 dark:border-slate-700 text-slate-100 text-center">
                                        </td>

                                        <td
                                            class="px-2 border border-slate-100 dark:bg-slate-800 dark:border-slate-700 text-slate-100 text-center">
                                            {{ number_format($driverRecords->sum('orders_count')) }} <small>Orders</small>
                                        </td>

                                        <td
                                            class="px-2 border border-slate-100 dark:bg-slate-800 dark:border-slate-700 text-slate-100 text-center">
                                            {{ number_format($driverRecords->sum('orders_total'),2) }} <small>EGP</small>
                                        </td>

                                        <td
                                            class="px-2 border border-slate-100 dark:bg-slate-800 dark:border-slate-700 text-slate-100 text-center">
                                            {{ number_format($driverRecords->sum('kgs_total'),2) }} <small>KG</small>
                                        </td>

                                        <td
                                            class="px-2 border border-slate-100 dark:bg-slate-800 dark:border-slate-700 text-slate-100 text-center">
                                            {{ number_format($driverRecords->sum('total_cash'),2) }} <small>EGP</small>
                                        </td>

                                        <td
                                            class="px-2 border border-slate-100 dark:bg-slate-800 dark:border-slate-700 text-slate-100 text-center">
                                            {{ number_format($driverRecords->sum('total_wallet'),2) }} <small>EGP</small>
                                        </td>

                                        <td
                                            class="px-2 border border-slate-100 dark:bg-slate-800 dark:border-slate-700 text-slate-100 text-center">
                                            {{ number_format($driverRecords->sum('total_bank'),2) }} <small>EGP</small>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                        <tfoot class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                            <th scope="col" class="table-th">
                                {{ $totals->unique('shift_title')->count() }}
                            </th>
                            <th scope="col" class="table-th">
                                {{ $totals->unique('name')->count() }}
                            </th>
                            <th scope="col" class="table-th">
                                {{ $totals->sum('orders_count') }}
                            </th>
                            <th scope="col" class="table-th">
                                {{ $totals->sum('orders_total') }}
                            </th>
                            <th scope="col" class="table-th">
                                {{ number_format($totals->sum('kgs_total')) }}
                            </th>
                            <th scope="col" class="table-th">
                                {{ $totals->sum('total_cash') }}
                            </th>
                            <th scope="col" class="table-th">
                                {{ $totals->sum('total_wallet') }}
                            </th>
                            <th scope="col" class="table-th">
                                {{ $totals->sum('total_bank') }}
                            </th>
                        </tfoot>
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

    <script src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        document.addEventListener('livewire:initialized', () => {
            var start = moment();
            var end = moment();

            function cb(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                Livewire.dispatch('dateRangeSelected', {
                    data: [start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD')]
                });

            }

            $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,
                alwaysShowCalendars: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'months').startOf('month'), moment().subtract(1,
                        'months').endOf('month')],
                    'Last 3 Months': [moment().subtract(3, 'months'), moment()],
                }
            }, cb);

            cb(start, end);
        });
    </script>
</div>
