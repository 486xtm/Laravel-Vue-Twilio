<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use App\Models\BotWorkFlow;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BotController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        $bots = Bot::whereNotIn('status_id',[3])->orderBy('created_at')->get();
        
        return Inertia::render(
            'Bot/Index',
            [
                'bots' => $bots
            ]
        );
    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bot $bot)
    {   
        $workflows = [];
        
        $parents = BotWorkFlow::where('bot_id','=',$bot->id)
                                ->where('parent','=', null)
                                ->where('status_id','=', 1)
                                ->orderBy('stage')                                
                                ->get();

        foreach($parents as $parent){

            $children = BotWorkFlow::where('parent','=',$parent->id)            
                                    ->where('status_id','=', 1)
                                    ->orderBy('id')                                
                                    ->get();
           
            $workflows[] = [ 'id' => $parent->id, 
                             'name' => $parent->name, 
                             'stage' => $parent->stage, 
                             'message' => $parent->message, 
                             'parent' => $parent->parent, 
                             'type' => $parent->type, 
                             'conditional' => $parent->conditional, 
                             'status_id' => $parent->status_id, 
                             'children' => $children];
        }
        
        return Inertia::render(
            'Bot/Edit',
            [
                'bot' => $bot,
                'workflows' => $workflows,
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bot $bot)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'token' => 'required|string|max:255',
            'sid' => 'required|string|max:255'
        ]);

        $bot->name = $request->name;
        $bot->phone = $request->phone;
        $bot->token = $request->token;
        $bot->sid = $request->sid;
        $bot->welcome_msg = $request->welcome_msg;
        $bot->options_msg = $request->option_msg;
        $bot->default_msg = $request->default_msg;
        $bot->invalid_answer = $request->invalid_answer;
        $bot->save();
        sleep(1);

        return redirect()->route('bot.index')->with('message', 'Bot atualizado com sucesso');
    }

          /**
     * Update the specified resource in storage.
     */
    public function setStatus(Request $request, Bot $bot)
    {
        $sts = 0;
        $bot = Bot::find($request->id);

        if($bot->status_id == 1){
            $sts = 2;
            $msg = 'Bot desativado com sucesso';
        }elseif($bot->status_id == 2){
            $msg = 'Bot ativado com sucesso';
            $sts = 1;
        }
           

        $bot->status_id = $sts;
        $bot->save();
        sleep(1);

        return redirect()->back()->with('message', $msg);
    }
}

