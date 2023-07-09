<div class="container-fluid sidebar_open @if($errors->any()) show_sidebar_create @endif" id="add_appointment_sidebar">
    <div class="row">
        <div class="col">
            <div class="card py-3 border-0">
                <!-- Card header -->
                <div class="border_bottom_pink pb-3 pt-2 mb-4">
                    <span class="h3">{{__('Add Appointment')}}</span>
                    <button type="button" class="add_appointment close">&times;</button>
                </div>
                <form class="form-horizontal" id="create_appointment_form" method="POST" enctype="multipart/form-data" action="{{url('/admin/booking/store')}}">
                    @csrf
                    <div class="my-0">

                        <?php
                            $id = rand(10000,99999);
                        ?>
                        
                        <div class="form-group">
                            <label class="form-control-label" for="booking_id">{{__('Booking id')}}</label>
                            <input type="text" name="booking_id" value="#{{$id}}" id="booking_id" class="form-control" placeholder="Booking id" readonly>
                        </div>

                        {{-- User --}}
                        <div class="form-group">
                            <label class="form-control-label">{{__('Client')}}</label>
                            <select class="form-control select2" name="user_id" id="services"  dir="{{ session()->has('direction')&& session('direction') == 'rtl'? 'rtl':''}}">
                                <option disabled selected value> {{__('-- Select Client --')}} </option>
                                @foreach ($users as $user)
                                    <option value={{$user->id}}>{{$user->name}}</option>
                                @endforeach
                            </select>
                            <div class="invalid-div"><span class="user_id"></span></div>
                        </div>

                        {{-- Services --}}
                        <div class="form-group">
                            <label class="form-control-label">{{__('Services')}}</label>
                            <select class="form-control select2 service_class" multiple="multiple" name="service_id[]" id="select2" data-placeholder='{{ __("-- Select Service --")}}' placeholder='{{ __("-- Select Service --")}}'  dir="{{ session()->has('direction')&& session('direction') == 'rtl'? 'rtl':''}}">
                                @foreach ($services as $service)
                                    <option value={{$service->service_id}}>{{$service->name}}</option>
                                @endforeach
                            </select>
                            <div class="invalid-div"><span class="service_id"></span></div>
                        </div>

                        <div class="form-group">
                            <label class="form-control-label" for="date">{{__('Date')}}</label>
                            <input type="date" name="date"  id="date" class="form-control select_date" placeholder="{{__('-- Select Date --')}}">
                            <div class="invalid-div"><span class="date"></span></div>
                        </div>

                        <div class="form-group">
                            <label class="form-control-label" for="start_time">{{__('Time')}}</label>
                            <select class="form-control select2 start_time" name="start_time" id="start_time"  dir="{{ session()->has('direction')&& session('direction') == 'rtl'? 'rtl':''}}">
                                <option disabled selected value='Workinghours->period_list'> {{__('-- Select Time --')}} </option>
                                {{-- @foreach ($workinghours->period_list as $time)
                                
                                <option value={{$time->service_id}}>{{$time->time}}</option>
                            @endforeach  --}}
                            </select>
                            <div class="invalid-div"><span class="start_time"></span></div>
                        </div>

                        <div class="form-group">
                            <label class="form-control-label" for="payment">{{__('Payment')}}</label>
                            <input type="text" name="payment" id="payment" class="form-control" placeholder="{{__('Payment')}}">
                            <div class="invalid-div"><span class="payment"></span></div>
                        </div>

                        {{-- Employees --}}
                        
                        <div class="form-group">
                            <label class="form-control-label">{{__('Enterpreneur')}}</label>
                            <select class="form-control select2" name="enterpreneur_id" id="services_id[]"  dir="{{ session()->has('direction')&& session('direction') == 'rtl'? 'rtl':''}}">
                                <option disabled selected value> {{__('-- Select Enterpreneur --')}} </option>
                                @foreach ($enterpreneur as $enter)
                                    <option value={{$enter->id}}>{{$enter->name}}</option>
                                @endforeach
                            </select>
                            <div class="invalid-div"><span class="enterpreneur_id"></span></div>
                        </div>

                        <div class="text-center">
                            {{-- <a href="{{url('/admin/booking/create')}}" target="_blank" rel="noopener noreferrer"></a> --}}
                         <button type="button" onclick="all_create('create_appointment_form','booking')" id="create_btn" class="btn btn-primary rtl-float-none mt-4 mb-5">{{ __('Book Appointment') }}</button> 
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>