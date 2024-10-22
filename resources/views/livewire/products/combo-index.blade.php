<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Combos
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            {{-- @can('create', App\Models\Customers\Customer::class) --}}
            <button wire:click="openNewComboSec"
                class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1 btn-sm">
                Add Combo
            </button>
            {{-- @endcan --}}

        </div>
    </div>
    <div class="card">
        <header class="card-header cust-card-header noborder">
            <iconify-icon wire:loading wire:target="searchTerm" class="loading-icon text-lg"
                icon="line-md:loading-twotone-loop"></iconify-icon>
            <input type="text" class="form-control !pl-9 mr-1 basis-1/4" placeholder="Search here..."
                wire:model.live.debounce.400ms="searchTerm">
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
                                @if ($selectedAllCombos)
                                    <th colspan="4" class="table-th"><iconify-icon style="vertical-align: top;"
                                            icon="lucide:info" width="1.2em" height="1.2em"></iconify-icon> A
                                        {{ count($selectedCombos) }} combo selected ..
                                        <span class="clickable-link" wire:click='undoSelectAllCombos'>Undo</span>
                                    </th>
                                @else
                                    <th colspan="4" class="table-th"><iconify-icon style="vertical-align: top;"
                                            icon="lucide:info" width="1.2em" height="1.2em"></iconify-icon>
                                        {{ count($selectedCombos) }} combo
                                        selected .. <span class="clickable-link" wire:click='selectAllCombos'>Select
                                            All Combos</span></th>
                                @endif
                            @else
                                <th scope="col" class="table-th">
                                   
                                        Price
                                       
                                </th>

                                <th scope="col" class="table-th">
                                    <span wire:click="sortByColomn('product_count')" class="clickable-header">
                                        Products
                                        @if ($sortColomn === 'product_count')
                                            @if ($sortDirection === 'asc')
                                                <iconify-icon icon="fluent:arrow-up-12-filled"></iconify-icon>
                                            @else
                                                <iconify-icon icon="fluent:arrow-down-12-filled"></iconify-icon>
                                            @endif
                                        @endif
                                    </span>
                                </th>

                            @endif

                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">

                        @foreach ($combos as $combo)
                            <tr>

                                <td class="table-td flex items-center sticky-column bg-white dark:bg-slate-800 colomn-shadow"
                                    style="position: sticky; left: -25px;  z-index: 10;">
                                    <div class="checkbox-area">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" wire:model="selectedCombos"
                                                value="{{ $combo->id }}" class="hidden" id="select-all">
                                            <span
                                                class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                <img src="assets/images/icon/ck-white.svg" alt=""
                                                    class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                        </label>
                                    </div>
                                    <a href="{{ route('combo.show', $combo->id) }}"> <span class="hover-underline">
                                            <b>
                                                {{ $combo->name }}
                                            </b>
                                        </span>
                                    </a>

                                </td>

                                <td class="table-td">
                                    <b>{{ number_format($combo->total_price) }}</b>&nbsp;<small>EGP</small>
                                </td>

                                <td class="table-td">
                                    <b>{{ $combo->products->count() }}</b>&nbsp;<small>Product{{ $combo->products->count() !== 1 ? 's' : '' }}</small>
                                </td>


                            </tr>
                        @endforeach

                    </tbody>

                </table>


                @if ($combos->isEmpty())
                    {{-- START: empty filter result --}}
                    <div class="card m-5 p-5">
                        <div class="card-body rounded-md bg-white dark:bg-slate-800">
                            <div class="items-center text-center p-5">
                                <h2>
                                    <iconify-icon icon="icon-park-outline:search"></iconify-icon>
                                </h2>
                                <h2 class="card-title text-slate-900 dark:text-white mb-3">No combos with the
                                    applied
                                    filters</h2>
                                <p class="card-text">Try changing the filters or search terms for this view.
                                </p>
                                <a href="{{ url('/combos') }}"
                                    class="btn inline-flex justify-center mx-2 mt-3 btn-primary active btn-sm">View
                                    all combos</a>
                            </div>
                        </div>
                    </div>
                    {{-- END: empty filter result --}}
                @endif


            </div>


        </div>
        <div style="position: sticky ; bottom:0;width:100%; z-index:10;"
            class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
            {{ $combos->links('vendor.livewire.simple-bootstrap') }}
        </div>

    </div>


    @if ($newComboSection)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Create new combo
                            </h3>
                            <button wire:click="closeNewComboSec" type="button"
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
                            <div class="form-group">
                                <div class="input-area">
                                    <label for="comboName" class="form-label">Combo Name*</label>
                                    <input id="comboName" type="text"
                                        class="form-control @error('comboName') !border-danger-500 @enderror"
                                        wire:model.lazy="comboName" autocomplete="off">
                                </div>
                                @error('comboName')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                @error('productQuantities')
                                    <div
                                        class="py-[18px] px-6 font-normal text-sm rounded-md bg-white text-danger-500 border border-danger-500
                                    dark:bg-slate-800 mb-3">
                                    <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                        <div class="flex-shrink-0">
                                            <iconify-icon icon="ant-design:alert-outlined" width="1.2em" height="1.2em"></iconify-icon>
                                        </div>
                                        <div class="flex-1 font-Inter">
                                            {{ $message }}
                                        </div>
                                    </div>
                                    
                                    </div>
                                @enderror
                                <div class="space-y-4">
                                    <div class="flex items-center justify-around space-x-2">
                                        <p>Product</p>
                                        <p>Quantity</p>
                                        <p>Price</p>
                                        @if (count($productQuantities) > 1)
                                                <button class="action-btn2"  style="border:0" type="button">
                                                </button>
                                            @endif
                                    </div>
                                    @foreach ($productQuantities as $index => $quantity)
                                    
                                        <div class="flex items-center space-x-2">
                                            <select wire:model="productQuantities.{{ $index }}.product_id"
                                                class="form-control @error('productQuantities.' . $index . '.product_id') !border-danger-500 @enderror">
                                                <option value="">Select Product</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}">
                                                        {{ ucwords($product->name) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="number"
                                                wire:model="productQuantities.{{ $index }}.quantity"
                                                class="form-control @error('productQuantities.' . $index . '.quantity') !border-danger-500 @enderror"
                                                placeholder="Quantity" min="1">

                                                <input type="number"
                                                wire:model="productQuantities.{{ $index }}.price"
                                                class="form-control @error('productQuantities.' . $index . '.price') !border-danger-500 @enderror"
                                                placeholder="Price" min="1">
                                            @if (count($productQuantities) > 1)
                                                <button class="action-btn2" type="button"
                                                    wire:click="removeProduct({{ $index }})">
                                                    <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                </button>
                                            @endif
                                        </div>
                                        @error('productQuantities.' . $index . '.product_id')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                        @error('productQuantities.' . $index . '.quantity')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                        @error('productQuantities.' . $index . '.price')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    @endforeach

                                </div>
                                <button wire:click="addProduct"
                                    class="btn inline-flex justify-center btn-outline-dark mt-5 btn-sm"><iconify-icon
                                        icon="material-symbols:add" width="1.2em" height="1.2em"></iconify-icon> Add
                                    Product</button>
                            </div>
                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="addNewCombo" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="addNewCombo">Submit</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="addNewCombo"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif




</div>
