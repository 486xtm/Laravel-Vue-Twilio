<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use App\Models\Channel;
use App\Models\Origem;
use App\Models\Product;
use App\Models\Company;
use App\Models\LeadLog;
use App\Models\LeadNote;
use App\Models\Status;
use App\Models\FunnelStep;
use App\Models\ReadyMessage;
use App\Models\ProposalType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Auth;

class FunnelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $company_id = getCompany($user);

        $findme   = '@';

        if ($request->has('date')) {
            $startDate = date('Y-m-d 00:00:00',strtotime($request->date[0]));
            $endDate = date('Y-m-d 23:59:59',strtotime($request->date[1]));
            $dates = [$startDate, $endDate];

            $request->session()->put('funnel.dates', $dates);
        }else{
            
            if($request->session()->has('funnel.dates')){
                $dates = $request->session()->pull('funnel.dates');
                $request->session()->put('funnel.dates', $dates);
            }else{
                $startDate = now()->startOfMonth();
                $startDate = date('Y-m-d 00:00:00',strtotime($startDate));

                $endDate = now()->endOfDay();
                $endDate = date('Y-m-d 23:59:59',strtotime($endDate));

                $dates = [$startDate, $endDate];
            }
        }

        $kabanCards = FunnelStep::where('status_id','=',1)->orderBy("order", "asc")->get();
        $list = [];
        
        foreach ($kabanCards as $step)
        {
            $leads = Lead::join('users', 'leads.user_id', '=', 'users.id')
                        ->leftjoin('products', 'product_id', '=', 'products.id')
                        ->join('status', 'leads.status_id', '=', 'status.id')
                        ->leftjoin('origems', 'leads.origem_id', '=', 'origems.id')
                        ->leftjoin('channels', 'leads.canal_id', '=', 'channels.id') 
                        ->join('funnel_steps', 'leads.funnel_step_id', '=', 'funnel_steps.id')    
                        ->select('leads.id', 'leads.name', 'leads.celular', 'users.name as usuario', 'products.name as product', 'leads.favorite', 'leads.status_id', 'leads.created_at', 'leads.new_lead', 'status.name as status', 'leads.status_id', 'leads.funnel_step_id', 'schedule_date', 'interaction_date','origems.name as origem', 'channels.name as channel', 'funnel_steps.name as funnel_step')
                        ->whereNotIn('leads.status_id', [3,12])                        
                        ->whereBetween ('leads.created_at', $dates)
                        ->where('leads.funnel_step_id', '=', $step->id);
                        

            $leads->when($request->term, function ($query) use ($request, $user) {            
                if(is_numeric($request->term)){
                    $query->where('leads.celular', 'LIKE', '%'.$request->term.'%');
                }else{
                    if(strpos($request->term, "@")) 
                        $query->where('leads.email', 'LIKE', '%'.$request->term.'%');
                    else
                        $query->where('leads.name', 'LIKE', '%'.$request->term.'%');
                }
            });    

            if($user->role_id == 1){
                if($request->has('companies')){
                    $leads->whereIn('leads.company_id', $request->companies);
                }else{
                    $leads->where('leads.company_id', '=', $company_id);
                }

                if ($request->has('user')) {
                    $leads->whereIn ('leads.user_id', $request->user);
                }

            }elseif($user->role_id == 4){
                if ($request->has('user')) {
                    $leads->whereIn('leads.user_id', $request->user);
                    
                }else{
                    $obj = User::select('id')->where('manager_id', $user->id)->get(); 
                    $leads->whereIn('leads.user_id', $obj);
                }

            }elseif($user->role_id == 2){
                $leads->where('leads.user_id','=', $user->id);
            }

            if ($request->has('product')) {
                $leads->whereIn('leads.product_id', $request->product);
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

            if ($request->has('stars')) {
                $leads->where('leads.stars','=', (int)$request->stars);
            }
            
            $list[] = [ 'id' => $step->id, 
                        'name' => $step->name, 
                        'color' => $step->color, 
                        'count' => $leads->count(), 
                        'leads' => $leads->limit(100)->get(),
                      ];
            
        }

        $users = [];

        if($user->role_id === 1){
            if ($request->has('companies')) {
                $users = User::select('id as value', 'name as label','company_id')
                            ->whereIn('company_id', $request->companies)
                            ->orderBy('name')
                            ->get();
            }else{
                $users = User::select('id as value', 'name as label', 'company_id')
                            ->where("company_id","=", $user->company_id)
                            ->orderBy('name')
                            ->get();
            }
        }elseif($user->role_id === 4){
            $obj = User::select('id')->where('manager_id', $user->id)->get(); 
            $users = User::select('id as value', 'name as label', 'company_id')
                ->whereIn("id",$obj)
                ->orderBy('name')
                ->get();
        }

        $filter = array(
            'term'=> $request->only(['term']),
            'product' => $request->product,
            'funnelStep' => $request->funnelStep,
            'origem' => $request->origem,
            'channel' => $request->channel,
            'date' => $dates,
            'status' => $request->status,
            'user' => $request->user,
            'companies' => $request->companies,
            'stars' => $request->stars,            
        );

        return Inertia::render(
            'Funnel/Index',
            [
                'filters' => $filter,
                'products' => Product::select('id as value', 'name as label')->where("status_id","=",1)->orderBy('id')->get(),
                'origens' => Origem::select('id as value', 'name as label')->where("status_id","=",1)->orderBy('id')->get(),
                'funnelSteps' => FunnelStep::select('id as value', 'name as label')->where("status_id","=",1)->orderBy('id')->get(),
                'channels' => Channel::select('id as value', 'name as label')->where("status_id","=",1)->orderBy('id')->get(),
                'status' => Status::select('id as value', 'name as label')->whereIn("id",[10,7,8,9,16])->orderBy('name')->get(),
                'proposalTypes' => ProposalType::orderBy('id')->get(),
                'companies' => Company::select('id as value', 'name as label')->where('status_id', '=', 1)->orderBy('name')->get(),
                'kabanCards' => $list,   
                'users' => $users,
                'role' => $user->role_id,                 
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
            'name' => 'required|string|max:255'
        ]);
        
        $step = new FunnelStep();
        $step->name =$request->name;
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

        $step->name = $request->name;
        $step->save();
        sleep(1);

        return redirect()->route('funnelstep.index')->with('message', 'Etapa atualizada com sucesso');
    }
}
