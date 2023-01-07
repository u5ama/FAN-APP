@extends('admin.layouts.master')
@section('css')
    <link href="{{URL::asset('assets/plugins/fancybox/jquery.fancybox.css')}}" rel="stylesheet">
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h2 class="content-title mb-0 my-auto">Player Details</h2>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <!-- row opened -->

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="image">ID Front image</label> <br>
                            <img src="{{url($user->id_card_front)}}" alt="" class="img-fluid" style="width: 500px">
{{--                            <label for="image">ID Front image<span--}}
{{--                                    class="error">*</span></label>--}}
{{--                            <input type="file" class="form-control dropify"--}}
{{--                                   name="image"--}}
{{--                                   data-default-file="{{url($user->id_card_front)}}"--}}
{{--                                   id="image" required/>--}}
{{--                            <div class="help-block with-errors error"></div>--}}
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label for="image">ID Back image</label> <br>
                            <img src="{{url($user->id_card_back)}}" alt="" class="img-fluid" style="width: 500px">
{{--                            <label for="image">ID Back image<span--}}
{{--                                    class="error">*</span></label>--}}
{{--                            <input type="file" class="form-control dropify"--}}
{{--                                   name="image"--}}
{{--                                   data-default-file="{{url($user->id_card_back)}}"--}}
{{--                                   id="image" required/>--}}
{{--                            <div class="help-block with-errors error"></div>--}}
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!--/div-->
    </div>
    <!-- /row -->
    </div>
    <!-- Container closed -->
    </div>

    <!-- main-content closed -->
@endsection
@section('js')
    <script src="{{URL::asset('assets/plugins/fancybox/jquery.fancybox.js')}}"></script>
    <script src="{{URL::asset('assets/js/custom/teams.js')}}?v={{ time() }}"></script>
@endsection
