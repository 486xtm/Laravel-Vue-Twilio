<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receivable extends Model
{
    use HasFactory;
    protected $fillable = ['description', 'type', 'subtotal', 'descount', 'interest', 'total', 'due_date', 'proposal', 'status'];

    public function proposal(): HasOne
    {
        return $this->hasOne(Proposal::class);
    }

    public function getAll($request, $user, $company_id){
        
        $receivable = Receivable::join('proposals', 'receivables.proposal_id', '=', 'proposals.id')
                                    ->join('users', 'proposals.user_id', '=', 'users.id')
                                    ->join('leads', 'proposals.lead_id', '=', 'leads.id')
                                    ->join('status', 'receivables.status_id', '=', 'status.id')
                                    ->select('receivables.id','leads.name','receivables.group','receivables.quota', 'description', 'receivables.subtotal','receivables.descount','receivables.interest','receivables.total', 'receivables.due_date', 'users.name as user_name', 'status.name as status_name', 'proposals.price as total_proposal', 'receivables.parcel')
                                    ->where('leads.company_id', '=', $company_id)
                                    ->whereNotIn('proposals.status_id', [3]);
            
        $receivable = $this->filter($receivable, $request, $user, $company_id);

        return $receivable;    
    }

    public function filter($receivables, $request, $user, $company_id){
       
        $receivables->when($request->term, function ($query) use ($request) {
            $query->where('receivables.description', 'LIKE', '%'.$request->term.'%');
        });
        
        if ($request->has('due_date')) {
            $receivables->whereBetween ('receivables.due_date', [date('Y-m-d 00:00:00',strtotime($request->due_date[0])), date('Y-m-d 23:59:59',strtotime($request->due_date[1]))]); 
        }
        
        if ($request->has('status')) {
            $receivables->whereIn('receivables.status_id', $request->status);
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
}
