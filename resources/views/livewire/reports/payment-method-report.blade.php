<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Payment Method Report
                <iconify-icon class="ml-3" style="position: absolute" wire:loading wire:target="setFilterPaymentMethod, setFilterPaymentDate"
                    icon="svg-spinners:180-ring"></iconify-icon>
            </h4>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Payment Method Filter -->
                <div class="form-group">
                    <label for="paymentMethod" class="form-label">Payment Method</label>
                    <div class="relative">
                        <input type="text" 
                               class="form-control !pr-10" 
                               placeholder="Select Payment Method" 
                               value="{{ $paymentMethod ? ucwords(str_replace('_', ' ', $paymentMethod)) : '' }}"
                               readonly>
                        <button class="absolute right-0 top-1/2 -translate-y-1/2 w-9 h-full border-l text-xl border-l-slate-200 dark:border-l-slate-600 dark:text-slate-300 flex items-center justify-center"
                                wire:click="openFilterPaymentMethod">
                            <iconify-icon icon="heroicons-outline:selector"></iconify-icon>
                        </button>
                    </div>
                </div>

                <!-- Date Range Filter -->
                <div class="form-group">
                    <label for="paymentDateRange" class="form-label">Payment Date Range</label>
                    <div class="relative">
                        <input type="text" 
                               class="form-control !pr-10" 
                               placeholder="Select Date Range" 
                               value="{{ $payment_date_from ? \Carbon\Carbon::parse($payment_date_from)->format('d/m/Y') : '' }} - {{ $payment_date_to ? \Carbon\Carbon::parse($payment_date_to)->format('d/m/Y') : '' }}"
                               readonly>
                        <button class="absolute right-0 top-1/2 -translate-y-1/2 w-9 h-full border-l text-xl border-l-slate-200 dark:border-l-slate-600 dark:text-slate-300 flex items-center justify-center"
                                wire:click="openFilterPaymentDate">
                            <iconify-icon icon="heroicons-outline:calendar"></iconify-icon>
                        </button>
                    </div>
                </div>

                    <!-- Title Filter -->
                    <div class="form-group">
                        <label for="title" class="form-label">Title</label>
                        <div class="relative">
                            <input type="text" 
                                   class="form-control !pr-10" 
                                   placeholder="Select Title" 
                                   value="{{ $selected_title ? $selected_title->title : '' }}"
                                   readonly>
                            <button class="absolute right-0 top-1/2 -translate-y-1/2 w-9 h-full border-l text-xl border-l-slate-200 dark:border-l-slate-600 dark:text-slate-300 flex items-center justify-center"
                                    wire:click="openFilterTitle">
                                <iconify-icon icon="heroicons-outline:selector"></iconify-icon>
                            </button>
                        </div>
                    </div>
    
            </div>
        </div>
    </div>

    <!-- Balance Summary Cards -->
    @if(!$selected_title)
    <div class="grid grid-cols-12 gap-5 mt-5">
        <div class="col-span-12">
            <div class="grid md:grid-cols-3 grid-cols-1 gap-4">
                <div class="card">
                    <div class="card-body pt-4 pb-3 px-4">
                        <div class="flex space-x-3 rtl:space-x-reverse">
                            <div class="flex-none">
                                <div class="h-12 w-12 rounded-full flex flex-col items-center justify-center text-2xl bg-info-100 dark:bg-slate-900 text-info-500">
                                    <iconify-icon icon="mdi:cash-register"></iconify-icon>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="text-slate-600 dark:text-slate-300 text-sm mb-1 font-medium">
                                    Starting Balance
                                </div>
                                <div class="text-slate-900 dark:text-white text-lg font-medium">
                                    {{ number_format($startBalance, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body pt-4 pb-3 px-4">
                        <div class="flex space-x-3 rtl:space-x-reverse">
                            <div class="flex-none">
                                <div class="h-12 w-12 rounded-full flex flex-col items-center justify-center text-2xl {{ $totalAmount >= 0 ? 'bg-success-100 text-success-500' : 'bg-danger-100 text-danger-500' }} dark:bg-slate-900">
                                    <iconify-icon icon="{{ $totalAmount >= 0 ? 'mdi:cash-plus' : 'mdi:cash-minus' }}"></iconify-icon>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="text-slate-600 dark:text-slate-300 text-sm mb-1 font-medium">
                                    Total Change
                                </div>
                                <div class="text-slate-900 dark:text-white text-lg font-medium {{ $totalAmount < 0 ? 'text-danger-500' : '' }}">
                                    {{ number_format($totalAmount, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body pt-4 pb-3 px-4">
                        <div class="flex space-x-3 rtl:space-x-reverse">
                            <div class="flex-none">
                                <div class="h-12 w-12 rounded-full flex flex-col items-center justify-center text-2xl bg-primary-100 dark:bg-slate-900 text-primary-500">
                                    <iconify-icon icon="mdi:cash-check"></iconify-icon>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="text-slate-600 dark:text-slate-300 text-sm mb-1 font-medium">
                                    Ending Balance
                                </div>
                                <div class="text-slate-900 dark:text-white text-lg font-medium">
                                    {{ number_format($endBalance, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else 
    <div class="grid grid-cols-12 gap-5 mt-5">
        <div class="col-span-12">
            <div class="grid md:grid-cols-2 grid-cols-1 gap-4">
                <div class="card">
                    <div class="card-body pt-4 pb-3 px-4">
                        <div class="flex space-x-3 rtl:space-x-reverse">
                            <div class="flex-none">
                                <div class="h-12 w-12 rounded-full flex flex-col items-center justify-center text-2xl bg-info-100 dark:bg-slate-900 text-info-500">
                                    <iconify-icon icon="mdi:cash-register"></iconify-icon>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="text-slate-600 dark:text-slate-300 text-sm mb-1 font-medium">
                                    Total Amount
                                </div>
                                <div class="text-slate-900 dark:text-white text-lg font-medium">
                                    {{ number_format($totalAmount, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    @endif
    <!-- Payments Table -->
    <div class="card mt-5">
        <header class="card-header noborder">
            <h4 class="card-title">Transactions</h4>
        </header>
        <div class="card-body px-6 pb-6">
            <div class="overflow-x-auto -mx-6">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                            <thead class="bg-slate-200 dark:bg-slate-700">
                                <tr>
                                    <th scope="col" class="table-th">Date</th>
                                    <th scope="col" class="table-th">Reference</th>
                                    <th scope="col" class="table-th">Title</th>
                                    <th scope="col" class="table-th">Note</th>
                                    <th scope="col" class="table-th">Amount</th>
                                    <th scope="col" class="table-th">Balance</th>
                                    <th scope="col" class="table-th">Creator</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                @forelse($payments as $payment)
                                <tr class="even:bg-slate-50 dark:even:bg-slate-700">
                                    <td class="table-td">{{ $payment->payment_date->format('d/m/Y') }}</td>
                                    <td class="table-td">
                                        @if($payment->customer)
                                            <a href="{{ route('customer.show', $payment->customer->id) }}" class="hover-underline">
                                                {{ $payment->customer->name }}
                                            </a>
                                        @elseif($payment->supplier)
                                            <a href="{{ route('supplier.show', $payment->supplier->id) }}" class="hover-underline">
                                                {{ $payment->supplier->name }}
                                            </a>
                                        @elseif($payment->order)
                                            <a href="{{ route('orders.show', $payment->order->id) }}" class="hover-underline">
                                                Order #{{ $payment->order->order_number }}
                                            </a>
                                        @elseif($payment->invoice)
                                            <a href="{{ route('invoice.show', $payment->invoice->id) }}" class="hover-underline">
                                                Invoice #{{ $payment->invoice->code }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="table-td">{{ $payment->title->title ?? 'N/A' }}</td>
                                    <td class="table-td">{{ $payment->note }}</td>
                                    <td class="table-td font-medium {{ $payment->amount < 0 ? 'text-danger-500' : 'text-success-500' }}">
                                        {{ number_format($payment->amount, 2) }}
                                    </td>
                                    <td class="table-td font-medium">
                                        {{ number_format($payment->type_balance, 2) }}
                                    </td>
                                    <td class="table-td">{{ $payment->createdBy->full_name ?? 'System' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="table-td text-center">
                                        <div class="flex justify-center p-5">
                                            <div class="text-center">
                                                <iconify-icon icon="icon-park-outline:search" width="40" height="40"></iconify-icon>
                                                <div class="text-slate-500 dark:text-slate-300 text-sm mt-2">No transactions found for the selected criteria</div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        
        </div>
    </div>

    <!-- Payment Method Filter Modal -->
    @if($Edited_paymentMethod_sec)
    <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
         tabindex="-1" aria-labelledby="payment_method_modal" aria-modal="true" role="dialog"
         style="display: block;">
        <div class="modal-dialog relative w-auto pointer-events-none">
            <div class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                    <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                        <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                            Select Payment Method
                        </h3>
                        <button wire:click="closeFilterPaymentMethod" type="button"
                                class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white">
                            <iconify-icon icon="heroicons-outline:x"></iconify-icon>
                        </button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="from-group">
                            <div class="input-area">
                                <label for="Edited_paymentMethod" class="form-label">Payment Method*</label>
                                <select name="Edited_paymentMethod" id="Edited_paymentMethod"
                                        class="form-control w-full mt-2"
                                        wire:model="Edited_paymentMethod" autocomplete="off">
                                    @foreach ($PAYMENT_METHODS as $METHOD)
                                    <option value="{{ $METHOD }}">
                                        {{ ucwords(str_replace('_', ' ', $METHOD)) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                        <button wire:click="setFilterPaymentMethod" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                            <span wire:loading.remove wire:target="setFilterPaymentMethod">Apply</span>
                            <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="setFilterPaymentMethod"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Title Filter Modal -->
    @if($Edited_title_id_sec)
    <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
         tabindex="-1" aria-labelledby="title_modal" aria-modal="true" role="dialog"
         style="display: block;">
        <div class="modal-dialog relative w-auto pointer-events-none">
            <div class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                    <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                        <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                            Select Title
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="from-group">
                            <div class="input-area">
                                <label for="Edited_title_id" class="form-label">Title*</label>
                                <select name="Edited_title_id" id="Edited_title_id"
                                        class="form-control w-full mt-2"
                                        wire:model="Edited_title_id" autocomplete="off">
                                    @foreach ($titles as $title)
                                    <option value="{{ $title->id }}">{{ $title->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                        <button wire:click="setFilterTitle" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                            <span wire:loading.remove wire:target="setFilterTitle">Apply</span>
                            <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="setFilterTitle"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                        </button>
                    </div>
                </div>
            </div>
        </div>   
    </div>
    @endif


        
    <!-- Date Range Filter Modal -->
    @if($Edited_payment_date_from_sec)
    <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
         tabindex="-1" aria-labelledby="date_range_modal" aria-modal="true" role="dialog"
         style="display: block;">
        <div class="modal-dialog relative w-auto pointer-events-none">
            <div class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                    <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                        <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                            Select Date Range
                        </h3>
                        <button wire:click="closeFilterPaymentDate" type="button"
                                class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white">
                            <iconify-icon icon="heroicons-outline:x"></iconify-icon>
                        </button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label for="edited_payment_date_from" class="form-label">From Date</label>
                                <input type="date" id="edited_payment_date_from" class="form-control" 
                                       wire:model="edited_payment_date_from">
                            </div>
                            <div class="form-group">
                                <label for="edited_payment_date_to" class="form-label">To Date</label>
                                <input type="date" id="edited_payment_date_to" class="form-control" 
                                       wire:model="edited_payment_date_to">
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                        <button wire:click="clearFilterPaymentDates" class="btn inline-flex justify-center text-white bg-danger-500 mr-2">
                            Clear
                        </button>
                        <button wire:click="setFilterPaymentDate" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                            <span wire:loading.remove wire:target="setFilterPaymentDate">Apply</span>
                            <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="setFilterPaymentDate"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div> 