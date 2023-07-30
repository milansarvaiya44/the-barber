@extends('layouts.app')
@section('content')


    @include('layouts.top-header', [
            'title' => __('Videos') ,
            'class' => 'col-lg-7'
        ])

<div class="container-fluid mt--6 mb-5">
    <div class="row">
      <div class="col">
        <div class="card">
          <!-- Card header -->
          <div class="card-header border-0">
            <span class="h3">{{__('Videos')}}</span>
          </div>

           <!-- width="640" height="480" -->

          <div class="row">


            @if (count($medias) != 0)
                @foreach ($medias as $key => $image)
                    <div class="col-md-4">                   
                        <div style="margin: 5px;">
                            <video autoplay="" loop="" controls="">
                            <source type="video/mp4" src="{{asset('storage/videos/media/'.$image->media_file)}}">
                            </video>
                        </div>
                    </div>            
                @endforeach
            @else
                <div class="col-md-4">     
                    {{__('No Videos')}}         
                </div>         
            @endif
                      
          </div>
          
        </div>
      </div>
    </div>
</div>

@endsection