<?php

namespace App\Http\Controllers;
use App\Models\Lead;
use App\Models\LeadLog;
use App\Models\LeadArchiveReason;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Auth;


class UpdateStatusController extends Controller
{
    
    /**
     * Update the specified status.
     */
    public function setStatusArchive()
    {
        
        $date = now()->subDays(15)->startOfDay();

        $leads = Lead::where('interaction_date', '<', $date)
                    ->whereIn('status_id', [10])
                    ->get();

        foreach ($leads as $lead )
        {
            $obj = Lead::find($lead->id);
            $obj->status_id = 8; //arquivado
            $obj->save();
            sleep(1);  

            $reason = new LeadArchiveReason();
            $reason->reason = 6;
            $reason->lead_id = $lead->id;
            $reason->save();
            sleep(1);
            
            $log = new LeadLog();
            $log->action = "🗄️ Arquivado";
            $log->description = "O lead foi arquivado automaticamente por inatividade";
            $log->lead_id = $lead->id;
            $log->status_id = 1;
            $log->save();
            sleep(1);
        }
    }

    /**
     * Update the specified status.
     */
    public function setStatusPending()
    {

        $user = Auth::user();
        $date = now()->startOfDay();

        $leads = Lead::where('schedule_date', '<', $date)
                    ->where('interaction_date', '<', $date)
                    ->whereIn('status_id', [7])
                    ->get();
        foreach ($leads as $lead )
        {
            $obj = Lead::find($lead->id);
            $obj->status_id = 10; //pendente
            $obj->save();
            sleep(1);    
        }
    }
}