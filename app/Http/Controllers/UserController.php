<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $auth = Auth::user();
        $company_id = getCompany($auth);

        if(($auth->role_id != 1)){
            return redirect()->route('dashboard');    
        }

        Session::put('user_url', $request->fullUrl());
       
        $users = User::leftjoin('companies','company_id','companies.id')
                    ->leftjoin('roles','role_id','roles.id') 
                    ->select('users.id','users.name','users.email','users.phone', 'companies.name as company', 'role_id','roles.name as role','users.status_id')
                    ->whereIn('users.status_id',[1,2]);                            
        
        $users->when($request->term, function ($query) use ($request) {
            $query->where('users.name', 'LIKE', '%'.$request->term.'%');
        }, function ($query) {
            $query->orderBy('users.id');
        });
    
        $filter = array(
            'term'=> $request->only(['term']),          
        );

        return Inertia::render(
            'User/Index',
            [
                'users' => $users->paginate(30)->withQueryString(),
                'filters' => $filter,
            ]
        );
    }

    public function setCompany(Request $request)
    {
        Session::put('user.company', $request->id);
    }

    public function getCompany(Request $request)
    {
        return Session::get('user.company');
    }

    public function getUsers(Request $request)
    {
        return User::select('id as value', 'name as label', 'company_id')->orderBy('name')->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return Inertia::render(
            'User/Create'
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $companies = Company::where('status_id', '=', 1)
                            ->orderBy('name')->get();

        $roles = Role::orderBy('name')->get();

        return Inertia::render(
            'User/Edit',
            [
                'user' => $user,
                'roles' => $roles,
                'managers' => User::where('role_id', '=', 4)->where('status_id', '=', 1)->orderBy('name')->get(),
                'companies' => $companies,
            ]
        );
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'phone' => 'required|string|max:16',
            'role' => 'required',
            'company' => 'required',
        ]);
     
        $role = Role::where('id', '=' ,$user->role_id)->select('name')->get();
        
        if(count($role) > 0)
            $user->removeRole($role[0]['name']);

        $role = Role::where('id', '=' ,$request->role)->select('name')->get();
        $user->assignRole($role[0]['name']);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->role_id = $request->role;

        if($request->role == 4)
            $user->manager_id = $user->id;
        elseif($request->role == 2)
            $user->manager_id = $request->manager;

        $user->company_id = $request->company;
        $user->save();
        sleep(1);
        


        if(session('user_url')){
            return redirect(session('user_url'))->with('message', 'Usuário atualizado com sucesso');
        }

        return redirect()->route('user.index')->with('message', 'Usuário atualizado com sucesso');
    }

     /**
     * Update the status 
     */
    public function pause(Request $request, User $user)
    {
        $user->status_id = 2;
        $user->save();
        sleep(1);

        if($request->route)
            return redirect()->route($request->route)->with('message', 'Usuário pausado com sucesso');
        else
            return redirect()->route('user.index')->with('message', 'Usuário pausado com sucesso');
    }

    /**
     * Update the status 
     */
    public function play(Request $request, User $user)
    {
        $user->status_id = 1;
        $user->save();
        sleep(1);
        
        if($request->route)
            return redirect()->route($request->route)->with('message', 'Usuário ativado com sucesso');
        else
            return redirect()->route('user.index')->with('message', 'Usuário ativado com sucesso');
    }

    /**
     * Update the status 
     */
    public function destroy(Request $request, User $user)
    {
        $user->status_id = 3;
        $user->save();
        sleep(1);

        return redirect()->route('user.index')->with('message', 'Usuário excluído com sucesso');
    }
}