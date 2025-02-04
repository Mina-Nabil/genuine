<div>

    <div class="card dark active mb-5">
        <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base menu-open">
            <div class="items-center p-5">
                <h3 class="card-title text-slate-900 dark:text-white">{{ $account->name }}</h3>
                @if ($account->desc)
                    <p class="card-text my-5 break-words">{{ $account->desc }}</p>
                @endif
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ ucwords($account->nature) }}</p>
            </div>
        </div>
    </div>

    <div class="card">



        <header class=" card-header noborder">
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Start Balance</p>
                <h4 class="card-title">
                    {{ $entries->first()
                        ? ($account->nature == 'debit'
                            ? number_format(
                                $entries->first()->account_balance + $entries->first()->credit_amount - $entries->first()->debit_amount,
                                2,
                            )
                            : number_format(
                                $entries->first()->account_balance + $entries->first()->debit_amount - $entries->first()->credit_amount,
                                2,
                            ))
                        : number_format($account->balance, 2) }}
                </h4>
            </div>
            <input type="text" class="form-control w-auto d-inline-block cursor-pointer" style="width:auto"
                name="datetimes" id="reportrange" />
        </header>

        <header class="card-header noborder">
            <iconify-icon wire:loading wire:target="searchText" class="loading-icon text-lg"
                    icon="line-md:loading-twotone-loop"></iconify-icon>
                <input type="text" class="form-control !pl-9 mr-1 basis-1/4" placeholder="Search..."
                    wire:model="searchText">
        </header>

        <div class="card-body px-6 pb-6">
            <div class="overflow-x-auto -mx-6">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden ">
                        <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                            <thead class=" border-t border-slate-100 dark:border-slate-800 no-wrap">
                                <tr>
                                    <th scope="col" class="table-th">#</th>
                                    <th scope="col" class="table-th">Title</th>
                                    <th scope="col" class="table-th">Debit</th>
                                    <th scope="col" class="table-th">Credit</th>
                                    <th scope="col" class="table-th">Balance</th>
                                    <th scope="col" class="table-th">Debit $</th>
                                    <th scope="col" class="table-th">Credit $</th>
                                    <th scope="col" class="table-th">Balance $</th>
                                    <th scope="col" class="table-th">Creator</th>

                                </tr>
                            </thead>
                            <tbody
                                class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">

                                @foreach ($entries as $entry)
                                    <tr>
                                        <td class="table-td"><b>{{ $entry->id }}</b></td>
                                        <td class="table-td"><b>{{ $entry->name }}</b></td>
                                        <td class="table-td">{{ number_format($entry->debit_amount, 2) }}</td>
                                        <td class="table-td">{{ number_format($entry->credit_amount, 2) }}</td>
                                        <td class="table-td">{{ number_format($entry->account_balance, 2) }}</td>
                                        <td class="table-td">{{ number_format($entry->debit_foreign_amount, 2) }}</td>
                                        <td class="table-td">{{ number_format($entry->credit_foreign_amount, 2) }}</td>
                                        <td class="table-td">{{ number_format($entry->account_foreign_balance, 2) }}
                                        </td>
                                        <td class="table-td">{{ $entry->username }}</td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($entries->isEmpty())
                    {{-- START: empty filter result --}}
                    <div class="card m-5 p-5">
                        <div class="card-body rounded-md bg-white dark:bg-slate-800">
                            <div class="items-center text-center p-5">
                                <h2><iconify-icon icon="icon-park-outline:search"></iconify-icon>
                                </h2>
                                <h2 class="card-title text-slate-900 dark:text-white mb-3">
                                    No entries found!</h2>
                                <p class="card-text">Try changing the filters or search terms for this view.
                                </p>
                                <a href="{{ url('/accounts') }}"
                                    class="btn inline-flex justify-center mx-2 mt-3 btn-primary active btn-sm">
                                    View Accounts</a>
                            </div>
                        </div>
                    </div>
                    {{-- END: empty filter result --}}
                @endif
            </div>
        </div>
        @if (!$entries->isEmpty())
            <header class=" card-header noborder">
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">End Balance</p>
                    <h4 class="card-title">{{ number_format($account->balance, 2) }}
                    </h4>
                </div>
            </header>
        @endif
    </div>


    @if ($is_open_edit)
    @endif



</div>
