<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Titles
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            {{-- @can('create', \App\Models\Accounting\AccountType::class) --}}
            <button wire:click="openNewTypeModal"
                class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1">
                <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                New Title
            </button>
            {{-- @endcan --}}
        </div>
    </div>

    <div class="card">
        <header class="card-header cust-card-header noborder">
            <iconify-icon wire:loading wire:target="searchText" class="loading-icon text-lg"
                icon="line-md:loading-twotone-loop"></iconify-icon>
            <input type="text" class="form-control !pl-9 mr-1 basis-1/4" placeholder="Search"
                wire:model="searchText">
        </header>

        <div class="tab-content mt-6" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-list" role="tabpanel" aria-labelledby="pills-list-tab">
                <div class="tab-content">
                    <div class="card">
                        <div class="card-body px-6 rounded overflow-hidden pb-3">
                            <div class="overflow-x-auto -mx-6">
                                <div class="inline-block min-w-full align-middle">
                                    <div class="overflow-hidden ">
                                        <table
                                            class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700 ">
                                            <thead class="bg-slate-200 dark:bg-slate-700">
                                                <tr>
                                                    <th scope="col" class="table-th ">
                                                        Name
                                                    </th>

                                                    <th scope="col" class="table-th ">
                                                        Description
                                                    </th>

                                                    {{-- <th scope="col" class="table-th ">
                                                        Allowed For
                                                    </th> --}}

                                                    <th scope="col" class="table-th ">
                                                        ACTION
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody
                                                class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">
                                                @foreach ($titles as $title)
                                                    <tr class="even:bg-slate-50 dark:even:bg-slate-700">
                                                        <td class="table-td">
                                                            <span class="block date-text">{{ $title->name }}</span>
                                                        </td>

                                                        <td class="table-td text-start max-w-[63%]">
                                                            <div class="overflow-hidden text-ellipsis whitespace-nowrap max-w-full"
                                                                style="max-width: 400px;">
                                                                {{ $title->desc }}
                                                            </div>
                                                        </td>
                                                        {{-- <td class="table-td text-start max-w-[63%]">
                                                            <div class="overflow-hidden text-ellipsis whitespace-nowrap max-w-full"
                                                                style="max-width: 400px;">
                                                                {{ $title->allowed_users->pluck('username')->implode(', ') }}
                                                            </div>
                                                        </td> --}}


                                                        <td class="table-td">
                                                            <div class="dropstart relative">
                                                                <button class="inline-flex justify-center items-center"
                                                                    type="button" data-bs-toggle="dropdown"
                                                                    aria-expanded="false">
                                                                    <iconify-icon class="text-xl ltr:ml-2 rtl:mr-2"
                                                                        icon="heroicons-outline:dots-vertical"></iconify-icon>
                                                                </button>
                                                                <ul
                                                                    class="dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">
                                                                    <!-- Edit Button -->
                                                                    <li>
                                                                        <button href="#" data-bs-toggle="modal"
                                                                            data-bs-target="#editModal"
                                                                            wire:click="openEditModel({{ $title->id }})"
                                                                            class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300 last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize rtl:space-x-reverse">
                                                                            <iconify-icon
                                                                                icon="heroicons-outline:pencil-alt"></iconify-icon>
                                                                            <span>Edit</span>
                                                                        </button>
                                                                    </li>
                                                                         <!-- Edit Button -->
                                                                         {{-- <li>
                                                                            <button href="#" 
                                                                                wire:click.prevent="openEditUsersModel({{ $title->id }})"
                                                                                class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300 last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize rtl:space-x-reverse">
                                                                                <iconify-icon
                                                                                    icon="heroicons-outline:users"></iconify-icon>
                                                                                <span>Edit Allowed Users</span>
                                                                            </button>
                                                                        </li> --}}
                                                                    <!-- Delete Button -->
                                                                    <li>
                                                                        <a href="#"
                                                                            wire:click.prevent="delete({{ $title->id }})"
                                                                            class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full px-4 py-2 text-sm dark:text-slate-300 cursor-pointer rounded-b flex space-x-2 items-center capitalize rtl:space-x-reverse">
                                                                            <iconify-icon
                                                                                icon="heroicons-outline:trash"></iconify-icon>
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
                                @if ($titles->isEmpty())
                                    {{-- START: empty filter result --}}
                                    <div class="card m-5 p-5">
                                        <div class="card-body rounded-md bg-white dark:bg-slate-800">
                                            <div class="items-center text-center p-5">
                                                <h2><iconify-icon icon="icon-park-outline:search"></iconify-icon>
                                                </h2>
                                                <h2 class="card-title text-slate-900 dark:text-white mb-3">
                                                    No Titles found!</h2>
                                                <p class="card-text">Try changing the filters or search terms for this
                                                    view.
                                                </p>
                                                <a href="{{ url('accounts/titles') }}"
                                                    class="btn inline-flex justify-center mx-2 mt-3 btn-primary active btn-sm">View
                                                    all Titles</a>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- END: empty filter result --}}
                                @endif

                            </div>



                            {{ $titles->links('vendor.livewire.bootstrap') }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


    @if ($isAddNewModalOpen || $TitleId)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none" style="max-width: 800px;">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                {{ $TitleId ? 'Update Title' : 'Add New Title' }}
                            </h3>

                            <button wire:click="closeModal" type="button"
                                class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                data-bs-dismiss="modal">
                                <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10
                                11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="p-6 space-y-4">
                            <div class="from-group">

                                <div class="from-group mb-5">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control mt-2 w-full"
                                        wire:model.defer="name" max=100>
                                    @error('name')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="from-group">
                                    <label for="desc" class="form-label">Description</label>
                                    <textarea name="desc" class="form-control mt-2 w-full" wire:model.defer="desc"></textarea>
                                    @error('desc')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class=" card-body rounded-md shadow-base menu-open p-5 mt-5">
                                    <h4 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Accounts <small>({{ count($accounts) }})</small>
                                    </h4>

                                    <div class="from-group">
                                        @foreach ($accounts as $index => $account)
                                            @php
                                                $printed_arr = [];
                                            @endphp
                                            <div class="card-body rounded-md bg-[#E5F9FF] dark:bg-slate-700 shadow-base mb-5 p-2">
                                                <div class="input-area col-span-2">
                                                    <select class="form-control mt-1 block w-full p-2 border rounded-md {{ $errors->has('accounts.' . $index . '.account_id') ? '!border-danger-500' : 'border-gray-300' }}" wire:model.defer="accounts.{{ $index }}.account_id">
                                                        <option value="">Select Account</option>
                                                        @foreach ($accounts_list as $account)
                                                            {{ printAccountChildren('', $account, $printed_arr) }}
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 mb-3">
                                                    <!-- Amount -->
                                                    <div class="input-area">
                                                        <input type="number" class="form-control w-full mt-2 @error('accounts.' . $index . '.limit') !border-danger-500 @enderror" wire:model.defer="accounts.{{ $index }}.limit" placeholder="Limit">
                                                    </div>

                                                    <!-- Currency -->
                                                    <div class="input-area">
                                                        <select class="form-control w-full mt-2 @error('accounts.' . $index . '.nature') !border-danger-500 @enderror" wire:model.defer="accounts.{{ $index }}.nature">
                                                            @foreach ($NATURES as $NATURE)
                                                                <option value="{{ $NATURE }}">{{ ucwords($NATURE) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="flex items-center gap-2 w-full">
                                                    <!-- Remove Button -->
                                                    @if (count($this->accounts) > 1)
                                                        <div class="flex-shrink-0">
                                                            <button class="action-btn" wire:click="removeAccount({{ $index }})" type="button">
                                                                <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>

                                            </div>
                                        @endforeach

                                        <button wire:click="addAnotherAccount" class="btn btn-sm mt-2 inline-flex justify-center btn-dark">
                                            Add Account
                                        </button>
                                    </div>

                                </div>

                            </div>

                            <!-- Modal footer -->
                            @if ($TitleId)
                                <div
                                    class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                    <button wire:click="editTitle" data-bs-dismiss="modal"
                                        class="btn inline-flex justify-center text-white bg-black-500">
                                        <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                            wire:loading wire:target="editTitle"
                                            icon="line-md:loading-twotone-loop"></iconify-icon>
                                        <span wire:loading.remove="editTitle">Submit</span>
                                    </button>
                                </div>
                            @else
                                <div
                                    class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                    <button wire:click="addTitle" data-bs-dismiss="modal"
                                        class="btn inline-flex justify-center text-white bg-black-500">
                                        <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                            wire:loading wire:target="addTitle"
                                            icon="line-md:loading-twotone-loop"></iconify-icon>
                                        <span wire:loading.remove="addTitle">Submit</span>
                                    </button>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($isAllowedModalOpen)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none" style="max-width: 800px;">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Set Allowed Users
                            </h3>

                            <button wire:click="closeUsersModal" type="button"
                                class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                data-bs-dismiss="modal">
                                <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10
                                11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="p-6 space-y-4">
                            <div class="from-group">

                                <div class="from-group mb-5">

                                    
                                    <label for="name" class="form-label">Allowed Users</label>
                                    <div class="w-full">
                                        <select wire:model.defer="loadedUsers" id="multiSelect" multiple
                                            aria-label="multiple select example" class="select2 form-control w-full mt-2 py-2"
                                            multiple="multiple" style="height: 250px">
                                            @foreach ($assistants as $a)
                                                <option
                                                    {{ in_array($a->id, $loadedUsers) ? 'selected="selected"' : '' }}
                                                    value="{{ $a->id }}" class="">
                                                    {{ $a->first_name . ' ' . $a->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                            </div>

                            <!-- Modal footer -->
                            <div
                            class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                            <button wire:click="editTitleUsers" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="editTitleUsers"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                                <span wire:loading.remove="editTitleUsers">Submit</span>
                            </button>
                        </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
