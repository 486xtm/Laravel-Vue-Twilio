<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ChannelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $channels = Channel::when($request->term, function ($query) use ($request) {
            $query->where('name', 'LIKE', '%'.$request->term.'%')->paginate();
                        
        }, function ($query) {
            $query->orderBy('id')->paginate()->map(fn ($channel) => [
                'id' => $channel->id,
                'name' => $channel->name,
            ]);
        })->get();

         
        return Inertia::render(
            'Channel/Index',
            [
                'channels' => $channels
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render(
            'Channel/Create'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);
        
        Channel::create([
            'name' => $request->name
        ]);
        sleep(1);

        return redirect()->route('channel.index')->with('message', 'Canal Criado Com Sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(Channel $channel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Channel $channel)
    {
           return Inertia::render(
                'Channel/Edit', 
                [
                    'channel' => $channel
                ]
            );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Channel $channel)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $channel->name = $request->name;
        $channel->save();
        sleep(1);

        return redirect()->route('channel.index')->with('message', 'Canal atualizado com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Channel $channel)
    {
        //
    }
}
