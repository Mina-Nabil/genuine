<div>
    <div class="space-y-5 profile-page mx-auto" style="max-width: 1000px">
        <div class="flex justify-between">
            <h4><b>Create Periodic Order</b></h4>
            @if (!empty($fetchedProducts))
                <button wire:click='createOrder' class="btn inline-flex justify-center btn-dark btn-sm">Save</button>
            @endif
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

        <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-6 md:gap-5 mb-5 text-wrap">

            <div class="col-span-4">

                {{-- Products --}}
                <div class="card mb-5">
                    <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base">
                        <div class="items-center p-5">
                            <div class="flex justify-between items-end space-x-6">
                                <div class="input-area w-full">
                                    <label for="phone" class="form-label"><b>Products</b></label>
                                    <input id="phone" type="tel" class="form-control"
                                        wire:click='openProductsSection' wire:model.live='dummyProductsSearch'
                                        placeholder="Search products..." autocomplete="off">
                                </div>
                                <button wire:click='openCombosSection'
                                    class="btn inline-flex justify-center btn-outline-light btn-sm">Combos</button>
                            </div>

                            @if (!empty($fetchedProducts))
                                <div class="card-body px-6 pb-6 mt-2">
                                    <div class="overflow-x-auto -mx-6">
                                        <div class="inline-block min-w-full align-middle">
                                            <div class="overflow-hidden ">


                                                <table
                                                    class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                                    <thead class="border-t border-slate-100 dark:border-slate-800">
                                                        <tr>
                                                            <th scope="col" class="table-th imp-p-2">Product</th>
                                                            <th scope="col" class="table-th imp-p-2">Quantity</th>
                                                            <th scope="col" class="table-th imp-p-2">Price/item</th>
                                                            <th scope="col" class="table-th imp-p-2">Total</th>
                                                            <th scope="col" class="table-th imp-p-2"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody
                                                        class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                                        @foreach ($fetchedProducts as $index => $product)
                                                            <tr>
                                                                <!-- Product Name Column -->
                                                                <td class="table-td imp-p-2">
                                                                    <div class="flex-1 text-start">
                                                                        <div class="text-start overflow-hidden text-ellipsis whitespace-nowrap"
                                                                            style="max-width:200px;">
                                                                            <h6
                                                                                class="text-slate-600 dark:text-slate-300 overflow-hidden text-ellipsis whitespace-nowrap">
                                                                                {{ $product['name'] }}
                                                                            </h6>
                                                                        </div>
                                                                        <div
                                                                            class="text-xs font-normal text-slate-600 dark:text-slate-400">
                                                                            @if ($product['combo_id'])
                                                                                Added From Combo
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </td>

                                                                <!-- Quantity Input Column -->
                                                                <td class="table-td imp-p-2">
                                                                    <input type="number" min="1"
                                                                        class="form-control @error('fetchedProducts.' . $index . '.quantity') !border-danger-500 @enderror"
                                                                        style="max-width: 100px;"
                                                                        wire:model="fetchedProducts.{{ $index }}.quantity"
                                                                        wire:input="updateTotal({{ $index }})">
                                                                </td>

                                                                <!-- Price Input Column -->
                                                                <td class="table-td imp-p-2">
                                                                    <input type="number" min="0"
                                                                        class="form-control @error('fetchedProducts.' . $index . '.price') !border-danger-500 @enderror"
                                                                        style="max-width: 100px;"
                                                                        wire:model="fetchedProducts.{{ $index }}.price"
                                                                        wire:input="updateTotal({{ $index }})">
                                                                </td>

                                                                <!-- Total Calculation Column -->
                                                                <td class="table-td imp-p-2">
                                                                    @if ($product['quantity'] && $product['price'])
                                                                        <span>{{ number_format($product['quantity'] * $product['price'], 2) }}
                                                                            <small>EGP</small> </span>
                                                                    @else
                                                                        <span>0.00
                                                                            <small>EGP</small> </span>
                                                                    @endif


                                                                </td>
                                                                <td class="table-td imp-p-2">
                                                                    <button class="action-btn" type="button"
                                                                        wire:click="removeProduct({{ $product['id'] }})">
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

                <div class="col-span-2">
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
                                                <td class=" text-xs text-slate-500 dark:text-slate-400">Shipping &
                                                    Delivery</td>
                                                <td class=" text-xs text-slate-500 dark:text-slate-400">
                                                    {{ $zoneName ?? '-' }}</td>
                                                <td class="float-right text-dark">
                                                    <b>{{ $shippingFee ? number_format($shippingFee, 2) : '-' }}<small>&nbsp;EGP</small></b>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class=" text-xs text-slate-500 dark:text-slate-400">
                                                    @if ($discountAmount)
                                                        Discount &nbsp;
                                                        <span class="clickable-link" wire:click='openDiscountSection'>
                                                            edit
                                                        </span>
                                                    @else
                                                        <span class="clickable-link" wire:click='openDiscountSection'>
                                                            Add Discount
                                                        </span>
                                                    @endif

                                                </td>
                                                <td class=" text-xs text-slate-500 dark:text-slate-400"></td>
                                                <td class="float-right text-dark">
                                                    <b>{{ $discountAmount ? '-' . number_format($discountAmount, 2) : '-' }}<small>&nbsp;EGP</small></b>
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

                <div class="col-span-2">
                    <div class="card mb-5">
                        <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base">
                            <div class="items-center p-5">
                                <div class="input-area w-full">
                                    <label for="periodicOption" class="form-label"><b>Periodic option</b></label>
                                    <select name="periodicOption" id="periodicOption"
                                        class="form-control w-full @error('periodicOption') !border-danger-500 @enderror"
                                        wire:model.live="periodicOption" autocomplete="off">
                                        @foreach ($PERIODIC_OPTIONS as $PERIODIC_OPTION)
                                            <option value="{{ $PERIODIC_OPTION }}">
                                                {{ ucwords(str_replace('_', ' ', $PERIODIC_OPTION)) }}</option>
                                        @endforeach
                                    </select>
                                    @error('periodicOption')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                                @if ($periodicOption === App\Models\Orders\PeriodicOrder::PERIODIC_WEEKLY || $periodicOption === App\Models\Orders\PeriodicOrder::PERIODIC_BI_WEEKLY)
                                    <div class="input-area w-full mt-5">
                                        <label for="orderDay" class="form-label"><b>Day of week</b></label>
                                        <select name="orderDay" id="orderDay"
                                            class="form-control w-full @error('orderDay') !border-danger-500 @enderror"
                                            wire:model.live="orderDay" autocomplete="off">
                                            @foreach ($daysofweek as $index => $day)
                                                <option value="{{ $index }}">
                                                    {{ ucwords(str_replace('_', ' ', $day)) }}</option>
                                            @endforeach
                                        </select>
                                        @error('orderDay')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @elseif($periodicOption === App\Models\Orders\PeriodicOrder::PERIODIC_MONTHLY)
                                    <div class="input-area w-full mt-5">
                                        <label for="ddate" class="form-label"><b>Day of month</b></label>
                                        <input ty name="orderDay" id="orderDay" type="number" min="1" max="30"
                                            class="form-control w-full @error('orderDay') !border-danger-500 @enderror"
                                            wire:model.live="orderDay" autocomplete="off">
                                        @error('orderDay')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-span-2">
                    <div class="card">
                        <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base">
                            <div class="items-center p-5">
                                <div class="input-area w-full">
                                    <label for="phone" class="form-label"><b>Notes</b></label>
                                    <textarea wire:model='note' class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @if ($isOpenSelectProductSec)
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
                                Add products
                            </h3>
                            <button wire:click="closeProductsSection" type="button"
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
                            <input wire:model.live='productsSearchText' type="text" class="form-control"
                                placeholder="Search product...">

                            <div class=""> <!-- Add this wrapper to allow horizontal scroll -->
                                <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700 ">
                                    <thead
                                        class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                                        <tr>
                                            <th scope="col"
                                                class="table-th  flex items-center border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700"
                                                style="position: sticky; left: -25px;  z-index: 10;">
                                                Name
                                            </th>

                                            <th scope="col" class="table-th">
                                                Price
                                            </th>

                                            <th scope="col" class="table-th">
                                                Weight
                                            </th>

                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">

                                        @foreach ($products as $product)
                                            <tr>

                                                <td class="table-td flex items-center sticky-column bg-white dark:bg-slate-800 colomn-shadow"
                                                    style="position: sticky; left: -25px;  z-index: 10;">
                                                    <div class="checkbox-area">
                                                        <label class="inline-flex items-center cursor-pointer">
                                                            <input type="checkbox" wire:model="selectedProducts"
                                                                value="{{ $product->id }}" class="hidden"
                                                                id="select-all">
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
                                                                {{ $product->name }}
                                                            </b>
                                                        </span>
                                                    </label>

                                                </td>

                                                <td class="table-td">
                                                    <b>{{ number_format($product->price, 2) }}</b><small>EGP</small>
                                                </td>

                                                <td class="table-td">
                                                    <b>{{ number_format($product->weight) }}</b>gm
                                                </td>


                                            </tr>
                                        @endforeach

                                    </tbody>

                                </table>


                                @if ($products->isEmpty())
                                    {{-- START: empty filter result --}}
                                    <div class="card m-5 p-5">
                                        <div class="card-body rounded-md bg-white dark:bg-slate-800">
                                            <div class="items-center text-center p-5">
                                                <h2>
                                                    <iconify-icon icon="icon-park-outline:search"></iconify-icon>
                                                </h2>
                                                <h2 class="card-title text-slate-900 dark:text-white mb-3">No
                                                    products
                                                    with the
                                                    applied
                                                    filters</h2>
                                                <p class="card-text">Try changing the filters or search terms for
                                                    this
                                                    view.
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

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="addProducts" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500 btn-sm">
                                <span wire:loading.remove wire:target="addProducts">Add</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="addProducts"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    @endif

    @if ($isOpenSelectComboSec)
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
                                Add combo products
                            </h3>
                            <button wire:click="closeCombosSection" type="button"
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
                            <input wire:model.live='combosSearchText' type="text" class="form-control"
                                placeholder="Search combo...">

                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1"><iconify-icon
                                    icon="material-symbols:info-outline" width="1.2em"
                                    height="1.2em"></iconify-icon> Adding this combo will replace any individual
                                products in your selection that match the combo's products.</p>


                            <div class=""> <!-- Add this wrapper to allow horizontal scroll -->
                                <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700 ">
                                    <thead
                                        class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                                        <tr>
                                            <th scope="col"
                                                class="table-th  flex items-center border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700"
                                                style="position: sticky; left: -25px;  z-index: 10;">
                                                Name
                                            </th>

                                            <th scope="col" class="table-th">
                                                Price
                                            </th>

                                            <th scope="col" class="table-th">
                                                Products
                                            </th>

                                            <th scope="col" class="table-th">
                                            </th>

                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">

                                        @foreach ($combos as $combo)
                                            <tr>

                                                <td class="table-td">
                                                    <b>{{ $combo->name }}</b>
                                                </td>

                                                <td class="table-td">
                                                    <b>{{ number_format($combo->total_price) }}</b>&nbsp;<small>EGP</small>
                                                </td>

                                                <td class="table-td">
                                                    <b>{{ $combo->products->count() }}</b>&nbsp;<small>Product{{ $combo->products->count() !== 1 ? 's' : '' }}</small>
                                                </td>

                                                <td class="table-td">
                                                    <button wire:click='selectCombo({{ $combo->id }})'
                                                        class="btn inline-flex justify-center btn-dark btn-sm">
                                                        Select combo
                                                    </button>
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
                                                <h2 class="card-title text-slate-900 dark:text-white mb-3">No
                                                    Combos
                                                    Found!</h2>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- END: empty filter result --}}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    @endif

    @if ($isOpenSelectCustomerSec)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog"
            style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none" style="max-width: 850px;">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Set Customer
                            </h3>
                            <button wire:click="closeCustomerSection" type="button"
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
                            <input wire:model.live='customersSearchText' type="text" class="form-control"
                                placeholder="Search customer...">

                            <div class="overflow-x-auto"> <!-- Add this wrapper to allow horizontal scroll -->
                                <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700 ">
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

                                            <th scope="col" class="table-th">
                                                Zone
                                            </th>


                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">

                                        @foreach ($customers as $customer)
                                            <tr wire:click='selectCustomer({{ $customer->id }})'
                                                class="hover:bg-slate-200 dark:hover:bg-slate-700 cursor-pointer">

                                                <td class="table-td">
                                                    <b>{{ $customer->name }}</b>
                                                </td>

                                                <td class="table-td">
                                                    {{ $customer->phone }}
                                                </td>

                                                <td class="table-td">
                                                    {{ $customer->zone?->name }}
                                                </td>

                                            </tr>
                                        @endforeach

                                    </tbody>

                                </table>


                                @if ($customers->isEmpty())
                                    {{-- START: empty filter result --}}
                                    <div class="card m-5 p-5">
                                        <div class="card-body rounded-md bg-white dark:bg-slate-800">
                                            <div class="items-center text-center p-5">
                                                <h2>
                                                    <iconify-icon icon="icon-park-outline:search"></iconify-icon>
                                                </h2>
                                                <h2 class="card-title text-slate-900 dark:text-white mb-3">No
                                                    Customers
                                                    Found!</h2>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- END: empty filter result --}}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    @endif

    @if ($isOpenEditDiscount)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog"
            style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none" style="max-width: 850px;">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Set Discount
                            </h3>
                            <button wire:click="closeDiscountSection" type="button"
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


                        <div class="p-6 space-y-4">

                            <div class="from-group">
                                <div class="input-area">
                                    <label for="initiateDiscountAmount" class="form-label">Discount Fees*</label>
                                    <input id="initiateDiscountAmount" type="number"
                                        class="form-control @error('initiateDiscountAmount') !border-danger-500 @enderror"
                                        wire:model.lazy="initiateDiscountAmount" autocomplete="off">
                                </div>
                                @error('initiateDiscountAmount')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="updateDiscount" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="updateDiscount">Submit</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="updateDiscount"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>


                    </div>
                </div>
            </div>
    @endif

    <div wire:loading wire:target="createOrder"
        class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
        tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog">
        <div class="modal-dialog relative w-auto pointer-events-none">
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-75" role="dialog"
                aria-labelledby="vertically_center" aria-modal="true" style="z-index: 500">

                <div class="text-slate-800">
                    <iconify-icon icon="svg-spinners:180-ring" style="font-size: 5rem;"></iconify-icon>
                </div>
            </div>
        </div>
    </div>


</div>
