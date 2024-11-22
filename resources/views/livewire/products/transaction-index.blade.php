<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Products Transactions
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">

        </div>
    </div>
    <div class="card">
        <header class="card-header cust-card-header noborder">
            <iconify-icon wire:loading wire:target="search" class="loading-icon text-lg"
                icon="line-md:loading-twotone-loop"></iconify-icon>
            <input type="text" class="form-control !pl-9 mr-1 basis-1/4" placeholder="Search here..."
                wire:model.live.debounce.400ms="search">


        </header>

        <header class="card-header cust-card-header noborder">
            @if ($product)
                <span class="badge bg-slate-900 text-white capitalize">
                    <span class="cursor-pointer">
                        <span class="text-secondary-500 ">Product:</span>&nbsp;
                        {{ $product->name }}

                    </span>

                    &nbsp;&nbsp;<iconify-icon wire:click="clearProduct" icon="material-symbols:close"
                        class="cursor-pointer" width="1.2em" height="1.2em"></iconify-icon>
                </span>
            @endif
        </header>

        <div class="card-body px-6 pb-6  overflow-x-auto">
            <div class=""> <!-- Add this wrapper to allow horizontal scroll -->
                <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700 ">
                    <thead class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                        <tr>
                            <th scope="col"
                                class="table-th  flex items-center border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700"
                                style="position: sticky; left: -25px;  z-index: 10;">
                                <div class="checkbox-area">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" wire:model.live="selectAll" class="hidden"
                                            id="select-all">
                                        <span
                                            class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                            <img src="assets/images/icon/ck-white.svg" alt=""
                                                class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                    </label>
                                </div>
                                <span wire:click="sortByColomn('name')" class="clickable-header">Name
                                    @if ($sortColomn === 'name')
                                        @if ($sortDirection === 'asc')
                                            <iconify-icon icon="fluent:arrow-up-12-filled"></iconify-icon>
                                        @else
                                            <iconify-icon icon="fluent:arrow-down-12-filled"></iconify-icon>
                                        @endif
                                    @endif
                                </span>
                            </th>
                            @if ($selectAll)
                                @if ($selectedAllTrans)
                                    <th colspan="5" class="table-th"><iconify-icon style="vertical-align: top;"
                                            icon="lucide:info" width="1.2em" height="1.2em"></iconify-icon> A
                                        {{ count($selectedTrans) }} Transaction selected ..
                                        <span class="clickable-link" wire:click='undoSelectAllTrans'>Undo</span>
                                    </th>
                                @else
                                    <th colspan="5" class="table-th"><iconify-icon style="vertical-align: top;"
                                            icon="lucide:info" width="1.2em" height="1.2em"></iconify-icon>
                                        {{ count($selectedTrans) }} Transaction
                                        selected .. <span class="clickable-link" wire:click='selectAllTrans'>Select
                                            All Trans</span></th>
                                @endif
                            @else
                                <th scope="col" class="table-th">Quantity</th>

                                <th scope="col" class="table-th">Before</th>

                                <th scope="col" class="table-th">After</th>

                                <th scope="col" class="table-th">Date</th>

                                <th scope="col" class="table-th">Creator</th>


                            @endif

                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">

                        @foreach ($trans as $Tran)
                            <tr>

                                <td class="table-td flex items-center sticky-column bg-white dark:bg-slate-800 colomn-shadow"
                                    style="position: sticky; left: -25px;  z-index: 10;">
                                    <div class="checkbox-area">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" wire:model="selectedTrans"
                                                value="{{ $Tran->id }}" class="hidden" id="select-all">
                                            <span
                                                class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                <img src="assets/images/icon/ck-white.svg" alt=""
                                                    class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                        </label>
                                    </div>
                                    <a href="{{ route('product.show', $Tran->inventory->inventoryable->id) }}"> <span
                                            class="hover-underline">
                                            <b>
                                                {{ $Tran->inventory->inventoryable->name }}
                                            </b>
                                        </span>
                                    </a>

                                </td>

                                <td class="table-td">
                                    @if ($Tran->quantity > 0)
                                        +
                                    @endif{{ $Tran->quantity }}
                                </td>

                                <td class="table-td">
                                    <b>{{ number_format($Tran->before) }}</b>
                                </td>

                                <td class="table-td">
                                    <b>{{ number_format($Tran->after) }}</b>
                                </td>

                                <td class="table-td">
                                    {{ $Tran->created_at->isToday() ? 'Today at ' . $Tran->created_at->format('H:i') : ($Tran->created_at->isYesterday() ? 'Yesterday at ' . $Tran->created_at->format('H:i') : $Tran->created_at->format('l Y-m-d')) }}
                                </td>

                                <td class="table-td">
                                    {{ $Tran->user->full_name }}
                                </td>

                            </tr>
                        @endforeach

                    </tbody>

                </table>


                @if ($trans->isEmpty())
                    {{-- START: empty filter result --}}
                    <div class="card m-5 p-5">
                        <div class="card-body rounded-md bg-white dark:bg-slate-800">
                            <div class="items-center text-center p-5">
                                <h2>
                                    <iconify-icon icon="icon-park-outline:search"></iconify-icon>
                                </h2>
                                <h2 class="card-title text-slate-900 dark:text-white mb-3">No transactions with the
                                    applied
                                    filters</h2>
                                <p class="card-text">Try changing the filters or search terms for this view.
                                </p>
                                <a href="{{ url('/transactions') }}"
                                    class="btn inline-flex justify-center mx-2 mt-3 btn-primary active btn-sm">View
                                    all transactions</a>
                            </div>
                        </div>
                    </div>
                    {{-- END: empty filter result --}}
                @endif


            </div>


        </div>
        <div style="position: sticky ; bottom:0;width:100%; z-index:10;"
            class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
            {{ $trans->links('vendor.livewire.simple-bootstrap') }}
        </div>

    </div>

</div>
