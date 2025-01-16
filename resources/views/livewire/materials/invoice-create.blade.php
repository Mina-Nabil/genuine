<div>
    <div class="space-y-5 profile-page mx-auto" style="max-width: 1000px">
        <div class="flex justify-between">
            <h4><b>Create Invoice</b></h4>
            <button wire:click='createOrder' class="btn inline-flex justify-center btn-dark btn-sm">
                Save
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-6 gap-5 mb-5 text-wrap">
            <div class="col-span-4">
                <div class="col-span-2">
                    <div class="card mb-5">
                        <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base">

                            <div class="items-center p-5">
                                <div class="mb-2">
                                    <label for="invoiceTitle" class="form-label !m-0">Invoice Title</label>
                                    <input wire:model='invoiceTitle' type="text" name="invoiceTitle"
                                        placeholder="Enter invoice title..."
                                        class="form-control @error('invoiceTitle') !border-danger-500 @enderror">
                                    @error('invoiceTitle')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

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

                            <div class="card mb-5">
                                <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base no-wrap">
                                    <div class="items-center p-5">
                                        <div class="flex justify-between items-end space-x-6 mb-3">
                                            <div class="input-area w-full">
                                                <label for="dummySearch" class="form-label"><b>Raw Materials</b></label>
                                                <input id="dummySearch" type="text" class="form-control"
                                                    wire:click='openMaterialsSection'
                                                    wire:model.live='dummyMaterialsSearch'
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
                                                                                            class="text-slate-600 dark:text-slate-300 overflow-hidden text-ellipsis whitespace-nowrap">
                                                                                            {{ $material['name'] }}
                                                                                        </h6>
                                                                                    </div>
                                                                                    @error('fetchedMaterials.' . $index
                                                                                        . '.quantity')
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
                                                                                <input type="number" min="0"
                                                                                    class="form-control @error('fetchedMaterials.' . $index . '.price') !border-danger-500 @enderror"
                                                                                    style="width: 100px;"
                                                                                    wire:model="fetchedMaterials.{{ $index }}.price"
                                                                                    wire:input.debounce.400ms="updateTotal({{ $index }})">
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
                                                                                <button class="action-btn"
                                                                                    type="button"
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
                                                        <td class=" text-xs text-slate-500 dark:text-slate-400">Subtotal</td>
                                                        <td class=" text-xs text-slate-500 dark:text-slate-400">
                                                            {{ $totalItems ? $totalItems . ' items' : '-' }}</td>
                                                        <td class="float-right text-dark">
                                                            <b>{{ $subtotal ? number_format($subtotal, 2) : '-' }}<small>&nbsp;EGP</small></b>
                                                        </td>
                                                    </tr>
        
        
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
                                                        <td class=" text-xs text-slate-500 dark:text-slate-400">Total</td>
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

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-span-2">
                <div class="card mb-5">
                    <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base">
                        <div class="items-center p-5">
                            <div class="input-area w-full">
                                <label for="ddate" class="form-label"><b>Customer</b></label>
                                @if ($customerId || $customerIsNew)
                                    @if ($customerId)
                                        <div
                                            class="badge bg-slate-900 text-white capitalize w-full flex justify-between items-center">
                                            <span>{{ $customerName }}</span>
                                            <span class="cursor-pointer" wire:click='clearCustomer'><iconify-icon
                                                    icon="material-symbols:close" width="1.2em"
                                                    height="1.2em"></iconify-icon></span>
                                        </div>

                                        @if ($customerBalance > 0)
                                            <div
                                                class="bg-slate-50 dark:bg-slate-900 rounded p-4 mt-2 flex justify-between flex-wrap">
                                                <div class="space-y-1">
                                                    <h4 class="text-slate-600 dark:text-slate-200 text-xs font-normal">
                                                        Balance
                                                    </h4>
                                                    <div class="text-sm font-medium text-slate-900 dark:text-white">
                                                        <b>{{ number_format($customerBalance, 2) }}</b><small>&nbsp;EGP</small>
                                                    </div>
                                                    <div class="checkbox-area">
                                                        <label class="inline-flex items-center cursor-pointer">
                                                            <input wire:model.live='detuctFromBalance' type="checkbox"
                                                                class="hidden" name="checkbox" checked="checked">
                                                            <span
                                                                class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                                <img src="{{ asset('assets/images/icon/ck-white.svg') }}"
                                                                    alt=""
                                                                    class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                                            <span
                                                                class="text-slate-500 dark:text-slate-400 text-sm leading-6">
                                                                Detuct from balance ?</span>
                                                        </label>
                                                    </div>
                                                </div>

                                            </div>
                                        @endif

                                        @if ($customerLastOrder)
                                            <button wire:click='reorderLastOrder'
                                                class="btn inline-flex justify-center btn-primary block-btn btn-sm mt-5">
                                                <span class="flex items-center">
                                                    <span>Repeat Last Order</span>
                                                </span>
                                            </button>
                                        @endif
                                    @elseif($customerIsNew)
                                        <div class="mb-2">
                                            <label for="customerName" class="form-label !m-0">Name</label>
                                            <input wire:model='customerName' type="text" name="customerName"
                                                class="form-control  @error('customerName') !border-danger-500 @enderror">
                                            @error('customerName')
                                                <span
                                                    class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif


                                    <div class="mb-2 mt-3">
                                        <label for="shippingAddress" class="form-label !m-0">Shipping
                                            Address</label>
                                        <textarea name="shippingAddress" wire:model="shippingAddress"
                                            class="form-control  @error('shippingAddress') !border-danger-500 @enderror"></textarea>
                                        @error('shippingAddress')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-2 mt-3">
                                        <label for="locationURL" class="form-label !m-0">
                                            Location URL
                                        </label>
                                        <textarea name="locationURL" wire:model="locationURL"
                                            class="form-control  @error('locationURL') !border-danger-500 @enderror"></textarea>
                                        @error('locationURL')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="customerPhone" class="form-label !m-0">Phone</label>
                                        <input wire:model='customerPhone' type="text" name="customerPhone"
                                            class="form-control @error('customerPhone') !border-danger-500 @enderror">
                                        @error('customerPhone')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="zoneId" class="form-label !m-0">Zone</label>
                                        <select name="zoneId" id="zoneId"
                                            class="form-control w-full @error('zoneId') !border-danger-500 @enderror"
                                            wire:model.live="zoneId" autocomplete="off">
                                            <option value="">None</option>
                                            @foreach ($zones as $SINGLE_ZONE)
                                                <option value="{{ $SINGLE_ZONE->id }}">
                                                    {{ ucwords(str_replace('_', ' ', $SINGLE_ZONE->name)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('zoneId')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    @if ($customerIsNew)
                                        <button wire:click='clearCustomer'
                                            class="btn inline-flex justify-center btn-light block-btn btn-sm mt-5">
                                            <span class="flex items-center">
                                                <span>Cancel</span>
                                            </span>
                                        </button>
                                    @endif
                                @else
                                    <div class="grid grid-cols-2 gap-2">
                                        <button wire:click='openCustomerSection'
                                            class="btn inline-flex justify-center btn-light block-btn btn-sm">
                                            <span class="flex items-center">
                                                <span>Select existing</span>
                                            </span>
                                        </button>
                                        <button wire:click='NewCustomerSection'
                                            class="btn inline-flex justify-center btn-light block-btn btn-sm">
                                            <span class="flex items-center">
                                                <span>New Customer</span>
                                            </span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($isOpenSelectMaterialSec)
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
