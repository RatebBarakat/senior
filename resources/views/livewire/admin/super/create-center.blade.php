<div class="container-sm bg-white m-auto py-4 profile-show">
    <div class="row">
        <h4 class="text-center col-12">create center</h4>
        <hr class="w-100">
        <div class="col-md-8 row m-0 p-0 align-items-start">
            <div class="w-100 px-2">
                <h5 class="">info of the center</h5>
                <form action="" method="post" wire:submit.prevent="addCenter">
                    <div class="w-100 form-group">
                        <label for="center-name">center name</label>
                        <span class="text-danger">@error('centerName'){{$message}}@enderror</span>
                        <input type="text" class="form-control" wire:model="centerName"
                               placeholder="center name">
                    </div>

                    <div class="form-group">
                        <label for="admin">select an location</label>
                        <span class="text-danger">@error('location'){{$message}}@enderror</span>
                        <select wire:model="location" name="" id="admin" class="custom-select">
                            <option value="select-admin"></option>
                            @foreach($locations as $location)
                                <option value="{{$location->id}}">{{$location->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <span class="text-danger">@error('adminCenter'){{$message}}@enderror</span>

                @if(count($centerAdmins) > 0)
                        <div class="form-group">
                            <label for="admin">select an admin</label>
                            <select wire:model="adminCenter" name="" id="admin" class="custom-select">
                                <option value="{{null}}"></option>
                                @foreach($centerAdmins as $centerAdmin)
                                    <option value="{{$centerAdmin->id}}">{{$centerAdmin->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div class="form-group">
                            no centers admins available you must create one for this center
                        </div>
                    @endif

                    <button type="submit" wire:loading.attr="disabled" wire:target="addCenter"
                            class="btn btn-success mt-1 col-12 btn-block w-100">
                        create center
                    </button>
                </form>
            </div>
        </div>

        <div class="col">
            <h5 class="">admin of the center</h5>
            <form action="" method="post" wire:submit.prevent="createCenterAdmin">
                <div class="form-group">
                    <label for="">admin name</label>
                    <span class="text-danger">@error('adminName'){{$message}}@enderror</span>
                    <input type="text" wire:model.defer="adminName" class="form-control">
                </div>

                <div class="form-group">
                    <label for="">email</label>
                    <span class="text-danger">@error('email'){{$message}}@enderror</span>
                    <input type="email" wire:model.defer="email" class="form-control">
                </div>
{{-- 
                <div class="form-group">
                    <label for="">password</label>
                    <span class="text-danger">@error('password'){{$message}}@enderror</span>
                    <input type="password" wire:model.defer="password" class="form-control">
                </div>

                <div class="form-group">
                    <label for="">password confirm</label>
                    <span class="text-danger">@error('passwordConfirm'){{$message}}@enderror</span>
                    <input type="password" wire:model.defer="passwordConfirm" class="form-control">
                </div> --}}
                <button type="submit" wire:loading.attr="disabled" wire:target="createCenterAdmin"
                        class="btn btn-success mt-1 col-12 btn-block">
                    create center admin
                </button>
            </form>
        </div>
    </div>
</div>
