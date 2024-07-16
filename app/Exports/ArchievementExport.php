<?php

namespace App\Exports;
use App\Models\Lead;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

use Maatwebsite\Excel\Concerns\FromCollection;

class ArchievementExport implements FromCollection
{
    use Exportable;
    
    public function __construct($request, $user)
    {
        $this->request = $request;
        $this->user = $user;
    }
    
    public function collection()
    {   
        $company_id = getCompany($this->user);
        $request = $this->request;

        if ($request->has('date')) {
            $dates = $request->date;
            $startDate = date('Y-m-d 00:00:00',strtotime($request->date[0]));
            $endDate = date('Y-m-d 23:59:59',strtotime($request->date[1]));
            $dates[] = [$startDate, $endDate];
        }else{
            $dates = null;            
        }

        $leads = Lead::join('users', 'user_id', '=', 'users.id')
                        ->leftJoin('origems', 'leads.origem_id', '=', 'origems.id')
                        ->leftJoin('channels', 'leads.canal_id', '=', 'channels.id') 
                        ->leftJoin('lead_archive_reasons', 'lead_archive_reasons.lead_id', '=', 'leads.id')                           
                        ->select('leads.id', 'leads.name', 'leads.celular', 'users.name as usuario',  'leads.created_at', 'origems.name as origem', 'channels.name as channel');

        if($request->date != null)
            $leads->whereBetween ('leads.created_at', [$startDate, $endDate]);        
        
        $leads->when($request->term, function ($query) use ($request) {
            $query->where('leads.name', 'LIKE', '%'.$request->term.'%');            
        });    
    
        if ($request->has('user')) {
            $leads->whereIn ('leads.user_id', $request->user);
        }    

        if ($request->has('reasons')) {
            $leads->whereIn ('lead_archive_reasons.reason', $request->reasons);
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

        $leads->where('leads.status_id','=', 8);               
        $leads->orderby('leads.created_at', 'DESC');        
        
        return $leads->get();
    }
    
    /**
    * @var Invoice $invoice
    */
    public function map($lead): array
    {
        return [
            $lead->name,
            $lead->celular,
            $lead->email,
            $lead->usuario,
            $lead->product,
            $lead->funnel_step,
            $lead->origem,
            $lead->channel,
            Date::dateTimeToExcel($lead->created_at),
            $lead->status,
        ];
    }

    public function headings(): array
    {
        return [
            'Nome',
            'Celular',
            'Email',
            'Usuario',
            'Produto',
            'Funil',
            'Origem',
            'Canal',
            'Criado',
            'Status',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'I' => NumberFormat::FORMAT_DATE_DDMMYYYY,            
        ];
    }
}
