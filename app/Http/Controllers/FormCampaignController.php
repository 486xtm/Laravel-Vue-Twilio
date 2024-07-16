<?php

namespace App\Http\Controllers;

use App\Models\FormCampaign;
use App\Models\Campaign;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;


class FormCampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $forms = FormCampaign::leftJoin('products', 'product_id', '=', 'products.id')
                               ->join('campaigns', 'campaign_id', '=', 'campaigns.id')
                               ->select('form_campaigns.id','form_campaigns.name', 'products.name as product_name', 'form_campaigns.facebook_form_id', 'campaigns.name as campaign_name')
                               ->orderBy('form_campaigns.id');

        $forms->when($request->term, function ($query) use ($request) {
            $query->where('form_campaigns.name', 'LIKE', '%'.$request->term.'%')
            ->where('form_campaigns.status_id','=', 1);
                        
        }, function ($query) {
            $query->where('form_campaigns.status_id','=', 1);
        }); 

        $filter = array(
            'term'=> $request->only(['term']),
        );

        
        return Inertia::render(
            'FormCampaign/Index',
            [
                'forms' => $forms->paginate(15)->withQueryString(),
                'filters' => $filter,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $campaigns = Campaign::where('status_id','=',1)->orderBy('name')->get();
        $products = Product::where('status_id','=',1)->orderBy('name')->get();
        
        return Inertia::render(
            'FormCampaign/Create',
            [
                'campaigns' => $campaigns,
                'products' => $products,
            ]

        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1024',
            'facebook_form_id' => 'required|string|max:64',
            'campaign_id' => 'required',
            'product_id' => 'required',
        ]);
        
        FormCampaign::create([
            'name' => $request->name,
            'description' => $request->description,
            'facebook_form_id' => $request->facebook_form_id,
            'campaign_id' => $request->campaign_id,
            'product_id' => $request->product_id,
            'status_id' => 1
        ]);
        sleep(1);

        return redirect()->route('formcampaign.index')->with('message', 'Formulário de campanha criado com sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(FormCampaign $formCampaign)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FormCampaign $form)
    {
        $campaigns = Campaign::where('status_id','=',1)->orderBy('name')->get();
        $products = Product::where('status_id','=',1)->orderBy('name')->get();
        
        return Inertia::render(
            'FormCampaign/Edit',
            [
                'form' => $form,
                'campaigns' => $campaigns,
                'products' => $products,
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FormCampaign $form)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1024',
            'facebook_form_id' => 'required|numeric',
            'campaign_id' => 'required',    
            'product_id' => 'required',       
        ]);
       
        $form->name =$request->name;
        $form->description =$request->description;       
        $form->facebook_form_id =$request->facebook_form_id;       
        $form->campaign_id =$request->campaign_id;       
        $form->product_id =$request->product_id;       
        $form->save();
        sleep(1);

        return redirect()->route('formcampaign.index')->with('message', 'Formulário atualizado com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FormCampaign $form)
    {
        $form->status_id = 3;
        $form->save();
        sleep(1);

        return redirect()->back()->with('message', 'Formulário excluído com sucesso');
    }
}
