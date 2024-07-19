<?php

namespace App\Http\Controllers;

use App\Exports\CustomersExport;
use App\Exports\DesignsExport;
use App\Exports\EnquiriesExport;
use App\Exports\OrdersExport;
use App\Exports\RemindersExport;
use App\Exports\UnitsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;

class ListPageExportController extends Controller
{
    protected function getInput($request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'table_name' => 'required|string',
            'list_ids' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $userID = Auth::id();
        $role = Auth::user()->role;

        $Data = new stdClass();
        $Data->table_name = $input['table_name'];
        $Data->list_ids = $input['list_ids'];
        return $Data;
    }

    public function ExportList(Request $request)
    {
        $inputs = $this->getInput($request);

        if (is_array($inputs)) {
            return $inputs;
        }

        $table_name = strtolower($inputs->table_name);

        if (strpos($table_name, 'user') !== false) {
            return $this->ExportUser($inputs);
        } elseif (strpos($table_name, 'order') !== false) {
            return $this->ExportOrder($inputs);
        } elseif (strpos($table_name, 'customer') !== false) {
            return $this->ExportCustomer($inputs);
        } elseif (strpos($table_name, 'design') !== false) {
            return $this->ExportDesign($inputs);
        } elseif (strpos($table_name, 'reminder') !== false) {
            return $this->ExportReminder($inputs);
        } elseif (strpos($table_name, 'unit') !== false) {
            return $this->ExportUnits($inputs);
        } elseif (strpos($table_name, 'enquiries') !== false) {
            return $this->ExportEnqury($inputs);
        } else {
            return response()->json(['error' => 'Invalid table name.'], 400);
        }
    }

    protected function ExportUser($inputs)
    {
        return response()->json(['message' => 'User export not yet implemented.']);
    }

    protected function ExportOrder($inputs)
    {
        return Excel::download(new OrdersExport($inputs->list_ids), 'orders.xlsx');
    }

    protected function ExportCustomer($inputs)
    {
        return Excel::download(new CustomersExport($inputs->list_ids), 'customers.xlsx');
    }

    protected function ExportDesign($inputs)
    {
        return Excel::download(new DesignsExport($inputs->list_ids), 'designs.xlsx');
    }

    protected function ExportReminder($inputs)
    { 
        return Excel::download(new RemindersExport($inputs->list_ids), 'reminders.xlsx');

    }

    protected function ExportUnits($inputs)
    {
        return Excel::download(new UnitsExport($inputs->list_ids), 'units.xlsx');
    }

    protected function ExportEnqury($inputs)
    {
        return Excel::download(new EnquiriesExport($inputs->list_ids), 'enquiries.xlsx');
    }
}