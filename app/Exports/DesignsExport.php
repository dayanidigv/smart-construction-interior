<?php

namespace App\Exports;

use App\Models\Designs;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DesignsExport implements FromCollection, WithHeadings, WithMapping
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
        return Designs::withTrashed()->whereIn('id', $this->list_ids)->get();
    }


    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Image',
            'Name',
            'Category',
            'Sub Category',
            'Type',
            'Unit',
            'Category Key',
            'Created At',
            'Updated At',
            'Deleted At',
        ];
    }

     /**
     * @var Designs $design
     * @return array
     */
    public function map($design): array
    {
        return [
            $design->id,
            $this->generateImageFormula(url($design->image_url)),
            $design->name,
            optional($design->category->parentCategory)->name,
            optional($design->category)->name,
            $design->type,
            $design->unit->name . " (" . $design->unit->description . ")",
            optional($design->categoryKey)->key,
            $design->created_at,
            $design->updated_at,
            $design->deleted_at,
        ];
    }

    /**
     * Generate the Excel formula for displaying the image URL
     * @param string $url
     * @return string
     */
    protected function generateImageFormula($url): string
    {
        return '=IMAGE("' . $url . '", 1)';
    }

}
