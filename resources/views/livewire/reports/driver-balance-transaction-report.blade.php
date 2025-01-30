<div>
    <div class="flex justify-between flex-wrap items-right content-right mb-5">
{{-- 
        <div class="py-[18px] px-4 rounded-[6px] bg-[#E5F9FF] dark:bg-slate-900">
            <div class="flex items-center space-x-6 rtl:space-x-reverse">
                <div class="flex-1">
                    <div class="text-slate-800 dark:text-slate-300 text-sm mb-1 font-medium">

                    </div>
                    <div class="text-slate-900 dark:text-white text-lg font-medium">

                    </div>
                </div>
            </div>
        </div> --}}

        <div class="card dark active">
            <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base menu-open"  style="min-width: 150px">
                <div class="items-center p-5">
                    <h3 class="card-title text-slate-900 dark:text-white">حسابك</h3>
                    <p class="text-lg my-2" style="{{ $user->balance > 0 ? 'color:#00ff5d;' : 'color:#e50000;' }}"><b>{{ number_format($user->balance,2) }}</b>&nbsp;ج.م</p>
                </div>
            </div>
        </div>

        <div class="md:mb-6 mb-4 space-x-3 float-right rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block  rtl:pl-4 system-font">
                <b>حســاب مــندوب</b>
            </h4>

            <div class="mb-2 flex justify-end mt-5">
                @if ($user)
                    <div class="space-y-2">
                        <div class="dropdown relative" style="display: contents">
                            <span class="badge bg-slate-900 text-white capitalize"
                                @if (!auth()->user()->is_driver) type="button"
                            id="secondaryFlatDropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" @endif>
                                <span class="cursor-pointer">
                                    {{-- <span class="text-secondary-500 ">Driver:</span>&nbsp; --}}
                                    {{ ucwords($user->full_name) }}

                                </span>
                            </span>
                            <ul
                                class=" dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow
                                    z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">
                                @foreach ($drivers as $driveruser)
                                    <li wire:click='ChangeUser({{ $driveruser->id }})'
                                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                                        {{ $driveruser->full_name }}</b>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>




    {{-- <div class="card">
        <div class="card-body pb-6  overflow-x-auto">
            <div class=""> <!-- Add this wrapper to allow horizontal scroll -->
                <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700"
                    style="direction: rtl; text-align: right;">
                    <thead class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                        <tr>
                            <th scope="col"
                                class="table-th text-lg flex items-center border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700 system-font">
                                البيــان
                            </th>

                            <th scope="col" class="table-th system-font" style="direction: rtl; text-align: right;">
                                المبــلغ</th>

                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">
                        @foreach ($transactions as $transaction)
                            <tr class="even:bg-slate-100 dark:even:bg-slate-700">
                                <td class="table-td system-font">
                                    <b>{{ $transaction->description }}</b>
                                </td>

                                <td class="table-td"
                                    style="direction: ltr; text-align: right; {{ $transaction->amount > 0 ? 'color:#00a53c;' : 'color:#e50000;' }}">
                                    <b>{{ $transaction->amount > 0 ? '+' : '' }}&nbsp;{{ number_format($transaction->amount, 2) }}</b>&nbsp;ج.م
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if ($transactions->isEmpty())
                    <div class="card m-5 p-5">
                        <div class="card-body rounded-md bg-white dark:bg-slate-800">
                            <div class="items-center text-center p-5">
                                <h2>
                                    <iconify-icon icon="icon-park-outline:search"></iconify-icon>
                                </h2>
                                <h2 class="card-title text-slate-900 dark:text-white mb-3">No transactions</h2>
                                <p class="card-text">Try changing the filters or search terms for this view.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div> --}}

    <div class="grid gap-5" style="direction: rtl; text-align: right;">
        @foreach ($transactions as $transaction)
            <div class="py-[18px] px-4 rounded-[6px] card ring-1 ring-secondary-500 " {{-- style="background-color:{{ $transaction->amount > 0 ? '#00ff2224;' : '#ff00001f;' }} " --}}>
                <div class="flex items-center space-x-6 rtl:space-x-reverse">
                    <div class="flex-1">
                        <div class="text-slate-800 dark:text-slate-300 text-sm mb-1 font-medium system-font">
                            <b>{{ $transaction->description }}</b>
                        </div>
                        <div class="flex justify-between">
                            <div class="text-slate-900 dark:text-white text-lg font-medium system-font"
                                style="{{ $transaction->amount > 0 ? 'color:#00a53c;' : 'color:#e50000;' }}">
                                <b>&nbsp;{{ number_format($transaction->amount, 2) }}</b>&nbsp;ج.م
                            </div>
                            <div class="text-xs" style="place-content: space-around;">
                                {{ $transaction->created_at->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        @if ($transactions->isEmpty())
            {{-- START: empty filter result --}}
            <div class="card p-5">
                <div class="card-body rounded-md bg-white dark:bg-slate-800">
                    <div class="items-center text-center p-5">
                        <h2>
                            <iconify-icon icon="icon-park-outline:search"></iconify-icon>
                        </h2>
                        <h2 class="card-title text-slate-900 dark:text-white mb-3">لم يتم العثور على أي معاملات</h2>
                    </div>
                </div>
            </div>
            {{-- END: empty filter result --}}
        @endif
    </div>
    <div style="position: sticky ; bottom:0;width:100%; z-index:10;"
        class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
        {{ $transactions->links('vendor.livewire.simple-bootstrap') }}
    </div>
</div>
