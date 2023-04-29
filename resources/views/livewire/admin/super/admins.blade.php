<div class="container-lg">
    @can('create-admins')
        <button wire:click="showAddModal" class="btn btn-success align-self-end float-right mb-1">
            add admin
        </button>
    @endcan


    <div class="table-responsive">
        <div class="form-group w-25 w-md-50">
            <input type="search" name="" placeholder="search..." id="search" class="form-control"
                   wire:model="search">
            <select name="" id="" wire:model="typeFilter" class="custom-select w-50 mt-2">
                <option value="0">filter by role</option>
                @foreach($roles as $role)
                    <option value="{{$role->id}}">{{$role->name}}</option>
                @endforeach
            </select>
        </div>
        <table class="table">
            <thead class="bg-primary">
            <td class="text-white">#</td>
            <td class="text-white">checked</td>
            <td class="text-white">name</td>
            <td class="text-white">email</td>
            <td class="text-white">role</td>
            <td class="text-white">actions</td>
            </thead>
            <tbody wire:loading.remove wire:target="addAdmin,updateAdmin,deleteAdmin">
            @forelse($admins as $admin)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>
                        <input type="checkbox" wire:model.defer="selectedAdmins" value="{{$admin->id}}">
                    </td>
                    <td>{{$admin->name}}</td>
                    <td>
                        {{$admin->email}}
                    </td>
                    <td>
                        {{$admin->role->name}}
                    </td>
                    <td>
                        <button class="btn btn-outline-primary btn-sm"
                                wire:click="openEditModal({{$admin->id}})"
                                wire:loading.attr="disabled" wire:target="openEditModal({{$admin->id}})">
                            edit
                        </button>
                        <button type="button" wire:click="openDeleteModal({{$admin->id}})" class="btn btn-outline-danger btn-sm">
                            delete
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align-last: center" class="text-admin"> no admins </td>
                </tr>
            @endforelse
            </tbody>

            <tr wire:loading wire:target="addAdmin,updateAdmin,deleteAdmin">
                <td colspan="7" style="text-align-last: center" class="text-admin text-center">
                    loading ...
                </td>
            </tr>

        </table>
        {{$admins->links()}}
    </div>

    <div wire:ignore.self class="modal fade" tabindex="-1" admin="dialog" id="deleteModal">
            <div class="modal-dialog" admin="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">delete admins</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>are you sure that you want to delete this admin?</p>
                    </div>
                    <div class="modal-footer">
                        <form action="" wire:submit.prevent="deleteAdmin" method="post">
                            <button type="submit" wire:loading.attr="disabled" class="btn btn-danger">delete admin</button>
                        </form>
                        <button type="button" id="closeDeleteSelectedModal" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    <div wire:ignore.self class="modal fade" tabindex="-1" admin="dialog" id="addModal">
        <div class="modal-dialog" admin="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">add admin</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            wire:click="hideAddModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" wire:submit.prevent="addAdmin">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">name</label>
                            <span class="text-danger">@error('name'){{$message}}@enderror</span>
                            <input type="text" wire:model.defer="name" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="">email</label>
                            <span class="text-danger">@error('email'){{$message}}@enderror</span>
                            <input type="email" wire:model.defer="email" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="admin-admin">admin</label>
                            <span class="text-danger">@error('role_id'){{$message}}@enderror</span>
                            <select name="" id="" wire:model.defer="role_id" class="custom-select">
                                <option value="">select a admin</option>
                                @foreach($roles as $role)
                                    <option value="{{$role->id}}">{{$role->name}}</option>
                                @endforeach
                            </select>
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

    <div wire:ignore.self class="modal fade" tabindex="-1" admin="dialog" id="editModal">
        <div class="modal-dialog" admin="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal Title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            wire:click="hideAddModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" wire:submit.prevent="updateAdmin">
                    @csrf
                    <div class="modal-body">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="">name</label>
                                <span class="text-danger">@error('name'){{$message}}@enderror</span>
                                <input type="text" wire:model.defer="name" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="">email</label>
                                <span class="text-danger">@error('email'){{$message}}@enderror</span>
                                <input type="email" wire:model.defer="email" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="admin-admin">admin</label>
                                <span class="text-danger">@error('role_id'){{$message}}@enderror</span>
                                <select name="" id="" wire:model.defer="role_id" class="custom-select">
                                    <option value="">select a admin</option>
                                    @foreach($roles as $role)
                                        <option value="{{$role->id}}">{{$role->name}}</option>
                                    @endforeach
                                </select>
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
