<div>
    <div class="space-y-5 profile-page mx-auto" style="max-width: 1000px">
        <div class="flex justify-between">
            <h4>
                <b>Create Invoice</b>
                <span wire:loading>
                    <iconify-icon icon="line-md:loading-twotone-loop" class="text-xl spin-slow"></iconify-icon>
                </span>
            </h4>

            <button wire:click='addInvoice' class="btn inline-flex justify-center btn-dark btn-sm">
                Save
            </button>
        </div>

        @if ($errors->any())
            <div
                class="py-[18px] px-6 font-normal text-sm rounded-md bg-white text-danger-500 border border-danger-500 dark:bg-slate-800">
                <div class="flex items-start space-x-3 rtl:space-x-reverse">
                    <div class="flex-1 font-Inter">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-6 gap-5 mb-5 text-wrap">
            <div class="col-span-4">
                <div class="col-span-2">
                    {{-- Info --}}
                    <div class="card mb-5">
                        <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base">

                            <div class="items-center p-5">
                                <div class="mb-2">
                                    <label for="invoiceCode" class="form-label !m-0">Invoice Code</label>
                                    <input wire:model='invoiceCode' type="text" name="invoiceCode"
                                        placeholder="Enter invoice code..."
                                        class="form-control @error('invoiceCode') !border-danger-500 @enderror">
                                    @error('invoiceCode')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>



                        </div>
                    </div>



                    @if ($supplierId)
                        {{-- Selected Supplier --}}
                        <div class="card mb-5">
                            <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base">
                                <div class="items-center p-5">
                                    <div class="input-area w-full">
                                        <label for="ddate" class="form-label"><b>Supplier</b></label>
                                        <div class="flex justify-between">
                                            <p class="text-lg">{{ $supplierName }}</p>
                                            <button wire:click='clearSupplier' class="action-btn" type="button">
                                                <iconify-icon icon="heroicons:trash"></iconify-icon>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Materials --}}
                        <div class="card mb-5">
                            <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base no-wrap">
                                <div class="items-center p-5">
                                    <div class="flex justify-between items-end space-x-6 mb-3">
                                        <div class="input-area w-full">
                                            <label for="dummySearch" class="form-label"><b>Raw Materials</b></label>
                                            <input id="dummySearch" type="text" class="form-control"
                                                wire:click='openMaterialsSection' wire:model.live='dummyMaterialsSearch'
                                                placeholder="Search raw materials..." autocomplete="off">
                                        </div>
                                    </div>

                                    @if (!empty($fetchedMaterials))
                                        <div class="card-body px-6 pb-6 mt-2">
                                            <div class="overflow-x-auto -mx-6">
                                                <div class="inline-block min-w-full align-middle">
                                                    <div class="overflow-hidden ">


                                                        <table
                                                            class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                                            <thead
                                                                class="border-t border-slate-100 dark:border-slate-800">
                                                                <tr>
                                                                    <th scope="col" class="table-th imp-p-2">Raw
                                                                        Material</th>
                                                                    <th scope="col" class="table-th imp-p-2">
                                                                        Quantity</th>
                                                                    <th scope="col" class="table-th imp-p-2">
                                                                        Price/item</th>
                                                                    <th scope="col" class="table-th imp-p-2">
                                                                        Total</th>
                                                                    <th scope="col" class="table-th imp-p-2">
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody
                                                                class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                                                @foreach ($fetchedMaterials as $index => $material)
                                                                    <tr>
                                                                        <td class="table-td imp-p-2">
                                                                            <div class="flex-1 text-start">
                                                                                <div class="text-start overflow-hidden text-ellipsis whitespace-nowrap"
                                                                                    style="max-width:200px;">
                                                                                    <h6
                                                                                        class="text-slate-600 dark:text-slate-300 overflow-hidden text-ellipsis whitespace-nowrap py-2">
                                                                                        {{ $material['name'] }}
                                                                                    </h6>
                                                                                </div>
                                                                                @error('fetchedMaterials.' . $index .
                                                                                    '.quantity')
                                                                                    <span
                                                                                        class="font-Inter text-xs text-danger-500 inline-block">{{ $message }}</span>
                                                                                @enderror
                                                                            </div>
                                                                        </td>

                                                                        <!-- Quantity Input Column -->
                                                                        <td class="table-td imp-p-2">
                                                                            <input type="number" min="1"
                                                                                class="form-control @error('fetchedMaterials.' . $index . '.quantity') !border-danger-500 @enderror"
                                                                                style="width: 100px;"
                                                                                wire:model="fetchedMaterials.{{ $index }}.quantity"
                                                                                wire:input="updateTotal({{ $index }})">

                                                                        </td>

                                                                        <!-- Price Input Column -->
                                                                        <td class="table-td imp-p-2">
                                                                            <input type="number" min="1" max="fetchedMaterials.{{ $index }}.max_price"
                                                                            class="form-control @error('fetchedMaterials.' . $index . '.price') !border-danger-500 @enderror"
                                                                            style="width: 100px;"
                                                                            wire:model="fetchedMaterials.{{ $index }}.price"
                                                                            wire:input="updateTotal({{ $index }})">
                                                                            {{-- {{ $fetchedMaterials[$index]['price'] }} --}}
                                                                        </td>

                                                                        <!-- Total Calculation Column -->
                                                                        <td class="table-td imp-p-2">
                                                                            @if ($material['quantity'] && $material['price'])
                                                                                <span>{{ number_format($material['quantity'] * $material['price'], 2) }}
                                                                                    <small>EGP</small> </span>
                                                                            @else
                                                                                <span>0.00
                                                                                    <small>EGP</small> </span>
                                                                            @endif

                                                                        </td>
                                                                        <td class="table-td imp-p-2">
                                                                            <button class="action-btn" type="button"
                                                                                wire:click="removeMaterial({{ $index }})">
                                                                                <iconify-icon
                                                                                    icon="heroicons:trash"></iconify-icon>
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>


                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Payments --}}
                        <div class="card mb-5">
                            <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base">
                                <div class="items-center p-5">
                                    <div class="input-area w-full">
                                        <label for="ddate" class="form-label"><b>Payments</b></label>
                                    </div>

                                    <div class="card-body flex flex-col justify-between border rounded-lg h-full menu-open p-0 mb-5 p-2 px-6"
                                        style="border-color:rgb(224, 224, 224);">

                                        <table
                                            class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                            <tbody class="bg-white dark:bg-slate-800 ">

                                                <tr>
                                                    <td class=" text-xs text-slate-500 dark:text-slate-400">Subtotal
                                                    </td>
                                                    <td class=" text-xs text-slate-500 dark:text-slate-400">
                                                        {{ $totalItems ? $totalItems . ' items' : '-' }}</td>
                                                    <td class="float-right text-dark">
                                                        <b>{{ $subtotal ? number_format($subtotal, 2) : '-' }}<small>&nbsp;EGP</small></b>
                                                    </td>
                                                </tr>

                                                @if($extraFeeAmount)
                                                <tr>
                                                    <td class=" text-xs text-slate-500 dark:text-slate-400">Extra Fees
                                                    </td>
                                                    <td class=" text-xs text-slate-500 dark:text-slate-400"></td>
                                                    <td class="float-right text-dark">
                                                        <b>{{ number_format($extraFeeAmount, 2) }}<small>&nbsp;EGP</small></b>
                                                    </td>
                                                </tr>
                                                @endif

                                                <tr>
                                                    <td class=" text-xs text-slate-500 dark:text-slate-400"></td>
                                                    <td class=" text-xs text-slate-500 dark:text-slate-400"></td>
                                                    <td class="float-right text-dark"></td>
                                                </tr>
                                                <tr>
                                                    <td class=" text-xs text-slate-500 dark:text-slate-400"></td>
                                                    <td class=" text-xs text-slate-500 dark:text-slate-400"></td>
                                                    <td class="float-right text-dark"></td>
                                                </tr>

                                                <tr class="!pt-5">
                                                    <td class=" text-xs text-slate-500 dark:text-slate-400">Total
                                                    </td>
                                                    <td class=" text-xs text-slate-500 dark:text-slate-400"></td>
                                                    <td class="float-right text-dark" style="color: black">
                                                        <b>{{ $total ? number_format($total, 2) : '-' }}<small>&nbsp;EGP</small></b>
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Supplier --}}
                        <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base">
                            <div class="items-center p-5">
                                <div class="input-area w-full mb-1">
                                    <label for="ddate" class="form-label"><b>Supplier</b></label>
                                    <p class="text-xs">* Choose a supplier to begin adding raw materials..</p>
                                </div>

                                <input wire:model.live='suppliersSearchText' type="text" class="form-control mb-2"
                                    placeholder="Search supplier...">

                                <div class="overflow-x-auto"> <!-- Add this wrapper to allow horizontal scroll -->
                                    <table
                                        class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700 ">
                                        <thead
                                            class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                                            <tr>
                                                <th scope="col"
                                                    class="table-th  flex items-center border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700"
                                                    style="position: sticky; left: -25px;  z-index: 10;">
                                                    Name
                                                </th>

                                                <th scope="col" class="table-th">
                                                    Phone
                                                </th>


                                            </tr>
                                        </thead>
                                        <tbody
                                            class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">

                                            @foreach ($suppliers as $supplier)
                                                <tr wire:click='selectSupplier({{ $supplier->id }})'
                                                    class="hover:bg-slate-200 dark:hover:bg-slate-700 cursor-pointer">

                                                    <td class="table-td">
                                                        <b>{{ $supplier->name }}</b>
                                                    </td>

                                                    <td class="table-td">
                                                        {{ $supplier->phone1 }}
                                                    </td>

                                                </tr>
                                            @endforeach

                                        </tbody>

                                    </table>


                                    @if ($suppliers->isEmpty())
                                        {{-- START: empty filter result --}}
                                        <div class="card m-5 p-5">
                                            <div class="card-body rounded-md bg-white dark:bg-slate-800">
                                                <div class="items-center text-center p-5">
                                                    <h2>
                                                        <iconify-icon icon="icon-park-outline:search"></iconify-icon>
                                                    </h2>
                                                    <h2 class="card-title text-slate-900 dark:text-white mb-3">
                                                        No Suppliers Found!</h2>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- END: empty filter result --}}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-span-2">
                <div class="card mb-5">
                    <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base">
                        <div class="items-center p-5">
                            <div class="mb-5">
                                <label for="entry_date" class="form-label !m-0">Entry Date</label>
                                <input wire:model='entry_date' type="date" name="entry_date"
                                    class="form-control  @error('entry_date') !border-danger-500 @enderror">
                                @error('entry_date')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="">
                                <label for="payment_due" class="form-label !m-0">Payment Due</label>
                                <input wire:model='payment_due' type="date" name="payment_due"
                                    class="form-control  @error('payment_due') !border-danger-500 @enderror">
                                @error('payment_due')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-5">
                    <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base">
                        <div class="items-center p-5">
                            <div class="mb-5">
                                <label for="extraFeeDesc" class="form-label !m-0">Extra Fee Description</label>
                                <input wire:model='extraFeeDesc' type="text" name="extraFeeDesc"
                                    class="form-control  @error('extraFeeDesc') !border-danger-500 @enderror">
                                @error('extraFeeDesc')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="">
                                <label for="extraFeeAmount" class="form-label !m-0">Extra Fee Amount</label>
                                <input wire:model.live.debounce.400ms='extraFeeAmount' type="number" min="0" name="extraFeeAmount"
                                    class="form-control  @error('extraFeeAmount') !border-danger-500 @enderror">
                                @error('extraFeeAmount')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-5">
                    <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base">
                        <div class="items-center p-5">
                            <div class="mb-2">
                                <label for="note" class="form-label !m-0">Note</label>
                                <textarea wire:model='note' name="note" class="form-control  @error('note') !border-danger-500 @enderror"></textarea>
                                @error('note')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($isOpenSelectMaterialSec)
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
                                Add Raw Materials
                            </h3>
                            <button wire:click="closeMaterialsSection" type="button"
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
                            <input wire:model.live='materialsSearchText' type="text" class="form-control"
                                placeholder="Search raw materials...">

                            <div class="">
                                <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700 ">
                                    <thead
                                        class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                                        <tr>
                                            <th scope="col"
                                                class="table-th  flex items-center border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700"
                                                style="position: sticky; left: -25px;  z-index: 10;">
                                                Name
                                            </th>

                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">
                                        @foreach ($rawMaterials as $rawMaterial)
                                            <tr>
                                                <td class="table-td flex items-center sticky-column bg-white dark:bg-slate-800 colomn-shadow"
                                                    style="position: sticky; left: -25px;  z-index: 10;">
                                                    <div wire:key="{{ $rawMaterial->id }}" class="checkbox-area">
                                                        <label class="inline-flex items-center cursor-pointer">
                                                            <input type="checkbox" wire:model="selectedMaterials"
                                                                value="{{ $rawMaterial->id }}" class="hidden">
                                                            <span
                                                                class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                                <img src="{{ asset('assets/images/icon/ck-white.svg') }}"
                                                                    alt=""
                                                                    class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                                        </label>
                                                    </div>
                                                    <label>
                                                        <span>
                                                            <b>
                                                                {{ $rawMaterial->name }}
                                                            </b>
                                                        </span>
                                                    </label>

                                                </td>

                                            </tr>
                                        @endforeach

                                    </tbody>

                                </table>


                                @if ($rawMaterials->isEmpty())
                                    {{-- START: empty filter result --}}
                                    <div class="card m-5 p-5">
                                        <div class="card-body rounded-md bg-white dark:bg-slate-800">
                                            <div class="items-center text-center p-5">
                                                <h2>
                                                    <iconify-icon icon="icon-park-outline:search"></iconify-icon>
                                                </h2>
                                                <h2 class="card-title text-slate-900 dark:text-white mb-3">No
                                                    materials
                                                    with the
                                                    applied
                                                    filters</h2>
                                                <p class="card-text">Try changing the filters or search terms for
                                                    this
                                                    view.
                                                </p>
                                                <a href="{{ url('/materials') }}"
                                                    class="btn inline-flex justify-center mx-2 mt-3 btn-primary active btn-sm">View
                                                    all materials</a>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- END: empty filter result --}}
                                @endif


                            </div>

                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="addMaterials" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500 btn-sm">
                                <span wire:loading.remove wire:target="addMaterials">Add</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="addMaterials"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    @endif

</div>
