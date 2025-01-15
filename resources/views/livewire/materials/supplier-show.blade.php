<div>
    <div class="space-y-5 profile-page mx-auto" style="max-width: 1000px">

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

        <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-5 mb-5 text-wrap">
            <div class="md:flex-1 rounded-md overlay min-w-\[var\(500px\)\] sm:col-span-2" style="min-width: 400px;">
                <div class="card-body flex flex-col justify-center bg-cover card p-4 ">
                    <div class="card-text flex flex-col justify-between  menu-open">
                        <p>
                            <b>Supplier info</b>
                        </p>
                        <br>

                        <div>
                            <p><iconify-icon icon="gg:phone" width="16" height="16"></iconify-icon>&nbsp;<b>Phone</b></p>
                            <div class="flex">
                                <a href="tel:{{ $supplier->phone1 }}">{{ $supplier->phone1 ?? 'N/A' }}</a>
                                @if ($supplier->phone1)
                                    ,&nbsp;<a href="tel:{{ $supplier->phone2 }}">{{ $supplier->phone2 ?? 'N/A' }}</a>
                                @endif
                            </div>
                        </div>

                        <div class="mt-5">
                            <p><iconify-icon icon="ic:outline-email" width="16" height="16"></iconify-icon>&nbsp;<b>Email</b></p>
                            <div class="flex">
                                <p>{{ $supplier->email ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="mt-5">
                            <p><iconify-icon icon="mdi:address-marker-outline" width="16" height="16"></iconify-icon>&nbsp;<b>Address</b></p>
                            <div class="flex">
                                <p>{{ $supplier->address ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="mt-5">
                            <p><iconify-icon icon="hugeicons:contact-01" width="16" height="16"></iconify-icon>&nbsp;<b>Contact</b></p>
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
            </div>

            <div>

                <div class="bg-info-500 rounded-md p-4 bg-opacity-[0.15] dark:bg-opacity-25 relative z-[1]">
                    <div class="overlay absolute left-0 top-0 w-full h-full z-[-1]">
                        <img src="{{ asset('assets/images/all-img/shade-2.png') }}" alt="" draggable="false"
                            class="w-full h-full object-contain">
                    </div>
                    <span class="flex items-center mb-6 text-sm text-slate-900 dark:text-white font-medium">
                        <iconify-icon icon="fluent:money-24-regular" width="24" height="24" class="mr-2"></iconify-icon>
                        Balance
                    </span>                    
                    <span class="block mb- text-2xl text-slate-900 dark:text-white font-medium mb-6">
                        EGP {{ $supplier->balance }}
                    </span>
                    <div class="flex space-x-2 rtl:space-x-reverse">
                        <div class="flex-none text-xl  text-primary-500">
                            <iconify-icon icon="heroicons:arrow-trending-up"></iconify-icon>
                        </div>
                        <div class="flex-1 text-sm">
                            <span class="block mb-[2px] text-primary-500">
                                8.67%
                            </span>
                            <span class="block mb-1 text-slate-600 dark:text-slate-300">
                                From last week
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @can('update', $supplier)
        @if ($editInfoSection)
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
                                        <textarea id="supplierAddress" type="text"
                                            class="form-control @error('supplierAddress') !border-danger-500 @enderror" wire:model="supplierAddress"
                                            autocomplete="off"></textarea>
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
</div>
