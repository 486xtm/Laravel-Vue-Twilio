<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use App\Models\Bot;
use App\Models\Channel;
use App\Models\Origem;
use App\Models\Product;
use App\Models\Company;
use App\Models\LeadLog;
use App\Models\LeadNotes;
use App\Models\LeadMessage;
use App\Models\LeadSchedule;
use App\Models\FunnelStep;
use App\Models\Status;
use App\Models\ReadyMessage;
use App\Models\ProposalType;
use App\Models\ArchiveReason;
use App\Models\LeadArchiveReason;
use App\Http\Controllers\ChatBotController;
use App\Mail\NotificationMail;
use App\Mail\ForwardMail;
use App\Exports\LeadsExport;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Excel;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Cache;
use Spatie\WebhookServer\WebhookCall;
use Carbon\Carbon;
use Laravel\Socialite\Facades\Socialite;
use Database\Seeders\ReadyMessagesSeeder;

use Auth;


class LeadController extends Controller
{   

    public function __construct()
    {
        $this->user = Auth::user();
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request )
    {   
        if(Auth::check()){
            $user = Auth::user();
            $company_id = getCompany($user);
            
            Session::put('lead_url', $request->fullUrl());

            if ($request->has('date')) {
                $dates = [date('Y-m-d 00:00:00',strtotime($request->date[0])), date('Y-m-d 23:59:59',strtotime($request->date[1]))];                
                $request->session()->put('lead.dates', $dates);
            }else{                
                if($request->session()->has('lead.dates')){
                    $dates = $request->session()->pull('lead.dates');
                    $request->session()->put('lead.dates', $dates);
                }else{
                    $dates = [date('Y-m-d 00:00:00',strtotime(now()->startOfMonth())), date('Y-m-d 23:59:59',strtotime(now()->endOfDay()))];        
                }               
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
                'product' => $request->product,
                'funnelStep' => $request->funnelStep,
                'origem' => $request->origem,
                'channel' => $request->channel,
                'status' => $request->status,
                'date' => $dates,
                'user' => $request->user,
                'companies' => $request->companies,
                'stars' => $request->stars,
                'bots' => $request->bots,
            );

            return Inertia::render(
                'Lead/Index',
                [
                    'leads' => [],
                    'filters' => $filter,
                    'products' => Product::select('id as value', 'name as label')->where("status_id","=",1)->orderBy('id')->get(),
                    'users' => $users,
                    'companies' => Company::select('id as value', 'name as label')->where('status_id', '=', 1)->orderBy('name')->get(),
                    'origens' => Origem::select('id as value', 'name as label')->where("status_id","=",1)->orderBy('name')->get(),
                    'funnelSteps' => FunnelStep::select('id as value', 'name as label', 'color')->where("status_id","=",1)->orderBy('id')->get(),
                    'channels' => Channel::select('id as value', 'name as label')->where("status_id","=",1)->orderBy('name')->get(),
                    'status' => Status::select('id as value', 'name as label')->whereIn("id",[10,7,8,9,16])->orderBy('name')->get(),
                    'proposalTypes' => ProposalType::orderBy('id')->get(), 
                    'role' => $user->role_id,                 
                ]
            );
        }
    }

    public function filterLeadsByRole($leads, $request){ 
        $user = Auth::user();
        $company_id = getCompany($user);

        if($user->role_id === 1 || $user->role_id === 3){
            if ($request->has('companies')) {
                $leads->whereIn('leads.company_id', $request->companies);                    
            }else{
                $leads->where('leads.company_id', '=', $company_id);
            }
            
            if ($request->has('user')) {
                $leads->whereIn('leads.user_id', $request->user);
            }
        }elseif($user->role_id === 4){
            
            if ($request->has('user')) {
                $leads->whereIn('leads.user_id', $request->user);
            }else{
                $list = User::select('id')->where('manager_id', $user->id)->get(); 
                $leads->whereIn('leads.user_id', $list);
            }
        }elseif($user->role_id === 2){
            $leads->where('leads.user_id','=', $user->id);
        }

        return $leads;
    }
    
    public function filter($request){
       
        $user = Auth::user();
        $company_id = getCompany($user);

        $leads = Lead::join('users', 'user_id', '=', 'users.id')
                    ->leftJoin('products', 'product_id', '=', 'products.id')
                    ->join('status', 'leads.status_id', '=', 'status.id')
                    ->leftJoin('origems', 'leads.origem_id', '=', 'origems.id')
                    ->leftJoin('channels', 'leads.canal_id', '=', 'channels.id') 
                    ->join('funnel_steps', 'leads.funnel_step_id', '=', 'funnel_steps.id')    
                    ->select('leads.id', 'leads.name', 'leads.celular','leads.email', 'users.name as usuario', 'products.name as product', 'leads.favorite', 'leads.status_id', 'leads.created_at', 'leads.new_lead', 'status.name as status', 'leads.status_id', 'schedule_date', 'interaction_date','origems.name as origem', 'channels.name as channel', 'leads.funnel_step_id', 'funnel_steps.name as funnel_step', 'funnel_steps.color as funnel_step_color', 'bot_stage','forward');

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

        $leads = $this->filterLeadsByRole($leads, $request);
        
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

    public function getLeads(Request $request){
        $leads = $this->filter($request);
        return response()->json($leads->orderBy('created_at')->paginate(15));
    }

    public function getWaiting(Request $request){
        
        $leads = $this->filter($request);
        $leads->where('leads.status_id','=', 9);
        $leads->orderby('leads.created_at','Desc');

        return response()->json($leads->paginate(15));
    }

    public function getSchedule(Request $request){
        
        $leads = $this->filter($request);
        
        $leads->where('leads.status_id','=', 7);
        $leads->whereBetween('leads.schedule_date',[date('Y-m-d 00:00:00',strtotime(now()->startOfDay())), date('Y-m-d 23:59:59',strtotime(now()->endOfDay()))]);
        $leads->orderby('leads.schedule_date', 'ASC');        

        return response()->json($leads->paginate(10));
    }

    public function getPendding(Request $request){
        
        $leads = $this->filter($request);
        $leads->where('leads.status_id','=', 10);
        $leads->orderby('leads.created_at', 'ASC');        

        return response()->json($leads->paginate(10));
    }

    public function getFavorite(Request $request)
    {        
        $leads = $this->filter($request);
        $leads->where('leads.favorite','=', 1);        
        $leads->orderby('leads.created_at', 'ASC');        

        return response()->json($leads->paginate(15));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $company_id = getCompany($user);
        

        $users = User::where("company_id","=", $company_id)
                       ->where("status_id","=", 1) 
                       ->orderBy('id')->get();
        
        $canais = Channel::where("status_id","=",1)
                    ->orderBy('name')->get();
        
        $origens = Origem::where("status_id","=",1)
                    ->orderBy('name')->get();
        
        $products = Product::where("status_id","=",1)
                    ->orderBy('name')->get();
        
        return Inertia::render(
            'Lead/Create',
            [
                'users' => $users,
                'user'=> $user->id,
                'canais' => $canais,
                'origens' => $origens,
                'products' => $products
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $company_id = getCompany($user);
                
        $request->validate([
            'name' => 'required|string|max:255',
            'celular' => 'required|string|max:16',
            'product' => 'required',
            'user' => 'required',
            'canal' => 'required',
            'origem' => 'required'
        ]);

        $tel = preg_replace('/^(\+)|\D/','',$request->telefone);
        $cel = preg_replace('/^(\+)|\D/','',$request->celular);

        $lead = new Lead();
        $lead->name =ucwords(strtolower($request->name));
        $lead->email =$request->email;
        $lead->telefone = $tel;
        $lead->celular = $cel;
        $lead->product_id =$request->product;
        $lead->mensagem =$request->mensagem;
        $lead->user_id =$request->user;
        $lead->canal_id =$request->canal;
        $lead->origem_id =$request->origem;
        $lead->company_id = $company_id;
        $lead->new_lead = 1;
        $lead->status_id = 9; //STATUS = AGUARDANDO 
        $lead->save();
        sleep(1);

        $log = new LeadLog();
        $log->action = "🔔 Cadatrado";
        $log->description = "Um novo lead foi cadastro";
        $log->lead_id = $lead->id;
        $log->status_id = 1;
        $log->user_id = $user->id;
        $log->save();
        sleep(1);

        if(session('lead_url')){
            return redirect(session('lead_url'))->with('message', 'Lead criado com sucesso');
        }

        return redirect()->route('lead.index')->with('message', 'Lead criado com sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function view(Lead $lead)
    {
        $user = Auth::user();
        $company_id = getCompany($user);

        //VERIFICA SE O LEAD PERTENCE AO USER 
        if($user->role_id == 2){
            $l = Lead::find($lead->id);
            
            if($l->user_id != $user->id){
                return redirect()->route('lead.index');
            }
        }

        $activities = LeadLog::join('users', 'user_id', '=', 'users.id')
                        ->select('action','lead_logs.created_at','description','name as user_name')
                        ->where("lead_id","=",$lead->id)
                        ->orderBy("lead_logs.created_at","desc")
                        ->get();
        
        $messages = LeadMessage::leftjoin('users', 'user_id', '=', 'users.id')
                        ->select('message','lead_messages.created_at','name as user_name', 'lead_id','bot','origen', 'channel_msg')
                        ->where("lead_id","=",$lead->id)
                        ->orderBy("lead_messages.created_at","asc")
                        ->get();

        $notes = LeadNotes::join('users', 'user_id', '=', 'users.id')
                        ->select('lead_notes.id','description','lead_notes.created_at','name as user_name', 'lead_notes.lead_id')
                        ->where("lead_id","=",$lead->id)
                        ->orderBy("lead_notes.created_at","desc")
                        ->get();

        $schedules = LeadSchedule::join('users', 'user_id', '=', 'users.id')
                        ->select('lead_schedules.id','schedule_date','description','lead_schedules.created_at','name as user_name', 'lead_schedules.lead_id')
                        ->where("lead_id","=",$lead->id)
                        ->orderBy("lead_schedules.created_at","desc")
                        ->get();
        
        $leads = Lead::leftjoin('form_campaigns','leads.form_id','form_campaigns.facebook_form_id')
                       ->leftJoin('origems', 'leads.origem_id', '=', 'origems.id')
                       ->leftJoin('channels', 'leads.canal_id', '=', 'channels.id') 
                       ->select('leads.id','leads.name','celular','leads.email','stars','form_campaigns.name as form_name', 'leads.created_at','origems.name as origem', 'channels.name as channel', 'funnel_step_id', 'user_id', 'leads.status_id','leads.favorite') 
                       ->where('leads.id', '=', $lead->id)->get();

        $readyMsg = ReadyMessage::join('users', 'user_id', '=', 'users.id')
                                ->select('ready_messages.id', 'ready_messages.title', 'message', 'users.name') 
                                ->where('ready_messages.status_id','=' , 1)
                                ->where('user_id','=' , $user->id)
                                ->orderBy('title')->get();

        if($readyMsg->count() == 0){
            $ready = new ReadyMessagesSeeder();
            $ready->run();
        }

        $funnelSteps = FunnelStep::where('status_id','=',1)->orderBy('order')->get();

        $users = [];
        
        if($user->role_id === 1 || $user->role_id === 3){
            $users = User::whereNotIn('status_id',[3])->get();
        }elseif($user->role_id === 4){
            $list = User::select('id')->where('manager_id', $user->id)->get(); 
            $users = User::whereIn("id",$list)
                ->orderBy('name')
                ->get();
        }
        
        return Inertia::render(
            'Lead/View',
            [
                'leads' => $leads,
                'leadLogs' => $activities,
                'leadMessages' => $messages,
                'leadNotes' => $notes,
                'leadSchedules' => $schedules,
                'readyMsgs' => $readyMsg,
                'funnelSteps'=> $funnelSteps,
                'products' => Product::select('id as value', 'name as label')->where("status_id","=",1)->orderBy('id')->get(),
                'reasons' => ArchiveReason::select('id', 'name')->orderBy('name')->get(),
                'proposalTypes' => ProposalType::orderBy('id')->get(),
                'users'=>$users,
                'role'=>$user->role_id,
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lead $lead)
    {
        $user = Auth::user();

        return Inertia::render(
            'Lead/Edit',
            [
                'lead' => $lead,
                'users' => User::where("company_id","=", $user->company_id)->where("status_id","=", 1)->orderBy('id')->get(),
                'canais' => Channel::where("status_id","=",1)->orderBy('name')->get(),
                'origens' => Origem::where("status_id","=",1)->orderBy('name')->get(),
                'products' => Product::where("status_id","=",1)->orderBy('name')->get()
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lead $lead)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'celular' => 'required|string|max:16',
            'product' => 'required',
            'user' => 'required',
            'canal' => 'required',
            'origem' => 'required'
        ]);

        $tel = preg_replace('/^(\+)|\D/','',$request->telefone);
        $cel = preg_replace('/^(\+)|\D/','',$request->celular);
        
        $lead->name = ucwords(strtolower($request->name));
        $lead->email =$request->email;
        $lead->telefone = $tel;
        $lead->celular = $cel;
        $lead->product_id =$request->product;
        $lead->mensagem =$request->mensagem;
        $lead->user_id =$request->user;
        $lead->canal_id =$request->canal;
        $lead->origem_id =$request->origem;
        $lead->save();
        sleep(1);

        $log = new LeadLog();
        $log->action = "📝Editado";
        $log->description = "O lead foi editado";
        $log->lead_id = $lead->id;
        $log->status_id = 1;
        $log->user_id = $user->id;
        $log->save();
        sleep(1);

        if(session('lead_url')){
            return redirect(session('lead_url'))->with('message', 'Lead atualizado com sucesso');
        }

        return redirect()->route('lead.index')->with('message', 'Lead atualizado com sucesso');
    }

    /**
     * Update the specified resource in storage.
     */
    public function setFavorite(Request $request, Lead $lead)
    {
        
        $user = Auth::user();
        $lead->favorite = $request->favorite;
        $lead->save();
        sleep(1);

        return redirect()->back()->with('message', 'Lead favoritado com sucesso');
    }

    /**
     * Update the specified resource in storage.
     */
    public function setStars(Request $request, Lead $lead)
    {
        $user = Auth::user();
        
        $lead->stars = $request->number;
        $lead->save();
        sleep(1);

        $log = new LeadLog();
        $log->action = "⭐ Classificado";
        $log->description = "O lead foi classificado com ".$request->number." estrelas";
        $log->lead_id = $lead->id;
        $log->status_id = 1;
        $log->user_id = $user->id;
        $log->save();
        sleep(1);

        return redirect()->back()->with('message', 'Resultado atualizado com sucesso');
    }



    /**
     * Update the specified resource in storage.
     */
    public function leadsForward(Request $request)
    {   
        $auth = Auth::user();
        
        $leads = Lead::whereBetween('created_at', [ date('Y-m-d 00:00:00',strtotime($request->date_forward[0])),date('Y-m-d 23:59:59',strtotime($request->date_forward[1]))])
                       ->whereNotIn('status_id',[3]);
        
        if($request->status)
            $leads->whereIn('status_id', (array) $request->status);

        $leads = $leads->whereIn('user_id', (array) $request->user_from)
                       ->get();
        
        $a = (array) $request->user_to;
        $count = count($a) - 1;
        
        $i = 0;
        foreach($leads as $lead){
            
            $user = User::find($a[$i]);
            
            $lead->user_id = $a[$i];
            $lead->company_id = $user->company_id;
            $lead->forward = 1;
            $lead->status_id = 9;
            $lead->save();
            
            $log = new LeadLog();
            $log->action = "↪ Encaminhado";
            $log->description = "O lead foi encaminhado para ".$user->name;
            $log->lead_id = $lead->id;
            $log->status_id = 1;
            $log->user_id = $auth->id;
            $log->save();
            
            if($i < $count)
                $i++;
            else
                $i = 0;
        }   
        
        if(count($leads) == 0)
            $msg = 'Nenhum lead encontrado';
        else
            $msg = count($leads).' lead(s) encaminhados com sucesso';

        return redirect()->back()->with('message', $msg);
    }


        /**
     * Update the specified resource in storage.
     */
    public function forward(Request $request, Lead $lead)
    {   
        $user = Auth::user();
        $user_forward = User::find($request->user_id);

        $lead->user_id = $request->user_id;
        $lead->company_id = $user_forward->company_id;
        $lead->forward = 1;
        $lead->status_id = 9;
        $lead->save();
        sleep(1);        

        $log = new LeadLog();
        $log->action = "↪ Encaminhado";
        $log->description = "O lead foi encaminhado para ".$user_forward->name;
        $log->lead_id = $request->lead_id;
        $log->status_id = 1;
        $log->user_id = $user->id;
        $log->save();
        sleep(1);

        Mail::to($user_forward->email)
                ->cc("webmaster@portobrasilconsorcios.com.br")
                ->send(new ForwardMail());

        return redirect()->back()->with('message', 'Lead encaminhado com sucesso');
    }

      /**
     * Update the specified resource in storage.
     */
    public function setFunnelStep(Request $request, Lead $lead)
    {
        $user = Auth::user();

        $lead = Lead::find($request->lead_id);
        $lead->funnel_step_id = $request->funnelStep;
        $lead->save();
        sleep(1);

        $funnelSteps = FunnelStep::find($request->funnelStep);

        $log = new LeadLog();
        $log->action = "🔔 Etapa de Funil";
        $log->description = "A etapa de funil do lead foi atualizada para ".$funnelSteps->name;
        $log->lead_id = $request->lead_id;
        $log->status_id = 1;
        $log->user_id = $user->id;
        $log->save();
        sleep(1);


        return redirect()->back()->with('message', 'Etapa atualizada com sucesso');
    }

    /**
     * Update the specified resource in storage.
     */
    public function setArchive(Request $request, Lead $lead)
    {
        $user = Auth::user();

        $request->validate([
            'reason' => 'required'
        ]);

        $reason = new LeadArchiveReason();
        $reason->reason = $request->reason;
        $reason->lead_id = $request->lead_id;
        $reason->save();
        sleep(1);
        
        $lead = Lead::find($request->lead_id);
        $lead->status_id = 8;
        $lead->save();
        sleep(1);

        $motivo = ArchiveReason::where('id','=', $request->reason)->get();
        
        $log = new LeadLog();
        $log->action = "🗄️ Arquivado";
        $log->description = "O lead foi arquivado (".$motivo[0]->name.")";
        $log->lead_id = $lead->id;
        $log->status_id = 1;
        $log->user_id = $user->id;
        $log->save();
        sleep(1);
        
        return redirect()->route('lead.index')->with('message', 'Lead arquivado com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lead $lead)
    {
        $lead->delete();
        sleep(1);

        return redirect()->route('lead.index')->with('message', 'Lead excluído com sucesso');
    }

    public function export(Request $request, Excel $excel)
    {   
        $user = Auth::user();        
        return $excel->download(new LeadsExport($request, $user),'leads.xlsx');
        
    }

    /**
     * return the number of new leads to show on menu.
    */
    public function getNewLeads(){
        
        $user = Auth::user();
        $company_id = getCompany($user);
        
        $leads = Lead::where('status_id','=', 9)
                    ->where('company_id','=',$company_id);

        if($user->role_id == 2){
            $leads->where('user_id','=',$user->id);
        }elseif($user->role_id == 4){
            $user = User::select('id')->where('manager_id', $user->id)->get(); 
            $leads->whereIn('user_id',$user);
        }
            
                
        return $leads->count();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeWebhookResponse($webhookResponseData)
    {
        $data = $webhookResponseData;        
        $l = json_decode($data['payload'], TRUE);
        $ad_id = $data['ad_id'];
        $phone_valid = true;
        
        $parsed = array();
        foreach ($l['field_data'] as $item) {
            if (!isset($parsed[$item['name']])) {
                $parsed[$item['name']] = $item['values'];
            } else {
                $parsed[$item['name']] = array_merge($parsed[$item['name']], $item['values']);
            }
        }

        $number = substr($parsed['phone_number'][0],3);

        if(strlen($number) == 11){
            $cel = $number;
        }elseif(strlen($number) == 9){
            $phone_valid = false;
            $cel = $number;
        }else{
            $phone_valid = false;
            $cel = $number;
        }

        
        //VERIFICAR SE O LEAD JA EXISTE EM NOSSA BASE DE DADOS
        $newDateTime = now()->subMinute(60);
        $lead = Lead::where('celular','LIKE', '%'.$cel.'%')
                    ->where('created_at','>', $newDateTime)  
                    ->count();    

        if($lead == 0){
            
            $lead = new Lead();
            $lead->name = ucwords(strtolower(preg_replace('/[^a-zA-Z]+/', ' ', $parsed['full_name'][0])));
            $lead->email =$parsed['email'][0];
            $lead->celular = $cel;
            $lead->origem_id = 2; //INSTAGRAM LEADS
            $lead->canal_id = 2; //REDE SOCIAL
            $lead->new_lead = 1; 
            $lead->ad_id = $ad_id; 
            $lead->bot_stage = 0; 
            $lead->form_id =$data['form_id'];
            $lead->leadgen_id =$data['leadgen_id'];
            $lead->status_id = 20; 
            $lead->save();
            sleep(1);

            $log = new LeadLog();
            $log->action = "🔔 Cadatrado";
            $log->description = "Um novo lead foi cadastro via Facebook Leads";
            $log->lead_id = $lead->id;
            $log->status_id = 1;
            $log->save();
            sleep(1);

            $note = new LeadNotes();
            $note->description = "informações  telefone: ".$parsed['phone_number'][0]." cidade: ".ucwords(strtolower($parsed['city'][0]));
            $note->lead_id = $lead->id;
            $note->user_id = 1;
            $note->status_id = 1; // INTERAGIDO
            $note->save();
            sleep(1);        

            $conversation = new ChatBotController();
            $bot = Bot::find(1);

            if($bot->status_id == 1){ //BOT ONLINE
                if($phone_valid){
                    $conversation->runBot($bot, $lead, $cel, "");
                    $status_id = 20;
                }else{
                    $status_id = 12;    
                }
            }else{
                $status_id = 12;
            }

            $lead->status_id = $status_id;
            $lead->save();
            sleep(1);
        }
    }
}
