<div>
    <div class="space-y-5 profile-page mx-auto" style="max-width: 800px">
        <div class="flex justify-between flex-wrap items-center">
            <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
                <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                    Titles
                </h4>
            </div>
            <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
                <button wire:click="openAddTitleModal"
                    class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1 btn-sm">
                    Add Title
                </button>
            </div>
        </div>
        <div class="card">
            <header class="card-header cust-card-header noborder">
                <iconify-icon wire:loading wire:target="search" class="loading-icon text-lg"
                    icon="line-md:loading-twotone-loop"></iconify-icon>
                <input type="text" class="form-control !pl-9 mr-1 basis-1/4" placeholder="Search"
                    wire:model.live.debounce.400ms="search">
            </header>

            <div class="card-body px-6 pb-6 overflow-x-auto">
                <div class="">
                    <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                        <thead class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                            <tr>
                                <th scope="col" class="table-th">Title</th>
                                <th scope="col" class="table-th">Limit</th>
                                <th scope="col" class="table-th">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">
                            @foreach ($titles as $title)
                                <tr>
                                    <td class="table-td">{{ $title->title }}</td>
                                    <td class="table-td">{{ $title->limit }}</td>
                                    <td class="table-td">
                                        <button wire:click="openEditTitleModal({{ $title->id }})"
                                            class="btn btn-sm btn-primary">Edit</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if ($titles->isEmpty())
                        <div class="card m-5 p-5">
                            <div class="card-body rounded-md bg-white dark:bg-slate-800">
                                <div class="items-center text-center p-5">
                                    <h2>
                                        <iconify-icon icon="icon-park-outline:search"></iconify-icon>
                                    </h2>
                                    <h2 class="card-title text-slate-900 dark:text-white mb-3">No titles found</h2>
                                    <p class="card-text">Try changing the filters or search terms for this view.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div style="position: sticky; bottom:0; width:100%; z-index:10;"
                class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                {{ $titles->links('vendor.livewire.simple-bootstrap') }}
            </div>
        </div>
    </div>

    @if ($addTitleModal)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">Create new title</h3>
                            <button wire:click="closeAddTitleModal" type="button"
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
                                    <label for="newTitleName" class="form-label">Title</label>
                                    <input id="newTitleName" type="text"
                                        class="form-control @error('newTitleName') !border-danger-500 @enderror"
                                        wire:model.lazy="newTitleName" autocomplete="off">
                                </div>
                                @error('newTitleName')
                                    <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="from-group">
                                <div class="input-area">
                                    <label for="newTitleLimit" class="form-label">Limit</label>
                                    <input id="newTitleLimit" type="number"
                                        class="form-control @error('newTitleLimit') !border-danger-500 @enderror"
                                        wire:model.lazy="newTitleLimit" autocomplete="off">
                                </div>
                                @error('newTitleLimit')
                                    <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="from-group">
                                <div class="input-area">
                                    <label for="newTitleDescription" class="form-label">Description</label>
                                    <input id="newTitleDescription" type="text"
                                        class="form-control @error('newTitleDescription') !border-danger-500 @enderror"
                                        wire:model.lazy="newTitleDescription" autocomplete="off">
                                </div>
                                @error('newTitleDescription')
                                    <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                            <button wire:click="saveTitle" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="saveTitle">Submit</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="saveTitle"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($editTitleModal)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">Edit title</h3>
                            <button wire:click="closeEditTitleModal" type="button"
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
                                    <label for="editTitleName" class="form-label">Title</label>
                                    <input id="editTitleName" type="text"
                                        class="form-control @error('editTitleName') !border-danger-500 @enderror"
                                        wire:model.lazy="editTitleName" autocomplete="off">
                                </div>
                                @error('editTitleName')
                                    <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="from-group">
                                <div class="input-area">
                                    <label for="editTitleLimit" class="form-label">Limit</label>
                                    <input id="editTitleLimit" type="number"
                                        class="form-control @error('editTitleLimit') !border-danger-500 @enderror"
                                        wire:model.lazy="editTitleLimit" autocomplete="off">
                                </div>
                                @error('editTitleLimit')
                                    <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="from-group">
                                <div class="input-area">
                                    <label for="editTitleDescription" class="form-label">Description</label>
                                    <input id="editTitleDescription" type="text"
                                        class="form-control @error('editTitleDescription') !border-danger-500 @enderror"
                                        wire:model.lazy="editTitleDescription" autocomplete="off">
                                </div>
                                @error('editTitleDescription')
                                    <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                            <button wire:click="updateTitle" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="updateTitle">Submit</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="updateTitle"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>