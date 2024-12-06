<div>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js'></script>
    <div class="card">
        <header class=" card-header noborder">
            <h4 class="card-title">
                Genuine Calendar
            </h4>
            <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 rtl:space-x-reverse">
                <button wire:click="openNewEventSec" class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1">
                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                    Create Event
                </button>
            </div>
        </header>
        <div class="card-body px-6 pb-6">
            <div wire:ignore id='calendar'></div>

        </div>


        @if ($newEventSection)
            <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show" tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog" style="display: block;">
                <div class="modal-dialog top-1/2 !-translate-y-1/2 relative w-auto pointer-events-none">
                    <div class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                        <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                            <!-- Modal header -->
                            <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                                <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                    Add Event
                                </h3>

                                <button wire:click="closeNewEventSec" type="button" class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white" data-bs-dismiss="modal">
                                    <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10
                        11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>


                            <!-- Modal body -->
                            <div class="p-6 space-y-4">
                                @if (session()->has('message'))
                                    <div class="alert alert-success mt-3">
                                        {{ session('message') }}
                                    </div>
                                @endif
                                <div class="from-group">
                                    <div class="input-area">
                                        <label for="title" class="form-label">Title</label>
                                        <input id="title" type="text" class="form-control @error('title') !border-danger-500 @enderror" wire:model.defer="title">
                                        @error('title')
                                            <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                                        <div class="input-area">
                                            <label for="start_time" class="form-label">Start Time</label>
                                            <input id="start_time" type="datetime-local" class="form-control @error('start_time') !border-danger-500 @enderror" wire:model.defer="start_time">
                                        </div>
                                        <div class="input-area">
                                            <label for="end_time" class="form-label">End Time</label>
                                            <input id="end_time" type="datetime-local" class="form-control @error('end_time') !border-danger-500 @enderror" wire:model.defer="end_time">
                                        </div>
                                    </div>
                                    @error('start_time')
                                        <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror

                                    @error('end_time')
                                        <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror

                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                                        <div class="input-area">
                                            <label for="all_day" class="form-label">All Day</label>
                                            <input id="all_day" type="checkbox" class="form-control @error('all_day') !border-danger-500 @enderror" wire:model.defer="all_day">
                                        </div>
                                        <div class="input-area">
                                            <label for="all_user" class="form-label">All User</label>
                                            <input id="all_user" type="checkbox" class="form-control @error('all_user') !border-danger-500 @enderror" wire:model.defer="all_user">
                                        </div>
                                    </div>

                                    <div class="input-area">
                                        <label for="location" class="form-label">Location</label>
                                        <input id="location" type="text" class="form-control @error('location') !border-danger-500 @enderror" wire:model.defer="location">
                                        @error('location')
                                            <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="input-area">
                                        <label for="note" class="form-label">Note</label>
                                        <textarea id="note" class="form-control @error('note') !border-danger-500 @enderror" wire:model.defer="note"></textarea>
                                        @error('note')
                                            <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="grid grid-cols-1 mt-3">
                                        @foreach ($users_array as $index => $user)
                                            <div class="card-body rounded-md bg-[#E5F9FF] dark:bg-slate-700 shadow-base mb-5 p-2">
                                                <div class="grid grid-cols-8 gap-2 items-center">
                                                    <div class="input-area col-span-4">
                                                        <label for="user_tag_{{ $index }}" class="form-label">User Tag</label>
                                                        <select name="users_array[{{ $index }}][tag]" id="user_tag_{{ $index }}" class="form-control w-full mt-2 @error('users_array.' . $index . '.tag') !border-danger-500 @enderror" wire:model="users_array.{{ $index }}.tag">
                                                            <option>None</option>
                                                            @foreach ($USER_TAGS as $USER_TAG)
                                                                <option value="{{ $USER_TAG }}">{{ $USER_TAG }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    @if ($user['tag'] == 'guest')
                                                        <div class="input-area col-span-4">
                                                            <label for="guest_name_{{ $index }}" class="form-label">Guest Name</label>
                                                            <input id="guest_name_{{ $index }}" type="text" class="form-control w-full mt-2 @error('users_array.' . $index . '.guest_name') !border-danger-500 @enderror" wire:model.defer="users_array.{{ $index }}.guest_name" placeholder="Guest Name">
                                                        </div>
                                                    @else
                                                        <div class="input-area col-span-4">
                                                            <label for="userId_{{ $index }}" class="form-label">Name</label>
                                                            <select name="users_array[{{ $index }}][user_id]" id="userId_{{ $index }}" class="form-control w-full mt-2 @error('users_array.' . $index . '.user_id') !border-danger-500 @enderror" wire:model.defer="users_array.{{ $index }}.user_id">
                                                                <option>Select user...</option>
                                                                @foreach ($USERS as $USER)
                                                                    <option value="{{ $USER->id }}">{{ ucwords($USER->first_name) }} {{ ucwords($USER->last_name) }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    @endif

                                                    @if ($loop->index > 0)
                                                        <div class="col-span-1 flex items-center">
                                                            <button class="action-btn" wire:click="removeUser({{ $index }})" type="button">
                                                                <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach

                                        <button type="button" wire:click="addUser" class="btn btn-dark mt-3 btn-sm">Add User</button>
                                    </div>
                                </div>

                                <!-- Modal footer -->
                                <div class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                    <button wire:click="addEvent" data-bs-dismiss="modal" class="btn inline-flex justify-center text-white bg-black-500">
                                        <span wire:loading.remove wire:target="addEvent">Submit</span>
                                        <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]" wire:loading wire:target="addEvent" icon="line-md:loading-twotone-loop"></iconify-icon>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
        @endif

        @if ($eventID)
            <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show" tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog" style="display: block;">
                <div class="modal-dialog top-1/2 !-translate-y-1/2 relative w-auto pointer-events-none">
                    <div class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                        <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                            <!-- Modal header -->
                            <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                                <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                    Add Event
                                </h3>

                                <button wire:click="closeUpdateEvent" type="button" class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white" data-bs-dismiss="modal">
                                    <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10
                        11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>


                            <!-- Modal body -->
                            <div class="p-6 space-y-4">
                                @if (session()->has('message'))
                                    <div class="alert alert-success mt-3">
                                        {{ session('message') }}
                                    </div>
                                @endif
                                <div class="from-group">
                                    <div class="input-area">
                                        <label for="title" class="form-label">Title</label>
                                        <input id="title" type="text" class="form-control @error('title') !border-danger-500 @enderror" wire:model.defer="title">
                                        @error('title')
                                            <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                                        <div class="input-area">
                                            <label for="start_time" class="form-label">Start Time</label>
                                            <input id="start_time" type="datetime-local" class="form-control @error('start_time') !border-danger-500 @enderror" wire:model.defer="start_time">
                                        </div>
                                        <div class="input-area">
                                            <label for="end_time" class="form-label">End Time</label>
                                            <input id="end_time" type="datetime-local" class="form-control @error('end_time') !border-danger-500 @enderror" wire:model.defer="end_time">
                                        </div>
                                    </div>
                                    @error('start_time')
                                        <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror

                                    @error('end_time')
                                        <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror

                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                                        <div class="input-area">
                                            <label for="all_day" class="form-label">All Day</label>
                                            <input id="all_day" type="checkbox" class="form-control @error('all_day') !border-danger-500 @enderror" wire:model.defer="all_day">
                                        </div>
                                        <div class="input-area">
                                            <label for="all_user" class="form-label">All User</label>
                                            <input id="all_user" type="checkbox" class="form-control @error('all_user') !border-danger-500 @enderror" wire:model.defer="all_user">
                                        </div>
                                    </div>

                                    <div class="input-area">
                                        <label for="location" class="form-label">Location</label>
                                        <input id="location" type="text" class="form-control @error('location') !border-danger-500 @enderror" wire:model.defer="location">
                                        @error('location')
                                            <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="input-area">
                                        <label for="note" class="form-label">Note</label>
                                        <textarea id="note" class="form-control @error('note') !border-danger-500 @enderror" wire:model.defer="note"></textarea>
                                        @error('note')
                                            <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Modal footer -->
                                @if (!$deleteEventConfirmation)
                                    <div class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">

                                        <button wire:click="deleteThisEvent" data-bs-dismiss="modal" class="btn inline-flex justify-center text-white bg-danger-500">
                                            <span wire:loading.remove wire:target="deleteThisEvent">Delete event</span>
                                            <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]" wire:loading wire:target="deleteThisEvent" icon="line-md:loading-twotone-loop"></iconify-icon>
                                        </button>

                                        <button wire:click="updateEvent" data-bs-dismiss="modal" class="btn inline-flex justify-center text-white bg-black-500">
                                            <span wire:loading.remove wire:target="updateEvent">Submit</span>
                                            <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]" wire:loading wire:target="updateEvent" icon="line-md:loading-twotone-loop"></iconify-icon>
                                        </button>
                                    </div>
                                @else
                                    <div class="flex items-center p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                        <div class="py-[18px] px-6 font-normal font-Inter text-sm rounded-md bg-warning-500 bg-opacity-[14%] text-warning-500">
                                            <div class="flex items-start space-x-3 rtl:space-x-reverse">
                                                <div class="flex-1">
                                                    Are you sure you want to delete ?
                                                </div>
                                            </div>
                                        </div>

                                        <button wire:click="ConfirmdDeleteThisEvent" data-bs-dismiss="modal" class="btn inline-flex justify-center text-white bg-danger-500">
                                            <span wire:loading.remove wire:target="ConfirmdDeleteThisEvent">Yes, Delete</span>
                                            <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]" wire:loading wire:target="ConfirmdDeleteThisEvent" icon="line-md:loading-twotone-loop"></iconify-icon>
                                        </button>

                                        <button wire:click="IgnoreDeleteThisEvent" data-bs-dismiss="modal" class="btn inline-flex justify-center text-white bg-black-500">
                                            <span wire:loading.remove wire:target="IgnoreDeleteThisEvent">No</span>
                                            <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]" wire:loading wire:target="IgnoreDeleteThisEvent" icon="line-md:loading-twotone-loop"></iconify-icon>
                                        </button>
                                    </div>
                                @endif



                            </div>
                        </div>
                    </div>
        @endif



    </div>

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            console.log(@json($events))
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next',
                    center: 'title',
                    right: 'today dayGridMonth,timeGridWeek,dayGridDay'
                },
                // timeZone: 'UTC',
                selectable: true,
                eventBorderColor: 'white',
                events: @json($events),
                eventClick: function(info) {
                    info.jsEvent.preventDefault(); // don't let the browser navigate

                    // Check if the event is a CalendarEvent and trigger the Livewire method
                    if (info.event.id.startsWith('event')) {
                        let eventId = info.event.id.replace('event', '');
                        Livewire.emit('showEvent', eventId);
                    } else if (info.event.url) {
                        window.open(info.event.url);
                    }
                }
            });
            calendar.render();
        });
    </script>
</div>
