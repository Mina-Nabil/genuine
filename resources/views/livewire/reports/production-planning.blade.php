<div>

    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Production Planning
            </h4>
        </div>
    </div>
    <div class="card">
        <header class="card-header cust-card-header noborder">
            <iconify-icon wire:loading wire:target="searchTerm" class="loading-icon text-lg"
                icon="line-md:loading-twotone-loop"></iconify-icon>
            <input type="text" class="form-control !pl-9 mr-1 basis-1/4" placeholder="Search here..."
                wire:model.live.debounce.400ms="searchTerm">

            
        </header>

        <header class="card-header cust-card-header noborder pt-0">
        <div class="input-area">
            <input id="deliveryDate" type="date"
                class="form-control @error('deliveryDate') !border-danger-500 @enderror"
                wire:model.live="deliveryDate" autocomplete="off">
        </div>
        </header>



        <div class="card-body px-6 pb-6  overflow-x-auto">
            <div class=""> <!-- Add this wrapper to allow horizontal scroll -->
                <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700 "
                    style="display: none">
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
                                Name
                            </th>
                            @if ($selectAll)
                                @if ($selectedAllProducts)
                                    <th colspan="4" class="table-th"><iconify-icon style="vertical-align: top;"
                                            icon="lucide:info" width="1.2em" height="1.2em"></iconify-icon> A
                                        {{ count($selectedProducts) }} product selected ..
                                        <span class="clickable-link" wire:click='undoSelectAllProducts'>Undo</span>
                                    </th>
                                @else
                                    <th colspan="4" class="table-th"><iconify-icon style="vertical-align: top;"
                                            icon="lucide:info" width="1.2em" height="1.2em"></iconify-icon>
                                        {{ count($selectedProducts) }} product
                                        selected .. <span class="clickable-link" wire:click='selectAllProducts'>Select
                                            All Products</span></th>
                                @endif
                            @else
                                <th scope="col" class="table-th">On Hand</th>

                                <th scope="col" class="table-th">
                                    <span wire:click="sortByColomn('weight')" class="clickable-header">
                                        Committed
                                        @if ($sortColomn === 'weight')
                                            @if ($sortDirection === 'asc')
                                                <iconify-icon icon="fluent:arrow-up-12-filled"></iconify-icon>
                                            @else
                                                <iconify-icon icon="fluent:arrow-down-12-filled"></iconify-icon>
                                            @endif
                                        @endif
                                    </span>

                                </th>

                                <th scope="col" class="table-th">
                                    <span wire:click="sortByColomn('weight')" class="clickable-header">
                                        Available
                                        @if ($sortColomn === 'weight')
                                            @if ($sortDirection === 'asc')
                                                <iconify-icon icon="fluent:arrow-up-12-filled"></iconify-icon>
                                            @else
                                                <iconify-icon icon="fluent:arrow-down-12-filled"></iconify-icon>
                                            @endif
                                        @endif
                                    </span>

                                </th>

                                {{-- <th scope="col" class="table-th">
                                    <span wire:click="sortByColomn('weight')" class="clickable-header">
                                        Current Stock
                                        @if ($sortColomn === 'weight')
                                            @if ($sortDirection === 'asc')
                                                <iconify-icon icon="fluent:arrow-up-12-filled"></iconify-icon>
                                            @else
                                                <iconify-icon icon="fluent:arrow-down-12-filled"></iconify-icon>
                                            @endif
                                        @endif
                                    </span>
                                    
                                </th> --}}

                                <th scope="col" class="table-th">
                                    <span wire:click="sortByColomn('weight')" class="clickable-header">
                                        Required Stock
                                        @if ($sortColomn === 'weight')
                                            @if ($sortDirection === 'asc')
                                                <iconify-icon icon="fluent:arrow-up-12-filled"></iconify-icon>
                                            @else
                                                <iconify-icon icon="fluent:arrow-down-12-filled"></iconify-icon>
                                            @endif
                                        @endif
                                    </span>

                                </th>

                                <th scope="col" class="table-th">
                                    <span wire:click="sortByColomn('weight')" class="clickable-header">
                                        Production Required
                                        @if ($sortColomn === 'weight')
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

                        @foreach ($orderProducts as $product)
                            <tr>

                                <td class="table-td flex items-center sticky-column bg-white dark:bg-slate-800 colomn-shadow"
                                    style="position: sticky; left: -25px;  z-index: 10;">
                                    <div class="checkbox-area">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" wire:model="selectedProducts"
                                                value="{{ $product->id }}" class="hidden" id="select-all">
                                            <span
                                                class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                <img src="assets/images/icon/ck-white.svg" alt=""
                                                    class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                        </label>
                                    </div>
                                    <a href="{{ route('product.show', $product->product->id) }}"> <span
                                            class="hover-underline">
                                            <b>
                                                {{ $product->product_name }}
                                            </b>
                                        </span>
                                    </a>

                                </td>

                                <td class="table-td">
                                    {{ $product->on_hand }}
                                </td>

                                <td class="table-td">
                                    {{ $product->committed }}
                                </td>


                                <td class="table-td">
                                    {{ $product->available }}
                                </td>

                                {{-- <td class="table-td">
                                    {{ $product->current_stock   }}
                                </td> --}}

                                <td class="table-td">
                                    {{ $product->required_stock }}
                                </td>

                                <td class="table-td">
                                    {{ $product->production_required }}
                                </td>

                            </tr>
                        @endforeach

                    </tbody>

                </table>


                @foreach ($orderProducts as $product)
                    <div class="card-body flex flex-col justify-between border rounded-lg h-full menu-open p-0 mb-5"
                        style="border-color:rgb(224, 224, 224)">
                        <div class="break-words flex items-center my-1 m-4">

                            <h5 class="capitalize py-3 flex">
                                <div class="checkbox-area">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" wire:model="selectedProducts"
                                            value="{{ $product->id }}" class="hidden" id="select-all">
                                        <span
                                            class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                            <img src="assets/images/icon/ck-white.svg" alt=""
                                                class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                    </label>
                                </div>
                                <a href="{{ route('product.show', $product->product->id) }}"> <span
                                        class="hover-underline">
                                        <b>
                                            {{ $product->product_name }}
                                        </b>
                                    </span>
                                </a>
                                {{-- {{ $product->product_name }} --}}
                            </h5>

                            {{-- <div class="ml-auto">
                                <div class="relative">
                                    <div class="dropdown relative">
                                        <button class="text-xl text-center block w-full " type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <iconify-icon icon="heroicons-outline:dots-vertical"></iconify-icon>
                                        </button>
                                        <ul
                                            class=" dropdown-menu min-w-[120px] absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">
                                            <li>
                                            <li>
                                                <button wire:click="removePet({{ $pet->id }})"
                                                    class="text-slate-600 dark:text-white block font-Inter text-left font-normal w-full px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white">
                                                    Remove</button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div> --}}

                        </div>
                        <hr><br>
                        <div class="grid grid-cols-5 lg:grid-cols-5 mb-4 text-center">


                            <div class="border-r ml-5 col-span-4 md:col-span-1 mb-3 md:mb-0">
                                @if ($product->production_required < 0)
                                    <span class="badge bg-success-500 text-dark-500 bg-opacity-50 capitalize">
                                        <iconify-icon icon="icon-park-outline:dot" width="1.2em"
                                            height="1.2em"></iconify-icon>
                                        Sufficient Quantity
                                    </span>
                                @else
                                    <h5>{{ $product->production_required }}</h5>
                                    <p class="text-sm mt-2">
                                        <span class="badge bg-warning-500 text-dark-500 bg-opacity-50 capitalize">
                                            <iconify-icon icon="octicon:dot-16" width="1.2em"
                                                height="1.2em"></iconify-icon>
                                            Production Required
                                        </span>
                                    </p>
                                @endif

                            </div>

                            <div class="border-r ">
                                <h6>{{ $product->on_hand }}</h6>
                                <p class="text-sm">On Hand</p>
                            </div>

                            <div class="border-r">
                                <h6>{{ $product->committed }}</h6>
                                <p class="text-sm">Committed</p>
                            </div>

                            <div class="border-r">
                                <h6>{{ $product->available }}</h6>
                                <p class="text-sm">Available</p>
                            </div>

                            <div class="">

                                <h6>{{ $product->required_stock }}</h6>
                                <p class="text-sm">Required Stock</p>

                            </div>



                        </div>
                    </div>
                @endforeach


                @if ($orderProducts->isEmpty())
                    {{-- START: empty filter result --}}
                    <div class="card m-5 p-5">
                        <div class="card-body rounded-md bg-white dark:bg-slate-800">
                            <div class="items-center text-center p-5">
                                <h2>
                                    <iconify-icon icon="icon-park-outline:search"></iconify-icon>
                                </h2>
                                <h2 class="card-title text-slate-900 dark:text-white mb-3">No products with the
                                    applied
                                    filters</h2>
                                <p class="card-text">Try changing the filters or search terms for this view.
                                </p>
                                <a href="{{ url('/products') }}"
                                    class="btn inline-flex justify-center mx-2 mt-3 btn-primary active btn-sm">View
                                    all products</a>
                            </div>
                        </div>
                    </div>
                    {{-- END: empty filter result --}}
                @endif


            </div>


        </div>
        <div style="position: sticky ; bottom:0;width:100%; z-index:10;"
            class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
            {{ $orderProducts->links('vendor.livewire.simple-bootstrap') }}
        </div>

    </div>
</div>
