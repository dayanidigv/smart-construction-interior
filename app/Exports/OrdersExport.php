<?php

namespace App\Exports;

use App\Http\Helpers\Helper;
use App\Models\Orders;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersExport implements FromCollection, WithHeadings, WithMapping
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
        return Orders::withTrashed()->whereIn('id', $this->list_ids)->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Invoice Number',
            'Customer Name',
            'Customer Phone',
            'Customer Address',
            'Type',
            'Order Items',
            'Sub Total Amount',
            'Discount Percentage',
            'Discount Amount',
            'Advance Amount',
            'Total Amount',
            'Balance Amount',
            'Order Status',
            'Payment Status',
            'Created At',
            'Created By',
        ];
    }

    public function map($order): array
    {
        return [
                $order->id,
                $order->invoice->invoice_number,
                $order->customer->name,
                $order->customer->phone,
                $order->customer->address,
                $order->type,
                $order->orderItems->count(),
                Helper::format_inr(number_format($order->invoice->sub_total_amount)),
                $order->invoice->discount_percentage,
                Helper::format_inr(number_format($order->invoice->discount_amount)),
                Helper::format_inr(number_format($order->invoice->advance_pay_amount)),
                Helper::format_inr(number_format( $order->invoice->total_amount)),
                Helper::format_inr(number_format( $order->invoice->balance_amount)),
                $order->status,
                $order->invoice->payment_status,
                $order->created_at->toDateTimeString(),
                $order->creator->name,
        ];
    }

}

