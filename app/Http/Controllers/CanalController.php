<?php

namespace App\Http\Controllers;

use App\Models\Canal;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class CanalController extends Controller
{
    public function index(Request $request)
    {
        $canais = Canal::when($request->term, function ($query) use ($request) {
            $query->where('nome', 'LIKE', '%'.$request->term.'%')->paginate();
                        
        }, function ($query) {
            $query->orderBy('id')->paginate()->map(fn ($canal) => [
                'id' => $canal->id,
                'nome' => $canal->nome,
            ]);
        })->get();

         
        return Inertia::render(
            'Canal/Index',
            [
                'canais' => $canais
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render(
            'Canal/Criar'
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
        
        Canal::create([
            'nome' => $request->nome
        ]);
        sleep(1);

        return redirect()->route('canal.index')->with('message', 'Canal Criado Com Sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(Canal $canal)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Canal $canal)
    {
        return Inertia::render(
            'Canal/Editar',
            [
                'canal' => $canal
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Canal $canal)
    {
        $request->validate([
            'nome' => 'required|string|max:255'
        ]);

        $canal->nome = $request->nome;
        $canal->save();
        sleep(1);

        return redirect()->route('canal.index')->with('message', 'Canal atualizado com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Canal $canal)
    {
        dd('echo');
        
        $canal->delete();
        sleep(1);

        return redirect()->route('canal.index')->with('message', 'Canal deletado com sucesso');
    }
}
