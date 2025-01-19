<div>
    <p>Remaining to pay {{ $invoice->remaining_to_pay }}</p>
    <div class="space-y-5 profile-page mx-auto" style="max-width: 1000px">
        <div class="card mb-5">
            <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base">
                <div class="items-center p-5">
                    <header
                        class="flex mb-5 items-center border-b border-slate-100 dark:border-slate-700 pb-5  -mx-6 px-6">
                        <div class="flex-1">
                            <div class="card-title text-slate-900 dark:text-white flex items-center">
                                <iconify-icon icon="stash:invoice" width="25" height="25"
                                    class="mr-2"></iconify-icon>
                                Invoice
                            </div>
                            @if ($invoice->title)
                                <h5><b>{{ ucwords($invoice->title) }}</b></h5>
                            @endif

                        </div>
                        @if ($invoice->code)
                            <span class="badge bg-slate-900 text-white capitalize">{{ $invoice->code }}</span>
                        @endif
                    </header>

                    <header
                        class="flex mb-2 items-center border-b border-slate-100 dark:border-slate-700 pb-5  -mx-6 px-6">
                        <div class="flex-1">
                            <div class="text-xs text-slate-500 dark:text-slate-400 flex items-center mb-3 ">
                                <iconify-icon icon="clarity:user-solid-badged" width="15" height="15"
                                    class="mr-1"></iconify-icon>
                                Supplier
                            </div>
                            <a href="">
                                <h6 class="hover-underline cursor-pointer">
                                    <b>{{ ucwords($invoice->supplier->name) }}</b>
                                </h6>
                            </a>

                            <p class="text-xs">{{ $invoice->supplier->phone1 }}</p>

                        </div>
                    </header>

                    <div>
                        <div class="overflow-x-auto ">
                            <div class="inline-block min-w-full align-middle">
                                <div class="overflow-hidden ">
                                    <table
                                        class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                        <thead class="">
                                            <tr>
                                                <th scope="col" class=" table-th ">
                                                    Raw Material
                                                </th>
                                                <th scope="col" class=" table-th ">
                                                    Qty
                                                </th>
                                                <th scope="col" class=" table-th ">
                                                    Price
                                                </th>
                                                <th scope="col" class=" table-th ">
                                                    Total
                                                </th>
                                                <th scope="col" class=" table-th ">
                                                    Action
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-slate-800 ">
                                            @foreach ($invoice->rawMaterials as $rawMaterial)
                                                <tr>
                                                    <td class="table-td ">{{ $rawMaterial->name }}</td>
                                                    <td class="table-td ">x {{ $rawMaterial->pivot->quantity }}</td>
                                                    <td class="table-td ">{{ $rawMaterial->pivot->price }}
                                                        <small>EGP</small>
                                                    </td>
                                                    <td class="table-td ">
                                                        {{ number_format($rawMaterial->pivot->quantity * $rawMaterial->pivot->price, 2) }}
                                                        <small>EGP</small>
                                                    </td>
                                                    <td class="table-td ">
                                                        <div class="flex space-x-3 rtl:space-x-reverse">
                                                            <button
                                                                wire:click='openReturnRawMaterialQtyModal({{ $rawMaterial->id }})'
                                                                class="action-btn" type="button">
                                                                <iconify-icon
                                                                    icon="heroicons:arrow-uturn-left"></iconify-icon>
                                                            </button>
                                                            <button
                                                                wire:click='openReturnRawMaterialModal({{ $rawMaterial->id }})'
                                                                class="action-btn" type="button">
                                                                <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                            </button>
                                                        </div>
                                                    </td>

                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="5" class="table-td">
                                                    <button wire:click="openAddRawMaterialModal"
                                                        class="btn inline-flex items-center justify-center text-dark bg-slate-100 btn-sm mb-3">
                                                        <iconify-icon icon="heroicons:plus"
                                                            class="mr-2"></iconify-icon> Add Raw Material
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot class="mt-3">
                                            <tr>
                                                <th class="table-th">
                                                    Total
                                                </th>
                                                <th colspan="2" class="table-th">
                                                    <span
                                                        class="text-lg text-success-500">{{ $invoice->rawMaterials->sum('pivot.quantity') }}
                                                        <small>{{ $invoice->rawMaterials->sum('pivot.quantity') > 1 ? 'items' : 'item' }}</small>
                                                    </span>
                                                </th>
                                                <th scope="col" class="table-th">
                                                    <span
                                                        class="text-lg text-success-500">{{ number_format($invoice->total_amount, 2) }}
                                                        <small>EGP</small> </span>
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @if ($returnedRawMateralId)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-danger-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Remove Raw Material
                            </h3>
                            <button wire:click="closeReturnRawMaterialModal" type="button"
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
                            Are you sure you want to remove this raw material?
                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="returnRawMaterial" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-danger-500">
                                <span wire:loading.remove wire:target="returnRawMaterial">Confirm</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="returnRawMaterial"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    @endif

    @if ($addRawmaterialModal)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog"
            style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-slate-900">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Add Raw Material
                            </h3>
                            <button wire:click="closeAddRawMaterialModal" type="button"
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
                            @if ($errors->has('selectedRawMaterial'))
                                <div class="text-danger-500 text-sm">
                                    {{ $errors->first('selectedRawMaterial') }}
                                </div>
                            @endif

                            <div class="text-slate-500 text-xs">
                                * If selected raw material already exists in this invoice. The price will be updated,
                                and the quantity will be incremented accordingly.
                            </div>
                            <div>
                                <label for="searchRawMaterialText"
                                    class="block text-sm font-medium text-gray-700">Search Raw Material</label>
                                <input wire:model.live='searchRawMaterialText' type="text"
                                    name="searchRawMaterialText" placeholder="Search raw material..."
                                    class="form-control @error('searchRawMaterialText') !border-danger-500 @enderror">
                            </div>
                            <div class="overflow-x-auto">

                                @forelse ($rawMaterials as $rawMaterial)
                                    <div class="basicRadio">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" class="hidden" wire:model="selectedRawMaterial"
                                                name="selectedRawMaterial" value="{{ $rawMaterial->id }}">
                                            <span
                                                class="flex-none bg-white dark:bg-slate-500 rounded-full border inline-flex ltr:mr-2 rtl:ml-2 relative transition-all
                                                duration-150 h-[16px] w-[16px] border-slate-400 dark:border-slate-600 dark:ring-slate-700"></span>
                                            <span
                                                class="text-secondary-500 text-sm leading-6 capitalize">{{ $rawMaterial->name }}</span>
                                        </label>
                                    </div>
                                @empty
                                    <div class="p-6 text-center text-slate-500 dark:text-slate-400">
                                        No raw material found
                                    </div>
                                @endforelse

                            </div>
                            <div class="flex space-x-4">
                                <div class="flex-1">
                                    <label for="quantity"
                                        class="block text-sm font-medium text-gray-700">Quantity</label>
                                    <input wire:model='quantity' type="number" name="quantity"
                                        placeholder="Enter quantity..."
                                        class="form-control @error('quantity') !border-danger-500 @enderror">
                                </div>

                                <div class="flex-1">
                                    <label for="price"
                                        class="block text-sm font-medium text-gray-700">Price/item</label>
                                    <input wire:model='price' type="number" name="price"
                                        placeholder="Enter price..."
                                        class="form-control @error('price') !border-danger-500 @enderror">
                                </div>
                            </div>
                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="addRawMaterial" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-slate-900">
                                <span wire:loading.remove wire:target="addRawMaterial">Add</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="addRawMaterial"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($returnedRawMaterial)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog"
            style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-slate-900">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Return {{ $returnedRawMaterial->rawMaterial->name }}
                            </h3>
                            <button wire:click="closeReturnRawMaterialQtyModal" type="button"
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
                            @if ($errors->has('returnedRawMaterialQty'))
                                <div class="text-danger-500 text-sm">
                                    {{ $errors->first('returnedRawMaterialQty') }}
                                </div>
                            @endif

                            <div class="input-area">
                                <div class="relative">
                                    <input type="text" wire:model='returnedRawMaterialQty'
                                        class="form-control !pr-32" placeholder="Returned Quantity..." min="0"
                                        max="{{ $returnedRawMaterial->quantity }}">
                                    <span
                                        class="absolute right-0 top-1/2 px-3 -translate-y-1/2 h-full border-none flex items-center justify-center"
                                        style="--tw-translate-y: -65% !important; --tw-translate-x: -40%;">
                                        / {{ $returnedRawMaterial->quantity }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="returnRawMaterialQty" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-slate-900">
                                <span wire:loading.remove wire:target="returnRawMaterialQty">Add</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="returnRawMaterialQty"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
