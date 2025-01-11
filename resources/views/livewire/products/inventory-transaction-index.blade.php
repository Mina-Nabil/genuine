<div>
    @if ($hasChanges)
        <div class="dropdup relative select-action-btns-container" wire:click='openReviewChanges'>
            <button class="btn inline-flex justify-center btn-success items-center  no-wrap" type="button"
                id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 50px;">
                Save Changes
            </button>
        </div>
    @endif
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Inventories
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            {{-- btnHere --}}
        </div>
    </div>
    <div class="card">
        <header class="card-header cust-card-header noborder">
            <iconify-icon wire:loading wire:target="searchTerm,sortByColomn" class="loading-icon text-lg"
                icon="line-md:loading-twotone-loop"></iconify-icon>
            <input type="text" class="form-control !pl-9 mr-1 basis-1/4" placeholder="Search here..."
                wire:model.live.debounce.400ms="searchTerm">
        </header>

        <div class="card-body px-6 pb-6  overflow-x-auto">
            <div class=""> <!-- Add this wrapper to allow horizontal scroll -->
                <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700 no-wrap">
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
                                @if ($selectedAllInventories)
                                    <th colspan="4" class="table-th"><iconify-icon style="vertical-align: top;"
                                            icon="lucide:info" width="1.2em" height="1.2em"></iconify-icon> A
                                        {{ count($selectedInventories) }} inventory selected ..
                                        <span class="clickable-link" wire:click='undoSelectAllInventories'>Undo</span>
                                    </th>
                                @else
                                    <th colspan="4" class="table-th"><iconify-icon style="vertical-align: top;"
                                            icon="lucide:info" width="1.2em" height="1.2em"></iconify-icon>
                                        {{ count($selectedInventories) }} inventory
                                        selected .. <span class="clickable-link"
                                            wire:click='selectAllInventories'>Select
                                            All Inventories</span></th>
                                @endif
                            @else
                                {{-- <th scope="col" class="table-th">
                                    <span wire:click="sortByColomn('on_hand')" class="clickable-header">
                                        On Hand
                                        @if ($sortColomn === 'on_hand')
                                            @if ($sortDirection === 'asc')
                                                <iconify-icon icon="fluent:arrow-up-12-filled"></iconify-icon>
                                            @else
                                                <iconify-icon icon="fluent:arrow-down-12-filled"></iconify-icon>
                                            @endif
                                        @endif
                                    </span>
                                </th> --}}

                                {{-- <th scope="col" class="table-th">
                                    <span wire:click="sortByColomn('committed')" class="clickable-header">
                                        Committed
                                        @if ($sortColomn === 'committed')
                                            @if ($sortDirection === 'asc')
                                                <iconify-icon icon="fluent:arrow-up-12-filled"></iconify-icon>
                                            @else
                                                <iconify-icon icon="fluent:arrow-down-12-filled"></iconify-icon>
                                            @endif
                                        @endif
                                    </span>
                                </th>

                                <th scope="col" class="table-th">
                                    <span wire:click="sortByColomn('available')" class="clickable-header">
                                        Available
                                        @if ($sortColomn === 'available')
                                            @if ($sortDirection === 'asc')
                                                <iconify-icon icon="fluent:arrow-up-12-filled"></iconify-icon>
                                            @else
                                                <iconify-icon icon="fluent:arrow-down-12-filled"></iconify-icon>
                                            @endif
                                        @endif
                                    </span>
                                </th> --}}

                                <th scope="col" class="table-th">New</th>

                                <th scope="col" class="table-th"></th>

                            @endif

                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">

                        @foreach ($inventories as $index => $inventory)
                            @if ($inventory->inventoryable)
                                <tr>

                                    <td class="table-td flex items-center sticky-column bg-white dark:bg-slate-800 colomn-shadow"
                                        style="position: sticky; left: -25px;  z-index: 10;">
                                        <div class="checkbox-area">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="checkbox" wire:model="selectedInventories"
                                                    value="{{ $inventory->id }}" class="hidden" id="select-all">
                                                <span
                                                    class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                    <img src="assets/images/icon/ck-white.svg" alt=""
                                                        class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                            </label>
                                        </div>
                                        <a href="{{ route('product.show', $inventory->inventoryable?->id) }}"> <span
                                                class="hover-underline">
                                                <b>
                                                    {{ $inventory->inventoryable?->name }}
                                                </b>
                                            </span>
                                        </a>

                                    </td>

                                    {{-- <td class="table-td">
                                        <input type="number" class="form-control" style="max-width: 100px;"
                                            wire:model="productsChanges.data.{{ $index }}.on_hand"
                                            wire:change="updateAvailable({{ $index }})" min="0">
                                    </td> --}}

                                    {{-- <td class="table-td">
                                        <b>{{ number_format($inventory?->committed) }}</b>
                                    </td>

                                    <td class="table-td">
                                        {{ $productsChanges['data'][$index]['available'] }}
                                        {{-- <input type="number"
                                        class="form-control @if ($inventory->available <= 0) !border-danger-500 @endif"
                                        style="max-width: 100px; "
                                        wire:model="productsChanges.data.{{ $index }}.available"
                                        wire:change="updateOnHand({{ $index }})"> 
                                    </td> --}}

                                    <td class="table-td">
                                        <input type="number" 
                                        class="form-control @error("productsChanges.data.$index.new") !border-danger-500 @enderror" 
                                        style="max-width: 100px;" 
                                        wire:model="productsChanges.data.{{ $index }}.new"
                                        wire:change="updateNew({{ $index }})" 
                                        min="0">
                                    </td>

                                    <td class="table-td">
                                        <a href="\transactions?product_id={{ $inventory->inventoryable?->id }}"
                                            target="_blanck"
                                            class="btn inline-flex justify-center mx-2 mt-3 btn-light active btn-sm">View
                                            transactions</a>
                                    </td>

                                </tr>
                            @endif
                        @endforeach

                    </tbody>

                </table>


                @if ($inventories->isEmpty())
                    {{-- START: empty filter result --}}
                    <div class="card m-5 p-5">
                        <div class="card-body rounded-md bg-white dark:bg-slate-800">
                            <div class="items-center text-center p-5">
                                <h2>
                                    <iconify-icon icon="icon-park-outline:search"></iconify-icon>
                                </h2>
                                <h2 class="card-title text-slate-900 dark:text-white mb-3">No inventories with the
                                    applied
                                    filters</h2>
                                <p class="card-text">Try changing the filters or search terms for this view.
                                </p>
                                <a href="{{ url('/inventories') }}"
                                    class="btn inline-flex justify-center mx-2 mt-3 btn-primary active btn-sm">View
                                    all inventories</a>
                            </div>
                        </div>
                    </div>
                    {{-- END: empty filter result --}}
                @endif


            </div>


        </div>
        <div style="position: sticky ; bottom:0;width:100%; z-index:10;"
            class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
            {{ $inventories->links('vendor.livewire.simple-bootstrap') }}
        </div>

    </div>

    @if ($newChanges)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog"
            style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Review Changes
                            </h3>
                            <button wire:click="closeReviewChanges" type="button"
                                class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                data-bs-dismiss="modal">
                                <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>

                        <!-- Modal body -->
                        <div class="p-6 space-y-4">

                            <div
                                class="py-[18px] px-6 font-normal text-sm rounded-md bg-warning-500 bg-opacity-[14%]  text-white">
                                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                    <iconify-icon class="text-slate-900" icon="line-md:alert" width="2em"
                                        height="2em"></iconify-icon>
                                    <p class="flex-1 text-slate-900 font-Inter">
                                        Please review the changes made to the 'On Hand' and 'Available' quantities
                                        before submitting the transaction. Ensure that the inventory counts are accurate
                                        to avoid discrepancies.

                                    </p>
                                </div>
                            </div>

                            <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                <thead class="">
                                    <tr>

                                        <th scope="col"
                                            class=" table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700 ">
                                            Product
                                        </th>

                                        <th scope="col"
                                            class=" table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700 ">
                                            Available
                                        </th>

                                        <th scope="col"
                                            class=" table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700 ">
                                            New
                                        </th>

                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    @foreach ($newChanges as $productChanged)
                                        <tr>
                                            <td
                                                class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                                {{ $productChanged['product_name'] }}</td>
                                            <td
                                                class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700 ">
                                                {{ $productChanged['from_available'] }} ->
                                                {{ $productChanged['to_available'] }}</td>
                                            <td
                                                class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700 ">
                                                {{ $productChanged['to_available'] - $productChanged['from_available'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="from-group">
                                <div class="input-area">
                                    <label for="transRemark" class="form-label">Remark</label>
                                    <textarea id="transRemark" class="form-control @error('transRemark') !border-danger-500 @enderror"
                                        wire:model="transRemark" autocomplete="off"></textarea>
                                    @error('transRemark')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                    <small class="text-gray-500">Optional: Provide any additional notes or remarks
                                        related to this transaction. This can help clarify the reason for the
                                        entry.</small>
                                </div>

                            </div>
                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="submitTransaction" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="submitTransaction">Submit Transaction</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="submitTransaction"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    @endif

</div>
