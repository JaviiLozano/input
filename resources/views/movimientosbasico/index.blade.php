@extends('layouts.app')

@section('template_title')
    Movimientosbasico
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Movimientosbasico') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('movimientosbasicos.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
                                  {{ __('Create New') }}
                                </a>
                              </div>
                        </div>
                    </div>
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>
                                        
										<th>Codigo</th>
										<th>Descripcion</th>
										<th>Descuento</th>
										<th>Agregar</th>
										<th>Alerta</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($movimientosbasicos as $movimientosbasico)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
											<td>{{ $movimientosbasico->Codigo }}</td>
											<td>{{ $movimientosbasico->Descripcion }}</td>
											<td>{{ $movimientosbasico->Descuento }}</td>
											<td>{{ $movimientosbasico->Agregar }}</td>
											<td>{{ $movimientosbasico->Alerta }}</td>

                                            <td>
                                                <form action="{{ route('movimientosbasicos.destroy',$movimientosbasico->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('movimientosbasicos.show',$movimientosbasico->id) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Show') }}</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('movimientosbasicos.edit',$movimientosbasico->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}</a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-fw fa-trash"></i> {{ __('Delete') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $movimientosbasicos->links() !!}
            </div>
        </div>
    </div>
@endsection
