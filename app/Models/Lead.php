<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = ['name','email', 'telefone', 'celular', 'origem', 'canal', 'user', 'mensagem', 'product'];

    public function leadMessage(): HasMany
    {
        return $this->hasMany(LeadMessage::class);
    }

    public function leadSchedule(): HasMany
    {
        return $this->hasMany(leadSchedule::class);
    }

    public function leadLog(): HasMany
    {
        return $this->hasMany(leadLog::class);
    }

    public function leadNote(): HasMany
    {
        return $this->hasMany(leadNotes::class);
    }

    public function funnelStep(): HasOne
    {
        return $this->hasOne(FunnelStep::class);
    }

    public function leadReason(): HasOne
    {
        return $this->hasOne(LeadArchiveReason::class);
    }

    public function getLeads($request, $user, $company_id){
        
        $leads = Lead::join('users', 'user_id', '=', 'users.id')
                    ->leftJoin('products', 'product_id', '=', 'products.id')
                    ->join('status', 'leads.status_id', '=', 'status.id')
                    ->leftJoin('origems', 'leads.origem_id', '=', 'origems.id')
                    ->leftJoin('channels', 'leads.canal_id', '=', 'channels.id') 
                    ->join('funnel_steps', 'leads.funnel_step_id', '=', 'funnel_steps.id')    
                    ->select('leads.id', 'leads.name', 'leads.celular','leads.email', 'users.name as usuario', 'products.name as product', 'leads.favorite', 'leads.status_id', 'leads.created_at', 'leads.new_lead', 'status.name as status', 'leads.status_id', 'schedule_date', 'interaction_date','origems.name as origem', 'channels.name as channel', 'funnel_steps.name as funnel_step', 'funnel_steps.color as funnel_step_color', 'bot_stage','forward');
        
        $leads = $this->filter($leads, $request, $user, $company_id);

        return $leads;    
    }

    public function filter($leads, $request, $user, $company_id){
       
        if ($request->has('date')) {
            $startDate = date('Y-m-d 00:00:00',strtotime($request->date[0]));
            $endDate = date('Y-m-d 23:59:59',strtotime($request->date[1]));            
        }else{
            $startDate = date('Y-m-d 00:00:00',strtotime(now()->startOfMonth()));
            $endDate = date('Y-m-d 23:59:59',strtotime(now()->endOfDay()));
        }

        if($request->date != null)
            $leads->whereBetween ('leads.created_at', [$startDate, $endDate]);        
        
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
        
        if ($request->has('product')) {
            $leads->whereIn('leads.product_id', $request->product);
        }
        
        if ($request->has('funnelStep')) {
            $leads->whereIn('leads.funnel_step_id', $request->funnelStep);
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

        if ($request->has('bots')) {
            
            $leads->whereIn('leads.bot_stage',$request->bots);
        }
        return $leads;
    }
}
