<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $campaigns = Campaign::select('campaigns.id','campaigns.name')
                               ->where('campaigns.status_id','=', 1)
                               ->orderBy('campaigns.id');
        
        $campaigns->when($request->term, function ($query) use ($request) {
            $query->where('campaigns.name', 'LIKE', '%'.$request->term.'%');                        
        });
           
        $filter = array(
            'term'=> $request->only(['term']),
        );
       
        return Inertia::render(
            'Campaign/Index',
            [
                'campaigns' => $campaigns->paginate(15)->withQueryString(),
                'filters' => $filter,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render(
            'Campaign/Create',
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1024',
        ]);
        
        Campaign::create([
            'name' => $request->name,
            'description' => $request->description,
            'status_id' => 1
        ]);
        sleep(1);

        return redirect()->route('campaign.index')->with('message', 'Campanha criada com sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(Campaign $campaign)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Campaign $campaign)
    {
        return Inertia::render(
            'Campaign/Edit', 
            [
                'campaign' => $campaign
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Campaign $campaign)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1024',           
        ]);
       
        $campaign->name =$request->name;
        $campaign->description =$request->description;       
        $campaign->save();
        sleep(1);

        return redirect()->route('campaign.index')->with('message', 'Campanha atualizada com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Campaign $campaign)
    {        
        $campaign->status_id = 3;
        $campaign->save();
        sleep(1);

        return redirect()->back()->with('message', 'Campanha excluída com sucesso');
    }
}
