@extends('layouts.app')

@section('template_title')
    Movimientosbasico
@endsection

@section('content')
<section class="content container-fluid">
    <div class="row">
        <div class="col-md-12">
            <style>
                .computer-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: 20px;
                    max-width: 1200px;
                    margin: 0 auto;
                    padding: 20px;
                }
                .computer-box {
                    border: 2px solid #ccc;
                    padding: 20px;
                    text-align: center;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    aspect-ratio: 1 / 1;
                }
                .computer-box:hover {
                    border-color: #007bff;
                    transform: translateY(-5px);
                    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                }
                .computer-box.selected {
                    background-color: #e6f2ff;
                    border-color: #007bff;
                }
                .computer-box a {
                    text-decoration: none;
                    color: inherit;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    height: 100%;
                    width: 100%;
                }
                .computer-box i {
                    font-size: 48px;
                    margin-bottom: 10px;
                }
                .computer-box p {
                    margin: 0;
                    font-size: 16px;
                    font-weight: bold;
                }
                @media (max-width: 768px) {
                    .computer-grid {
                        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                    }
                    .computer-box i {
                        font-size: 36px;
                    }
                    .computer-box p {
                        font-size: 14px;
                    }
                }
            </style>

            @php
                use App\Models\Caja;
                $computers = Caja::where('estado', 'Activo')->get();
                $Operaciones = App\Models\Movimientosbasico::orderBy('id','desc')->get();
                
            @endphp

            <div class="card card-default">
                <div class="card-header">
                    <h2 class="card-title">{{ __('Crear') }} Movimientos básico</h2>
                </div>
                <div class="card-body">
                    <div class="computer-grid">
                        @foreach($computers as $index => $computer)
                            <div class="computer-box">
                                <a href="#" onclick="valurComputador({{ $computer['id'] }})"  data-toggle="modal" data-target="#Modal{{ $computer['id'] }}">
                                    <i class="demo-pli-monitor-2"></i>
                                    <p>{{ $computer['Descripcion'] }}</p>
                                </a>
                            </div>
                            
                            <!-- Modal -->
                            <div class="modal fade" id="Modal{{ $computer['id'] }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Tipos Movimientos</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    </div>
                                    <div class="modal-body">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>
                                                        Tipos Movimientos
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        @foreach ($Operaciones as $movimiento)
                                                        <form action="{{ route('movimientos.crearPendientes', [
                                                            'users' => Auth::user()->id,
                                                            'caja' => $computer['id'],
                                                            'TipoMovimiento' => $movimiento->id
                                                        ]) }}" method="POST">
                                                            @csrf
                                                            <!-- Include other form fields as needed -->
                                                            <button type="submit">{{$movimiento->Descripcion}}</button>
                                                        @endforeach
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        
                                    </div>
                                    <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                    
                                    </div>
                                </div>
                                </div>
                            </div>

 

                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const computerBoxes = document.querySelectorAll('.computer-box');
        
        computerBoxes.forEach(box => {
            box.addEventListener('click', function() {
                computerBoxes.forEach(b => b.classList.remove('selected'));
                this.classList.add('selected');
            });
        });
    });
</script>

@endsection