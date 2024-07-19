<?php

namespace App\Exports;

use App\Models\QuantityUnits;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UnitsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $list_ids;

    public function __construct(array $list_ids)
    {
        $this->list_ids = $list_ids;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return QuantityUnits::withTrashed()->whereIn('id', $this->list_ids)->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Description',
            'Created At',
            'Updated At',
            'Deleted At',
        ];
    }


   public function map($unit): array
   {
       return [
               $unit->id,
               $unit->name,
               $unit->description,
               $unit->created_at,
               $unit->updated_at,
               $unit->deleted_at,
       ];
   }

}
