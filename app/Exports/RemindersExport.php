<?php

namespace App\Exports;

use App\Models\Reminders;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RemindersExport implements FromCollection, WithHeadings, WithMapping
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
        return Reminders::withTrashed()->whereIn('id', $this->list_ids)->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Description',
            'Reminder Time',
            'is Completed',
            'Priority',
            'Category',
            'Repeat',
            'Notes',
            'Visibility',
            'Enqure ID',
            'Order ID',
            'Created At',
            'Updated At',
            'Deleted At',
        ];
    }

    public function map($remider): array
    {
        return [
            $remider->id,
            $remider->title,
            $remider->description,
            $remider->reminder_time,
            $remider->is_completed,
            $remider->priority ==1 ?  "Red" :  ($remider->priority ==2 ? "Yellow" : "Green") ,
            $remider->category,
            $remider->repeat,
            $remider->notes,
            $remider->visibility,
            $remider->enqure_id,
            $remider->order_id,
            $remider->created_at,
            $remider->updated_at,
            $remider->deleted_at,
        ];
    }

}
