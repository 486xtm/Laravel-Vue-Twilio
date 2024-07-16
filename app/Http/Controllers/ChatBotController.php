<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use App\Models\Lead;
use App\Models\Bot;
use App\Models\BotWorkFlow;
use App\Models\LeadMessage;
use App\Models\Message;

class ChatBotController extends Controller
{
    private $conversation;

    function __construct() {
        $this->conversation = new ConversationController();
    }

    public function listenToReplies(Request $request)
    {
        $bot = Bot::find(1);
        $from = substr($request->input('From'),-11);
        $body = $request->input('Body');

        //logger($request);

        $conversation_id = $this->conversation->reciveMessage($request);

        if($bot->status_id == 1){ //BOT ONLINE
            
            $lead = Lead::select('id','name','bot_stage')
                        ->where('celular','=', $from)
                        ->whereIn('status_id', [9,20])
                        ->get();
            
            if($lead->count() > 0){
                $this->runBot($bot, $lead[0], $from, $body);
            }

            /*
            if($lead->count() > 0){

                if($lead[0]->bot_stage == 0){
                    $this->startBot($bot, $lead[0]->id, $lead[0]->name, $from);
                }elseif($lead[0]->bot_stage == 1){
                    $this->saveMsg($lead[0]->id, $body, 2);
                    $this->secondBot($bot, $lead[0]->id, $body, $from);

                }elseif($lead[0]->bot_stage == 2){
                    $this->saveMsg($lead[0]->id, $body, 2);
                    $this->thirdBot($lead[0]->id, $body, $from);
                
                }elseif($lead[0]->bot_stage == 3){
                    $this->saveMsg($lead[0]->id, $body, 2);
                    $this->forthBot($lead[0]->id, $body, $from);
                }
            }else{
                
                $lead = Lead::select('id','name')
                        ->where('celular','=', $from)
                        ->where('status_id', '<>', 3)
                        ->get();

                if($lead->count() > 0){

                    $name = $lead[0]->name;
                    $default_msg = $bot[0]->default_msg;
                    $msg = str_replace('[nome]',$name,$default_msg);

                    $this->conversation->sendMessage($msg, $from);
                }
            }*/
        }
        
        //return;
    }

    public function runBot($bot, $lead, $from, $body){

        $work_flow = BotWorkFlow::where('stage','=',$lead->bot_stage)->get();
        //logger($lead);
        $message = "";
        $stage = $lead->bot_stage + 1;

        if($work_flow->count() == 1){
            
            $message = $work_flow[0]->message;
            $message = $this->textReplace($message, $lead);
        }else{
            $work_flow = BotWorkFlow::where('stage','=',$lead->bot_stage)
                                    ->where('conditional','like', '%'.$body.'%')
                                    ->get();
            
            if($work_flow->count() > 0){
                $message = $work_flow[0]->message;
                $message = $this->textReplace($message, $lead);
            }
        }       

        if($lead->bot_stage == 0)
            $this->conversation->startMessage($message, $from, $lead->name);
        else{
            $this->saveMsg($lead->id, $body, 2);
            $this->conversation->sendMessage($message, $from);
        }
                    
        $this->saveMsg($lead->id, $message, 1);
        $this->saveStage($lead->id, $stage);
    }

    public function textReplace($text, $lead){

        $time = now()->hour;
        $saudacao = "";

        if($time < 12)
            $saudacao = "Bom dia";
        elseif($time > 12)
            $saudacao = "Boa tarde";
        elseif($time > 18)
            $saudacao = "Boa noite";

        $name = explode(" ", $lead->name);
        
        $text = str_replace('[nome]',$name[0], $text);
        $text = str_replace('[saudacao]',$saudacao,$text);
            
        return $text;
    }    

    public function startBot($bot,$lead_id, $name, $from)
    {
        $time = now()->hour;
        $saudacao = "";

        if($time < 12)
            $saudacao = "Bom dia";
        elseif($time > 12)
            $saudacao = "Boa tarde";
        elseif($time > 18)
            $saudacao = "Boa noite";
        
        $welcome_msg = $bot[0]->welcome_msg;    
        $welcome_msg = str_replace('[nome]',$name,$welcome_msg);
        $welcome_msg = str_replace('[saudacao]',$saudacao,$welcome_msg);

        $this->saveMsg($lead_id, $welcome_msg, 1);
        $this->saveStage($lead_id, 1);
        $this->conversation->sendMessage($welcome_msg, $from);
    }

    /**
    *SEGUNDO ESTAGIO DO BOT 
    */
    public function secondBot($bot,$lead_id, $answer, $from)
    {
        $this->saveMsg($lead_id,$bot[0]->options_msg, 1);
        $this->saveStage($lead_id, 2);
        $this->conversation->sendMessage($bot[0]->options_msg, $from);
    }

    /**
    *SEGUNDO ESTAGIO DO BOT 
    */
    public function thirdBot($lead_id, $answer, $from)
    {
        $answer = strtolower($answer);
        
        if(preg_match('/\b(?:imovel|imóvel|1)\b/', $answer)){

            $message = "Legal! Vamos trabalhar para te ajudar a realizar seu sonho.
        
Qual faixa de valores esta buscando?

1) Até 200.000,00
2) R$ 200.000,00 até 600.000,00
3) Acima de R$600.000,00";

            $bot_stage = 3;

        }elseif(preg_match('/\b(?:auto|automovel|automóvel|2)\b/', $answer)){
            $message = "Bacana! Conte conosco para adiquirir seu automóvel novo.
        
Qual faixa de valores você precisa?

1) Até 50.000,00
2) R$ 50.000,00 até 100.000,00
2) R$ 100.000,00 até 150.000,00
3) Acima de R$150.000,00";

            $bot_stage = 3;

        }elseif(preg_match('/\b(?:caminhao|caminhão|caminhões|caminhoes|onibus|ônibus|pesados|pesado|3)\b/', $answer)){
            $message = "Ótimo! Vou te ajudar com aquisição da sua carta de Pesados.
        
Qual faixa de valores você precisa?

1) Até 150.000,00
2) R$ 150.000,00 até 250.000,00
3) Acima de R$250.000,00";

            $bot_stage = 3;

        }elseif(preg_match('/\b(?:moto|motos|motocicleta|4)\b/', $answer)){
            $message = "Bacana! Conte conosco para adiquirir sua moto nova.
        
Qual faixa de valores você precisa?

1) Até 25.000,00
2) R$ 25.000,00 até 50.000,00
3) Acima de R$50.000,00";

            $bot_stage = 3;

        }elseif(preg_match('/\b(?:barco|embarcação|embarcacao|embarcações|embarcacoes|jet sky|jet|jetsky|5)\b/', $answer)){
            $message = "Ótimo! Conte conosco para adiquirir sua embarcação nova.
        
Qual faixa de valores você precisa?

1) Até 100.000,00
2) R$ 100.000,00 até 200.000,00
2) R$ 200.000,00 até 300.000,00
3) Acima de R$300.000,00";

            $bot_stage = 3;

        }else{
            $message = "Ops! Não identifiquei sua resposta. Vamos tentar novamente?

Seu interesse seria por cartas de crédito de:

1) Imóvel 🏠
2) Automóvel 🚙
3) Veículos Pesados 🚛
4) Motos 🏍️
5) Embarcações e Jet Sky 🛥️";
            $bot_stage = 2;
        }

        $this->saveStage($lead_id, $bot_stage);
        $this->saveMsg($lead_id, $message, 1);
        $this->conversation->sendMessage($message, $from);
    }

    /**
    *TERCEIRO ESTAGIO DO BOT 
    */
    public function forthBot($lead_id, $answer,$from)
    {
        if(preg_match('/\b(1|2|3|4)\b/', $answer)){

            $message = "Pode deixar 🙌 Agora que tenho as informações de que você procura, vou direcionar o seu atendimento ao nosso especialista de cartas de crédito.

Só aguardar o contato do especialista da Porto Brasil Consórcios, seja muito bem vindo 😉";

            $bot_stage = 0;
        }else{
            
            $message = "Ops! Não identifiquei sua resposta. Vamos tentar novamente?";
            $bot_stage = 2;
        }

        $this->saveStage($lead_id, $bot_stage);
        $this->saveMsg($lead_id, $message, 1);
        $this->conversation->sendMessage($message, $from);
    }

    /**
     * Save leads msg 
     */
    public function saveMsg($lead_id, $msg, $origen){        
        
        $message = new LeadMessage();
        $message->message = $msg;
        $message->lead_id = $lead_id;
        $message->bot = 1;
        $message->origen = $origen;
        $message->status_id = 1;
        $message->channel_msg = 'whatsapp';
        $message->save();
        sleep(1);    
    }

    /**
     * Save leads stage bot
     */
    public function saveStage($lead_id, $stage){
        $lead = Lead::find($lead_id);
        $lead->bot_stage = $stage;
        $lead->save();
        sleep(1);
    }


    public function updateStatus(){
        
        $newDateTime = now()->subMinute(10);
        $leads = Lead::where('status_id','=',20)->where('created_at','<=', $newDateTime)->get();        
        
        foreach ($leads as $lead) {
            $lead->status_id = 12;
            $lead->save();
            sleep(1);
        }
    }

    
    public function listenToRepliesTo(Request $request)
    {
        $from = $request->input('From');
        $body = $request->input('Body');

        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->request('GET', "https://api.github.com/users/$body");
            $githubResponse = json_decode($response->getBody());

            if ($response->getStatusCode() == 200) {
                $message = "*Name:* $githubResponse->name\n";
                $message .= "*Bio:* $githubResponse->bio\n";
                $message .= "*Lives in:* $githubResponse->location\n";
                $message .= "*Number of Repos:* $githubResponse->public_repos\n";
                $message .= "*Followers:* $githubResponse->followers devs\n";
                $message .= "*Following:* $githubResponse->following devs\n";
                $message .= "*URL:* $githubResponse->html_url\n";
                $this->sendWhatsAppMessage($message, $from);
            } else {
                $this->sendWhatsAppMessage($githubResponse->message, $from);
            }
        } catch (RequestException $th) {
            $response = json_decode($th->getResponse()->getBody());
            $this->sendWhatsAppMessage($response->message, $from);
        }
        return;
    }
    
}