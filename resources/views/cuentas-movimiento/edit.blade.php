@extends('layouts.app')

@section('template_title')
    {{ __('Update') }} Cuentas Movimiento
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                @includeif('partials.errors')

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Update') }} Cuentas Movimiento</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('cuentas-movimientos.update', $cuentasMovimiento->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('cuentas-movimiento.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
