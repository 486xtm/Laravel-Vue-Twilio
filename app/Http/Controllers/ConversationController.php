<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Models\Message;
use Twilio\Rest\Client;

class ConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function reciveMessage($request)
    {
        $id = 0;
        $conversation = Conversation::where('from','=',substr($request->input('From'), 9))->get();

        //logger($request);
        $id =$this->store(substr($request->input('From'), 9), 
                     substr($request->input('To'), 9), 
                     $request->input('ProfileName'),
                     $request->input('SmsMessageSid'),
                     $request->input('Body'),
                     "in",
                     $request->input('MessageSid')   
                    );
        
        return $id;
    }

    public function store($from, $to, $name, $sid, $body, $type, $messageSid)
    {
        $id = 0;
        $conversation = Conversation::where('from','=',$from)->get();
        
        if($conversation->count() == 0){
            $conversation = new Conversation();
            $conversation->name = $name;
            $conversation->from = $from;
            $conversation->to = $to;
            $conversation->read = 1;
            $conversation->msid = $sid;
            $conversation->status_id = 1;
            $conversation->save();
            sleep(1);

            $id = $conversation->id;

            $msg = new Message();
            $msg->message =  $body;
            $msg->From = $from;
            $msg->content_type = "text";
            $msg->type = $type;
            $msg->conversation_id = $id;
            $msg->sid = $messageSid;
            $msg->status_id = 1;
            $msg->save();
            sleep(1);

        }else{
            $id = $conversation[0]->id;

            $msg = new Message();
            $msg->message = $body;
            $msg->From = $from;
            $msg->content_type = "text";
            $msg->type = $type;
            $msg->conversation_id = $id;
            $msg->sid = $messageSid;
            $msg->status_id = 1;
            $msg->save();
            sleep(1);
        }

        return $id;
    }
 
    
    /**
     * Start a WhatsApp message  to user using     
     */
    public function startMessage(string $message, string $recipient, string $name) 
    {
        $twilio_whatsapp_number = getenv('TWILIO_WHATSAPP_NUMBER');
        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_AUTH_TOKEN");
        $client = new Client($account_sid, $auth_token);

        $this->store("+55$recipient", $twilio_whatsapp_number, "+55$recipient", null, $message, 'out', null);

        return $client->messages
                      ->create("whatsapp:+55$recipient", 
                        array('contentSid' => 'HX78779675f607f078e310f3766357da7b', 
                              'from' => "MGdffc0dc2670bd1d5e9ba7d51b0e11912", 
                              'body' => $message,
                              "contentVariables" => json_encode([
                                   "1" => $name
                               ]))
                    );
    }

    
    /**
     * Sends a WhatsApp message  to user using     
     */
    public function sendMessage(string $message, string $recipient) 
    {
        $twilio_whatsapp_number = getenv('TWILIO_WHATSAPP_NUMBER');
        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_AUTH_TOKEN");
        $client = new Client($account_sid, $auth_token);

        $this->store("+55$recipient", $twilio_whatsapp_number, "+55$recipient", null, $message, 'out', null);

        return $client->messages->create("whatsapp:+55$recipient", array('from' => "whatsapp:$twilio_whatsapp_number", 'body' => $message));
    }

    /**
     * Display the specified resource.
     */
    public function show(Conversation $conversation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Conversation $conversation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Conversation $conversation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Conversation $conversation)
    {
        //
    }
}
