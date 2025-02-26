<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Title Totals Report
            </h4>
        </div>
    </div>

    <div class="card">
        <div class="card-body flex flex-col p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="form-group">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" wire:model.live="startDate" id="start_date">
                </div>
                <div class="form-group">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" wire:model.live="endDate" id="end_date">
                </div>
            </div>
        </div>
    </div>
    <div class="card mt-6">
        <div class="card-body p-6 pb-6">
            <div class="overflow-x-auto">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden ">
                        <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                            <thead class="bg-slate-200 dark:bg-slate-700">
                                <tr>
                                    <th scope="col" class="table-th">Title</th>
                                    <th scope="col" class="table-th">Limit</th>
                                    <th scope="col" class="table-th">Total Amount</th>
                                    <th scope="col" class="table-th">Cash</th>
                                    <th scope="col" class="table-th">Bank</th>
                                    <th scope="col" class="table-th">Wallet</th>
                                    <th scope="col" class="table-th">Transactions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                @foreach ($titleTotals as $total)
                                    <tr class="even:bg-slate-50 dark:even:bg-slate-700">
                                        <td class="table-td">{{ $total->title_name ?? 'No Title' }}</td>
                                        <td class="table-td">
                                            {{ $total->title_limit ? number_format($total->title_limit, 2) : 'N/A' }}
                                        </td>
                                        <td class="table-td">
                                            {{ number_format($total->total_amount, 2) }}
                                        </td>
                                        <td class="table-td">
                                            {{ number_format($total->cash_total, 2) }}</td>
                                        <td class="table-td">
                                            {{ number_format($total->bank_total, 2) }}
                                        </td>
                                        <td class="table-td">
                                            {{ number_format($total->wallet_total, 2) }}
                                        </td>
                                        <td class="table-td">
                                            {{ $total->transaction_count }}
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
