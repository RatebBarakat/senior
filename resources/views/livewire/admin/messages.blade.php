<div class="container-lg">
    <div class="table-responsive">
        <div class="form-group w-25 w-md-50">
            <input type="search" name="" placeholder="search..." id="search" class="form-control"
                   wire:model="search">
            <select name="" id="" wire:model="filter" class="custom-select mt-3">
                <option value="">select to filter</option>
                <option value="request-event">requests for events</option>
            </select>
        </div>
        <table class="table">
            <thead class="bg-primary">
            <td class="text-white">#</td>
            <td class="text-white">body</td>
            <td class="text-white">sender</td>
            <td class="text-white">actions</td>
            </thead>
            <tbody>
            @forelse($messages as $message)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    {{--                    <td>--}}
                    {{--                        <input type="checkbox" wire:model.defer="selectedCategories" value="{{$message->id}}">--}}
                    {{--                    </td>--}}
                    <td>{{$message->body}}</td>
                    <td>
                        {{$message->sender->name}}
                    </td>
                    <td>
                        <button class="btn btn-outline-primary btn-sm"
                                wire:click="markAsRead({{$message->id}})"
                                wire:loading.attr="disabled" wire:target="markAsRead({{$message->id}})">
                            mark as read
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center"> no messages </td>
                </tr>
            @endforelse
            </tbody>
        </table>
        {{-- {{$messages->links()}} --}}
    </div>
</div>