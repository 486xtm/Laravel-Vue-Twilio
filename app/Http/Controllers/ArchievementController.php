<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\User;
use App\Models\Channel;
use App\Models\Origem;
use App\Models\Company;
use App\Models\Status;
use App\Models\ArchiveReason;
use App\Models\LeadArchiveReason;
use App\Exports\ArchievementExport;
use Maatwebsite\Excel\Excel;

use Inertia\Inertia;
use Auth;


class ArchievementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request )
    {   
        if(Auth::check()){
            $user = Auth::user();
            $company_id = getCompany($user);

            if ($request->has('date')) {
                $dates = $request->date;
                $startDate = date('Y-m-d 00:00:00',strtotime($request->date[0]));
                $endDate = date('Y-m-d 23:59:59',strtotime($request->date[1]));
                $dates[] = [$startDate, $endDate];
                
                $request->session()->put('archievement.dates', $dates);
            }else{
                if($request->session()->has('archievement.dates')){
                    $dates = $request->session()->pull('archievement.dates');
                    $request->session()->put('archievement.dates', $dates);
                }else{
                    $dates = null;
                }
            }
            
            $leads = Lead::join('users', 'user_id', '=', 'users.id')
                        ->leftJoin('origems', 'leads.origem_id', '=', 'origems.id')
                        ->leftJoin('channels', 'leads.canal_id', '=', 'channels.id') 
                        ->leftJoin('lead_archive_reasons', 'lead_archive_reasons.lead_id', '=', 'leads.id')                           
                        ->select('leads.id', 'leads.name', 'leads.celular', 'users.name as usuario',  'leads.created_at', 'origems.name as origem', 'channels.name as channel');

            if($request->date != null)
                $leads->whereBetween ('leads.created_at', [$startDate, $endDate]);        
            
            $leads->when($request->term, function ($query) use ($request, $user) {
                $query->where('leads.name', 'LIKE', '%'.$request->term.'%');            
            });    

            if(($user->role_id == 2)){
                $leads->where('leads.user_id','=', $user->id);
            }else{
                if($request->has('companies')){
                    $leads->whereIn('leads.company_id', $request->companies);

                }else{
                    $leads->where('leads.company_id', '=', $company_id);
                }
                                
                if ($request->has('user')) {
                    $leads->whereIn ('leads.user_id', $request->user);
                }
            }

            if ($request->has('reasons')) {
                $leads->whereIn ('lead_archive_reasons.reason', $request->reasons);
            }
            
            if ($request->has('origem')) {
                $leads->whereIn ('leads.origem_id', $request->origem);
            }

            if ($request->has('channel')) {
                $leads->whereIn ('leads.canal_id', $request->channel);
            }

            if ($request->has('status')) {
                $leads->whereIn ('leads.status_id', $request->status);
            }

            $leads->where('leads.status_id','=', 8);               
            $leads->orderby('leads.created_at', 'DESC');        

            if ($request->has('companies')) {
                $users = User::select('id as value', 'name as label')
                               ->whereIn('company_id', $request->companies)
                               ->orderBy('name');
            }else{
                $users = User::select('id as value', 'name as label')
                            ->where("company_id","=", $user->company_id)
                            ->orderBy('name');
            }

            $status[] = [8];

            $filter = array(
                'term'=> $request->only(['term']),
                'reasons' => $request->reasons,                
                'origem' => $request->origem,                
                'status' => $status,
                'date' => $dates,
                'user' => $request->user,                
            );

            return Inertia::render(
                'Archievement/Index',
                [
                    'leads' => $leads->paginate(15)->withQueryString(),
                    'filters' => $filter,
                    'users' => $users->get(),
                    'reasons' => ArchiveReason::select('id as value', 'name as label')->orderBy('name')->get(),
                    'status' => Status::select('id as value', 'name as label')->whereIn('id',[8])->orderBy('name')->get(),
                    'origens' => Origem::select('id as value', 'name as label')->where("status_id","=",1)->orderBy('name')->get(),
                    'channels' => Channel::select('id as value', 'name as label')->where("status_id","=",1)->orderBy('name')->get(),                    
                ]
            );
        }
    }

    public function export(Request $request, Excel $excel)
    {   
        $user = Auth::user();        
        return $excel->download(new ArchievementExport($request, $user),'leads.xlsx');
        
    }
}
