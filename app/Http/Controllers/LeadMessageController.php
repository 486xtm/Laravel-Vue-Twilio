<?php

namespace App\Http\Controllers;

use App\Models\LeadMessage;
use App\Models\LeadLog;
use App\Models\Lead;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;

class LeadMessageController extends Controller
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
            'message' => 'required'
        ]);
        
        $lead = Lead::find($request->lead_id);

       // if($lead->funnel_step_id == 1)
       //     $funnel_step_id = 5; //LEAD INTERAGIDO
       // else
        $funnel_step_id = $lead->funnel_step_id;

        $lead->interaction_date = now();
        $lead->funnel_step_id = $funnel_step_id; 
        $lead->new_lead = 0;
        $lead->status_id = 7; //INTERAGIDO
        $lead->save();
        sleep(1);
        
        $message = new LeadMessage();
        $message->message = $request->message;
        $message->lead_id = $request->lead_id;
        $message->status_id = 1;
        $message->user_id = $user->id;
        $message->save();
        sleep(1);

        $log = new LeadLog();
        $log->action = "💬 Nova Mensagem";
        $log->description = "Uma nova mensagem foi enviada";
        $log->lead_id = $request->lead_id;
        $log->status_id = 1;
        $log->user_id = $user->id;
        $log->save();
        sleep(1);


        return redirect()->route('lead.view', $request->lead_id)->with('message', '✉️ Messagem Criada Com Sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(LeadMessage $leadMessage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LeadMessage $leadMessage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LeadMessage $leadMessage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeadMessage $leadMessage)
    {
        //
    }
}
