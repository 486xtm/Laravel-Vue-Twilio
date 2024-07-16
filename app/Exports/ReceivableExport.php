<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Receivable;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;


class ReceivableExport implements FromCollection, WithMapping, WithHeadings, WithColumnFormatting
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

        $receivable = new Receivable();
        $receivable = $receivable->getAll($this->request, $this->user, $company_id);
        return $receivable->orderBy('due_date')->get();
    }
    
    /**
    * @var Invoice $invoice
    */
    public function map($receivable): array
    {
       
        return [
            $receivable->name,
            $receivable->group,
            $receivable->quota,
            $receivable->parcel,
            date('d/m/Y', strtotime($receivable->due_date)),
            $receivable->total,                   
            $receivable->total_proposal,     
            $receivable->user_name,            
            $receivable->status_name,            
        ];
    }

    public function headings(): array
    {
        return [
            'Nome',
            'Grupo',
            'Cota',
            'Parcela',
            'Vencimento',
            'Total Parcela',            
            'Total Proposta',                        
            'Consultor',                        
            'Status',                        
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_DATE_DDMMYYYY,            
        ];
    }
}
