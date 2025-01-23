<div>
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

                        @if ($invoice->remaining_to_pay > 0)
                            <div class="relative mt-2 ml-5">
                                <div class="dropdown relative">
                                    <button class="text-xl text-center block w-full " type="button"
                                        id="tableDropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                        <iconify-icon icon="heroicons-outline:dots-vertical"></iconify-icon>
                                    </button>
                                    <ul class=" dropdown-menu min-w-[120px] absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none"
                                        style="min-width: 180px">
                                        @foreach ($PAYMENT_METHODS as $PAYMENT_METHOD)
                                            <li wire:click="confirmPayInvoice('{{ $PAYMENT_METHOD }}')"
                                                class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:text-white hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                                                Pay
                                                {{ ucwords(str_replace('_', ' ', $PAYMENT_METHOD)) }}
                                            </li>
                                        @endforeach
                                        <li wire:click="openPayAmountSection"
                                            class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:text-white hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                                            Pay Amount
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </header>

                    <header
                        class="lg:flex mb-2 items-center border-b border-slate-100 dark:border-slate-700 pb-5  -mx-6 px-6">
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


                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-2 mb-3">
                            <div class="flex items-center">
                                <iconify-icon icon="heroicons-outline:calendar" width="15" height="15"
                                    class="mr-1"></iconify-icon>
                                Payment Due: {{ \Carbon\Carbon::parse($invoice->payment_due)->format('l, Y-m-d') }}
                            </div>

                            @if (\Carbon\Carbon::now()->greaterThan(\Carbon\Carbon::parse($invoice->payment_due)) && $invoice->remaining_to_pay > 0)
                                <div class="text-xs text-red-500 dark:text-red-400 flex items-center mb-3 mt-1">
                                    <iconify-icon icon="heroicons-outline:exclamation-circle" width="15"
                                        height="15" class="mr-1"></iconify-icon>
                                    Alert: Invoice payment is overdue!
                                </div>
                            @endif

                        </div>




                    </header>

                    <div>
                        <div class="overflow-x-auto ">
                            <div class="inline-block min-w-full align-middle">
                                <div class="overflow-hidden ">
                                    <table
                                        class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700 no-wrap">
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
                                                @can('editInfo', $invoice)
                                                    <th scope="col" class=" table-th ">
                                                        Action
                                                    </th>
                                                @endcan
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

                                                    @can('editInfo', $invoice)
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
                                                    @endcan
                                                </tr>
                                            @endforeach

                                            @if (!$invoice->extra_fee_amount == 0 && $invoice->extra_fee_description)
                                                <tr>
                                                    <td colspan="5" class="table-td ">
                                                    </td>
                                                </tr>
                                                <tr class="bg-slate-50 dark:bg-slate-700">
                                                    <td colspan="3" class="table-td ">
                                                        <div class="flex items-center">
                                                            <div class="flex-1 text-start">
                                                                <div
                                                                    class="text-xs font-normal text-slate-600 dark:text-slate-400">
                                                                    Extra Fees
                                                                </div>
                                                                <h4
                                                                    class="text-sm font-medium text-slate-600 whitespace-nowrap">
                                                                    {{ $invoice->extra_fee_description }}
                                                                </h4>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="table-td ">
                                                        {{ number_format($invoice->extra_fee_amount, 2) }}
                                                        <small>EGP</small>
                                                    </td>
                                                    @can('editInfo', $invoice)
                                                        <td class="table-td ">
                                                            <div class="flex space-x-3 rtl:space-x-reverse">
                                                                <button wire:click='openUpdateExtraFeeModal'
                                                                    class="action-btn" type="button">
                                                                    <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                                                                </button>
                                                                <button wire:click='confirmRemoveExtraFees'
                                                                    class="action-btn" type="button">
                                                                    <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    @endcan
                                                </tr>
                                                <tr>
                                                    <td colspan="5" class="table-td ">
                                                    </td>
                                                </tr>
                                            @endif

                                            @can('editInfo', $invoice)
                                                <tr>
                                                    <td colspan="5" class="table-td">
                                                        <button wire:click="openAddRawMaterialModal"
                                                            class="btn inline-flex items-center justify-center text-dark bg-slate-100 btn-sm mb-3">
                                                            <iconify-icon icon="heroicons:plus"
                                                                class="mr-2"></iconify-icon> Add Raw Material
                                                        </button>
                                                        @if ($invoice->extra_fee_amount == 0 && !$invoice->extra_fee_description)
                                                            <button wire:click="openUpdateExtraFeeModal"
                                                                class="btn inline-flex items-center justify-center text-dark bg-slate-100 btn-sm mb-3">
                                                                <iconify-icon icon="heroicons:plus"
                                                                    class="mr-2"></iconify-icon> Add Extra Fee
                                                            </button>
                                                        @endif

                                                    </td>
                                                </tr>
                                            @endcan

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

        @if ($invoice->remaining_to_pay && $invoice->total_paid)
            <div class="card rounded">
                <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base p-5 grid grid-cols-2 gap-2">
                    <div class="border-r border-slate-200 dark:border-slate-700 pr-5">
                        <div class="text-sm text-slate-600 dark:text-slate-300 mb-[6px]">
                            Remaining Amount
                        </div>
                        <div class="text-lg text-slate-900 dark:text-white font-medium mb-[6px]">
                            {{ number_format($invoice->remaining_to_pay, 2) }} <small>EGP</small>
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-slate-600 dark:text-slate-300 mb-[6px]">
                            Paid
                        </div>
                        <div class="text-lg text-slate-900 dark:text-white font-medium mb-[6px]">
                            {{ number_format($invoice->total_paid, 2) }} <small>EGP</small>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($invoice->total_paid)
            <div class="py-[18px] px-6 font-normal text-sm rounded-md bg-success-500 bg-opacity-[14%]  text-white">
                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                    <iconify-icon class="text-2xl flex-0 text-success-500"
                        icon="heroicons-outline:badge-check"></iconify-icon>
                    <p class="flex-1 text-success-500 font-Inter">
                        This invoice has been fully paid
                    </p>
                </div>
            </div>
        @elseif($invoice->remaining_to_pay)
            <div class="py-[18px] px-6 font-normal text-sm rounded-md bg-warning-500 bg-opacity-[14%]  text-white">
                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                    <iconify-icon class="text-2xl flex-0 text-slate-900"
                        icon="heroicons-outline:exclamation-circle"></iconify-icon>
                    <p class="flex-1 text-slate-900 font-Inter">
                        This invoice has not been paid yet
                    </p>
                </div>
            </div>
        @endif


        @if (!$invoice->balanceTransactions->isEmpty() || !$invoice->payments->isEmpty())
            <div class="card no-wrap mb-5">
                <div class="card-body px-6 pb-2">
                    <div class="overflow-x-auto -mx-6 ">
                        <span class=" col-span-8  hidden"></span>
                        <span class="  col-span-4 hidden"></span>
                        <div class="inline-block min-w-full align-middle">
                            <div class="overflow-hidden ">
                                <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                    <tbody
                                        class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                        @if (!$invoice->balanceTransactions->isEmpty())
                                            <tr>
                                                <td class="table-td  bg-slate-800" colspan="3">
                                                    <span class="text-slate-100 flex items-center"><iconify-icon
                                                            icon="material-symbols:currency-exchange" width="1.2em"
                                                            height="1.2em"></iconify-icon>&nbsp;
                                                        Balance
                                                        Transactions</span>

                                                </td>
                                            </tr>
                                            @foreach ($invoice->balanceTransactions as $balanceTransaction)
                                                <tr>
                                                    <td class="table-td ">
                                                        {{ \Carbon\Carbon::parse($balanceTransaction->payment_date)->format('l Y-m-d') }}
                                                        <span
                                                            class="block text-slate-500 text-xs">{{ $balanceTransaction->description }}</span>
                                                    </td>
                                                    <td class="table-td ">

                                                        <div class=" text-success-500">
                                                            {{ -$balanceTransaction->amount }}
                                                            <small>EGP</small>
                                                        </div>

                                                    </td>
                                                    <td class="table-td ">
                                                        <span
                                                            class="block text-slate-500 text-xs">{{ $balanceTransaction->createdBy->full_name }}</span>
                                                    </td>

                                                </tr>
                                            @endforeach
                                        @endif
                                        @if (!$invoice->payments->isEmpty())
                                            <tr>
                                                <td class="table-td  bg-slate-800" colspan="3">
                                                    <span class="text-slate-100 flex items-center"><iconify-icon
                                                            icon="material-symbols-light:payments-rounded"
                                                            width="1.2em" height="1.2em"></iconify-icon>&nbsp;
                                                        Payments</span>

                                                </td>
                                            </tr>
                                            @foreach ($invoice->payments as $payment)
                                                <tr>
                                                    <td class="table-td ">
                                                        {{ \Carbon\Carbon::parse($payment->payment_date)->format('l Y-m-d') }}
                                                        <span
                                                            class="block text-slate-500 text-xs">{{ $payment->note }}</span>
                                                    </td>
                                                    <td class="table-td ">

                                                        <div class=" text-success-500">
                                                            {{ number_format(abs($payment->amount), 2) }}
                                                            <small>EGP</small>
                                                            <span
                                                                class="block text-slate-500 text-xs">{{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}</span>
                                                        </div>

                                                    </td>
                                                    <td class="table-td ">
                                                        <span
                                                            class="block text-slate-500 text-xs">{{ $payment->createdBy->full_name }}</span>
                                                    </td>

                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>

    @if ($confirmRemoveExtraFeeModal)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog"
            style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-danger-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Remove Extra Fees
                            </h3>
                            <button wire:click="closeConfirmRemoveExtraFees" type="button"
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
                            Are you sure you want to extra fees <b>{{ number_format($invoice->extra_fee_amount, 2) }}
                                <small>EGP</small></b> ?
                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="removeExtraFee" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-danger-500">
                                <span wire:loading.remove wire:target="removeExtraFee">Confirm</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="removeExtraFee"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    @endif


    @if ($returnedRawMateralId)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog"
            style="display: block;">
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
                                    <input type="number" wire:model='returnedRawMaterialQty'
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

    @if ($payAmountSection)
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
                                Pay Amount To Supplier
                            </h3>
                            <button wire:click="closePayAmountSection" type="button"
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

                            <div class="input-area">
                                <label for="payAmountValue"
                                    class="block text-sm font-medium text-gray-700">Amount</label>
                                <input max="{{ $invoice->remaining_to_pay }}" wire:model='payAmountValue'
                                    type="number" name="payAmountValue" placeholder="Enter quantity..."
                                    class="form-control @error('payAmountValue') !border-danger-500 @enderror">
                                @error('payAmountValue')
                                    <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="input-area mb-5">
                                <label for="payAmountPymtMethod" class="form-label">Payment method</label>
                                <select name="payAmountPymtMethod" id="payAmountPymtMethod"
                                    class="form-control w-full @error('payAmountPymtMethod') !border-danger-500 @enderror"
                                    wire:model="payAmountPymtMethod" autocomplete="off">
                                    @foreach ($PAYMENT_METHODS as $PAYMENT_METHOD)
                                        <option value="{{ $PAYMENT_METHOD }}">
                                            {{ ucwords(str_replace('_', ' ', $PAYMENT_METHOD)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- <div class="input-area mb-5">
                                <div class="checkbox-area">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" wire:model.live='paidNow' class="hidden"
                                            name="checkbox">
                                        <span
                                            class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                            <img src="{{ asset('assets/images/icon/ck-white.svg') }}" alt=""
                                                class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                        <span class="text-slate-500 dark:text-slate-400 text-sm leading-6">
                                            Paid now ?
                                        </span>
                                    </label>
                                </div>
                            </div>

                            @if (!$paidNow)
                                <div class="input-area">
                                    <label for="paymentDate" class="block text-sm font-medium text-gray-700">Payment
                                        Date</label>
                                    <input wire:model='paymentDate' type="date" name="paymentDate"
                                        placeholder="Enter quantity..."
                                        class="form-control @error('paymentDate') !border-danger-500 @enderror">
                                    @error('paymentDate')
                                        <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            @endif --}}
                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="payAmount" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-slate-900">
                                <span wire:loading.remove wire:target="payAmount">Submit</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="payAmount"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($updateExtraFeeModal)
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
                                Extra Fees
                            </h3>
                            <button wire:click="closeUpdateExtraFeeModal" type="button"
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

                            <div class="input-area">
                                <label for="extraFeeAmount"
                                    class="block text-sm font-medium text-gray-700">Amount</label>
                                <input wire:model='extraFeeAmount' type="number" name="extraFeeAmount"
                                    placeholder="Enter quantity..."
                                    class="form-control @error('extraFeeAmount') !border-danger-500 @enderror">
                                @error('extraFeeAmount')
                                    <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="input-area">
                                <label for="extraFeeDesc"
                                    class="block text-sm font-medium text-gray-700">Description</label>
                                <input wire:model='extraFeeDesc' type="text" name="extraFeeDesc"
                                    placeholder="Enter quantity..."
                                    class="form-control @error('extraFeeDesc') !border-danger-500 @enderror">
                                @error('extraFeeDesc')
                                    <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="updateExtraFee" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-slate-900">
                                <span wire:loading.remove wire:target="updateExtraFee">Submit</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="updateExtraFee"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($PAY_BY_PAYMENT_METHOD)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog"
            style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-warning-500">
                            <h3 class="text-xl font-medium text-black dark:text-white capitalize">
                                Warning
                            </h3>
                            <button wire:click="closeConfirmPayInvoice" type="button"
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

                            Are you sure you want to proceed with the payment of
                            <b>{{ number_format($invoice->remaining_to_pay, 2) }}</b><small>EGP</small> using the
                            <b>{{ ucwords(str_replace('_', ' ', $PAY_BY_PAYMENT_METHOD)) }}</b> method?
                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="payInvoice" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="payInvoice">Procceed Transaction</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="payInvoice"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    @endif
</div>
