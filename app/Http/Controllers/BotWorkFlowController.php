<?php

namespace App\Http\Controllers;

use App\Models\BotWorkFlow;
use Illuminate\Http\Request;

class BotWorkFlowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /** 
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(BotWorkFlow $botWorkFlow)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BotWorkFlow $botWorkFlow)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BotWorkFlow $botWorkFlow)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'message' => 'required|string|max:2000',            
        ]);

        $workflow = BotWorkFlow::find($request->id);
        $workflow->name = $request->name;
        $workflow->message = $request->message;
        $workflow->conditional = $request->conditional;
        $workflow->save();
        sleep(1);
        
        return redirect()->back()->with('message', 'Fluxo atualizado com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BotWorkFlow $botWorkFlow)
    {
        //
    }
}
