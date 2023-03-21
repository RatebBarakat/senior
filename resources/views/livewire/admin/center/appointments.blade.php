<div>
    <div class="container-lg">
        <div class="table-responsive">
            <div class="form-group w-25 w-md-50">
                <input type="search" name="" placeholder="search..." id="search" class="form-control"
                       wire:model="search">
            </div>
            <table class="table">
                <thead class="bg-primary">
                <td class="text-white">#</td>
                <td class="text-white">checked</td>
                <td class="text-white">donor name</td>
                <td class="text-white">blood type</td>
                <td class="text-white">date</td>
                <td class="text-white">time</td>
                <td class="text-white">actions</td>
                </thead>
                <tbody wire:loading.remove wire:target="addAdmin,updateAdmin,deleteAdmin">
                @forelse($appointments as $appointment)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>
                            <input type="checkbox" wire:model.defer="selectedAdmins" value="{{$appointment->id}}">
                        </td>
                        <td>{{$appointment->user->name}}</td>
                        <td>{{$appointment->blood_type}}</td>
                        <td>
                            {{$appointment->date}}
                        </td>
                        <td>
                            {{$appointment->time}}
                        </td>
                        <td>
    {{--                        <button class="btn btn-outline-primary btn-sm"--}}
    {{--                                wire:click="openEditModal({{$appointment->id}})"--}}
    {{--                                wire:loading.attr="disabled" wire:target="openEditModal({{$appointment->id}})">--}}
    {{--                            edit--}}
    {{--                        </button>--}}
                            <button type="button" wire:click="showCompleteAppointment({{$appointment->id}})" class="btn btn-outline-danger btn-sm">
                                mark as complete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align-last: center" class="text-appointment"> no appointments </td>
                    </tr>
                @endforelse
                </tbody>
    
                <tr wire:loading wire:target="addAdmin,updateAdmin,deleteAdmin">
                    <td colspan="7" style="text-align-last: center" class="text-appointment text-center">
                        loading ...
                    </td>
                </tr>
    
            </table>
        </div>
    </div>

      <div wire:ignore.self class="modal fade" tabindex="-1" employee="dialog" id="completeModal">
        <div class="modal-dialog" employee="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">delete employees</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Enter information about appointment</p>

                    <form action="" wire:submit.prevent="completeAppointment" method="post">

                        <div class="form-group w-100">
                            <label for="">quantity</label>
                            <span class="text-danger">@error('quantity'){{$message}}@enderror</span>
                            <input type="number" wire:model.defer="quantity" class="form-control">
                        </div>
                       
                </div>
                <div class="modal-footer">
                        <button wire:loading.remove wire:target="completeAppointment" type="submit" wire:loading.attr="disabled" class="btn btn-success">
                            mark as complete
                        </button>

                        <button wire:loading wire:target="completeAppointment" type="submit" wire:loading.attr="disabled" class="btn btn-success">
                            sending email
                        </button>
                    </form>
                    <button type="button" id="closeDeleteSelectedModal" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
