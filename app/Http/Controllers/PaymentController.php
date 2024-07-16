<?php

namespace App\Http\Controllers;
use App\Models\PaymentReconciliation;
use App\Models\Status;
use App\Models\Receivable;
use App\Models\Proposal;
use App\Imports\PaymentImport;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Auth;
use Excel;
use Carbon\Carbon;

class PaymentController extends Controller
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

        Session::put('payment_url', $request->fullUrl());

        if ($request->has('date')) {
            $startDate = date('Y-m-d 00:00:00',strtotime($request->date[0]));
            $endDate = date('Y-m-d 23:59:59',strtotime($request->date[1]));
            $dates = [$startDate, $endDate];            
            $request->session()->put('payment.dates', $dates);
        }else{
            if($request->session()->has('payment.dates')){
                $dates = $request->session()->pull('payment.dates');
                $request->session()->put('payment.dates', $dates);
            }else{
                $startDate = now()->startOfMonth();
                $startDate = date('Y-m-d 00:00:00',strtotime($startDate));
    
                $endDate = now()->endOfDay();
                $endDate = date('Y-m-d 23:59:59',strtotime($endDate));

                $dates = [$startDate, $endDate];
            }
        }

        $payments = PaymentReconciliation::join('status','status_id', 'status.id')
                                        ->select('payment_reconciliations.id', 'deal', 'group', 'quota', 'closed_at', 'parcel', 'total', 'total_deal', 'comission', 'status_id','status.name as status')
                                        ->whereBetween ('closed_at', $dates);

        $payments->when($request->term, function ($query) use ($request) {
            $query->where('payment_reconciliations.group', 'LIKE', '%'.$request->term.'%')
                  ->orWhere('payment_reconciliations.quota', 'LIKE', '%'.$request->term.'%');

        });
        
        if ($request->has('status')) {
            $payments->whereIn('payment_reconciliations.status_id', $request->status);
        }else{
            $payments->whereIn('payment_reconciliations.status_id', [4,19,18,17]);
        }

        
        $status = Status::whereIn('id',[17,18,19])->orderBy("id", "asc")->get();
        $totals = [];
        
        foreach ($status as $sts)
        {

            $total = PaymentReconciliation::where('status_id', '=', $sts->id )
                                            ->whereBetween ('closed_at', $dates)
                                            ->sum('total');

            $totals[] = [ 'id' => $sts->id, 
            'name' => $sts->name, 
            'total' => $total,
          ];
        }
        
        $filter = array(
            'term'=> $request->only(['term']),
            'date'=> $dates,
            'status'=> $request->status,
        );        

        return Inertia::render(
            'Payment/Index',
            [
                'payments' => $payments->orderBy('payment_reconciliations.created_at','asc')->paginate(30)->withQueryString(),                
                'filters' => $filter,
                'totals' => $totals,
                'status' => Status::select('id as value', 'name as label')->whereIn('id',[4,17,18,19,3])->orderBy('name')->get(),
            ]
        );
    }

    public function importFile(Request $request){
        
        $request->validate([
            'date' => 'required',
            'file' => 'required'
        ]);

        if ($request->hasFile('file')) {
         
        }

        $file = $request->file('file'); 
        $name = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        if($extension == 'xlsx'){
            
            $path = Storage::putFileAs('files', $request->file('file'), 'relatorio.csv');   
            Excel::import(new PaymentImport($request->date), $request->file);

            return redirect()->back()->with('message', 'Arquivo importado com sucesso');

        }else{
            
            return redirect()->back()->withErrors(['message' => 'Somente arquivos xlsx podem ser importados']);
        }
    }

    public function receivables(Request $request){

        $proposal = Proposal::where('group','LIKE',$request->group)
                              ->where('quota','LIKE',$request->quota)
                              ->limit(1)
                              ->get();

        if($proposal->count() > 0){
            $receivables = Receivable::join('proposals','proposal_id','proposals.id')
                                        ->join('status', 'receivables.status_id', '=', 'status.id')
                                        ->selectRaw('receivables.id, DATE_FORMAT(receivables.due_date, "%Y-%m-%d") due_date, receivables.total, conciliation_id, receivables.status_id, status.name as status_name')
                                        ->where('proposals.id', '=',$proposal[0]->id )
                                        ->where('receivables.status_id','<>',2)
                                        ->orderBy('receivables.id')
                                        ->get();
        }else{
            $receivables = [];
        }

        $payment = PaymentReconciliation::where('id','=',$request->conciliationID)->get();

        $data[] = [
            'payment' => $payment,
            'receivables'=> $receivables,
        ];

        return  $data;
    }


    public function association(Request $request){

        $payment = PaymentReconciliation::find($request->conciliationID);
        $payment->receivable_id = $request->receivableID;
        $payment->status_id = 17;
        $payment->save();
        sleep(1);

        $receivable = Receivable::find($request->receivableID);
        $receivable->conciliation_id = $request->conciliationID;
        $receivable->subtotal = $payment->total;
        $receivable->total = $payment->total;
        $receivable->status_id = 15;
        $receivable->save();
        sleep(1);

        return  true;
    }

    public function disassociation(PaymentReconciliation $payment)
    {
        
        $receivable = Receivable::where('conciliation_id','=', $payment->id)->get();
        $receivable[0]->conciliation_id = null; 
        $receivable[0]->status_id = 4; 
        $receivable[0]->save();
        sleep(1);    

        $payment->status_id = 4; 
        $payment->save();
        sleep(1);

        if(session('payment_url')){
            return redirect(session('payment_url'))->with('message', ['type' => 'info', 'msg' => 'Pagamento estornado com sucesso']);
        }

        return redirect()->route('payments.index')->with('message', ['type' => 'info', 'msg' => 'Pagamento estornado com sucesso'] );
    }

    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentReconciliation $payment)
    {
        $payment->status_id = 3; //EXCLUIDO
        $payment->save();
        sleep(1);

        if(session('payment_url')){
            return redirect(session('payment_url'))->with('message',['type' => 'info', 'msg' => 'Pagamento excluído com sucesso']);
        }

        return redirect()->route('payments.index')->with('message', ['type' => 'info', 'msg' => 'Pagamento excluído com sucesso']);

    }
}
