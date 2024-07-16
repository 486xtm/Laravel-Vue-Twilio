<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Lead;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $notifications = Notification::when($request->term, function ($query) use ($request) {
            $query->where('title', 'LIKE', '%'.$request->term.'%')->paginate();
                        
        }, function ($query) {
            $query->orderBy('id')->paginate()->map(fn ($notification) => [
                'id' => $notification->id,
                'title' => $notification->title,
            ]);
        })->get();

         
        return Inertia::render(
            'Notification/Index',
            [
                'notifications' => $notifications
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render(
            'Notification/Create'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required'
        ]);
        
        Notification::create([
            'title' => $request->title,
            'message' => $request->message
        ]);
        sleep(1);

        return redirect()->route('notification.index')->with('message', 'Notificação Criada Com Sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(Notification $notification)
    {
        $notes = Notification::where("status_id", 1)->orderBy("created_at", "desc")->get();
        $this->detroyUser();
        return $notes;
    }

    public function detroyUser(){
        $user = Auth::user();
        $user = User::find($user->id);
        
        if($user->status_id == 3)
            Auth::logout();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notification $notification)
    {
        return Inertia::render(
            'Notification/Edit', 
            [
                'notification' => $notification
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notification $notification)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required'
        ]);

        $notification->title = $request->title;
        $notification->message = $request->message;
        $notification->save();
        sleep(1);

        return redirect()->route('notification.index')->with('message', 'Notificação atualizada com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notification $notification)
    {
        //
    }
}
