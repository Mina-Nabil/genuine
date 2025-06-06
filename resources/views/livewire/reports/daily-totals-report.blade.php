<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Daily Totals Report
            </h4>
        </div>
    </div>

    <div class="flex">
        <div class="dropdown relative mb-5">
            <button class="btn inline-flex justify-center btn-outline-dark items-center btn-sm" type="button"
                id="darkOutlineDropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                Year: {{ $selectedYear }}
                <iconify-icon class="text-xl ltr:ml-2 rtl:mr-2" icon="ic:round-keyboard-arrow-down"></iconify-icon>
            </button>
            <ul class="dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none"
                style="">
    
                @foreach ($lastYears as $year)
                    <li wire:click='selectYear({{ $year }})' class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                    dark:hover:text-white cursor-pointer">
                            {{ $year }}
                    </li>
                @endforeach
            </ul>
        </div>
    
        <div class="dropdown relative ml-2 mb-5">
            <button class="btn inline-flex justify-center btn-outline-dark items-center btn-sm" type="button"
                id="darkOutlineDropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                Month: {{ \Carbon\Carbon::createFromFormat('m', $selectedMonth)->format('F') }}
                <iconify-icon class="text-xl ltr:ml-2 rtl:mr-2" icon="ic:round-keyboard-arrow-down"></iconify-icon>
            </button>
            <ul class="dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none"
                style="">
    
                @foreach ($AllMonths as $month)
                    <li wire:click='selectMonth({{ $month }})' class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                    dark:hover:text-white cursor-pointer">
                    {{ \Carbon\Carbon::createFromFormat('m', $month)->format('F') }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    



    <div class="card">
        <div class="card-body pb-6  overflow-x-auto">
            <div class=""> <!-- Add this wrapper to allow horizontal scroll -->
                <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                    <thead class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                        <tr>


                            <th scope="col" class="table-th">Day</th>
                            <th scope="col" class="table-th">Confirmed Orders ({{ number_format($dailyTotals->sum('orders_count')) }})</th>
                            <th scope="col" class="table-th">Confirmed Total Weight ({{ number_format($dailyTotals->sum('total_weightsss') / 1000) }})</th>
                            <th scope="col" class="table-th">Confirmed Total Amount ({{ number_format($dailyTotals->sum('total_amount')) }})</th>

                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">

                        @foreach ($dailyTotals as $total)
                            <tr class="even:bg-slate-100 dark:even:bg-slate-700">

                                <td class="table-td">
                                    <b>{{ $total->dayName }} {{ $total->day }}</b>
                                </td>

                                <td class="table-td">
                                    <b>{{ number_format($total->orders_count) }}</b>
                                </td>

                                <td class="table-td">
                                    <b>{{ number_format($total->total_weightsss/1000, 2) }}</b> <small>KG</small>
                                </td>

                                <td class="table-td">
                                    <b>{{ number_format($total->total_amount, 2) }}</b> <small>EGP</small>
                                </td>

                            </tr>
                        @endforeach

                    </tbody>

                </table>


                @if ($dailyTotals->isEmpty())
                    {{-- START: empty filter result --}}
                    <div class="card m-5 p-5">
                        <div class="card-body rounded-md bg-white dark:bg-slate-800">
                            <div class="items-center text-center p-5">
                                <h2>
                                    <iconify-icon icon="icon-park-outline:search"></iconify-icon>
                                </h2>
                                <h2 class="card-title text-slate-900 dark:text-white mb-3">There was no data found for
                                    this month</h2>
                                <p class="card-text">Try changing the filters for this view.
                                </p>
                            </div>
                        </div>
                    </div>
                    {{-- END: empty filter result --}}
                @endif

            </div>
        </div>
    </div>
</div>
