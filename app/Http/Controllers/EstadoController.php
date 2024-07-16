<?php

namespace App\Http\Controllers;

use App\Models\Estado;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class EstadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $estados = Estado::when($request->term, function ($query) use ($request) {
            $query->where('nome', 'LIKE', '%'.$request->term.'%')->paginate();
                        
        }, function ($query) {
            $query->orderBy('id')->paginate()->map(fn ($estado) => [
                'id' => $estado->id,
                'nome' => $estado->nome,
            ]);
        })->get();

         
        return Inertia::render(
            'Estado/Index',
            [
                'estados' => $estados
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render(
            'Estado/Criar'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255'
        ]);
        
        Estado::create([
            'nome' => $request->nome
        ]);
        sleep(1);

        return redirect()->route('estado.index')->with('message', 'Estado Criado Com Sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(Estado $estado)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Estado $estado)
    {
        return Inertia::render(
            'Estado/Editar',
            [
                'estado' => $estado
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Estado $estado)
    {
        $request->validate([
            'nome' => 'required|string|max:255'
        ]);

        $estado->nome = $request->nome;
        $estado->save();
        sleep(1);

        return redirect()->route('estado.index')->with('message', 'Estado atualizado com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Estado $estado)
    {
        dd('echo');
        
        $estado->delete();
        sleep(1);

        return redirect()->route('estado.index')->with('message', 'Estado deletado com sucesso');
    }
}
