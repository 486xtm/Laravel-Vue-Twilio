<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use App\Models\Estado;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CidadeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cidades = Cidade::join('estados', 'estado_id', '=', 'estados.id')
                            ->select('cidades.id', 'cidades.nome', 'estados.nome as uf')
                            ->orderBy('cidades.nome')
                            ->get();
        
        

        return Inertia::render(
            'Cidade/Index',
            [
                'cidades' => $cidades
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $estados = Estado::orderBy('nome')->get();
        
        return Inertia::render(
            'Cidade/Criar',
            [
                'estados' => $estados
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'estado' => 'required'
        ]);
        
        $cidade = new Cidade();
        $cidade->nome =$request->nome;
        $cidade->estado_id =$request->estado;
        $cidade->save();
        sleep(1);

        return redirect()->route('cidade.index')->with('message', 'Cidade Criada Com Sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cidade $cidade)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cidade $cidade)
    {
        $estados = Estado::orderBy('nome')->get();
        
        return Inertia::render(
            'Cidade/Editar',
            [
                'cidade' => $cidade,
                'estados' => $estados
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cidade $cidade)
    {
        $request->validate([
            'nome' => 'required|string|max:255'
        ]);

        $cidade->nome = $request->nome;
        $cidade->estado_id = $request->estado;
        $cidade->save();
        sleep(1);

        return redirect()->route('cidade.index')->with('message', 'Cidade atualizada com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cidade $cidade)
    {
        $cidade->delete();
        sleep(1);

        return redirect()->route('cidade.index')->with('message', 'Cidade deletada com sucesso');
    }
}
