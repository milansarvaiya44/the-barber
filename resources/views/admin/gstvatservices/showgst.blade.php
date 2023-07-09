<div class="container-fluid  sidebar_open" id="show_gstvat_sidebar">
    <div class="row">
        <div class="col">
            <div class="card py-3 border-0">
                <!-- Card header -->
                <div class="border_bottom_pink pb-3 pt-2 mb-4">
                    <span class="h3">{{__('View Gst/vat/service_charges')}}</span>
                    <button type="button" class="show_coupon_close close">&times;</button>
                </div>
                <div class="table1">
                    <div class="row mt-3 mb-2">
                        <div class="col h4 text-right">{{__('Gst :')}}</div>
                        <div class="col" id="gst"></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col h4 text-right">{{__('Vat :')}}</div>
                        <div class="col" id="vat"></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col h4 text-right">{{__('Service_charges :')}}</div>
                        <div class="col" id="services_charges"></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col h4 text-right">{{__('Type :')}}</div>
                        <div class="col" id="type"></div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>