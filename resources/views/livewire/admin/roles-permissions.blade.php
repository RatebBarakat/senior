<div class="container-lg">
   @can('create-roles')
        <button wire:click="showAddModal" class="btn btn-success align-self-end float-right mb-1">
            add role
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
                <td class="text-white">name</td>
                <td class="text-white">permissions</td>
                <td class="text-white">actions</td>
                </thead>
                <tbody>
                @forelse($roles as $role)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        {{--                    <td>--}}
                        {{--                        <input type="checkbox" wire:model.defer="selectedCategories" value="{{$role->id}}">--}}
                        {{--                    </td>--}}
                        <td>{{$role->name}}</td>
                        <td>
                            @if($role->name == 'super-admin')
                                *
                            @else
                                @foreach($role->permissions as $permission)
                                    {{$permission->name}}   @if (!$loop->last), @endif
                                @endforeach
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-outline-primary btn-sm"
                                    wire:click="openEditModal({{$role->id}})"
                                    wire:loading.attr="disabled" wire:target="openEditModal({{$role->id}})">
                                edit
                            </button>
                            @can('delete-roles')
                                @if($role->editable )
                                    <button type="button" wire:click="openDeleteModal({{$role->id}})"
                                            class="btn btn-outline-danger btn-sm">
                                        delete
                                    </button>
                                @endif
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center"> no roles </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            {{$roles->links()}}
        </div>

    <div wire:ignore.self class="modal fade" tabindex="-1" role="dialog" id="addModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">add role</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            wire:click="hideAddModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" wire:submit.prevent="addRole">
                    @csrf
                    <div class="modal-body">
                            <div class="form-group">
                                <label for="">name</label>
                                <span class="text-danger">@error('name'){{$message}}@enderror</span>
                                <input type="text" wire:model.defer="name" class="form-control">
                            </div>
                        <div class="flex gap-1">
                            @foreach($permissions as $permission)
                                <span class="mx-1" style="display: inline-block">
                                    <input type="checkbox" name="" wire:model.defer="selectedPermissions"
                                           value="{{$permission->id}}" id="{{$permission->name}}">
                                <label for="{{$permission->name}}">{{$permission->name}}</label>
                                </span>
                            @endforeach
                        </div>
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

    <div wire:ignore.self class="modal fade" tabindex="-1" role="dialog" id="editModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal Title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            wire:click="hideAddModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" wire:submit.prevent="updateRole">
                    @csrf
                    <div class="modal-body">
                        @if($role->editable == 1)
                            <div class="form-group">
                                <label for="">name</label>
                                <span class="text-danger">@error('name'){{$message}}@enderror</span>
                                <input type="text" wire:model.defer="name" class="form-control">
                            </div>
                        @endif
                        <div class="flex gap-1">
                            @foreach($permissions as $permission)
                                <span class="mx-1" style="display: inline-block">
                                    <input type="checkbox" name="" wire:model.defer="selectedPermissions"
                                           value="{{$permission->id}}" id="{{$permission->id}}"
                                    @checked(in_array($permission->id,$selectedPermissions))>
                                <label for="{{$permission->id}}">{{$permission->name}}</label>
                                </span>
                            @endforeach
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

    <div wire:ignore.self class="modal fade" tabindex="-1" role="dialog" id="deleteModal">
        @csrf
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">delete roles</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>are you sure that you want to delete this role?</p>
                </div>
                <div class="modal-footer">
                    <form action="" wire:submit.prevent="deleteRole" method="post">
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-danger">delete role</button>
                    </form>
                    <button type="button" id="closeDeleteSelectedModal" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</div>
