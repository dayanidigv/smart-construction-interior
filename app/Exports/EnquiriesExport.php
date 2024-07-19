<?php

namespace App\Exports;

use App\Http\Helpers\Helper;
use App\Models\Enquiries;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EnquiriesExport implements FromCollection, WithHeadings, WithMapping
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
        return Enquiries::withTrashed()->whereIn('id', $this->list_ids)->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Customer Name',
            'Customer Phone',
            'Customer Address',
            'Customer Category',
            'Type',
            'Description',
            'Site Status',
            'Status',
            'Created At',
            'Created By',
        ];
    }

    public function map($enqury): array
    {
        return [
                $enqury->id,
                $enqury->customer->name,
                $enqury->customer->phone,
                $enqury->customer->address,
                $enqury->customerCategory->name,
                $enqury->type_of_work,
                $enqury->description,
                $enqury->site_status,
                $enqury->status,
                $enqury->created_at->toDateTimeString(),
                $enqury->user->name,
        ];
    }

}
