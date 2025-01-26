<div>
    <div class="space-y-5 profile-page mx-auto">

        <div>
            <div class="flex justify-between">
                <h4>
                    <b>{{ $supplier->name }}</b><iconify-icon class="ml-3" style="position: absolute" wire:loading
                        wire:target="changeSection" icon="svg-spinners:180-ring"></iconify-icon>
                </h4>
                <div class="float-right grid-col-2">
                    <button wire:click='openEditInfoSection'
                        class="btn inline-flex justify-center btn-outline-light btn-sm">Edit
                        info</button>
                </div>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">Raw Materials Supplier</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-8 gap-5 mb-5 text-wrap">
            <div class="md:flex-1 lg:col-span-5 rounded-md overlay min-w-\[var\(500px\)\] sm:col-span-2"
                style="min-width: 400px;">
                <div class="card-body flex flex-col justify-center bg-cover card p-4 ">
                    <div class="card-text flex flex-col justify-between  menu-open">
                        <p>
                            <b>Supplier info</b>
                        </p>
                        <br>

                        <div>
                            <p><iconify-icon icon="gg:phone" width="16"
                                    height="16"></iconify-icon>&nbsp;<b>Phone</b></p>
                            <div class="flex">
                                <a href="tel:{{ $supplier->phone1 }}">{{ $supplier->phone1 ?? 'N/A' }}</a>
                                @if ($supplier->phone1)
                                    ,&nbsp;<a href="tel:{{ $supplier->phone2 }}">{{ $supplier->phone2 ?? 'N/A' }}</a>
                                @endif
                            </div>
                        </div>

                        <div class="mt-5">
                            <p><iconify-icon icon="hugeicons:contact-01" width="16"
                                    height="16"></iconify-icon>&nbsp;<b>Contact</b></p>
                            <div class="flex">
                                <p>{{ $supplier->contact_name ?? 'N/A' }}</p>
                                @if ($supplier->contact_phone)
                                    :&nbsp;<a
                                        href="tel:{{ $supplier->contact_phone }}">{{ $supplier->contact_phone ?? 'N/A' }}</a>
                                @endif
                            </div>
                        </div>
                        <br>
                    </div>
                </div>

                <div class="card no-wrap mt-5">
                    <header class="card-header noborder flex justify-between">
                        <h4 class="card-title">Assigned Raw Materials
                        </h4>
                        <button wire:click='openAssignMaterialSection' class="btn btn-dark btn-sm">
                            Assign Material
                        </button>
                    </header>
                    <div class="card-body px-6 pb-6">
                        <div class="overflow-x-auto -mx-6 ">
                            <span class=" col-span-8  hidden"></span>
                            <span class="  col-span-4 hidden"></span>
                            <div class="inline-block min-w-full align-middle">
                                <div class="overflow-hidden ">
                                    <table
                                        class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                        <thead class=" border-t border-slate-100 dark:border-slate-800">
                                            <tr>

                                                <th scope="col" class=" table-th ">
                                                    Material Name
                                                </th>

                                                <th scope="col" class=" table-th ">
                                                    Price
                                                </th>

                                                <th scope="col" class=" table-th ">
                                                    Expiration Date
                                                </th>

                                                <th scope="col" class=" table-th ">
                                                    Action
                                                </th>

                                            </tr>
                                        </thead>
                                        <tbody
                                            class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">

                                            @forelse ($supplierMaterials as $material)
                                                <tr>
                                                    <td class="table-td ">
                                                        <a href="{{ route('material.show', $material->id) }}"
                                                            class="clickable-link cursor-pointer">
                                                            {{ $material->name }}
                                                        </a>
                                                    </td>
                                                    <td class="table-td ">
                                                        <b>{{ number_format($material->pivot->price, 2) }}</b>
                                                        <small>EGP</small>
                                                    </td>
                                                    <td class="table-td ">
                                                        <b>{{ \Carbon\Carbon::parse($material->pivot->expiration_date)->format('l d/m/Y') }}
                                                            - </b>
                                                        @if (\Carbon\Carbon::parse($material->pivot->expiration_date)->isPast())
                                                            <span class="badge bg-danger">Expired</span>
                                                        @else
                                                            {{ \Carbon\Carbon::parse($material->pivot->expiration_date)->diffForHumans() }}
                                                        @endif
                                                    </td>
                                                    <td class="table-td ">
                                                        <div class="flex space-x-3 rtl:space-x-reverse">
                                                            <button
                                                                wire:click='openUpdateRawMaterialSection({{ $material->id }})'
                                                                class="action-btn" type="button">
                                                                <iconify-icon
                                                                    icon="heroicons:pencil-square"></iconify-icon>
                                                            </button>
                                                            <button wire:click='openConfirmDeleteMaterial({{ $material->id }})' class="action-btn" type="button">
                                                                <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td class="table-td " colspan="3">
                                                        <p class="text-center text-slate-500 p-5">
                                                            No materials assigned to this supplier.
                                                        </p>
                                                    </td>
                                                </tr>
                                            @endforelse

                                        </tbody>
                                    </table>
                                </div>
                                <div style="position: sticky ; bottom:0;width:100%; z-index:10;"
                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    {{ $supplierMaterials->links('vendor.livewire.simple-bootstrap') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-3">
                <div class="bg-info-500 rounded-md p-4 bg-opacity-[0.15] dark:bg-opacity-25 relative z-[1]">
                    <div class="overlay absolute left-0 top-0 w-full h-full z-[-1]">
                        <img src="{{ asset('assets/images/all-img/shade-2.png') }}" alt="" draggable="false"
                            class="w-full h-full object-contain">
                    </div>
                    <div class="flex justify-between">
                        <span class="flex items-center mb-6 text-sm text-slate-900 dark:text-white font-medium">
                            <iconify-icon icon="fluent:money-24-regular" width="24" height="24"
                                class="mr-2"></iconify-icon>
                            Balance
                        </span>
                    </div>
                    <span class="block mb- text-2xl text-slate-900 dark:text-white font-medium mb-6">
                        EGP {{ $supplier->balance }}
                    </span>
                </div>

                <div class="card no-wrap mt-5">
                    <header class="card-header noborder">
                        <h4 class="card-title">All Payments
                        </h4>
                    </header>
                    <div class="card-body px-6 pb-6">
                        <div class="overflow-x-auto -mx-6 ">
                            <span class=" col-span-8  hidden"></span>
                            <span class="  col-span-4 hidden"></span>
                            <div class="inline-block min-w-full align-middle">
                                <div class="overflow-hidden ">
                                    <table
                                        class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                        <thead class=" border-t border-slate-100 dark:border-slate-800">
                                            <tr>

                                                <th scope="col" class=" table-th ">
                                                    Date
                                                </th>

                                                <th scope="col" class=" table-th ">
                                                    Method
                                                </th>

                                                <th scope="col" class=" table-th ">
                                                    Amount
                                                </th>

                                            </tr>
                                        </thead>
                                        <tbody
                                            class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">

                                            @forelse ($supplierPayments as $payment)
                                                <tr>
                                                    <td class="table-td ">
                                                        {{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}
                                                        <span
                                                            class="block text-slate-500 text-xs">{{ $payment->createdBy->full_name }}</span>
                                                    </td>
                                                    <td class="table-td ">
                                                        <div class="min-w-[100px]">
                                                            <span class="text-slate-500 dark:text-slate-400">
                                                                <span
                                                                    class="block text-slate-600 dark:text-slate-300">{{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}</span>
                                                                @if ($payment->order)
                                                                    <a
                                                                        href="{{ route('orders.show', $payment->order->id) }}">
                                                                        <span
                                                                            class="block text-slate-500 text-xs clickable-link">Order:
                                                                            #{{ $payment->order->order_number }}</span>
                                                                    </a>
                                                                @endif

                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="table-td ">

                                                        <div class=" text-success-500">
                                                            +<small>EGP</small> {{ $payment->amount }}
                                                        </div>

                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td class="table-td " colspan="3">
                                                        <p class="text-center text-slate-500 p-5">
                                                            No payments for this supplier.
                                                        </p>
                                                    </td>
                                                </tr>
                                            @endforelse

                                        </tbody>
                                    </table>
                                </div>
                                <div style="position: sticky ; bottom:0;width:100%; z-index:10;"
                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    {{ $supplierPayments->links('vendor.livewire.simple-bootstrap') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card no-wrap mt-5">
                    <header class="card-header noborder">
                        <h4 class="card-title">Balance Transactions
                        </h4>
                    </header>
                    <div class="card-body px-6 pb-6">
                        <div class="overflow-x-auto -mx-6 ">
                            <span class=" col-span-8  hidden"></span>
                            <span class="  col-span-4 hidden"></span>
                            <div class="inline-block min-w-full align-middle">
                                <div class="overflow-hidden ">
                                    <table
                                        class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                        <thead class=" border-t border-slate-100 dark:border-slate-800">
                                            <tr>

                                                <th scope="col" class=" table-th ">
                                                    Date
                                                </th>

                                                <th scope="col" class=" table-th ">
                                                    Method
                                                </th>

                                                <th scope="col" class=" table-th ">
                                                    Amount
                                                </th>

                                            </tr>
                                        </thead>
                                        <tbody
                                            class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">

                                            @forelse ($supplierTransactions as $transaction)
                                                <tr>
                                                    <td class="table-td ">
                                                        {{ \Carbon\Carbon::parse($transaction->payment_date)->format('Y-m-d') }}
                                                        <span
                                                            class="block text-slate-500 text-xs">{{ $transaction->createdBy->full_name }}</span>
                                                    </td>
                                                    <td class="table-td ">
                                                        <div class="min-w-[100px]">
                                                            <span class="text-slate-500 dark:text-slate-400">
                                                                <span
                                                                    class="block text-slate-600 dark:text-slate-300">{{ $transaction->description }}</span>
                                                                @if ($transaction->order)
                                                                    <a
                                                                        href="{{ route('orders.show', $transaction->order->id) }}">
                                                                        <span
                                                                            class="block text-slate-500 text-xs clickable-link">Order:
                                                                            #{{ $transaction->order->order_number }}</span>
                                                                    </a>
                                                                @endif

                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="table-td ">

                                                        <div class=" text-success-500">
                                                            <small>EGP</small> {{ $transaction->amount }}
                                                        </div>

                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td class="table-td " colspan="3">
                                                        <p class="text-center text-slate-500 p-5">
                                                            No balance transactions for this supplier.
                                                        </p>
                                                    </td>
                                                </tr>
                                            @endforelse

                                        </tbody>
                                    </table>
                                </div>
                                <div style="position: sticky ; bottom:0;width:100%; z-index:10;"
                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    {{ $supplierTransactions->links('vendor.livewire.simple-bootstrap') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @can('update', $supplier)
        @if ($editInfoSection)
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
                                    Edit info
                                </h3>
                                <button wire:click="closeNewSupplierSection" type="button"
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
                                        <label for="supplierName" class="form-label">Name</label>
                                        <input id="supplierName" type="text"
                                            class="form-control @error('supplierName') !border-danger-500 @enderror"
                                            wire:model.lazy="supplierName" autocomplete="off">
                                    </div>
                                    @error('supplierName')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="from-group">
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                                        <div class="input-area">
                                            <label for="supplierPhone1" class="form-label">Phone 1</label>
                                            <input id="supplierPhone1" type="text"
                                                class="form-control @error('supplierPhone1') !border-danger-500 @enderror"
                                                wire:model="supplierPhone1" autocomplete="off">
                                        </div>
                                        <div class="input-area">
                                            <label for="supplierPhone2" class="form-label">Phone 2</label>
                                            <input id="supplierPhone2" type="text"
                                                class="form-control @error('supplierPhone2') !border-danger-500 @enderror"
                                                wire:model="supplierPhone2" autocomplete="off">
                                        </div>
                                    </div>
                                    @error('supplierPhone1')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                    @error('supplierPhone2')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <hr>
                                <div class="from-group">
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                                        <div class="input-area">
                                            <label for="supplierContactName" class="form-label">Contact Name</label>
                                            <input id="supplierContactName" type="text"
                                                class="form-control @error('supplierContactName') !border-danger-500 @enderror"
                                                wire:model="supplierContactName" autocomplete="off">
                                        </div>
                                        <div class="input-area">
                                            <label for="supplierContactPhone" class="form-label">Contact Phone</label>
                                            <input id="supplierContactPhone" type="text"
                                                class="form-control @error('supplierContactPhone') !border-danger-500 @enderror"
                                                wire:model="supplierContactPhone" autocomplete="off">
                                        </div>
                                    </div>
                                    @error('supplierContactName')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                    @error('supplierContactPhone')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Modal footer -->
                            <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                                <button wire:click="editInfo" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="editInfo">Submit</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="editInfo"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endcan

    @can('update', $supplier)
        @if ($isOpenAssignMaterialSection)
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
                                    Assign Raw Material
                                </h3>
                                <button wire:click="closeAssignMaterialSection" type="button"
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

                                @error('rawMaterialId')
                                    <div
                                        class="py-[18px] px-6 font-normal font-Inter text-sm rounded-md bg-danger-500 bg-opacity-[14%] text-danger-500">
                                        <div class="flex items-start space-x-3 rtl:space-x-reverse">
                                            <div class="flex-1">
                                                {{ $message }}
                                            </div>
                                        </div>
                                    </div>
                                @enderror

                                @if ($selectedRawMaterial)
                                    <div class="text-slate-900 dark:text-slate-300 text-lg font-medium text-left">
                                        <p class='flex justify-between'><span>{{ $selectedRawMaterial->name }}</span>
                                            <button wire:click="clearSelectedMaterial"
                                                class="text-red-500 text-sm">Clear</button>
                                        </p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Raw Material</p>
                                    </div>

                                    <div class="from-group">
                                        <div class="input-area">
                                            <label for="price" class="form-label">Price*</label>
                                            <input id="price" type="text"
                                                class="form-control @error('price') !border-danger-500 @enderror"
                                                wire:model.lazy="price" autocomplete="off">
                                        </div>
                                        @error('price')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror

                                        <div class="input-area">
                                            <label for="expirationDate" class="form-label">Expiration Date*</label>
                                            <input id="expirationDate" type="date"
                                                class="form-control @error('expirationDate') !border-danger-500 @enderror"
                                                wire:model.lazy="expirationDate" autocomplete="off">
                                        </div>
                                        @error('expirationDate')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @else
                                    <div class="card">
                                        <header class="my-2">
                                            <iconify-icon wire:loading wire:target="materialsSearch"
                                                class="loading-icon text-lg"
                                                icon="line-md:loading-twotone-loop"></iconify-icon>
                                            <input type="text" class="form-control !pl-9 mr-1 basis-1/4"
                                                placeholder="materialsSearch"
                                                wire:model.live.debounce.400ms="materialsSearch">
                                        </header>

                                        <div class="card-body px-6 pb-6  overflow-x-auto">
                                            <div class="overflow-x-auto -mx-6">
                                                <div class="inline-block min-w-full align-middle">
                                                    <div class="overflow-hidden ">

                                                        <table
                                                            class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                                            <thead class="bg-slate-200 dark:bg-slate-700">
                                                                <tr>

                                                                    <th scope="col" class=" table-th ">
                                                                        Raw Material
                                                                    </th>

                                                                    <th scope="col" class=" table-th ">
                                                                        Qty
                                                                    </th>

                                                                </tr>
                                                            </thead>
                                                            <tbody
                                                                class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">

                                                                @foreach ($availableRawMaterials as $material)
                                                                    <tr wire:click='selectRawMaterial({{ $material->id }})'
                                                                        class="hover:bg-slate-200 dark:hover:bg-slate-700 cursor-pointer">
                                                                        <td class="table-td">{{ $material->name }}</td>
                                                                        <td class="table-td">
                                                                            {{ $material->inventory->available }}
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

                            <!-- Modal footer -->
                            <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                                <button wire:click="assignMaterial" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="assignMaterial">Submit</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="assignMaterial"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endcan

    @can('update', $supplier)
        @if ($isOpenUpdateRawMaterialSection)
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
                                    Update Raw Material
                                </h3>
                                <button wire:click="closeAssignMaterialSection" type="button"
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
                                            <label for="price" class="form-label">Price*</label>
                                            <input id="price" type="text"
                                                class="form-control @error('price') !border-danger-500 @enderror"
                                                wire:model.lazy="price" autocomplete="off">
                                        </div>
                                        @error('price')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror

                                        <div class="input-area mt-2">
                                            <label for="expirationDate" class="form-label">Expiration Date*</label>
                                            <input id="expirationDate" type="date"
                                                class="form-control @error('expirationDate') !border-danger-500 @enderror"
                                                wire:model.lazy="expirationDate" autocomplete="off">
                                        </div>
                                        @error('expirationDate')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                            </div>

                            <!-- Modal footer -->
                            <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                                <button wire:click="updateRawMaterial" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="updateRawMaterial">Submit</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="updateRawMaterial"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endcan

    @can('update', $supplier)
    @if ($deleteRawMaterialId)
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
                            <button wire:click="closeConfirmDeleteMaterial" type="button"
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
                            <button wire:click="deleteRawMaterial" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-danger-500">
                                <span wire:loading.remove wire:target="deleteRawMaterial">Confirm</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="deleteRawMaterial"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    @endif
    @endcan
</div>
