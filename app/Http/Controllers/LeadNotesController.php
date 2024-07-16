<?php

namespace App\Http\Controllers;

use App\Models\LeadNotes;
use App\Models\LeadLog;
use App\Models\Lead;

use Illuminate\Http\Request;
use Auth;

class LeadNotesController extends Controller
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
        $user = Auth::user();

        $request->validate([
            'description' => 'required'
        ]);

        $lead = Lead::find($request->lead_id);
        
        //if($lead->funnel_step_id == 1)
        //    $funnel_step_id = 5; //LEAD INTERAGIDO
        //else
        $funnel_step_id = $lead->funnel_step_id;

        $lead->interaction_date = now();
        $lead->funnel_step_id = $funnel_step_id;
        $lead->new_lead = 0;
        $lead->status_id = 7; //INTERAGIDO
        $lead->save();
        sleep(1);
                
        //NEW EVENT
        $note = new LeadNotes();
        $note->description = $request->description;
        $note->lead_id = $request->lead_id;
        $note->user_id = $user->id;
        $note->status_id = 1; // INTERAGIDO
        $note->save();
        sleep(1);

        //LOGS
        $log = new LeadLog();
        $log->action = "🗉 Nova Nota";
        $log->description = "Uma nova nota foi criada";
        $log->lead_id = $request->lead_id;
        $log->status_id = 1;
        $log->user_id = $user->id;
        $log->save();
        sleep(1);
    }

    /**
     * Display the specified resource.
     */
    public function show(LeadNotes $leadNotes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LeadNotes $leadNotes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LeadNotes $leadNotes)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeadNotes $leadNotes)
    {
        //
    }
}
