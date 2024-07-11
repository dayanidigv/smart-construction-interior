<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use stdClass;

class ListPageExportController extends Controller
{

    protected function getInput($request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'table_name' => 'required|string',
            'search_value' => 'nullable|string',
            'table_heads' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $Data = new stdClass();
        $Data->table_name = $input['table_name'];
        $Data->search_value = $input['search_value'];
        $Data->table_heads = $input['table_heads'];
        return $Data;
    }

    public function ExportUser(Request $request)
    {
        $inputs = $this->getInput($request);
        if (is_array($inputs)) {
            return $inputs; 
        }
        
    }

    public function ExportOrder(Request $request)
    {
        $inputs = $this->getInput($request);
        if (is_array($inputs)) {
            return $inputs; 
        }
        
    }

    public function ExportCustomer(Request $request)
    {
        $inputs = $this->getInput($request);
        if (is_array($inputs)) {
            return $inputs; 
        }
        
    }

    public function ExportDesign(Request $request)
    {
        $inputs = $this->getInput($request);
        if (is_array($inputs)) {
            return $inputs; 
        }
        
    }

    public function ExportReminder(Request $request)
    {
        $inputs = $this->getInput($request);
        if (is_array($inputs)) {
            return $inputs; 
        }
        
    }
}
