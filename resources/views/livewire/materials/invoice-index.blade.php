<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Invoices
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">

            <div class="dropdown relative">
                <button class="btn inline-flex justify-center btn-dark items-center btn-sm" type="button"
                    id="darkDropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    Filter
                    <iconify-icon class="text-xl ltr:ml-2 rtl:mr-2" icon="ic:round-keyboard-arrow-down"></iconify-icon>
                </button>
                <ul
                    class=" dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow
                            z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">
                    <li wire:click='openSupplierSection'
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                        Supplier
                    </li>
                    <li wire:click='openFilteryDueDate'
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                        Due Date
                    </li>
                    <li wire:click='filterisPaid'
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                        Paid
                    </li>
                </ul>
            </div>

            @can('create', App\Models\Materials\SupplierInvoice::class)
                <a href="{{ route('invoice.create') }}">
                    <button wire:click="openNewSupplierSection"
                        class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1 btn-sm">
                        Create Invoice
                    </button>
                </a>
            @endcan


        </div>
    </div>
    <div class="card">
        <header class="card-header cust-card-header noborder">
            <iconify-icon wire:loading wire:target="search" class="loading-icon text-lg"
                icon="line-md:loading-twotone-loop"></iconify-icon>
            <input type="text" class="form-control !pl-9 mr-1 basis-1/4" placeholder="Search"
                wire:model.live.debounce.400ms="search">
        </header>

        <header class="card-header cust-card-header noborder">

            @if ($dueDate)

                <span class="badge bg-slate-900 text-white capitalize">
                    <span class="cursor-pointer" wire:click='openFilteryDueDate'>
                        <span class="text-secondary-500 ">Due Date:</span>
                        @foreach ($dueDate as $sDdate)
                            &nbsp;
                            {{ $sDdate->isToday()
                                ? 'Today'
                                : ($sDdate->isYesterday()
                                    ? 'Yesterday'
                                    : ($sDdate->isTomorrow()
                                        ? 'Tomorrow'
                                        : $sDdate->format('l d-m-Y'))) }}
                            @if (!$loop->last)
                                ,
                            @endif
                        @endforeach
                    </span>

                    &nbsp;&nbsp;<iconify-icon wire:click="clearDueDate(closed)" icon="material-symbols:close"
                        class="cursor-pointer" width="1.2em" height="1.2em"></iconify-icon>
                </span>
            @endif

            @if ($selectedSupplier)
                <span class="badge bg-slate-900 text-white capitalize">
                    <span class="cursor-pointer" wire:click='openFilteryZone'>
                        <span class="text-secondary-500 ">Supplier:</span>&nbsp;
                        {{ ucwords($selectedSupplier->name) }}

                    </span>

                    &nbsp;&nbsp;<iconify-icon wire:click="clearProperty('selectedSupplier')"
                        icon="material-symbols:close" class="cursor-pointer" width="1.2em"
                        height="1.2em"></iconify-icon>
                </span>
            @endif

            @if (!is_null($is_paid))
                <span class="badge bg-slate-900 text-white capitalize">
                    <span class="cursor-pointer" wire:click='filterisPaid'>
                        <span class="text-secondary-500 ">Paid:</span>&nbsp;
                        @if ($is_paid)
                            Yes
                        @else
                            No
                        @endif

                    </span>

                    &nbsp;&nbsp;<iconify-icon wire:click="clearisPaid" icon="material-symbols:close"
                        class="cursor-pointer" width="1.2em" height="1.2em"></iconify-icon>
                </span>
            @endif
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
                                            <img src="{{ asset('assets/images/icon/ck-white.svg') }}" alt=""
                                                class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                    </label>
                                </div>
                                Name
                            </th>
                            @if ($selectAll)
                                @if ($selectedAllInvoices)
                                    <th colspan="5" class="table-th"><iconify-icon style="vertical-align: top;"
                                            icon="lucide:info" width="1.2em" height="1.2em"></iconify-icon> A
                                        {{ count($selectedInvoices) }} invoice selected ..
                                        <span class="clickable-link" wire:click='undoSelectAllInvoices'>Undo</span>
                                    </th>
                                @else
                                    <th colspan="5" class="table-th"><iconify-icon style="vertical-align: top;"
                                            icon="lucide:info" width="1.2em" height="1.2em"></iconify-icon>
                                        {{ count($selectedInvoices) }} invoice
                                        selected .. <span class="clickable-link" wire:click='selectAllInvoices'>Select
                                            All Invoices</span></th>
                                @endif
                            @else
                                <th scope="col" class="table-th">Code</th>
                                <th scope="col" class="table-th">Supplier</th>
                                <th scope="col" class="table-th">Total</th>
                                <th scope="col" class="table-th">Items</th>
                                <th scope="col" class="table-th">Payment Status</th>
                                <th scope="col" class="table-th">Payment Due</th>
                            @endif

                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">

                        @foreach ($invoices as $invoice)
                            <tr class="even:bg-slate-100 dark:even:bg-slate-700">

                                <td class="table-td flex items-center sticky-column colomn-shadow even:bg-slate-100 dark:even:bg-slate-700"
                                    style="position: sticky; left: -25px;  z-index: 10;">
                                    <div class="checkbox-area">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" wire:model="selectedInvoices"
                                                value="{{ $invoice->id }}" class="hidden" id="select-all">
                                            <span
                                                class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                <img src="{{ asset('assets/images/icon/ck-white.svg') }}"
                                                    alt=""
                                                    class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                        </label>
                                    </div>
                                    <a href="{{ route('invoice.show', $invoice->id) }}"> <span
                                            class="hover-underline">
                                            <b>
                                                {{ $invoice->title }}
                                            </b>
                                        </span>
                                    </a>

                                </td>


                                <td class="table-td">
                                    {{ $invoice->code }}
                                </td>

                                <td class="table-td">
                                    <a href="{{ route('supplier.show', $invoice->supplier->id) }}"
                                        class="hover-underline cursor-pointer">
                                        {{ $invoice->supplier->name }}
                                    </a>
                                </td>

                                <td class="table-td">
                                    {{ $invoice->total_amount }}<small>&nbsp;EGP</small>
                                </td>

                                <td class="table-td">
                                    {{ $invoice->total_items }}<small>&nbsp;{{ $invoice->total_items == 1 ? 'item' : 'items' }}</small>
                                </td>

                                <td class="table-td">
                                    @if ($invoice->is_paid)
                                        <span class="badge bg-success-500 text-dark-500 bg-opacity-50 capitalize">
                                            <iconify-icon icon="icon-park-outline:dot" width="1.2em"
                                                height="1.2em"></iconify-icon>
                                            Paid
                                        </span>
                                    @elseif ($invoice->isPartlyPaid())
                                        <span class="badge bg-warning-500 text-dark-500 bg-opacity-50 capitalize">
                                            Remaining:
                                            {{ number_format($invoice->remaining_to_pay, 2) }}<small>&nbsp;EGP</small>
                                        </span>
                                    @else
                                        <span class="badge bg-warning-500 text-dark-500 bg-opacity-50 capitalize">
                                            <iconify-icon icon="octicon:dot-16" width="1.2em"
                                                height="1.2em"></iconify-icon>
                                            Payment pending
                                        </span>
                                    @endif
                                </td>

                                <td class="table-td">
                                    <div class="flex items-center">

                                        @if ($invoice->payment_due->isPast() && !$invoice->is_paid)
                                            <span
                                                class="inline-flex h-[6px] w-[6px] bg-danger-500 ring-opacity-25 rounded-full ring-4 bg-danger-500 ring-danger-500 mr-2"></span>
                                        @endif
                                        {{ $invoice->payment_due->isToday() ? 'Today' : ($invoice->payment_due->isYesterday() ? 'Yesterday' : $invoice->payment_due->format('l Y-m-d')) }}
                                    </div>
                                </td>


                            </tr>
                        @endforeach

                    </tbody>

                </table>


                @if ($invoices->isEmpty())
                    {{-- START: empty filter result --}}
                    <div class="card m-5 p-5">
                        <div class="card-body rounded-md bg-white dark:bg-slate-800">
                            <div class="items-center text-center p-5">
                                <h2>
                                    <iconify-icon icon="icon-park-outline:search"></iconify-icon>
                                </h2>
                                <h2 class="card-title text-slate-900 dark:text-white mb-3">No invoices with the
                                    applied
                                    filters</h2>
                                <p class="card-text">Try changing the filters or search terms for this view.
                                </p>
                                <a href="{{ url('/invoices') }}"
                                    class="btn inline-flex justify-center mx-2 mt-3 btn-primary active btn-sm">View
                                    all invoices</a>
                            </div>
                        </div>
                    </div>
                    {{-- END: empty filter result --}}
                @endif


            </div>


        </div>
        <div style="position: sticky ; bottom:0;width:100%; z-index:10;"
            class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
            {{ $invoices->links('vendor.livewire.simple-bootstrap') }}
        </div>

    </div>

    @if ($isOpenSupplierSection)
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
                                Filter Supplier
                            </h3>
                            <button wire:click="closeSupplierSection" type="button"
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
                            <input wire:model.live='supplierSearchText' type="text" class="form-control"
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
                                                Phone 1
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
                                                <h2 class="card-title text-slate-900 dark:text-white mb-3">No
                                                    Suppliers
                                                    Found!</h2>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- END: empty filter result --}}
                                @endif
                            </div>
                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="setFilterDriver" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="setFilterDriver">Submit</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="setFilterDriver"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    @endif

    @if ($Edited_dueDate_sec)
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
                                Filter Due Date
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="removeSelectedDate,Edited_dueDate"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </h3>
                            <button wire:click="closeFilteryDueDate" type="button"
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
                                    <label for="Edited_dueDate" class="form-label">Due Date*</label>
                                    <p class="text-gray-600 text-xs mb-2">
                                        *You can select multiple dates by clicking on the date. Once done, click
                                        "Submit" to apply the filter.
                                    </p>
                                    <input name="Edited_dueDate" id="Edited_dueDate" type="date"
                                        class="form-control w-full mt-2 @error('Edited_dueDate') !border-danger-500 @enderror"
                                        wire:model.live="Edited_dueDate" autocomplete="off">
                                    {{-- <label for="multipleDate-picker" class="form-label">Multiple Dates</label>
                                    <input wire:model="Edited_dueDate"
                                        class="form-control py-2 flatpickr flatpickr-input active @error('Edited_dueDate') !border-danger-500 @enderror"
                                        id="multipleDate-picker" data-mode="multiple" value="" type="text"
                                        readonly="readonly"> --}}

                                </div>
                                @error('Edited_dueDate')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>

                            @foreach ($selectedDueDates as $index => $date)
                                <span class="badge bg-slate-900 text-white capitalize">
                                    <span class="cursor-pointer">
                                        {{ $date->isToday()
                                            ? 'Today'
                                            : ($date->isYesterday()
                                                ? 'Yesterday'
                                                : ($date->isTomorrow()
                                                    ? 'Tomorrow'
                                                    : $date->format('l d-m-Y'))) }}
                                    </span>

                                    &nbsp;&nbsp;<iconify-icon wire:click="removeSelectedDate({{ $index }})"
                                        icon="material-symbols:close" class="cursor-pointer" width="1.2em"
                                        height="1.2em"></iconify-icon>
                                </span>
                            @endforeach

                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="setFilteryDueDate" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="setFilteryDueDate">Submit</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="setFilteryDueDate"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    @endif
</div>
