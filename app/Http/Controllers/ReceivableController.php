<?php

namespace App\Http\Controllers;

use App\Models\Receivable;
use App\Models\User;
use App\Models\Proposal;
use App\Models\Status;
use App\Models\Origem;
use App\Imports\PaymentImport;
use App\Exports\ReceivableExport;
use Maatwebsite\Excel\Excel;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Auth;

class ReceivableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
        $user = Auth::user();
        $company_id = getCompany($user);

        if(($user->role_id != 1)){
            return redirect()->route('dashboard');    
        }

        $this->setLate();

        if ($request->has('due_date')) {
            $dates = $request->due_date;
            $startDate = date('Y-m-d 00:00:00',strtotime($request->due_date[0]));
            $endDate = date('Y-m-d 23:59:59',strtotime($request->due_date[1]));
            $dates[] = [$startDate, $endDate];
            
            $request->session()->put('receivable.dates', $dates);
        }else{
            if($request->session()->has('receivable.dates')){
                $dates = $request->session()->pull('receivable.dates');
                $request->session()->put('receivable.dates', $dates);
            }else{
                $startDate = now()->startOfMonth();
                $startDate = date('Y-m-d 00:00:00',strtotime($startDate));
    
                $endDate = now()->endOfDay();
                $endDate = date('Y-m-d 23:59:59',strtotime($endDate));

                $dates = [$startDate, $endDate];
            }
        }
        
        $receivables = Receivable::join('proposals', 'receivables.proposal_id', '=', 'proposals.id')
                       ->join('users', 'proposals.user_id', '=', 'users.id')
                       ->join('leads', 'proposals.lead_id', '=', 'leads.id')
                       ->join('status', 'receivables.status_id', '=', 'status.id')
                       ->select('receivables.id','receivables.group','receivables.quota', 'description', 'receivables.subtotal','receivables.descount','receivables.interest','receivables.total', 'receivables.due_date',  'users.name as usuario', 'receivables.status_id','receivables.created_at', 'status.name as status_name','conciliation_id','parcel')
                       ->where('leads.company_id', '=', $company_id)
                       ->whereNotIn('proposals.status_id', [3]);
        
        $receivables = $this->filter($receivables, $request, $dates, false);
        
        $users = User::select('id as value', 'name as label')
                        ->where("company_id","=", $company_id)
                        ->where("status_id","=", 1) 
                        ->orderBy('name');

        $status = Status::whereIn('id',[4,14,15])->orderBy("id", "asc")->get();
        $totals = [];
        
        foreach ($status as $sts)
        {

            $total = Receivable::join('proposals', 'receivables.proposal_id', '=', 'proposals.id')
                                ->join('users', 'proposals.user_id', '=', 'users.id')
                                ->join('leads', 'proposals.lead_id', '=', 'leads.id')
                                ->join('status', 'receivables.status_id', '=', 'status.id')
                                ->where('receivables.status_id','=',$sts->id);

            $total = $this->filter($total, $request, $dates, true);           
            $total = $total->sum('receivables.total');

            $totals[] = [ 'id' => $sts->id, 
                'name' => $sts->name, 
                'total' => $total,
            ];
        }

        $filter = array(
            'term'=> $request->only(['term']),
            'date'=> $request->date,
            'due_date'=> $request->due_date,
            'user'=> $request->user,
            'status'=> $request->status,
            'origem'=> $request->origem,
        );
         
        return Inertia::render(
            'Receivable/Index',
            [
                'receivables' => $receivables->orderBy('receivables.due_date','asc')->paginate(30)->withQueryString(),
                'users' => $users->get(),
                'status' => Status::select('id as value', 'name as label')->whereIn('id',[4,14,15])->orderBy('name')->get(),
                'origens'=> Origem::select('id as value', 'name as label')->whereNotIn('id',[3])->orderBy('name')->get(),
                'filters' => $filter,
                'totals' => $totals,
            ]
        );
    }

    public function filter($receivables, $request, $dates, $total){
        
        $user = Auth::user();
        $company_id = getCompany($user);

        $receivables->when($request->term, function ($query) use ($request) {
            $query->where('receivables.description', 'LIKE', '%'.$request->term.'%');

        });
        
        if ($request->has('due_date')) {
            $receivables->whereBetween ('receivables.due_date', $dates); 
        }
        
        if ($request->has('status')) {
            $receivables->whereIn('receivables.status_id', $request->status);
        }else{                
            if($total)
                $receivables->whereIn('receivables.status_id', [4,14,15]);
            else
                $receivables->whereIn('receivables.status_id', [4,14]);
        }        

        if ($request->has('date')) {
            $receivables->whereBetween ('receivables.created_at', [date('Y-m-d 00:00:00',strtotime($request->date[0])), date('Y-m-d 23:59:59',strtotime($request->date[1]))]); 
        }

        if ($request->has('approved')) {
            $receivables->whereBetween ('receivables.payed_at', [date('Y-m-d 00:00:00',strtotime($request->approved[0])), date('Y-m-d 23:59:59',strtotime($request->approved[1]))]); 
        }

        if ($request->has('user')) {
            $receivables->whereIn ('proposals.user_id', $request->user);
        }

        if ($request->has('origem')) {
            $receivables->whereIn ('leads.origem_id', $request->origem);
        }
        
        return $receivables;
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
        $request->validate([
            'group' => 'required|string|max:24',
            'quota' => 'required|string|max:24',
            'due_date' => 'required',
            'subtotal' => 'required|string|not_in:"0,00"',
        ]);

        $proposal = Proposal::join('leads','lead_id','leads.id')
                            ->select('proposals.id', 'leads.name')
                            ->where('group','=',$request->group)
                            ->where('quota','=',$request->quota)
                            ->where('proposals.status_id','=',13)
                            ->get();

        if($proposal->count()){
            
            $parcels = Receivable::where('proposal_id','=',$proposal[0]->id)->orderBy('id','desc')->limit(1)->get();
            $date = date('Y-m-d', strtotime($request->due_date));
            
            $receivable = new Receivable();
            $receivable->group = $request->group;
            $receivable->quota = $request->quota;
            $receivable->description = $proposal[0]->name." ".$request->group."/".$request->quota;
            $receivable->parcel = $parcels[0]->parcel + 1;
            $receivable->due_date = $date;
            $receivable->status_id = 4;
            $receivable->type = 1;
            $receivable->proposal_id = $proposal[0]->id;
            $receivable->subtotal = formatMoney($request->subtotal);
            $receivable->descount = formatMoney($request->descount);
            $receivable->interest = formatMoney($request->interest);
            $receivable->total = formatMoney($request->total);
            $receivable->save();
            sleep(1);
           
        }else{
            return redirect()->back()->withErrors(['message' => 'O grupo e cota informado não pertence a uma proposta apropriada']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Receivable $receivable)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Receivable $receivable)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Receivable $receivable)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'due_date' => 'required',
            'subtotal' => 'required|string|not_in:"0,00"',
        ]);

        if(!is_null($request->due_date))
            $date = date('Y-m-d H:i:s', strtotime($request->due_date));
                
        $receivable->description = $request->description;
        $receivable->due_date = $date;
        $receivable->status_id = 4;
        $receivable->subtotal = formatMoney($request->subtotal);
        $receivable->descount = formatMoney($request->descount);
        $receivable->interest = formatMoney($request->interest);
        $receivable->total = formatMoney($request->total);
        $receivable->save();
        sleep(1);

        return redirect()->route('receivable.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Receivable $receivable)
    {
        $receivable->status_id = 3;
        $receivable->payed_at = null;
        $receivable->save();
        sleep(1);    

        return redirect()->back()->with('message', 'Conta excluída com sucesso');
    }

    public function approve(Request $request, Receivable $receivable)
    {
        $receivable->status_id = 15;
        $receivable->payed_at = now();
        $receivable->save();
        sleep(1);    

        return redirect()->back()->with('message', 'Conta baixada com sucesso');
    }

    public function unpprove(Receivable $receivable)
    {
        $receivable->status_id = 4;
        $receivable->payed_at = null;
        $receivable->save();
        sleep(1);    

        return redirect()->back()->with('message', 'Conta estornada com sucesso');
    }

    public function setLate()
    {
        $receivables = Receivable::where("due_date","<", date(now()->startOfDay()))
                                ->where("status_id","=", 4)
                                ->get();

        foreach($receivables as $receivable){
            $receivable->status_id = 14;
            $receivable->save();
            sleep(1);
        }

    }

    public function export(Request $request, Excel $excel)
    {   
        $user = Auth::user();   
        return $excel->download(new ReceivableExport($request, $user),'contas-a-receber.xlsx');
        
    }
}
