<?php

namespace App\Http\Controllers;

use App\Models\FunnelStep;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class FunnelStepController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $steps = FunnelStep::select('id','name','color','order')
                            ->where('status_id',1);        
        
        $steps->when($request->term, function ($query) use ($request) {
            $query->where('name', 'LIKE', '%'.$request->term.'%');                        
        }, function ($query) {
            
        })->orderBy('order')->paginate(15);

         
        return Inertia::render(
            'FunnelStep/Index',
            [
                'steps' => $steps->get()
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render(
            'FunnelStep/Create'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:12',
            'order' => 'required|integer',
        ]);
        
        $step = new FunnelStep();
        $step->name =$request->name;
        $step->color =$request->color;
        $step->order =$request->order;
        $step->save();
        sleep(1);

        return redirect()->route('funnelstep.index')->with('message', 'Etapa Criada Com Sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(FunnelStep $funnelStep)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FunnelStep $step)
    {
        return Inertia::render(
            'FunnelStep/Edit',
            [
                'step' => $step
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FunnelStep $step)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:12',
            'order' => 'required|integer',
        ]);

        $step->name =$request->name;
        $step->color =$request->color;
        $step->order =$request->order;
        $step->save();
        sleep(1);

        return redirect()->route('funnelstep.index')->with('message', 'Etapa atualizada com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FunnelStep $funnelStep)
    {
        //
    }
}
