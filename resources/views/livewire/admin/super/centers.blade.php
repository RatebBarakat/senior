<div class="container-lg">
<div class="container-lg">
    @can('create-centers')
        <a href="{{route('admin.centers.create')}}" class="btn btn-success align-self-end float-right mb-1">
            add center
        </a>
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
            <td class="text-white">name</td>
            <td class="text-white">location</td>
            <td class="text-white">center admin</td>
            <td class="text-white">actions</td>
            </thead>
            <tbody>
            @forelse($centers as $center)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>
                        <input type="checkbox" wire:model.defer="selectedCenters" value="{{$center->id}}">
                    </td>
                    <td>{{$center->name}}</td>
                    <td>
                       {{$center->location->city ?? ""}}
                    </td>
                    <td>
                        @if($center->admin_id == null)
                            <span>no admin</span>
                        @else
                            <a href="{{route('admin.profile.show',[$center->admin_id])}}">
                                {{$center->admin->name}}
                            </a>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-outline-primary btn-sm"
                                wire:click="openEditModal({{$center->id}})"
                                wire:loading.attr="disabled" wire:target="openEditModal({{$center->id}})">
                            edit
                        </button>
                        <button type="button" wire:click="openDeleteModal({{$center->id}})" class="btn btn-outline-danger btn-sm">
                            delete
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center"> no centers </td>
                </tr>
            @endforelse
            </tbody>

{{--            <tr wire:loading wire:target="addCenter,updateCenter,deleteCenter">--}}
{{--                <td colspan="7" style="text-align-last: center" class="text-admin text-center">--}}
{{--                    loading ...--}}
{{--                </td>--}}
{{--            </tr>--}}

        </table>
        {{$centers->links()}}
    </div>

{{--    <div wire:ignore.self class="modal fade" tabindex="-1" center="dialog" id="addModal">--}}
{{--        <div class="modal-dialog" center="document">--}}
{{--            <div class="modal-content">--}}
{{--                <div class="modal-header">--}}
{{--                    <h5 class="modal-title">add center</h5>--}}
{{--                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"--}}
{{--                            wire:click="hideAddModal">--}}
{{--                        <span aria-hidden="true">&times;</span>--}}
{{--                    </button>--}}
{{--                </div>--}}
{{--                <form action="" wire:submit.prevent="addCenter">--}}
{{--                    @csrf--}}
{{--                    <div class="modal-body">--}}
{{--                        <div class="form-group">--}}
{{--                            <label for="">name</label>--}}
{{--                            <span class="text-danger">@error('name'){{$message}}@enderror</span>--}}
{{--                            <input type="text" wire:model.defer="name" class="form-control">--}}
{{--                        </div>--}}
{{--                        <span class="text-danger">@error('location_id'){{$message}}@enderror</span>--}}
{{--                        <select name="" id="" wire:model.defer="location_id" class="custom-select">--}}
{{--                            <option value="">select an option</option>--}}
{{--                            @foreach($locations as $location)--}}
{{--                                <option value="{{$location->id}}">{{$location->name}}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}
{{--                            <div class="form-group">--}}
{{--                                <label for="admin-center">admin center</label>--}}
{{--                                <select name="" id="" wire:model.defer="admin_id" class="custom-select">--}}
{{--                                    <option value="{{null}}">select an admin</option>--}}
{{--                                    @foreach($admins as $admin)--}}
{{--                                        <option value="{{$admin->id}}">{{$admin->name}}</option>--}}
{{--                                    @endforeach--}}
{{--                                </select>--}}
{{--                            </div>--}}
{{--                    </div>--}}
{{--                    <div class="modal-footer">--}}
{{--                        <button type="button" class="btn btn-secondary" data-dismiss="modal"--}}
{{--                                wire:click="hideAddModal">Close</button>--}}
{{--                        <button type="submit" wire:loading.attr="disabled" class="btn btn-primary">Save changes</button>--}}
{{--                    </div>--}}
{{--                </form>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

    <div wire:ignore.self class="modal fade" tabindex="-1" center="dialog" id="editModal">
        <div class="modal-dialog" center="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal Title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            wire:click="hideAddModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" wire:submit.prevent="updateCenter">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">name</label>
                            <span class="text-danger">@error('name'){{$message}}@enderror</span>
                            <input type="text" wire:model.defer="name" class="form-control">
                        </div>
                        @if($center != null)
                            <select class="custom-select" wire:model="location_id" name="" id="">
                                <option value="0">select a locarion</option>
                                @foreach($locations as $location)
                                    <option value="{{$location->id}}">
                                        {{$location->name}}
                                    </option>
                                @endforeach
                            </select>

                            <div class="form-group">
                                <label for="admin-center">admin center</label>
                                <select name="" id="" wire:model.defer="admin_id" class="custom-select">
                                    @if($centerAdmin != null)
                                        <option value="{{$centerAdmin->id}}">{{$centerAdmin->name}}</option>
                                    @endif
                                    @foreach($admins as $admin)
                                        <option value="{{$admin->id}}">
                                            {{$admin->name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
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

    <div wire:ignore.self class="modal fade" tabindex="-1" center="dialog" id="deleteModal">
        @csrf
        <div class="modal-dialog" center="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">delete centers</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>are you sure that you want to delete this center?</p>
                </div>
                <div class="modal-footer">
                    <form action="" wire:submit.prevent="deleteCenter" method="post">
                        @csrf
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-danger">delete center</button>
                    </form>
                    <button type="button" id="closeDeleteSelectedModal" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</div>
