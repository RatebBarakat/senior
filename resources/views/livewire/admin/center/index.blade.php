<div class="container-lg">
    @can('create-employees')
        <button wire:click="showAddModal" class="btn btn-success align-self-end float-right mb-1">
            add employee
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
            <td class="text-white">name</td>
            <td class="text-white">email</td>
            <td class="text-white">role</td>
            <td class="text-white">actions</td>
            </thead>
            <tbody wire:loading.remove wire:target="addAdmin,updateAdmin,deleteAdmin">
            @forelse($employees as $employee)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>
                        <input type="checkbox" wire:model.defer="selectedAdmins" value="{{$employee->id}}">
                    </td>
                    <td>{{$employee->name}}</td>
                    <td>
                        {{$employee->email}}
                    </td>
                    <td>
                        {{$employee->role->name}}
                    </td>
                    <td>
{{--                        <button class="btn btn-outline-primary btn-sm"--}}
{{--                                wire:click="openEditModal({{$employee->id}})"--}}
{{--                                wire:loading.attr="disabled" wire:target="openEditModal({{$employee->id}})">--}}
{{--                            edit--}}
{{--                        </button>--}}
                        <button type="button" wire:click="openDeleteModal({{$employee->id}})" class="btn btn-outline-danger btn-sm">
                            delete
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align-last: center" class="text-employee"> no employees </td>
                </tr>
            @endforelse
            </tbody>

            <tr wire:loading wire:target="addAdmin,updateAdmin,deleteAdmin">
                <td colspan="7" style="text-align-last: center" class="text-employee text-center">
                    loading ...
                </td>
            </tr>

        </table>
    </div>

    <div wire:ignore.self class="modal fade" tabindex="-1" employee="dialog" id="deleteModal">
        <div class="modal-dialog" employee="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">delete employees</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>are you sure that you want to delete this employee?</p>
                </div>
                <div class="modal-footer">
                    <form action="" wire:submit.prevent="deleteAdmin" method="post">
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-danger">delete employee</button>
                    </form>
                    <button type="button" id="closeDeleteSelectedModal" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" tabindex="-1" employee="dialog" id="addModal">
        <div class="modal-dialog" employee="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">add employee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            wire:click="hideAddModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" wire:submit.prevent="addAdminCenter">
                    @csrf
                    <div class="modal-body">
                        <span class="text-danger">@error('employee_id'){{$message}}@enderror</span>
                        <select name="" id="" wire:model="employee_id" class="custom-select">
                            <option value="0">filter by role</option>
                            @foreach($availableEmployees as $availableEmployee)
                                <option value="{{$availableEmployee->id}}">
                                    {{$availableEmployee->name}}
                                </option>
                            @endforeach
                        </select>
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

{{--    <div wire:ignore.self class="modal fade" tabindex="-1" employee="dialog" id="editModal">--}}
{{--        <div class="modal-dialog" employee="document">--}}
{{--            <div class="modal-content">--}}
{{--                <div class="modal-header">--}}
{{--                    <h5 class="modal-title">Modal Title</h5>--}}
{{--                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"--}}
{{--                            wire:click="hideAddModal">--}}
{{--                        <span aria-hidden="true">&times;</span>--}}
{{--                    </button>--}}
{{--                </div>--}}
{{--                <form action="" wire:submit.prevent="updateAdmin">--}}
{{--                    @csrf--}}
{{--                    <div class="modal-body">--}}
{{--                        <div class="modal-body">--}}
{{--                            <div class="form-group">--}}
{{--                                <label for="">name</label>--}}
{{--                                <span class="text-danger">@error('name'){{$message}}@enderror</span>--}}
{{--                                <input type="text" wire:model.defer="name" class="form-control">--}}
{{--                            </div>--}}

{{--                            <div class="form-group">--}}
{{--                                <label for="">email</label>--}}
{{--                                <span class="text-danger">@error('email'){{$message}}@enderror</span>--}}
{{--                                <input type="email" wire:model.defer="email" class="form-control">--}}
{{--                            </div>--}}


{{--                        </div>--}}
{{--                        <div class="modal-footer">--}}
{{--                        <div class="modal-footer">--}}
{{--                            <button type="button" class="btn btn-secondary" data-dismiss="modal"--}}
{{--                                    wire:click="hideEditModal">Close</button>--}}
{{--                            <button type="submit" wire:loading.attr="disabled" class="btn btn-primary">Save changes</button>--}}
{{--                        </div>--}}
{{--                </form>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

</div>
