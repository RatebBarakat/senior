<div class="container-lg">
    <button wire:click="showAddModal" class="btn btn-success align-self-end float-right mb-1"
    >add category</button>
    <div wire:ignore.self class="modal" tabindex="-1" role="dialog" id="addModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal Title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            wire:click="hideAddModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" wire:submit.prevent="addCategory">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">name</label>
                            <span class="text-danger">@error('name'){{$message}}@enderror</span>
                            <input type="text" wire:model="name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">slug</label>
                            <span class="text-danger">@error('slug'){{$message}}@enderror</span>
                            <input type="text" wire:model.defer="slug" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="visible">visible</label>
                            <span class="text-danger">@error('visible'){{$message}}@enderror</span>
                            <input id="visible" type="checkbox" wire:model.defer="visible"
                                   class="form-check">
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
    @if(count($categories)>0)
        <label for="selectAll">select all</label>
        <input type="checkbox" id="selectAll" wire:model="isSelectedAll" wire:click="selectAll">
    @endif
    <div class="table-responsive">
        <div class="form-group w-25 w-md-50">
            <input type="search" name="" placeholder="search..." id="search" class="form-control" wire:model="search">
        </div>
        @if(count($selectedCategories) > 0)
            <button type="button" class="btn btn-danger my-2 float-right"
                    onclick="$('#deleteSelected').modal('show')">delete selected
            </button>
            <div wire:ignore.self class="modal fade" tabindex="-1" role="dialog" id="deleteSelected">
                @csrf
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">delete categories</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>are you sure that you want to delete {{count($selectedCategories)}} selected categories?</p>
                        </div>
                        <div class="modal-footer">
                            <form action="" wire:submit.prevent="deleteSelectedCategories" method="post">
                                <button type="submit" wire:loading.attr="disabled" class="btn btn-danger">delete selected</button>
                            </form>
                            <button type="button" id="closeDeleteSelectedModal" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <table class="table">
            <thead class="bg-primary">
            <td class="text-white">#</td>
            <td class="text-white"></td>
            <td class="text-white">name</td>
            <td class="text-white">slug</td>
            <td class="text-white">visible</td>
            <td class="text-white">actions</td>
            </thead>
            <tbody>
            @forelse($categories as $category)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>
                        <input type="checkbox" wire:model="selectedCategories" value="{{$category->id}}">
                    </td>
                    <td>{{$category->name}}</td>
                    <td>{{$category->slug}}</td>
                    <td>{{$category->visible == 1 ? "visible" : "non visible" }}</td>
                    <td>
                        <button class="btn btn-outline-primary btn-sm"
                                wire:click="openEditModal({{$category->id}})"
                                wire:loading.attr="disabled" wire:target="openEditModal({{$category->id}})">
                            edit
                        </button>
                        <button type="button" wire:click="openDeleteModal({{$category->id}})" class="btn btn-outline-danger btn-sm">delete</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center"> no categories </td>
                </tr>
            @endforelse
            </tbody>
        </table>
        {{$categories->links()}}
    </div>
    <div wire:ignore.self class="modal fade" tabindex="-1" role="dialog" id="deleteCategory">
        @csrf
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">delete categories</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>are you sure that you want to delete this category?</p>
                </div>
                <div class="modal-footer">
                    <form action="" wire:submit.prevent="deleteCategory" method="post">
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-danger">delete category</button>
                    </form>
                    <button type="button" id="closeDeleteSelectedModal" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</div>
