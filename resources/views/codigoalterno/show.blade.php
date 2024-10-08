@extends('layouts.app')

@section('template_title')
    {{ $codigoalterno->name ?? "{{ __('Show') Codigoalterno" }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Codigoalterno</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('codigoalternos.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Descripcion:</strong>
                            {{ $codigoalterno->Descripcion }}
                        </div>
                        <div class="form-group">
                            <strong>Estado:</strong>
                            {{ $codigoalterno->estado }}
                        </div>
                        <div class="form-group">
                            <strong>Cantidad:</strong>
                            {{ $codigoalterno->cantidad }}
                        </div>
                        <div class="form-group">
                            <strong>Producto Id:</strong>
                            {{ $codigoalterno->producto_id }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
