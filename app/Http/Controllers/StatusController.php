<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class StatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = Status::when($request->term, function ($query) use ($request) {
            $query->where('name', 'LIKE', '%'.$request->term.'%');
                        
        }, function ($query) {
            $query->orderBy('id')->get()->map(fn ($status) => [
                'id' => $status->id,
                'name' => $status->nome,
            ]);
        })->get();

         
        return Inertia::render(
            'Status/Index',
            [
                'status' => $status
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render(
            'Status/Create'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:64'
        ]);
        
        Status::create([
            'name' => $request->name
        ]);
        sleep(1);

        return redirect()->route('status.index')->with('message', 'Status Criado Com Sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(Status $status)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Status $status)
    {
        return Inertia::render(
            'Status/Edit',
            [
                'status' => $status
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Status $status)
    {
        $request->validate([
            'name' => 'required|string|max:64'
        ]);

        $status->name = $request->name;
        $status->save();
        sleep(1);

        return redirect()->route('status.index')->with('message', 'Status atualizado com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Status $status)
    {
        //
    }
}
