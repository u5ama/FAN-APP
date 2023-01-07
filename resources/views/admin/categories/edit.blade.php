@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h2 class="content-title mb-0 my-auto">Edit School</h2>

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
                        <input type="hidden" id="edit_value" name="edit_value" value="{{ $category->id }}">
                        <input type="hidden" id="form-method" value="edit">

                        <div class="row row-sm">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name">Name<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="name"
                                           value="{{ $category->name }}"
                                           id="name"
                                           placeholder="Name" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            {{--<div class="col-12">
                                <div class="form-group">
                                    <label for="image">image<span
                                            class="error">*</span></label>
                                    <input type="file" class="form-control dropify"
                                           name="image"
                                           data-default-file="{{url($category->image)}}"
                                           id="image" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>--}}
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name">Color<span
                                            class="error">*</span></label>
                                    <input type="color" class="form-control"
                                           name="color"
                                           id="color"
                                           value="{{ $category->color }}"
                                           placeholder="Color" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-0 mt-3 justify-content-end">
                                    <div>
                                        <button type="submit"
                                                class="btn btn-success">{{ config('languageString.submit') }}</button>
                                        <a href="{{ route('admin.schools.index') }}"
                                           class="btn btn-secondary">Cancel</a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /row -->

    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection
@section('js')
    <script src="{{URL::asset('assets/js/custom/categories.js')}}?v={{ time() }}"></script>
@endsection
