<div>
    <div class="space-y-5 profile-page mx-auto" style="max-width: 1000px">

        <div class="flex justify-between">
            <div>
                <h3
                    class=" text-slate-900 dark:text-white {{ preg_match('/[\p{Arabic}]/u', $combo->name) ? 'text-right' : 'text-left' }}">
                    <b>{{ $combo->name }}</b>
                </h3>
            </div>

            @can('updteProducts', $combo)
                <div>
                    <button class="btn inline-flex justify-center btn-dark btn-sm"
                        wire:click='openEditComboSec'>Edit</button>
                </div>
            @endcan
        </div>

        <div class="card active relative mt-5 p-3  overflow-x-auto">
            <div class="my-2 flex justify-between items-center">
                <h6 class="mb-0">Products</h6>
                @can('updteProducts', $combo)
                    <button wire:click='openAddProductSec' class="btn inline-flex justify-center btn-outline-light btn-sm">
                        <iconify-icon icon="ic:baseline-plus" width="1.2em" height="1.2em"></iconify-icon>Add
                        Product
                    </button>
                @endcan
            </div>

            <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700 no-wrap">
                <thead class=" border-t border-slate-100 dark:border-slate-800">
                    <tr>

                        <th scope="col" class=" table-th ">
                            #
                        </th>

                        <th scope="col" class=" table-th ">
                            Name
                        </th>

                        <th scope="col" class=" table-th ">
                            Quantity
                        </th>

                        <th scope="col" class=" table-th ">
                            Price
                        </th>

                        <th scope="col" class=" table-th ">

                        </th>

                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                    @foreach ($combo->products as $index => $product)
                        <tr>
                            <td class="table-td">{{ $index+1 }}</td>

                            <td class="table-td">
                                <div class="flex items-center">
                                    <div class="flex-1 text-start">
                                        <h5 class=" whitespace-nowrap mb-1">
                                            {{ $product->name }}
                                        </h5>
                                        <div class="text-xs font-normal text-slate-600 dark:text-slate-400">
                                            Weight: {{ $product->weight }} grams
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="table-td">x {{ $product->pivot->quantity }}</td>

                            <td class="table-td ">
                                <div class="flex items-center">
                                    <div class="flex-1 text-start">
                                        <h6 class=" whitespace-nowrap mb-1">
                                            {{ number_format($product->pivot->price) }} <span class="text-sm">EGP</span>
                                        </h6>
                                        <div class="text-xs font-normal text-slate-600 dark:text-slate-400">
                                            Original Price: {{ number_format($product->price) }} EGP
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="table-td ">
                                @if ($combo->products->count() > 1)
                                    @can('updteProducts', $combo)
                                        <button wire:click='removeProduct({{ $product->id }})'
                                            wire:confirm='Are you sure you want to remove this product?' class="action-btn"
                                            type="button">
                                            <iconify-icon icon="heroicons:trash"></iconify-icon>
                                        </button>
                                    @endcan
                                @endif
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>

    </div>

    @can('update', $combo)
        @if ($editComboSec)
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
                                    Edit Combo
                                </h3>
                                <button wire:click="closeEditComboSec" type="button"
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

                                <div class="from-group">
                                    <div class="input-area">
                                        <label for="comboName" class="form-label">Name*</label>
                                        <input id="comboName" type="text"
                                            class="form-control @error('comboName') !border-danger-500 @enderror"
                                            wire:model="comboName" autocomplete="off">
                                        @error('comboName')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>


                            </div>

                            <!-- Modal footer -->
                            <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                                <button wire:click="editCombo" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="editCombo">Submit</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="editCombo"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
        @endif
    @endcan


    @can('updteProducts', $combo)
        @if ($newProductSec)
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
                                    Add Product
                                </h3>
                                <button wire:click="closeAddProductSec" type="button"
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

                                <div class="from-group">
                                    <div class="input-area">
                                        <label for="product_id" class="form-label">Product*</label>
                                        <select name="product_id" id="product_id"
                                            class="form-control w-full mt-2 @error('product_id') !border-danger-500 @enderror"
                                            wire:model.lazy="product_id" autocomplete="off">
                                            <option value="">Select Product</option>
                                            @foreach ($all_products as $single_products)
                                                <option value="{{ $single_products->id }}">
                                                    {{ ucwords($single_products->name) }}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                    @error('product_id')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                    <small class="text-gray-500">* If the product already exists, it will be updated
                                        automatically.</small>
                                </div>

                                <div class="from-group">
                                    <div class="input-area">
                                        <label for="productQuantity" class="form-label">Quantity*</label>
                                        <input id="productQuantity" type="number"
                                            class="form-control @error('productQuantity') !border-danger-500 @enderror"
                                            wire:model="productQuantity" autocomplete="off">
                                        @error('productQuantity')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                        <small class="text-gray-500">Enter the quantity of the product in this
                                            combo.</small>
                                    </div>
                                </div>

                                <div class="from-group">
                                    <div class="input-area">
                                        <label for="productPrice" class="form-label">Price*</label>
                                        <input id="productPrice" type="number"
                                            class="form-control @error('productPrice') !border-danger-500 @enderror"
                                            wire:model="productPrice" autocomplete="off">
                                        @error('productPrice')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                        <small class="text-gray-500">Enter the price of the product in this combo.</small>
                                    </div>
                                </div>


                            </div>

                            <!-- Modal footer -->
                            <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                                <button wire:click="addProduct" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="addProduct">Submit</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="addProduct"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
        @endif
    @endcan
</div>
