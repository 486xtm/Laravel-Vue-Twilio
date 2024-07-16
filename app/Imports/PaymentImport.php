<?php

namespace App\Imports;

use App\Models\PaymentReconciliation;
use App\Models\Proposal;
use App\Models\Receivable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\ToCollection;
Use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Carbon\Carbon;

class PaymentImport implements ToCollection, WithHeadingRow, WithValidation
{

    public function  __construct($closed_at)
    {
        $this->closed_at = $closed_at;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
       {
            $id = null;

            $count = PaymentReconciliation::where('group','=', $this->explode($row['grupo'],0))
                                            ->where('quota','=', $this->explode($row['grupo'],1))
                                            ->where('parcel','=', $row['parc'])
                                            ->whereNotIn('status_id',[3])
                                            ->count();
            if($count == 0){

                if($row['recebido'] > 0)
                    $total = $row['recebido'];
                else
                    $total = $row['atual'];

                $totais = [(int)$total, (int)$total + 0.99];

                $proposal = Proposal::where('group','=', $this->explode($row['grupo'],0))
                                      ->where('quota','=', $this->explode($row['grupo'],1))
                                      ->whereNotIn('status_id',[3])
                                      ->count();
                
                if($proposal > 0){

                    $receivable = Receivable::where('group','=', $this->explode($row['grupo'],0))
                                            ->where('quota','=', $this->explode($row['grupo'],1))
                                            ->where('parcel','=', $row['parc'])
                                            ->whereBetween('total', $totais)
                                            ->whereNotIn('status_id',[3])
                                            ->get();

                    if($receivable->count() > 0){
                        $id = $receivable[0]->id;
                        $receivable[0]->status_id = 15;
                        $receivable[0]->subtotal = $total;
                        $receivable[0]->total = $total;
                        $receivable[0]->save();
                        sleep(1);

                        $status_id = 17; 
                    }else{
                        $status_id = 18;
                    }                    
                }else{
                    $status_id = 19;
                }    

                $p = PaymentReconciliation::create([                
                    'group' => $this->explode($row['grupo'],0),
                    'quota' => $this->explode($row['grupo'],1),
                    'deal' => strval($row['contrato']),
                    'parcel' => strval($row['parc']),
                    'due_date' => Date::excelToDateTimeObject($row['data_venda'])->format('Y-m-d'),
                    'closed_at' => Carbon::parse($this->closed_at),
                    'total_deal' => $row['valor_base'],
                    'total' => $total,            
                    'comission' => $row[''],            
                    'status_id' => $status_id,
                ]);

                if($id != null){
                    $receivable = Receivable::find($id);
                    $receivable->conciliation_id = $p->id;
                    $receivable->save();
                }
            }
       }
    }

    public function rules(): array
    {
        return[
            'grupo' => 'required',            
            'contrato' => 'required',
            'parc' => 'required',
            'data_venda' => 'required',
            'valor_base' => 'required',
            'recebido' => 'required',               
            'atual' => 'required',               
        ];
    }

    
    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'grupo' => 'O dado na coluna :attribute é obrigatório.',
            'contrato' => 'O dado na coluna :attribute é obrigatório.',
            'parc' => 'O dado na coluna :attribute é obrigatório.',
            'data_venda' => 'O dado na coluna :attribute é obrigatório.',
            'valor_base' => 'O dado na coluna :attribute é obrigatório.',
            'recebido' => 'O dado na coluna :attribute é obrigatório.',
            'atual' => 'O dado na coluna :attribute é obrigatório.',
        ];
    }

    public function explode($p, $x){
        
        $p = explode("/", $p);
        return $p[$x];
    }

    public function map($row): array
    {
        if(gettype($row['data_venda']) == 'double'){
            $row['data_venda'] = Date::excelToDateTimeObject($row['data_venda'])->format('Y-m-d');
        }

        return $row;
    }
}
