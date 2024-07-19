<?php

namespace App\Exports;

use App\Models\Customers;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomersExport implements FromCollection, WithHeadings, WithMapping
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
        return Customers::withTrashed()->whereIn('id', $this->list_ids)->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Customer Name',
            'Customer Phone no.',
            'Customer Address',
            'Creater Name',
            'Creator Role',
            'Created At',
            'Updated At',
            'Deleted At',
        ];
    }

    public function map($customer): array
    {
        return [
            $customer->id,
            $customer->name,
            $customer->phone,
            $customer->address,
            $customer->user->name,
            $customer->user->role,
            $customer->created_at,
            $customer->updated_at,
            $customer->deleted_at,
        ];
    }

}
