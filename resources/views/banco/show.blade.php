@extends('layouts.app')

@section('template_title')
    {{ $banco->name ?? "{{ __('Show') Banco" }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Banco</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('bancos.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Nombre:</strong>
                            {{ $banco->nombre }}
                        </div>
                        <div class="form-group">
                            <strong>Estado:</strong>
                            {{ $banco->estado }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
