<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Main Account
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            {{-- @can('create', \App\Models\Accounting\AccountType::class) --}}
            <button wire:click="openNewTypeModal" class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1">
                <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                New Account
            </button>
            {{-- @endcan --}}
        </div>
    </div>

    <div class="card">
        <header class="card-header cust-card-header noborder">
            <iconify-icon wire:loading wire:target="searchText" class="loading-icon text-lg" icon="line-md:loading-twotone-loop"></iconify-icon>
            <input type="text" class="form-control !pl-9 mr-1 basis-1/4" placeholder="Search" wire:model="searchText">
        </header>

        <div class="tab-content mt-6" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-list" role="tabpanel" aria-labelledby="pills-list-tab">
                <div class="tab-content">
                    <div class="card">
                        <div class="card-body px-6 rounded overflow-hidden pb-3">
                            <div class="overflow-x-auto -mx-6">
                                <div class="inline-block min-w-full align-middle">
                                    <div class="overflow-hidden ">
                                        <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700 ">
                                            <thead class="bg-slate-200 dark:bg-slate-700">
                                                <tr>
                                                    <th scope="col" class="table-th ">
                                                        Title
                                                    </th>
                                                    <th scope="col" class="table-th ">
                                                        TYPE
                                                    </th>
                                                    <th scope="col" class="table-th ">
                                                        Description
                                                    </th>
                                                    <th scope="col" class="table-th ">
                                                        ACTION
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                                @foreach ($mainAccounts as $account)
                                                    <tr class="even:bg-slate-50 dark:even:bg-slate-700">
                                                        <td class="table-td">
                                                            <span class="block date-text">{{ $account->name }}</span>
                                                        </td>
                                                        <td class="table-td">
                                                            <span class="badge bg-primary-500 text-white capitalize inline-flex items-center">
                                                                @switch($account->type)
                                                                    @case('expense')
                                                                        <iconify-icon class="ltr:mr-1 rtl:ml-1" icon="heroicons-outline:cash"></iconify-icon>
                                                                    @break
                            
                                                                    @case('revenue')
                                                                        <iconify-icon class="ltr:mr-1 rtl:ml-1" icon="heroicons-outline:currency-dollar"></iconify-icon>
                                                                    @break
                            
                                                                    @case('asset')
                                                                        <iconify-icon class="ltr:mr-1 rtl:ml-1" icon="heroicons-outline:home"></iconify-icon>
                                                                    @break
                            
                                                                    @case('liability')
                                                                        <iconify-icon class="ltr:mr-1 rtl:ml-1" icon="heroicons-outline:document-text"></iconify-icon>
                                                                    @break
                            
                                                                    @default
                                                                        <iconify-icon class="ltr:mr-1 rtl:ml-1" icon="heroicons-outline:badge-check"></iconify-icon>
                                                                @endswitch
                                                                {{ ucfirst($account->type) }}
                                                            </span>
                                                        </td>
                                                        <td class="table-td">
                                                            <span class="block date-text">{{ $account->desc }}</span>
                                                        </td>
                                                        <td class="table-td">
                                                            <div class="dropstart relative">
                                                                <button class="inline-flex justify-center items-center" type="button"   data-bs-toggle="dropdown" aria-expanded="false">
                                                                    <iconify-icon class="text-xl ltr:ml-2 rtl:mr-2" icon="heroicons-outline:dots-vertical"></iconify-icon>
                                                                </button>
                                                                <ul class="dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">
                                                                    <!-- Edit Button -->
                                                                    <li>
                                                                        <button href="#" data-bs-toggle="modal" data-bs-target="#editModal" wire:click="loadAccountType({{ $account->id }})"
                                                                            class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300 last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize rtl:space-x-reverse">
                                                                            <iconify-icon icon="heroicons-outline:pencil-alt"></iconify-icon>
                                                                            <span>Edit</span>
                                                                        </button>
                                                                    </li>
                                                                    <!-- Delete Button -->
                                                                    <li>
                                                                        <a href="#" wire:click.prevent="delete({{ $account->id }})"
                                                                            class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full px-4 py-2 text-sm dark:text-slate-300 cursor-pointer rounded-b flex space-x-2 items-center capitalize rtl:space-x-reverse">
                                                                            <iconify-icon icon="heroicons-outline:trash"></iconify-icon>
                                                                            <span>Delete</span>
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @if ($mainAccounts->isEmpty())
                                    {{-- START: empty filter result --}}
                                    <div class="card m-5 p-5">
                                        <div class="card-body rounded-md bg-white dark:bg-slate-800">
                                            <div class="items-center text-center p-5">
                                                <h2><iconify-icon icon="icon-park-outline:search"></iconify-icon>
                                                </h2>
                                                <h2 class="card-title text-slate-900 dark:text-white mb-3">
                                                    No account found!</h2>
                                                <p class="card-text">Try changing the filters or search terms for this view.
                                                </p>
                                                <a href="{{ url('/accounts/main') }}" class="btn inline-flex justify-center mx-2 mt-3 btn-primary active btn-sm">View
                                                    all Main Accounts</a>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- END: empty filter result --}}
                                @endif

                            </div>



                            {{ $mainAccounts->links('vendor.livewire.bootstrap') }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


    @if ($isAddNewModalOpen)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show" tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none" style="max-width: 800px;">
                <div class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                {{ $mainAccountId ? 'Update Main Account' : 'Add New Main Account' }}
                            </h3>

                            <button wire:click="closeEditModal" type="button" class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white" data-bs-dismiss="modal">
                                <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10
                                11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="p-6 space-y-4">
                            <div class="from-group">
                                <div class="from-group mb-5">
                                    <label for="account_name" class="form-label">Name</label>
                                    <input type="text" name="account_name" class="form-control mt-2 w-full" wire:model.defer="account_name" max=100>
                                    @error('account_name')
                                        <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="from-group mb-5">
                                    <label for="account_type" class="form-label">Description</label>
                                    <select wire:model.defer='account_type' name="account_type" class="form-control w-full mt-2 py-2">
                                        <option class=" inline-block font-Inter font-normal text-sm text-slate-600">Select type</option>
                                        @foreach ($ACC_TYPES as $type)
                                            <option value="{{ $type }}" class=" inline-block font-Inter font-normal text-sm text-slate-600">{{ ucwords($type) }}</option>
                                        @endforeach
                                    </select>
                                    @error('account_type')
                                        <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="from-group">
                                    <label for="account_desc" class="form-label">Description</label>
                                    <textarea name="account_desc" class="form-control mt-2 w-full" wire:model.defer="account_desc"></textarea>
                                    @error('account_desc')
                                        <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Modal footer -->
                            <div class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                <button wire:click="save" data-bs-dismiss="modal" class="btn inline-flex justify-center text-white bg-black-500">
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]" wire:loading wire:target="save" icon="line-md:loading-twotone-loop"></iconify-icon>
                                    <span wire:loading.remove="save">Submit</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
