<div class="container-fluid  sidebar_open @if($errors->any()) show_sidebar_edit @endif" id="edit_gstvatservices_sidebar">
    <div class="row">
        <div class="col">
            <div class="card py-3 border-0">
                <div class="border_bottom_pink pb-3 pt-2 mb-4">
                    <span class="h3">{{__('Edit Gstvatservices')}}</span>
                    <button type="button" class="edit_gstvatservices_close close">&times;</button>
                </div>
                <form class="form-horizontal" id="edit_gstvatservices_form" action="#" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="my-0">
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
                        <input type="hidden" name="id">

                        <div class="text-center">
                            <button type="button" onclick="all_edit('edit_gstvatservices_form','gstvatservices')"  class="btn btn-primary rtl-float-none mt-4 mb-5">{{ __('Save Changes') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>