<div class="container-lg bg-white m-auto py-4 shadow" style="background: white">
        <div style="display: flex;align-items: center;justify-content: space-between;" class="w-100">
            <h3 class="text-primary">blood request</h3>
            <button class="btn btn-success" wire:loading.attr="disabled"
             wire:click="showCompleteRequest">
                complete request
            </button>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="patient_name text-primary">patient name</label>
                <p @readonly(true) class="form-control">{{$bloodRequest->patient_name}}</p>
            </div>
            <div class="form-group col-md-6">
                <label for="patient_name text-primary">hospital name</label>
                <p @readonly(true) class="form-control">{{$bloodRequest->hospital_name}}</p>
            </div>
            <div class="form-group col-md-6">
                <label for="patient_name text-primary">hospital_location</label>
                <p @readonly(true) class="form-control">{{$bloodRequest->hospital_location}}</p>
            </div>
            <div class="form-group col-md-6">
                <label for="patient_name text-primary">contact_name</label>
                <p @readonly(true) class="form-control">{{$bloodRequest->contact_name ?? 'no contact name'}}</p>
            </div>
            <div class="form-group col-md-6">
                <label for="patient_name text-primary">contact_phone_number</label>
                <p @readonly(true) class="form-control">{{$bloodRequest->contact_phone_number ?? 'no contact_phone_number'}}</p>
            </div>
            <div class="form-group col-md-6">
                <label for="patient_name text-primary">blood_type_needed</label>
                <p @readonly(true) class="form-control">{{$bloodRequest->blood_type_needed}}</p>
            </div>
            <div class="form-group col-md-6">
                <label for="patient_name text-primary">quantity_needed</label>
                <p @readonly(true) class="form-control">{{$bloodRequest->quantity_needed}}</p>
            </div>
            <div class="form-group col-md-6">
                <label for="patient_name text-primary">urgency_level</label>
                <p @readonly(true) class="form-control">{{$bloodRequest->urgency_level}}</p>
            </div>
            <div class="form-group col-md-6">
                <label for="patient_name text-primary">status</label>
                <p @readonly(true) class="form-control">{{$bloodRequest->status}}</p>
            </div>
        </div>
        <div wire:ignore.self class="modal" tabindex="-1" role="dialog" id="showComleteModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Modal Title</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                wire:click="hideAddModal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="" wire:submit.prevent="completeRequest">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="">quantity needed:</label>
                                <span class="text-danger" class="form-control">{{$bloodRequest->quantity_needed}}</span>
                            </div>
                            <div class="form-group">
                                <label for="">quantity selected:</label>
                                <span class="text-primary
                                 @if($totalSelected == $bloodRequest->quantity_needed) text-success @endif"
                                 >
                                 {{$totalSelected}}
                                </span>
                                @if($totalSelected == $bloodRequest->quantity_needed)
                                <p class="text-success">
                                    complete request
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#0f0" class="bi bi-check2-circle" viewBox="0 0 16 16">
                                        <path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0z"/>
                                        <path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l7-7z"/>
                                    </svg>
                                </p>
                               
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="">available blood</label>
                            <div class="d-flex gap-2" style="gap: 10px">
                                @forelse ($AvailableBLood as $blood)
                                    <button type="button" wire:loading.attr="disabled"
                                    class="btn btn-sm btn-success" wire:click="addBlood({{$blood->id}})">
                                        {{$blood->quantity}} litter
                                    </button>
                                @empty
                                    no blood available
                                @endforelse
                            </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                    wire:click="hideAddModal">Close</button>
                            <button type="submit" @disabled($totalSelected != $bloodRequest->quantity_needed) wire:loading.attr="disabled" class="btn btn-primary">
                                comlpete request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</div>