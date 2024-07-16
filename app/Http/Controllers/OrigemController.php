<?php

namespace App\Http\Controllers;

use App\Models\Origem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class OrigemController extends Controller
{
    public function index(Request $request)
    {   
        Session::put('origem_url', $request->fullUrl());

        $origens = Origem::when($request->term, function ($query) use ($request) {
            $query->where('name', 'LIKE', '%'.$request->term.'%')->paginate();
                        
        }, function ($query) {
            $query->orderBy('id')->paginate()->map(fn ($origem) => [
                'id' => $origem->id,
                'name' => $origem->nome,
            ]);
        })->get();

        $filter = array(
            'term'=> $request->only(['term']),          
        );
         
        return Inertia::render(
            'Origem/Index',
            [
                'origens' => $origens,
                'filters' => $filter
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render(
            'Origem/Create'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);
        
        Origem::create([
            'name' => $request->name
        ]);
        sleep(1);

        if(session('origem_url')){
            return redirect(session('origem_url'));
        }

        return redirect()->route('origem.index')->with('message', 'Origem Criada Com Sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(Origem $origem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Origem $origem)
    {
        return Inertia::render(
            'Origem/Edit',
            [
                'origem' => $origem
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Origem $origem)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $origem->name = $request->name;
        $origem->save();
        sleep(1);

        if(session('origem_url')){
            return redirect(session('origem_url'));
        }

        return redirect()->route('origem.index')->with('message', 'Origem atualizada com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Origem $origem)
    {
        
        $origem->delete();
        sleep(1);

        return redirect()->route('origem.index')->with('message', 'Origem deletada com sucesso');
    }
}
