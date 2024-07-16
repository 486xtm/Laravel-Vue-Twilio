<?php

namespace App\Http\Controllers;

use App\Models\ReadyMessage;
use Illuminate\Http\Request;
use Database\Seeders\ReadyMessagesSeeder;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Auth;

class ReadyMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {   
        $user = Auth::user();
        $message = ReadyMessage::where('user_id', '=', $user->id);

        $message->when($request->term, function ($query) use ($request, $user) {
            $query->where('title', 'LIKE', '%'.$request->term.'%')
                  ->orderBy('id');
                        
        }, function ($query) use ($user) {
            $query->orderBy('id');
        });

        if($message->count() == 0){
            $ready = new ReadyMessagesSeeder();
            $ready->run();
        }

        $filter = array(
            'term'=> $request->only(['term']),          
        );

        return Inertia::render(
            'Message/Index',
            [
                'messages' => $message->paginate(15)->withQueryString(),
                'filters' => $filter,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render( 
            'Message/Create'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required'
        ]);
        
        $message = new ReadyMessage();
        $message->title =$request->title;
        $message->message =$request->message;
        $message->user_id = $user->id; 
        $message->status_id = 1;
        $message->save();
        sleep(1);

        return redirect()->route('message.index')->with('message', 'Messagem Criada Com Sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(ReadyMessage $readyMessage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReadyMessage $message)
    {
        return Inertia::render(
            'Message/Edit',
            [
                'message' => $message
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReadyMessage $message)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required'
        ]);
        
        $message->title =$request->title;
        $message->message =$request->message;
        $message->status_id = 1;
        $message->save();
        sleep(1);

        return redirect()->route('message.index')->with('message', 'Menssagem atualizada com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReadyMessage $readyMessage)
    {
        //
    }
}
