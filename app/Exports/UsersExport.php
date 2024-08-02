<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
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
        return User::withTrashed()->whereIn('id', $this->list_ids)->get();
    }

     /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'UserName',
            'Role',
            'Created At',
            'Updated At',
            'Deleted At',
        ];
    }

    public function map($remider): array
    {
        return [
            $remider->id,
            $remider->name,
            $remider->username,
            $remider->role,
            $remider->created_at,
            $remider->updated_at,
            $remider->deleted_at,
        ];
    }

}
