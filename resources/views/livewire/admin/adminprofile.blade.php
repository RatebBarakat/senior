<div class="container-sm bg-white m-auto py-4 profile-show">
    <div class="row">
        <div class="col-md-4">
            @if($profile->avatar)
                <img class="w-100 h-100" src="{{asset('storage/'.$profile->avatar)}}" alt="">
            @else
                <div class="noimage h-100">
                    no image
                </div>
            @endif
        </div>
        <form action="" wire:submit.prevent="updateProfile" class="col-md-8 row m-0 p-0">
            <div class="col-12">
                <label for="name">name</label>
                @error('name'){{$message}}@enderror
                <input class="ready-only form-control" type="text" wire:model="name" name="" id="">
            </div>

            <div class="col-12">
                <label for="name">location</label>
                @error('location'){{$message}}@enderror
                <input class="ready-only form-control" type="text" name="" id=""
                       wire:model.defer="location">
            </div>

            <div class="col-12">
                avatar
                @error('newAvatar'){{$message}}@enderror

                <input class="form-control-file" type="file" wire:model="newAvatar">
            </div>

            <div class="col-12">
                <label for="name">bio</label>
                @error('bio'){{$message}}@enderror

                <textarea wire:model.defer="bio" class="ready-only form-control" type="text" name="" id="">
                    {{$profile->bio}}
                    </textarea>
            </div>

            <button type="submit" wire:loading.remove class="btn btn-success ml-2 mt-1">update</button>
            <button type="button" wire:loading class="btn btn-success ml-2 mt-1">loading ...</button>
        </form>
    </div>
</div>
