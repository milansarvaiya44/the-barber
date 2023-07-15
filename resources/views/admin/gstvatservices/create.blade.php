<div class="container-fluid sidebar_open @if($errors->any()) show_sidebar_create @endif" id="add_coupon_sidebar">
    <div class="row">
        <div class="col">            
            <div class="card py-3 border-0">
                <div class="border_bottom_pink pb-3 pt-2 mb-4">
                    <span class="h3">{{__('Create Gst/VatService_Charges')}}</span>
                    <button type="button" class="add_coupon close">&times;</button>
                </div>
                <form class="form-horizontal"  id="create_gstvatservices_form" method="post" enctype="multipart/form-data" action="{{url('admin/gstvatservices/store')}}">
                    @csrf
                    <div class="my-0">
                         {{-- <?php
                            // $permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                            // $code = substr(str_shuffle($permitted_chars), 0, 8);
                        ?> --}}
                        {{-- <div class="form-group">
                            <label class="form-control-label" for="code">{{__('Code')}}</label>
                             <input type="text" class="form-control" name="code" id="code" value="{{$code}}" placeholder="{{__('Coupon Code')}}" readonly>
                            <div class="invalid-div "><span class="code"></span></div>
                        </div> --}}

                        {{-- <div class="form-group">
                            <label for="desc" class="form-control-label">{{__('GSt')}}</label>
                            <input type="number"  name="gst" placeholder="{{__('Gst of invoice')}}">
                             <textarea class="form-control"  name="gst" placeholder="{{__('Gst of invoice')}}"></textarea>
                            <div class="invalid-div "><span class="desc"></span></div>
                        </div> --}}

                        <div class="form-group">
                            <label class="form-control-label">{{__('Type')}}</label><br>
                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" id="customRadio1" name="type" value="Percentage" class="custom-control-input" checked>
                                <label class="custom-control-label" for="customRadio1">{{__('Percentage')}}</label>
                            </div>
                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" id="customRadio2" name="type" value="Amount" class="custom-control-input">
                                <label class="custom-control-label" for="customRadio2">{{__('Amount')}}</label>
                            </div>
                        </div>
                        
                        {{-- <div class="form-group">
                            <label class="form-control-label" for="discount">{{__('Discount')}}</label>
                            <input type="number" value="{{ old('discount') }}" class="form-control" name="discount" id="discount" placeholder="{{__('Gst Discount')}}" >
                            <div class="invalid-div "><span class="discount"></span></div>
                        </div> --}}
                        <div class="form-group">
                            <label class="form-control-label" for="gst">{{__('Gst')}}</label>
                            <input type="number" value="{{ old('gst') }}" class="form-control" name="gst" id="gst" placeholder="{{__('invoice gst')}}" >
                            <div class="invalid-div "><span class="gst"></span></div>
                        </div>

                        <div class="form-group">
                            <label class="form-control-label" for="vat">{{__('Vat')}}</label>
                            <input type="number" value="{{ old('vat') }}" class="form-control" name="vat" id="vat" placeholder="{{__('invoice vat')}}" >
                            <div class="invalid-div "><span class="vat"></span></div>
                        </div>
                        <div class="form-group">
                            <label class="form-control-label" for="service_charges">{{__('service_charges')}}</label>
                            <input type="number" value="{{ old('services_charges') }}" class="form-control" name="services_charges" id="vat" placeholder="{{__('invoice vat')}}" >
                            <div class="invalid-div "><span class="service_charges"></span></div>
                        </div>

                        {{-- <div class="form-group">
                            <label for="max_use" class="form-control-label">{{__('Maximum use')}}</label>
                            <input type="number" value="{{ old('max_use') }}" class="form-control" name="max_use" id="max_use" placeholder="{{__('Maximum use')}}" >
                            <div class="invalid-div "><span class="max_use"></span></div>
                        </div>
                    
                        <div class="form-group">
                            <label for="start_date" class="form-control-label">{{__('Start date')}}</label>
                            <input type="date" value="{{ old('start_date') }}" class="form-control" name="start_date" id="start_date" placeholder="{{__('Start Date')}}" >
                            <div class="invalid-div "><span class="start_date"></span></div>
                        </div>

                        <div class="form-group">
                            <label for="end_date" class="form-control-label">{{__('End date')}}</label>
                            <input type="date" value="{{ old('end_date') }}" class="form-control" name="end_date" id="end_date" placeholder="{{__('End date')}}">
                            <div class="invalid-div "><span class="end_date"></span></div>
                        </div> --}}

                        <div class="text-center">
                           <button type="button" id="create_btn" onclick="all_create('create_gstvatservices_form','gstvatservices')" class="btn btn-primary rtl-float-none mt-4 mb-5">{{ __('Create') }}</button>
                        {{--  <button type="button" id="create_btn" onclick="all_create('create_coupon_form','coupon')" class="btn btn-primary">Create</button> --}}
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>