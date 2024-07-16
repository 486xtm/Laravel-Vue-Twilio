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

class LeadsExport implements FromCollection, WithMapping, WithHeadings, WithColumnFormatting
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
        $lead = new Lead;

        $lead = $lead->getLeads($this->request, $this->user, $company_id);
        
        return $lead->get();
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
