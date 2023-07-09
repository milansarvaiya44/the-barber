@extends('layouts.app')
@section('content')

@include('layouts.top-header', [
        'title' => __('Gst/Vat'),
        'class' => 'col-lg-7'
    ])

<div class="container-fluid mt--6 mb-5">
    <div class="row">
        <div class="col">
            <div class="card">
                <!-- Card header -->
                <div class="card-header border-0">
                    <span class="h3">{{__('Gst table')}}</span>
                    <button class="btn btn-primary addbtn float-right p-2 add_coupon" id="add_coupon"><i class="fas fa-plus mr-1"></i>{{__('Add New')}}</button>
                </div>
                <!-- table -->
                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col" class="sort">{{__('#')}}</th>
                                <th scope="col" class="sort">{{__('GST')}}</th>
                                <th scope="col" class="sort">{{__('Vat')}}</th>
                                <th scope="col" class="sort">{{__('Service_Charges')}}</th>
                                <th scope="col" class="sort">{{__('Type')}}</th>
                                <th scope="col" class="sort">{{__('Action')}}</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @foreach($invoice as $final)
                            <tr>
                                <td>{{$final->id}}</td>
                                <td>{{$final->gst}}</td>
                                <td>{{$final->vat}}</td>
                                <td>{{$final->services_charges}}</td>
                                <td>{{$final->type}}</td>
                                <td class="table-actions">
                                    @php
                                        $base_url = url('/');
                                     @endphp
                                    </button>
                                    {{-- <button class="btn-white btn shadow-none p-0 m-0 table-action text-info bg-white" action="{{url('admin/edit')}}"   data-toggle="tooltip" data-original-title="{{__('Edit Detail')}}"> --}}
                                      {{-- <a href="{{url('admin/edit/' .$final->id)}}">
                                        <i class="fas fa-user-edit"></i> --}}
                                        {{-- <a href="{{url('admin/gst/edit/' .$final->id)}}"> --}}
                                        <button class="btn-white btn shadow-none p-0 m-0 table-action text-info bg-white" onclick="edit_gstvatservices({{$final->id}},'{{$base_url}}')" data-toggle="tooltip" data-original-title="{{__('Edit gstvatservices')}}">
                                            <i class="fas fa-user-edit"></i>
                                        </button>
                                    {{-- </a> --}}
                                    {{-- </button> --}}
                                            <button class="btn-white btn shadow-none p-0 m-0 table-action text-warning bg-white"  data-toggle="tooltip" data-original-title="{{__(' Deleted')}}">
                                               <a href="{{url('admin/gst/delete/' .$final->id)}}"> <i class="fas fa-trash"></i></a>
                                            </button>
                                            
                                </td>
                              </tr>
                                       
                              @endforeach   
                                {{-- <tr>
                                    <th colspan="10" class="text-center">{{__('No Gst/Vat/Services_charges')}}</th>
                                </tr> --}}
                           
                                
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.gstvatservices.create')
@include('admin.gstvatservices.showgst')
@include('admin.gstvatservices.edit')

@endsection