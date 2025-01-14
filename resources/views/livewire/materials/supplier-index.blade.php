<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Suppliers
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            @can('create', App\Models\Materials\Supplier::class)
                <button wire:click="openNewSupplierSection"
                    class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1 btn-sm">
                    Add supplier
                </button>
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
                                            <img src="assets/images/icon/ck-white.svg" alt=""
                                                class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                    </label>
                                </div>
                                Name
                            </th>
                            @if ($selectAll)
                                @if ($selectedAllSuppliers)
                                    <th colspan="5" class="table-th"><iconify-icon style="vertical-align: top;"
                                            icon="lucide:info" width="1.2em" height="1.2em"></iconify-icon> A
                                        {{ count($selectedSuppliers) }} supplier selected ..
                                        <span class="clickable-link" wire:click='undoSelectAllSuppliers'>Undo</span>
                                    </th>
                                @else
                                    <th colspan="5" class="table-th"><iconify-icon style="vertical-align: top;"
                                            icon="lucide:info" width="1.2em" height="1.2em"></iconify-icon>
                                        {{ count($selectedSuppliers) }} supplier
                                        selected .. <span class="clickable-link" wire:click='selectAllSuppliers'>Select
                                            All Suppliers</span></th>
                                @endif
                            @else
                                <th scope="col" class="table-th">Phone 1</th>
                                <th scope="col" class="table-th">Phone 2</th>
                                <th scope="col" class="table-th">Email</th>
                                <th scope="col" class="table-th">Contact Name</th>
                                <th scope="col" class="table-th">Contact Phone</th>
                            @endif

                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">

                        @foreach ($suppliers as $supplier)
                        <tr class="even:bg-slate-100 dark:even:bg-slate-700">

                                <td class="table-td flex items-center sticky-column colomn-shadow even:bg-slate-100 dark:even:bg-slate-700"
                                    style="position: sticky; left: -25px;  z-index: 10;">
                                    <div class="checkbox-area">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" wire:model="selectedSuppliers"
                                                value="{{ $supplier->id }}" class="hidden" id="select-all">
                                            <span
                                                class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                <img src="assets/images/icon/ck-white.svg" alt=""
                                                    class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                        </label>
                                    </div>
                                    <a href="{{ route('supplier.show',$supplier->id) }}"> <span
                                            class="hover-underline">
                                            <b>
                                                {{ $supplier->name }}
                                            </b>
                                        </span>
                                    </a>

                                </td>


                                <td class="table-td">
                                    {{ $supplier->phone1 }}
                                </td>

                                <td class="table-td">
                                    {{ $supplier->phone2 }}
                                </td>

                                <td class="table-td">
                                    {{ $supplier->email }}
                                </td>

                                <td class="table-td">
                                    {{ $supplier->contact_name }}
                                </td>

                                <td class="table-td">
                                    {{ $supplier->contact_phone }}
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
                                <h2 class="card-title text-slate-900 dark:text-white mb-3">No suppliers with the
                                    applied
                                    filters</h2>
                                <p class="card-text">Try changing the filters or search terms for this view.
                                </p>
                                <a href="{{ url('/suppliers') }}"
                                    class="btn inline-flex justify-center mx-2 mt-3 btn-primary active btn-sm">View
                                    all suppliers</a>
                            </div>
                        </div>
                    </div>
                    {{-- END: empty filter result --}}
                @endif


            </div>


        </div>
        <div style="position: sticky ; bottom:0;width:100%; z-index:10;"
            class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
            {{ $suppliers->links('vendor.livewire.simple-bootstrap') }}
        </div>

    </div>


    @can('create', App\Models\Materials\Supplier::class)
        @if ($newSupplierSection)
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
                                    Create new supplier
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

                                <div class="from-group">
                                    <div class="input-area">
                                        <label for="supplierEmail" class="form-label">Email</label>
                                        <input id="supplierEmail" type="email"
                                            class="form-control @error('supplierEmail') !border-danger-500 @enderror"
                                            wire:model="supplierEmail" autocomplete="off">
                                    </div>
                                    @error('supplierEmail')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="from-group">
                                    <div class="input-area">
                                        <label for="supplierAddress" class="form-label">Address</label>
                                        <textarea id="supplierAddress" type="text" class="form-control @error('supplierAddress') !border-danger-500 @enderror"
                                            wire:model="supplierAddress" autocomplete="off"></textarea>
                                    </div>
                                    @error('supplierAddress')
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
                                    <button wire:click="addSupplier" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="addSupplier">Submit</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="addSupplier"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endcan
</div>
