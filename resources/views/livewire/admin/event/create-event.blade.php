<div class="container-sm bg-white m-auto py-4 profile-show">
        <form action="" method="POST" wire:submit.prevent="requestEvent">
            <span><div class="text-danger">@error('bloodType'){{$message}}@enderror</div>
                <select name="" id="" class=" custom-select w-25" wire:model="bloodType">
                    <option value="">select blood type</option>
                    @foreach ($bloodTypes as $blood)
                        <option value="{{$blood->blood_type}}">{{$blood->blood_type}}</option>
                    @endforeach
                </select>
                <div class="form-group">
                    <label for="quantity">quantity needed</label>
                    <span class="text-danger">@error('quantity'){{$message}}@enderror</span>
                    <input type="number" name=""
                    class="form-control w-25 mt-3" placeholder="quantity" id="quantity" min="0" max="50" wire:model="quantity">
                </div>
            </span>
            @if ($bloodType != "" && $quantity != "")
                <strong>message that will be send</strong> :{{"we need and event for {$bloodType} for donating a quantity of {$quantity} liters."}}
            @endif
            <div class="form-group mt-3">
                <button @disabled($bloodType == "" || $quantity == "") type="submit" class="btn btn-success">request event</button>
            </div>
        </form>
</div>