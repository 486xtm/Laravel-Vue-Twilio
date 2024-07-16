<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\Lead;
use App\Models\LeadLog;
use App\Models\User;
use App\Models\Company;
use App\Models\Status;
use App\Models\Product;
use App\Models\Origem;
use App\Models\ProposalType;
use App\Models\Receivable;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Auth;
use Carbon\Carbon;


class ProposalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        Session::put('proposal_url', $request->fullUrl());

        if ($request->has('date')) {
            $startDate = date('Y-m-d 00:00:00',strtotime($request->date[0]));
            $endDate = date('Y-m-d 23:59:59',strtotime($request->date[1]));
            $dates = [$startDate, $endDate];
            
            $request->session()->put('proposal.dates', $dates);
        }else{
            if($request->session()->has('proposal.dates')){
                $dates = $request->session()->pull('proposal.dates');
                $request->session()->put('proposal.dates', $dates);
            }else{
                $startDate = now()->startOfMonth();
                $startDate = date('Y-m-d 00:00:00',strtotime($startDate));
    
                $endDate = now()->endOfDay();
                $endDate = date('Y-m-d 23:59:59',strtotime($endDate));

                $dates = [$startDate, $endDate];
            }
        }

        $proposals = Proposal::join('leads', 'lead_id', '=', 'leads.id')
                       ->join('users', 'proposals.user_id', '=', 'users.id')
                       ->leftjoin('products', 'proposals.product_id', '=', 'products.id')
                       ->join('status', 'proposals.status_id', '=', 'status.id')
                       ->select('proposals.id', 'leads.id as lead_id', 'leads.name', 'leads.celular', 'users.name as usuario', 'products.name as product','proposals.group','proposals.quota','proposals.price','proposals.status_id','proposals.created_at', 'status.name as status_name', 'approved_at')
                       ->whereNotIn('proposals.status_id', [3]);
        
        $proposals = $this->filter($proposals, $request, $dates);

        $status = Status::whereIn('id',[4,11,13])->orderBy("id", "asc")->get();
        $totals = [];
        
        foreach ($status as $sts)
        {

            $total = Proposal::join('leads', 'lead_id', '=', 'leads.id')
                            ->join('users', 'proposals.user_id', '=', 'users.id')
                            ->leftjoin('products', 'proposals.product_id', '=', 'products.id')
                            ->join('status', 'proposals.status_id', '=', 'status.id')
                            ->where('proposals.status_id', '=', $sts->id );

            $total = $this->filter($total, $request, $dates);

            $total = $total->sum('price');

            $totals[] = [ 'id' => $sts->id, 
            'name' => $sts->name, 
            'total' => $total,
          ];
        }
        
        $users = [];

        if($user->role_id === 1 || $user->role_id === 3){
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
            $list = User::select('id')->where('manager_id', $user->id)->get(); 
            $users = User::select('id as value', 'name as label', 'company_id')
                ->whereIn("id",$list)
                ->orderBy('name')
                ->get();
        }

        $filter = array(
            'term'=> $request->only(['term']),
            'date'=> $dates,
            'approved'=> $request->approved,
            'user'=> $request->user,
            'companies'=> $request->companies,
            'status'=> $request->status,
            'origem'=> $request->origem,
        );
         
        return Inertia::render(
            'Proposal/Index',
            [
                'proposals' => $proposals->paginate(15)->withQueryString(),
                'users' => $users,
                'types' => ProposalType::select('id as value', 'name as label')->orderBy('name')->get(),
                'companies' => Company::select('id as value', 'name as label')->where('status_id', '=', 1)->orderBy('name')->get(),
                'status' => Status::select('id as value', 'name as label')->whereIn('id',[4,11,13])->orderBy('name')->get(),
                'origens'=> Origem::select('id as value', 'name as label')->whereNotIn('status_id',[3])->orderBy('name')->get(),
                'filters' => $filter,
                'totals' => $totals,
                'role' => $user->role_id,
            ]
        );
    }

    public function filter($proposals, $request, $dates){
        
        $user = Auth::user();
        $company_id = getCompany($user);

        $proposals->whereBetween ('proposals.created_at', $dates);
        
        if($request->has('companies')){
            $proposals->whereIn('proposals.company_id', $request->companies);
        }else{
            $proposals->where('proposals.company_id', '=', $company_id);
        }

        if(($user->role_id == 1)){
            if ($request->has('user')) {
                $proposals->whereIn ('proposals.user_id', $request->user);
            }
        }elseif($user->role_id == 4){
            
            if ($request->has('user')) {
                $proposals->whereIn('proposals.user_id', $request->user);
            }else{
                $list = User::select('id')->where('manager_id', $user->id)->get(); 
                $proposals->whereIn('proposals.user_id', $list);
            }

        }elseif(($user->role_id == 2)){
            $proposals->where('proposals.user_id', '=', $user->id);
        }
                       
        $proposals->when($request->term, function ($query) use ($request) {
            $query->where('leads.name', 'LIKE', '%'.$request->term.'%')
                  ->orWhere('proposals.group', 'LIKE', '%'.$request->term.'%')
                  ->orWhere('proposals.quota', 'LIKE', '%'.$request->term.'%');    

        }, function ($query) {
            $query->orderBy('proposals.id', 'desc');
        });
        
        if ($request->has('status')) {
            $proposals->whereIn('proposals.status_id', $request->status);
        }
        
        if ($request->has('approved')) {
            $proposals->whereBetween ('proposals.approved_at', [date('Y-m-d 00:00:00',strtotime($request->approved[0])), date('Y-m-d 23:59:59',strtotime($request->approved[1]))]); 
        }

        if ($request->has('types')) {
            $proposals->whereIn ('proposals.proposal_type', $request->types);
        }        

        if ($request->has('origem')) {
            $proposals->whereIn ('leads.origem_id', $request->origem);
        }        

        return $proposals;
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types = ProposalType::orderBy('name')->get();
        $products = Product::where('status_id','=',1)->orderBy('name')->get();
        
        return Inertia::render(
            'Proposal/Create',
            [
                'types' => $types,
                'products' => $products,
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {   
        $user = Auth::user();

        $request->validate([
            'group' => 'required|string|max:32',
            'quota' => 'required|string|max:32',
            'proposal_type' => 'required',
            'product' => 'required',
            'price' => 'required',            
        ]);

        $lead = Lead::find($request->lead_id);
        $lead->funnel_step_id = 3;
        $lead->save();
        sleep(1);

        $proposal = new Proposal();
        $proposal->group = strtoupper($request->group);
        $proposal->quota = strtoupper($request->quota);
        $proposal->proposal_type = $request->proposal_type;
        $proposal->product_id = $request->product;
        $proposal->price = formatMoney($request->price);
        $proposal->observation = $request->observation;
        $proposal->lead_id = $request->lead_id;
        $proposal->user_id = $lead->user_id;
        $proposal->company_id = $user->company_id;
        $proposal->status_id = 4; //STATUS = ABERTO 
        $proposal->save();
        sleep(1);

        $log = new LeadLog();
        $log->action = "📄 Proposta Cadatrada";
        $log->description = "Um nova proposta foi cadastra";
        $log->lead_id = $request->lead_id;
        $log->status_id = 1;
        $log->user_id = $user->id;
        $log->save();
        sleep(1);

        return redirect($request->page);
    }

    /**
     * Display the specified resource.
     */
    public function show(Proposal $proposal)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Proposal $proposal)
    {   
        $user = Auth::user();

        $lead = Lead::join('products', 'product_id', '=', 'products.id')
                       ->join('status', 'leads.status_id', '=', 'status.id')
                       ->join('origems', 'leads.origem_id', '=', 'origems.id')
                       ->join('channels', 'leads.canal_id', '=', 'channels.id')                       
                       ->select('leads.id', 'leads.name', 'leads.celular', 'products.name as product', 'leads.favorite', 'leads.status_id', 'leads.created_at', 'leads.new_lead', 'status.name as status', 'leads.status_id', 'origems.name as origem', 'channels.name as channel', 'product_id')
                       ->where('leads.id', '=', $proposal->lead_id)->get();

        
        $receivables = Receivable::selectRaw('id, DATE_FORMAT(due_date, "%Y-%m-%d") due_date, total')
                                    ->where('proposal_id', '=', $proposal->id)->get();
       
        $users = User::select('id', 'name')
                       ->where('company_id', '=', $user->company_id)->get();
       
        $types = ProposalType::orderBy('name')->get();
        $products = Product::where('status_id','=',1)->orderBy('name')->get();

        return Inertia::render(
            'Proposal/Edit',
            [
                'proposal' => $proposal,
                'types' => $types,
                'products' => $products,
                'lead' => $lead,
                'users' => $users,
                'receivables' => $receivables,
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Proposal $proposal)
    {
        $user = Auth::user();
        $date = null;
        
        $request->validate([
            'group' => 'required|string|max:255',
            'proposal_type' => 'required',
            'product' => 'required',
            'price' => 'required',
            'user' => 'required',            
        ]);

        if($request->comission == null ){
            $comission = 0;
            $comission_total = 0;
        }else{        
            $comission = formatMoney($request->comission);
            $comission_total = round($request->comission_total,2);
        }

        if(!is_null($request->due_date))
            $date = date('Y-m-d H:i:s', strtotime($request->due_date));
        
        if($request->status_id === 11)
            $approved_at = date('Y-m-d H:i',strtotime($request->approved_at));
        else
            $approved_at = null;

        $proposal->group = strtoupper($request->group);
        $proposal->quota = strtoupper($request->quota);
        $proposal->proposal_type =$request->proposal_type;
        $proposal->product_id =$request->product;
        $proposal->price = formatMoney($request->price);
        $proposal->comission = $comission;
        $proposal->comission_total = $comission_total;
        $proposal->parcels = $request->parcels;
        $proposal->due_date = $date;
        $proposal->created_at = date('Y-m-d H:i',strtotime($request->created_at));
        $proposal->approved_at = $approved_at;
        $proposal->due_date = $date;
        $proposal->observation =$request->observation;
        $proposal->user_id =$request->user;
        $proposal->save();
        sleep(1);

        $receivable = Receivable::where('proposal_id',$proposal->id)->delete();
        $i = 1;

        foreach($request->receivables as $receivable){

            $total = formatMoney($receivable['total']);
            
            $date = date('Y-m-d', strtotime($receivable['due_date']));
            $lead = Lead::find($proposal->lead_id);
            
            $receivable = new Receivable();
            $receivable->description = $lead->name.' '.$request->group.'/'.$request->quota;
            $receivable->group = $request->group;
            $receivable->quota = $request->quota;
            $receivable->parcel = $i;                        
            $receivable->type = 1;
            $receivable->proposal_id = $proposal->id;
            $receivable->due_date = $date;
            $receivable->descount = 0;
            $receivable->interest = 0;
            $receivable->subtotal = $total;
            $receivable->total= $total;
            $receivable->status_id = 2;
            $receivable->save();
            sleep(1);

            $i++;
        }

        if(session('proposal_url')){
            return redirect(session('proposal_url'))->with('message', ['type' => 'info', 'msg' => 'Proposta atualizada com sucesso']);
        }

        return redirect()->route('proposal.index')->with('message', ['type' => 'info', 'msg' => 'Proposta atualizada com sucesso']);
    }


    /**
     * Update the specified resource in storage.
     */
    public function approve(Request $request, Proposal $proposal)
    {
        $user = Auth::user();

        $proposal->status_id = 11;
        $proposal->approved_at = now();
        $proposal->save();
        sleep(1);    

        $lead = Lead::find($proposal->lead_id);
        $lead->funnel_step_id = 4; //NEGÓCIO FECHADO
        $lead->status_id = 16; //NEGÓCIO FECHADO
        $lead->save();
        sleep(1);

        $log = new LeadLog();
        $log->action = "📄 Proposta Aprovada";
        $log->description = "Proposta foi aprovada";
        $log->lead_id = $proposal->lead_id;
        $log->status_id = 1;
        $log->user_id = $user->id;
        $log->save();
        sleep(1);

        if(session('proposal_url')){
            return redirect(session('proposal_url'))->with('message', ['type' => 'info', 'msg' => 'Proposta atualizada com sucesso']);
        }

        return redirect()->back()->with('message', ['type' => 'info', 'msg' => 'Proposta aprovada com sucesso']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function appropriation(Request $request, Proposal $proposal)
    {
        $user = Auth::user();

        $receivables = Receivable::where('proposal_id','=', $proposal->id)->get();

        if($receivables->count() == 0 ){

            return redirect()->back()->with('message', ['type' => 'danger', 'msg' => 'Proposta sem contas à receber cadastrada']);

        }else{
        
            $proposal->status_id = 13; //Apropriado
            $proposal->appropriated_at = now();
            $proposal->save();
            sleep(1);    

            
            foreach($receivables as $receivable){
                $receivable->status_id = 4;
                $receivable->save();
                sleep(1);   
            }  

            if(session('proposal_url')){
                return redirect(session('proposal_url'))->with('message', ['type' => 'info', 'msg' => 'Proposta atualizada com sucesso']);
            }

            return redirect()->back()->with('message', ['type' => 'info', 'msg' => 'Proposta apropriada com sucesso']);
        }

    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proposal $proposal)
    {
        $proposal->status_id = 3; //EXCLUIDO
        $proposal->save();
        sleep(1);

        return redirect()->route('proposal.index')->with('message', ['type' => 'info', 'msg' => 'Proposta excluída com sucesso']);

    }

    public function unpprove(Proposal $proposal)
    {
        if($proposal->status_id == 11)
            $status = 4;//ABERTO
        elseif($proposal->status_id == 13)
            $status = 11;

        $lead = Lead::find($proposal->lead_id);
        $lead->funnel_step_id = 3; //NEGÓCIO FECHADO
        $lead->status_id = 7; //INTERAGIDO
        $lead->save();
        sleep(1);    

        $receivables = Receivable::where('proposal_id','=', $proposal->id)->get();
        
        foreach($receivables as $receivable){
            $receivable->status_id = 2;
            $receivable->save();
            sleep(1);   
        }

        if($status == 4)
            $proposal->approved_at = null; 

        $proposal->status_id = $status; 
        $proposal->save();
        sleep(1);

        return redirect()->route('proposal.index')->with('message', ['type' => 'info', 'msg' => 'Proposta estornada com sucesso'] );

    }
}
