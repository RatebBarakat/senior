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
                <td class="text-white">patient name</td>
                <td class="text-white">hosptital name</td>
                <td class="text-white">hospital location</td>
                <td class="text-white">constact name</td>
                <td class="text-white">contact phone number</td>
                <td class="text-white">blood type needed</td>
                <td class="text-white">quantity needed</td>
                <td class="text-white">urgency level</td>
                <td class="text-white">status</td>
                <td class="text-white">actions</td>
                </thead>
                <tbody wire:loading.remove wire:target="addAdmin,updateAdmin,deleteAdmin">
                    @forelse($bloodRequests as $bloodRequest)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$bloodRequest->patient_name}}</td>
                        <td>{{$bloodRequest->hospital_name}}</td>
                        <td>{{$bloodRequest->hospital_location}}</td>
                        <td>{{$bloodRequest->contact_name}}</td>
                        <td>{{$bloodRequest->contact_phone_number}}</td>
                        <td>{{$bloodRequest->blood_type_needed}}</td>
                        <td>{{$bloodRequest->quantity_needed}}</td>
                        <td>{{$bloodRequest->urgency_level}}</td>
                        <td>{{$bloodRequest->status}}</td>
                        <td>
                            <a  
                                @if(!isset($sumAvailableByType[$bloodRequest->blood_type_needed]) 
                                || $sumAvailableByType[$bloodRequest->blood_type_needed] < $bloodRequest->quantity_needed)
                                style="cursor: not-allowed;" href="javascript:void(0)"  
                                @else 
                                href="{{route('admin.blood-request.show',[$bloodRequest->id])}}"                               
                                @endif
                                class="btn btn-outline-danger btn-sm">
                                Try to Resolve It
                            </a>
                        </td>
                        
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align-last: center" class="text-bloodRequest"> no bloodRequests </td>
                    </tr>
                @endforelse
                
                </tbody>
    
                <tr wire:loading wire:target="addAdmin,updateAdmin,deleteAdmin">
                    <td colspan="7" style="text-align-last: center" class="text-bloodRequest text-center">
                        loading ...
                    </td>
                </tr>
    
            </table>
        </div>
    </div>
</div>
