<div>
    <div class="flex justify-between flex-wrap items-center mb-5">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Unapproved Journal Entry
            </h4>
        </div>
    </div>

    <div class="card">
        {{-- <header class=" card-header noborder">
            <h4 class="card-title">Table Head
            </h4>
        </header> --}}

        <div class="card-body px-6 pb-6">
            <div class="overflow-x-auto -mx-6">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden ">
                        <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                            <thead class="bg-slate-200 dark:bg-slate-700 no-wrap">
                                <tr>

                                    <th scope="col" class=" table-th !p-1">
                                        #
                                    </th>

                                    <th scope="col" class=" table-th ">
                                        Title
                                    </th>

                                    <th scope="col" class=" table-th ">
                                        Status
                                    </th>

                                    <th scope="col" class=" table-th ">
                                        Amnt
                                    </th>

                                    <th scope="col" class=" table-th ">
                                        Blnc
                                    </th>

                                    <th scope="col" class=" table-th ">
                                        $ Balance
                                    </th>

                                    <th scope="col" class=" table-th ">
                                        Curr.
                                    </th>

                                    <th scope="col" class=" table-th ">
                                        Curr. Amount
                                    </th>

                                    <th scope="col" class=" table-th ">
                                        Curr. Rate
                                    </th>

                                    <th scope="col" class=" table-th ">
                                        Actions
                                    </th>

                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">

                                @forelse ($entries as $entry)
                                    <tr>

                                        <td class="table-td"><b>#{{ $entry->id }}</b></td>

                                        <td class="table-td ">{{ $entry->entry_title->name }}</td>

                                        <td class="table-td ">
                                            @if ($entry->is_reviewed)
                                                <span class="badge bg-success-500 text-white capitalize inline-flex items-center mt-2">
                                                    <iconify-icon class="ltr:mr-1 rtl:ml-1" icon="gg:check-o"></iconify-icon>
                                                    Reviewed</span>
                                            @endif
                                            @if ($entry->approver_id)
                                                <span class="badge btn-outline-success text-white capitalize inline-flex items-center mt-2">
                                                    <iconify-icon class="ltr:mr-1 rtl:ml-1" icon="gg:check-o"></iconify-icon>
                                                    Approved</span>
                                            @endif
                                        </td>

                                        <td class="table-td ">-</td>

                                        <td class="table-td ">-</td>

                                        <td class="table-td ">-</td>

                                        <td class="table-td ">-</td>

                                        <td class="table-td ">-</td>

                                        <td class="table-td ">-</td>

                                        <td class="table-td flex justify-between">
                                            <div>
                                                @if ($entry->accounts->isNotEmpty())
                                                    @if (in_array($entry->id, $showChildAccounts))
                                                        <button class="action-btn mr-2" type="button" wire:click="hideThisChildAccount({{ $entry->id }})">
                                                            <iconify-icon icon="mingcute:up-fill"></iconify-icon>
                                                        </button>
                                                    @else
                                                        <button class="action-btn mr-2" type="button" wire:click="showThisChildAccount({{ $entry->id }})">
                                                            <iconify-icon icon="mingcute:down-fill"></iconify-icon>
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>

                                            <div>
                                                <button class="action-btn" type="button" wire:click="showEntry({{ $entry->id }})">
                                                    <iconify-icon icon="bi:info" class="text-lg"></iconify-icon>
                                                </button>

                                            </div>
                                            <div class="dropstart relative text-right">
                                                <button class="inline-flex justify-center items-center" type="button"   data-bs-toggle="dropdown" aria-expanded="false">
                                                    <iconify-icon class="text-xl ltr:ml-2 rtl:mr-2" icon="heroicons-outline:dots-vertical"></iconify-icon>
                                                </button>
                                                <ul class="dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">
                                                    @can('approve', \App\Models\Accounting\JournalEntry::class)
                                                        <li wire:click="$emit('showConfirmation', 'Are you sure you want to Approve this entry?','black','approveEntry' , {{ $entry->id }})">
                                                            <span
                                                                class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300  last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize  rtl:space-x-reverse">
                                                                <iconify-icon icon="mdi:check-all"></iconify-icon>
                                                                <span>Approve</span></span>
                                                        </li>
                                                    @endcan
                                                    <li wire:click="$emit('showConfirmation', 'Are you sure you want to delete this entry?','danger','deleteEntry' , {{ $entry->id }})">
                                                        <span
                                                            class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300  last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize  rtl:space-x-reverse">
                                                            <iconify-icon icon="material-symbols:delete-outline"></iconify-icon>
                                                            <span>Delete</span></span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>

                                    </tr>

                                    @if (in_array($entry->id, $showChildAccounts))
                                        @foreach ($entry->accounts as $childAccount)
                                            <tr class="bg-slate-50 dark:bg-slate-700">
                                                <td class="table-td" colspan="3"><b>{{ $childAccount->main_account->name }} • {{ $childAccount->name }}</b></td>

                                                <td class="table-td">
                                                    <span class="badge bg-black-500 text-white capitalize inline-flex items-center">
                                                        @if ($childAccount->nature === 'credit')
                                                            <iconify-icon class="ltr:mr-1 rtl:ml-1" icon="heroicons-outline:arrow-circle-up"></iconify-icon>
                                                        @elseif ($childAccount->nature === 'debit')
                                                            <iconify-icon class="ltr:mr-1 rtl:ml-1" icon="heroicons-outline:arrow-circle-down"></iconify-icon>
                                                        @else
                                                            <iconify-icon class="ltr:mr-1 rtl:ml-1" icon="heroicons-outline:question-mark-circle"></iconify-icon>
                                                        @endif
                                                        {{ ucfirst($childAccount->nature) }}
                                                    </span>
                                                </td>

                                                <td class="table-td"><b>{{ number_format($childAccount->pivot->amount, 2) }}</b></td>
                                                <td class="table-td">{{ number_format($childAccount->pivot->account_balance, 2) }}</td>
                                                <td class="table-td">{{ number_format($childAccount->pivot->account_foreign_balance, 2) }}</td>
                                                <td class="table-td">{{ $childAccount->pivot->currency }}</td>
                                                <td class="table-td">{{ number_format($childAccount->pivot->currency_amount, 2) }}</td>
                                                <td class="table-td">{{ $childAccount->pivot->currency_rate }}</td>
                                                <td class="table-td"></td>
                                            </tr>
                                        @endforeach
                                    @endif

                                @empty
                                    <tr>
                                        <td colspan="11">
                                            {{-- START: empty filter result --}}
                                            <div class="card m-5 p-5">
                                                <div class="card-body rounded-md bg-white dark:bg-slate-800 m-5">
                                                    <div class="items-center text-center p-5 m-5">
                                                        <h2><iconify-icon icon="icon-park-outline:search"></iconify-icon></h2>
                                                        <h2 class="card-title text-slate-900 dark:text-white mb-3">No Entries Found!</h2>
                                                        <p class="card-text">Try changing the filters or search terms for this view.</p>
                                                        <a href="{{ url('/accounts/entries/new') }}" class="btn inline-flex justify-center mx-2 mt-3 btn-primary active btn-sm">New Entry</a>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- END: empty filter result --}}
                                        </td>
                                    </tr>
                                @endforelse


                            </tbody>
                        </table>
                    </div>
                    {{ $entries->links('vendor.livewire.bootstrap') }}
                </div>
            </div>
        </div>
    </div>

</div>
