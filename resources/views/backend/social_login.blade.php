@extends('backend.layouts.app')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">Credenciales de Google</h5>
            </div>
            <form class="form-horizontal" action="{{ route('social.login.update') }}" method="POST">
                <div class="card-body">
                    @csrf
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">Activar Login con Google</label>
                        <div class="col-md-8">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input type="checkbox" name="login_google" @if( get_setting('login_google')=='on' )
                                    checked @endif>
                                <span></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-3">
                            <label class="col-from-label">Identificacion del Cliente</label>
                        </div>
                        <div class="col-md-7">
                            <input type="hidden" name="types[]" value="GOOGLE_CLIENT_ID">
                            <input type="text" class="form-control" name="GOOGLE_CLIENT_ID"
                                value="{{  env('GOOGLE_CLIENT_ID') }}" placeholder="ID Cliente de Google"
                                autocomplete="off">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-3">
                            <label class="col-from-label">Key Cliente</label>
                        </div>
                        <div class="col-md-7">
                            <input type="hidden" name="types[]" value="GOOGLE_CLIENT_SECRET">
                            <input type="text" class="form-control" name="GOOGLE_CLIENT_SECRET"
                                value="{{  env('GOOGLE_CLIENT_SECRET') }}" placeholder="Key Cliente" autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="card-header">
                    <h5 class="mb-0 h6">Credenciales de Facebook</h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">Activar Login con Facebook</label>
                        <div class="col-md-8">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input type="checkbox" name="login_facebook" @if( get_setting('login_facebook')=='on' )
                                    checked @endif>
                                <span></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-3">
                            <label class="col-from-label">Identificador</label>
                        </div>
                        <div class="col-md-7">
                            <input type="hidden" name="types[]" value="FACEBOOK_CLIENT_ID">
                            <input type="text" class="form-control" name="FACEBOOK_CLIENT_ID"
                                value="{{ env('FACEBOOK_CLIENT_ID') }}" placeholder="Identificador" autocomplete="off">
                        </div>
                    </div>

                    <div class="form-group row">
                        <input type="hidden" name="types[]" value="FACEBOOK_CLIENT_SECRET">
                        <div class="col-lg-3">
                            <label class="col-from-label">Clave Secreta</label>
                        </div>
                        <div class="col-md-7">
                            <input type="hidden" name="types[]" value="FACEBOOK_CLIENT_SECRET">
                            <input type="text" class="form-control" name="FACEBOOK_CLIENT_SECRET"
                                value="{{ env('FACEBOOK_CLIENT_SECRET') }}" placeholder="Clave Secreta"
                                autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="card-header">
                    <h5 class="mb-0 h6">Credenciales de Apple</h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">Activar Login con Apple</label>
                        <div class="col-md-8">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input type="checkbox" name="login_apple" @if( get_setting('login_apple')=='on' )
                                    checked @endif>
                                <span></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-3">
                            <label class="col-from-label">Identificador</label>
                        </div>
                        <div class="col-md-7">
                            <input type="hidden" name="types[]" value="APPLE_CLIENT_ID">
                            <input type="text" class="form-control" name="APPLE_CLIENT_ID"
                                value="{{ env('APPLE_CLIENT_ID') }}" placeholder="Identificador" autocomplete="off">
                        </div>
                    </div>

                    <div class="form-group row">
                        <input type="hidden" name="types[]" value="APPLE_CLIENT_SECRET">
                        <div class="col-lg-3">
                            <label class="col-from-label">Clave Secreta</label>
                        </div>
                        <div class="col-md-7">
                            <input type="hidden" name="types[]" value="APPLE_CLIENT_SECRET">
                            <input type="text" class="form-control" name="APPLE_CLIENT_SECRET"
                                value="{{ env('APPLE_CLIENT_SECRET') }}" placeholder="Clave Secreta" autocomplete="off">
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
