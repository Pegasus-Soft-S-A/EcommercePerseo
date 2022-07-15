@extends('backend.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">Configuracion Facebook Pixel </h5>
            </div>
            <form class="form-horizontal" action=" {{ route('business_settings.update_analytics') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-3">
                            <label class="col-from-label">Facebook Pixel</label>
                        </div>
                        <div class="col-md-7">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input value="1" name="facebook_pixel" type="checkbox" @if(
                                    get_setting('facebook_pixel')=='1' ) checked @endif>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group row">

                        <div class="col-lg-3">
                            <label class="col-from-label">Facebook Pixel ID</label>
                        </div>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="FACEBOOK_PIXEL_ID"
                                value="{{get_setting('FACEBOOK_PIXEL_ID')}}" placeholder="Facebook Pixel ID"
                                autocomplete="off">
                        </div>
                    </div>

                </div>
                <div class="card-header">
                    <h5 class="mb-0 h6">Configuracion Google Analytics</h5>
                </div>
                <div class="card-body">

                    <div class="form-group row">
                        <div class="col-lg-3">
                            <label class="col-from-label">Google Analytics</label>
                        </div>
                        <div class="col-md-7">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input value="1" name="google_analytics" type="checkbox" @if(
                                    get_setting('google_analytics')=='1' ) checked @endif>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-3">
                            <label class="col-from-label">Tracking ID</label>
                        </div>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="TRACKING_ID"
                                value="{{  get_setting('TRACKING_ID') }}" placeholder="Tracking ID" autocomplete="off">
                        </div>
                    </div>
                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection