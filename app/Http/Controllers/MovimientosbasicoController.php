<?php

namespace App\Http\Controllers;

use App\Models\Movimientosbasico;
use Illuminate\Http\Request;

/**
 * Class MovimientosbasicoController
 * @package App\Http\Controllers
 */
class MovimientosbasicoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $movimientosbasicos = Movimientosbasico::paginate();

        return view('movimientosbasico.index', compact('movimientosbasicos'))
            ->with('i', (request()->input('page', 1) - 1) * $movimientosbasicos->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $movimientosbasico = new Movimientosbasico();
        return view('movimientosbasico.create', compact('movimientosbasico'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(Movimientosbasico::$rules);

        $movimientosbasico = Movimientosbasico::create($request->all());

        return redirect()->route('movimientosbasicos.index')
            ->with('success', 'Movimientosbasico created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $movimientosbasico = Movimientosbasico::find($id);

        return view('movimientosbasico.show', compact('movimientosbasico'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $movimientosbasico = Movimientosbasico::find($id);

        return view('movimientosbasico.edit', compact('movimientosbasico'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Movimientosbasico $movimientosbasico
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Movimientosbasico $movimientosbasico)
    {
        request()->validate(Movimientosbasico::$rules);

        $movimientosbasico->update($request->all());

        return redirect()->route('movimientosbasicos.index')
            ->with('success', 'Movimientosbasico updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $movimientosbasico = Movimientosbasico::find($id)->delete();

        return redirect()->route('movimientosbasicos.index')
            ->with('success', 'Movimientosbasico deleted successfully');
    }
}