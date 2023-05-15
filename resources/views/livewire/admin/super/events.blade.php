<div class="container-lg">
    @can('super-admin')
        <button wire:click="showAddModal" class="btn btn-success align-self-end float-right mb-1">
            add event
        </button>
    @endcan


    <div class="table-responsive">
        <div class="form-group w-25 w-md-50">
            <input type="search" name="" placeholder="search..." id="search" class="form-control"
                   wire:model="search">
        </div>
        <table class="table">
            <thead class="bg-primary">
            <td class="text-white">#</td>
            <td class="text-white">checked</td>
            <td class="text-white">title</td>
            <td class="text-white">description</td>
            <td class="text-white">start at</td>
            <td class="text-white">end at</td>
            <td class="text-white">included centers</td>
            <td class="text-white">actions</td>
            </thead>
            <tbody wire:loading.remove wire:target="addEvent,updateEvent,deleteEvent">
            @forelse($events as $event)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>
                        <input type="checkbox" wire:model.defer="selectedevents" value="{{$event->id}}">
                    </td>
                    <td>{{$event->title}}</td>
                    <td>
                        {{$event->description}}
                    </td>
                    <td>
                        {{$event->start_date}}
                    </td>
                    <td>
                        {{$event->end_date}}
                    </td>
                    <td>
                        @foreach ($event->centers as $center)
                            {{$center->name}} @if (!$loop->last)
                                ,
                            @endif
                        @endforeach
                    </td>
                    <td>
                        <button class="btn btn-outline-primary btn-sm"
                                wire:click="openEditModal({{$event->id}})"
                                wire:loading.attr="disabled" wire:target="openEditModal({{$event->id}})">
                            edit
                        </button>
                        <button type="button" wire:click="openDeleteModal({{$event->id}})" class="btn btn-outline-danger btn-sm">
                            delete
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align-last: center" class="text-event"> no events </td>
                </tr>
            @endforelse
            </tbody>

            <tr wire:loading wire:target="addEvent,updateEvent,deleteEvent">
                <td colspan="7" style="text-align-last: center" class="text-event text-center">
                    loading ...
                </td>
            </tr>

        </table>
        {{$events->links()}}
    </div>

    <div wire:ignore.self class="modal fade" tabindex="-1" event="dialog" id="deleteModal">
            <div class="modal-dialog" event="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">delete events</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>are you sure that you want to delete this event?</p>
                    </div>
                    <div class="modal-footer">
                        <form action="" wire:submit.prevent="deleteEvent" method="post">
                            <button type="submit" wire:loading.attr="disabled" class="btn btn-danger">delete event</button>
                        </form>
                        <button type="button" id="closeDeleteSelectedModal" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    <div wire:ignore.self class="modal fade" tabindex="-1" event="dialog" id="addModal">
        <div class="modal-dialog" event="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">add event</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            wire:click="hideAddModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" wire:submit.prevent="addEvent">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">title</label>
                            <span class="text-danger">@error('title'){{$message}}@enderror</span>
                            <input type="text" wire:model.defer="title" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="">description</label>
                            <span class="text-danger">@error('description'){{$message}}@enderror</span>
                            <textarea type="text" wire:model.defer="description" class="form-control">
                            </textarea>
                        </div>

                        <div class="form-group">
                            <label for="">included centers</label>
                            <span class="text-danger">@error('selectedCenters'){{$message}}@enderror</span>
                            <div class="d-flex gap-2" style="gap: 10px">
                                @forelse ($centers as $center)
                                    <button type="button" wire:loading.attr="disabled"
                                    class="btn btn-sm btn-primary 
                                    @if(in_array($center->id,$selectedcenters)) btn-success @endif" 
                                    wire:click="toggleCenter({{$center->id}})">
                                    @if (in_array($center->id,$selectedcenters))
                                    <svg style="margin-right: 3px" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#0f0" class="bi bi-check2-circle" viewBox="0 0 16 16">
                                        <path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0z"/>
                                        <path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l7-7z"/>
                                    </svg>
                                    @endif
                                        {{$center->name}}
                                    </button>
                                @empty
                                    no center available
                                @endforelse
                            </div>
                            
                            
                        </div>

                        <div class="form-group">
                            <label for="">start at</label>
                            <span class="text-danger">@error('startAt'){{$message}}@enderror</span>
                            <input type="date" wire:model.defer="startAt" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="">end at</label>
                            <span class="text-danger">@error('endAt'){{$message}}@enderror</span>
                            <input type="date" wire:model.defer="endAt" class="form-control">
                        </div>

                        {{-- <div class="form-group">
                            <label for="event-event">event</label>
                            <span class="text-danger">@error('role_id'){{$message}}@enderror</span>
                            <select name="" id="" wire:model.defer="role_id" class="custom-select">
                                <option value="">select a event</option>
                                {{-- @foreach($roles as $role)
                                    <option value="{{$role->id}}">{{$role->name}}</option>
                                @endforeach --}}
                            {{-- </select> --}}
                        {{-- </div> --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                wire:click="hideAddModal">Close</button>
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" tabindex="-1" event="dialog" id="editModal">
        <div class="modal-dialog" event="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal Title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            wire:click="hideAddModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" wire:submit.prevent="updateEvent">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">title</label>
                            <span class="text-danger">@error('title'){{$message}}@enderror</span>
                            <input type="text" wire:model.defer="title" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="">description</label>
                            <span class="text-danger">@error('description'){{$message}}@enderror</span>
                            <textarea type="text" wire:model.defer="description" class="form-control">
                            </textarea>
                        </div>

                        <div class="form-group">
                            <label for="">included centers</label>
                            <span class="text-danger">@error('selectedCenters'){{$message}}@enderror</span>
                            <div class="d-flex gap-2" style="gap: 10px">
                                @forelse ($centers as $center)
                                    <button type="button" wire:loading.attr="disabled"
                                    class="btn btn-sm btn-primary 
                                    @if(in_array($center->id,$selectedcenters)) btn-success @endif" 
                                    wire:click="toggleCenter({{$center->id}})">
                                    @if (in_array($center->id,$selectedcenters))
                                    <svg style="margin-right: 3px" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#0f0" class="bi bi-check2-circle" viewBox="0 0 16 16">
                                        <path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0z"/>
                                        <path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l7-7z"/>
                                    </svg>
                                    @endif
                                        {{$center->name}}
                                    </button>
                                @empty
                                    no center available
                                @endforelse
                            </div>
                            
                            
                        </div>

                        <div class="form-group">
                            <label for="">start at</label>
                            <span class="text-danger">@error('startAt'){{$message}}@enderror</span>
                            <input type="date" wire:model.defer="startAt" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="">end at</label>
                            <span class="text-danger">@error('endAt'){{$message}}@enderror</span>
                            <input type="date" wire:model.defer="endAt" class="form-control">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                wire:click="hideEditModal">Close</button>
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
