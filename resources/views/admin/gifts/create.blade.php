@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h2 class="content-title mb-0 my-auto">Add Gift</h2>

            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" data-parsley-validate="" id="addEditForm" role="form">
                        @csrf
                        <input type="hidden" id="form-method" value="add">

                        <div class="row row-sm">
                            <div class="col-12">
                            <div class="form-group">
                                <label for="name">Name<span
                                        class="error">*</span></label>
                                <input type="text" class="form-control"
                                       name="name"
                                       id="name"
                                       placeholder="Name" required/>
                                <div class="help-block with-errors error"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="price">Price<span
                                        class="error">*</span></label>
                                <input type="number" class="form-control"
                                       name="price"
                                       id="price"
                                       placeholder="Price" required/>
                                <div class="help-block with-errors error"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="commission">Commission<span
                                        class="error">*</span></label>
                                <input type="number" class="form-control"
                                       name="commission"
                                       id="commission"
                                       placeholder="Commission" required/>
                                <div class="help-block with-errors error"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="image">image<span
                                        class="error">*</span></label>
                                <input type="file" class="form-control dropify"
                                       name="image"
                                       id="image" required/>
                                <div class="help-block with-errors error"></div>
                            </div>
                        </div>


                            <div class="col-12">
                                <div class="form-group mb-0 mt-3 justify-content-end">
                                    <div>
                                        <button type="submit"
                                                class="btn btn-success">{{ config('languageString.submit') }}</button>
                                        <a href="{{ route('admin.gifts.index') }}"
                                           class="btn btn-secondary">{{ config('languageString.cancel') }}</a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{URL::asset('assets/js/custom/gifts.js')}}?v={{ time() }}"></script>
@endsection
