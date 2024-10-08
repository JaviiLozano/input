@extends('layouts.app')

@section('template_title')
    {{ __('Create') }} Empresa
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                @includeif('partials.errors')

                <div class="card card-default">
                    <h3 class="card-header">
                        <span class="card-title">{{ __('Create') }} Empresa</span>
                    </h3>
                    <div class="panel-body">
                        <form method="POST" action="{{ route('empresas.store') }}"  role="form" enctype="multipart/form-data">
                            @csrf

                            @include('empresa.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
