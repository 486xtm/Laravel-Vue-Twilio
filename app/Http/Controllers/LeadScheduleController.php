<?php

namespace App\Http\Controllers;

use App\Models\LeadSchedule;
use App\Models\Lead;
use App\Models\LeadLog;

use Illuminate\Http\Request;
use Auth;

class LeadScheduleController extends Controller
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
            'date' => 'required',
            'description' => 'required'
        ]);
        
        //UPDATE STATUS
        $lead = Lead::find($request->lead_id);
        
        //if($lead->funnel_step_id == 1)
        //    $funnel_step_id = 5; //LEAD INTERAGIDO
        //else
        $funnel_step_id = $lead->funnel_step_id;
 
        $lead->status_id = 7; //INTERAGIDO
        $lead->funnel_step_id = $funnel_step_id; 
        $lead->interaction_date = now();
        $lead->new_lead = 0;
        $lead->schedule_date = date('Y-m-d H:i:s', strtotime($request->date));
        $lead->save();
        sleep(1);

        $events = LeadSchedule::where('lead_id',$request->lead_id)->delete();

        foreach($request->schedules as $schedule){
            
            //NEW EVENT
            $event = new LeadSchedule();
            $event->schedule_date = date('Y-m-d H:i:s', strtotime($schedule['schedule_date']));
            $event->description = $schedule['description'];
            $event->lead_id = $request->lead_id;
            $event->status_id = 1;
            $event->user_id = $user->id;
            $event->created_at = date('Y-m-d H:i:s', strtotime($schedule['created_at']));;
            $event->save();
            sleep(1);
        }

        //LOGS
        $log = new LeadLog();
        $log->action = "📅 Novo Agendamento";
        $log->description = "Um novo agendamento foi criado";
        $log->lead_id = $request->lead_id;
        $log->status_id = 1;
        $log->user_id = $user->id;
        $log->save();
        sleep(1);

    }

    /**
     * Display the specified resource.
     */
    public function show(LeadSchedule $leadSchedule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LeadSchedule $leadSchedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LeadSchedule $leadSchedule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeadSchedule $leadSchedule)
    {
        //
    }
}
