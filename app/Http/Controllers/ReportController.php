<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use App\Models\Lead;
use App\Models\FormCampaign;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Carbon\Carbon;
use Auth;


class ReportController extends Controller
{
    public function index(Request $request)
    {   
        $user = Auth::user();
        $label = [];
        $data = [];

        if ($request->has('date')) {
            $dates = $request->date;
            $dates[] = [date('Y-m-d 00:00:00',strtotime($request->date[0])), date('Y-m-d 23:59:59',strtotime($request->date[1]))];
            $request->session()->put('dashboard.dates', $dates);
        }else{
            if($request->session()->has('dashboard.dates')){
                $dates = $request->session()->pull('dashboard.dates');
                $request->session()->put('dashboard.dates', $dates);
            }else{
                $dates = [date('Y-m-d 00:00:00',strtotime(now()->startOfMonth())), date('Y-m-d 23:59:59',strtotime(now()->endOfDay()))];
            }
        }
        
        $reports = array(
            'bots' => $this->getBotData($request, $dates),
            'forms' => $this->getFormData($request, $dates),
        );

        $filters = array(
            'date' => $dates,
        );

        return Inertia::render(
            'Reports/Index',
            [
                'reports' => $reports,
                'filters' => $filters,
            ]
        );
    }

    public function getBotData(Request $request, $dates){

        $waiting = Lead::where('status_id','=', 20)
                        ->whereBetween('created_at', $dates)                        
                        ->count();

        $totals = Lead::where('status_id','<>', 3)
                       ->whereBetween('created_at', $dates)
                       ->where('bot_stage','>', 0)
                       ->count();

        $no_interact = Lead::where('status_id','<>', 3)
                        ->whereBetween('created_at', $dates)
                        ->where('bot_stage','=', 1)
                        ->count();
        
        $interact = Lead::where('status_id','<>', 3)
                        ->whereBetween('created_at', $dates)
                        ->whereIn('bot_stage', [2,3,4])
                        ->count();        

        $conclued = Lead::where('status_id','<>', 3)
                        ->whereBetween('created_at', $dates)
                        ->where('bot_stage','=', 4)
                        ->count();    

        $bot = array(
            'totals' => $totals,            
            'waiting' => $waiting,            
            'interact' => $interact,            
            'no_interact' => $no_interact,            
            'conclued' => $conclued,            
        );

        return $bot;
    }

    public function getFormData(Request $request, $dates){

        $ranking = [];
        $forms = FormCampaign::where('status_id','=',1)->orderBy('id')->get();
        
        foreach($forms as $form){

            $leads = Lead::join('companies','leads.company_id', 'companies.id')
                    ->selectRaw("leads.id")
                    ->where('leads.status_id','<>','3')
                    ->where('leads.form_id','=', $form->facebook_form_id)
                    ->where('leads.status_id','=','12');
   
            if($leads->count() > 0){
                $ranking[] = [
                    'name' => $form->name,
                    'total' => $leads->count(),                  
                ];
            }
        }
        
        return $ranking;
    }
}
