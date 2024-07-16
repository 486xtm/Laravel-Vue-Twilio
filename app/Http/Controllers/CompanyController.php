<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\Cidade;
use App\Models\Estado;
use App\Models\Campaign;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Auth;

class CompanyController extends Controller
{
    public function index(Request $request)
    {   
        $auth = Auth::user();
        
        if(($auth->role_id != 1)){
            return redirect()->route('dashboard');    
        }

        Session::put('company_url', $request->fullUrl());
        
        $companies = Company::join('status','companies.status_id','status.id')
                            ->leftJoin('campaigns','campaign_id','campaigns.id')
                            ->select('companies.id','companies.name','document','accountable','companies.status_id', 'status.name as status_name', 'campaigns.name as campaign_name')
                            ->whereNotIn('companies.status_id',[3]);
                                

        $companies->when($request->term, function ($query) use ($request) {
            $query->where('companies.name', 'LIKE', '%'.$request->term.'%');
        });

        $filter = array(
            'term'=> $request->only(['term']),
        );
        
        return Inertia::render(
            'Company/Index',
            [
                'companies' => $companies->orderBy('companies.id')->paginate(15)->withQueryString(),
                'filters' => $filter,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $citys = Cidade::orderBy('nome')->get();
        $states = Estado::orderBy('nome')->get();
        $campaigns = Campaign::where('status_id','=',1)->orderBy('name')->get();
      
        return Inertia::render(
            'Company/Create',
            [
                'citys' => $citys,
                'states' => $states,
                'campaigns' => $campaigns,
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Cache::forget('companies');

        $request->validate([
            'name' => 'required|string|max:255',
            'document' => 'string|max:20',
            'address' => 'string|max:255',
            'number' => 'string|max:16',
            'region' => 'string|max:128',
            'zip' => 'string|max:16',
            'city' => 'required',
            'state' => 'required',
            'accountable' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'phone' => 'string|max:16'
        ]);
    
        $company = new Company();
        $company->name =$request->name;
        $company->document =$request->document;
        $company->address =$request->address;
        $company->number =$request->number;
        $company->complement =$request->complement;
        $company->region =$request->region;
        $company->zip =$request->zip;
        $company->city_id =$request->city;
        $company->state_id =$request->state;        
        $company->accountable =$request->accountable;
        $company->email =$request->email;
        $company->phone =$request->phone;
        $company->campaign_id = $request->campaign;
        $company->status_id = 1;
        $company->save();
        sleep(1);
        
        if(session('company_url')){
            return redirect(session('company_url'))->with('message', 'Empresa criada com sucesso');
        }

        return redirect()->route('company.index')->with('message', 'Empresa criada com sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        $citys = Cidade::orderBy('nome')->get();
        $states = Estado::orderBy('nome')->get();
        $campaigns = Campaign::where('status_id','=',1)->orderBy('name')->get();

        return Inertia::render(
            'Company/Edit',
            [
                'company' => $company,
                'citys' => $citys,
                'states' => $states,
                'campaigns' => $campaigns,
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'document' => 'string|max:20',
            'address' => 'string|max:255',
            'number' => 'string|max:16',
            'region' => 'string|max:128',
            'zip' => 'string|max:16',
            'city' => 'required',
            'state' => 'required',
            'accountable' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'phone' => 'string|max:16'
        ]);
       
        $company->name =$request->name;
        $company->document =$request->document;
        $company->address =$request->address;
        $company->number =$request->number;
        $company->complement =$request->complement;
        $company->region =$request->region;
        $company->zip =$request->zip;
        $company->city_id =$request->city;
        $company->state_id =$request->state;        
        $company->accountable =$request->accountable;
        $company->email =$request->email;
        $company->phone =$request->phone;
        $company->campaign_id = $request->campaign;
        $company->status_id = 1;
        $company->save();
        sleep(1);

        if(session('company_url')){
            return redirect(session('company_url'))->with('message', 'Empresa atualizada com sucesso');
        }

        return redirect()->route('company.index')->with('message', 'Empresa atualizada com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Company $company)
    {
        $company->status_id = $request->status; //DESATIVADO
        $company->save();
        sleep(1);

        $msg = 'Empresa ativada com sucesso';

        if( $request->status == 2)
            $msg = 'Empresa desativada com sucesso';
        else if( $request->status == 3)
            $msg = 'Empresa excluída com sucesso';
        
        return redirect()->back()->with('message', $msg);
    }
        /**
     * Display the specified resource.
     */
    public function getCompanies()
    {
        $companies = Cache::rememberForever('companies', function(){
            return Company::where('status_id','=',1)->whereNotIn('id',[2])->orderBy('name')->get();
        });
        
        return $companies;
    }
}
