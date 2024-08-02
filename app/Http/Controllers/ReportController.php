<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Models\Log;
use App\Models\Orders;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{


    public function export(Request $request) 
    {


        $isAll = $request->query('is_all', false);
        $message_return_type = $request->query('message_return_type');
        $status = $request->query('status');
        $managerId = $request->query('manager_id');
        $customerId = $request->query('customer_id');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $type = $request->query('type');
        $location = $request->query('location');

        $query = Orders::query();


        try {
        if (!$isAll) {
            // Apply status filter
            if ($status) {
                $query->where('status', $status);
            }

            // Apply manager filter
            if ($managerId) {
                $query->where('user_id', $managerId);
            }

            // Apply customer filter
            if ($customerId) {
                $query->where('customer_id', $customerId);
            }

           // Apply date filter
            if ($dateFrom) {
                $dateFromFormatted = Carbon::createFromFormat('d-m-Y', $dateFrom)->format('d-m-Y');
                $query->whereDate('created_at', '>=', $dateFromFormatted);
            }
            if ($dateTo) {
                $dateToFormatted = Carbon::createFromFormat('d-m-Y', $dateTo)->format('d-m-Y');
                $query->whereDate('created_at', '<=', $dateToFormatted);
            }

            // Apply location filter
            if ($location) {
                $query->where('location', 'like', '%' . $location . '%');
            }

            // Apply type filter
            if ($type) {
                $query->where('type', $type);
            }
        }

        // Fetch data based on conditions
        $orders = $query->get();


        if($orders->isEmpty()){
            return back()->with('error', 'No orders found for the selected filters')->withInput();
        }else{
            return Excel::download(new ReportExport($orders), 'Report_'.now().'.xlsx');
        }
    } catch (Exception $e) {
            // Log the exception
            Log::create([
                'message' => 'Error in export: ',
                'level' => 'error',
                'type' => 'export',
                'ip_address' => $request->ip(),
                'context' => 'web',
                'source' => 'export_report',
                'extra_info' => json_encode([
                    'user_agent' => $request->header('User-Agent'),
                    'error_message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'error'=>$e])
            ]);
            
    
            // Redirect back with error message
            return back()->with('error', 'An error occurred while exporting the report. Please try again.')->withInput();
        }
    }

    public function getReportByFilter(Request $request)
    {
        try {
            $isAll = $request->query('is_all', false);
            $status = $request->query('status');
            $managerId = $request->query('manager_id');
            $customerId = $request->query('customer_id');
            $dateFrom = $request->query('date_from');
            $dateTo = $request->query('date_to');
            $type = $request->query('type');

           

    
            $query = Orders::query();

            if (!$isAll) {
                // Apply status filter
                if ($status) {
                    $query->where('status', $status);
                }
    
                // Apply manager filter
                if ($managerId) {
                    $query->where('user_id', $managerId);
                }
    
                // Apply customer filter
                if ($customerId) {
                    $query->where('customer_id', $customerId);
                }
    
                // Apply date filter
                if ($dateFrom) {
                    $dateFromFormatted = Carbon::createFromFormat('d-m-Y', $dateFrom)->format('Y-m-d');
                    $query->whereDate('created_at', '>=', $dateFromFormatted);
                }
                if ($dateTo) {
                    $dateToFormatted = Carbon::createFromFormat('d-m-Y', $dateTo)->format('Y-m-d');
                    $query->whereDate('created_at', '<=', $dateToFormatted);
                }
    
                // Apply type filter
                if ($type) {
                    $query->where('type', $type);
                }
            }
    
            $orders = $query->get()->map(function ($order) {
                return [
                    'order_id' => $order->id,
                    'ceartor_name' => $order->user->name,
                    'location' => $order->location,
                    'customer_name' => $order->customer->name,
                    'status' => $order->status,
                    'type' => $order->type,
                    'manager_id' => $order->user_id,
                    'customer_id' => $order->customer_id,
                    'created_at' => Carbon::parse($order->created_at)->format('Y-m-d'),
                ];
            });

            if ($orders->isEmpty()) {
                return response()->json(['message' => 'No orders found for the selected filters'], 404);
            } else {
                return response()->json($orders);
            }

        }catch (Exception $e) {
            Log::create([
                'message' => 'An error occurred while fetching the orders',
                'level' => 'error',
                'type' => 'report',
                'ip_address' => $request->ip(),
                'context' => 'web',
                'source' => 'report_fetch',
                'extra_info' => json_encode([
                    'user_agent' => $request->header('User-Agent'),
                    'error_message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'error'=>$e
                    ])
            ]);
            return response()->json(['message' => 'An error occurred while fetching the orders', 'error' => $e->getMessage()], 500);
        }
    }
    public function exportForManager(Request $request)
    {
        try {
            $isAll = $request->query('is_all', false);
            $message_return_type = $request->query('message_return_type');
            $status = $request->query('status');
            $customerId = $request->query('customer_id');
            $dateFrom = $request->query('date_from');
            $dateTo = $request->query('date_to');
            $type = $request->query('type');
            $location = $request->query('location');

            
    
            $query = Orders::query()->where('user_id', Auth::user()->id);
    
            if (!$isAll) {
                // Apply status filter
                if ($status) {
                    $query->where('status', $status);
                }
    
                // Apply customer filter
                if ($customerId) {
                    $query->where('customer_id', $customerId);
                }
    
                // Apply date filter
                if ($dateFrom) {
                    $dateFromFormatted = Carbon::createFromFormat('d-m-Y', $dateFrom)->format('Y-m-d');
                    $query->whereDate('created_at', '>=', $dateFromFormatted);
                }
                if ($dateTo) {
                    $dateToFormatted = Carbon::createFromFormat('d-m-Y', $dateTo)->format('Y-m-d');
                    $query->whereDate('created_at', '<=', $dateToFormatted);
                }
            
                // Apply location filter
                if ($location) {
                    $query->where('location', 'like', '%' . $location . '%');
                }
    
                // Apply type filter
                if ($type) {
                    $query->where('type', $type);
                }
            }
    
            // Fetch data based on conditions
            $orders = $query->get();
    
            if ($orders->isEmpty()) {
                return back()->with('error', 'No orders found for the selected filters')->withInput();
            } else {
                return Excel::download(new ReportExport($orders), 'Report_' . now() . '.xlsx');
            }
        } catch (Exception $e) {
            // Log the exception
            Log::create([
                'message' => 'Error in exportForManager: ',
                'level' => 'error',
                'type' => 'export',
                'ip_address' => $request->ip(),
                'context' => 'web',
                'source' => 'export_report',
                'extra_info' => json_encode([
                    'user_agent' => $request->header('User-Agent'),
                    'error_message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'error'=>$e
                    ])
            ]);
    
            // Redirect back with error message
            return back()->with('error', 'An error occurred while exporting the report. Please try again.')->withInput();
        }
    }
    


    public function getReportByFilterForManager(Request $request)
    {
        try {
            $isAll = $request->query('is_all', false);
            $status = $request->query('status');
            $customerId = $request->query('customer_id');
            $dateFrom = $request->query('date_from');
            $dateTo = $request->query('date_to');
            $type = $request->query('type');
            $location = $request->query('location');

        
    
            $query = Orders::query()->where('user_id',Auth::user()->id);
    
            if (!$isAll) {
                // Apply status filter
                if ($status) {
                    $query->where('status', $status);
                }
    
                // Apply customer filter
                if ($customerId) {
                    $query->where('customer_id', $customerId);
                }
    
                // Apply date filter
                if ($dateFrom) {
                    $dateFromFormatted = Carbon::createFromFormat('d-m-Y', $dateFrom)->format('Y-m-d');
                    $query->whereDate('created_at', '>=', $dateFromFormatted);
                }
                if ($dateTo) {
                    $dateToFormatted = Carbon::createFromFormat('d-m-Y', $dateTo)->format('Y-m-d');
                    $query->whereDate('created_at', '<=', $dateToFormatted);
                }
                
                // Apply location filter
                if ($location) {
                    $query->where('location', 'like', '%' . $location . '%');
                }

                // Apply type filter
                if ($type) {
                    $query->where('type', $type);
                }
            }
    
            $orders = $query->get()->map(function ($order) {
                return [
                    'order_id' => $order->id,
                    'customer_name' => $order->customer->name,
                    'status' => $order->status,
                    'type' => $order->type,
                    'customer_id' => $order->customer_id,
                    'created_at' => Carbon::parse($order->created_at)->format('Y-m-d'),
                ];
            });

            if ($orders->isEmpty()) {
                return response()->json(['message' => 'No orders found for the selected filters'], 404);
            } else {
                return response()->json($orders);
            }

        }catch (\Exception $e) {
            Log::create([
                'message' => 'An error occurred while fetching the orders',
                'level' => 'error',
                'type' => 'report',
                'ip_address' => $request->ip(),
                'context' => 'web',
                'source' => 'report_fetch',
                'extra_info' => json_encode([
                    'user_agent' => $request->header('User-Agent'),
                    'error_message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),])
            ]);
            return response()->json(['message' => 'An error occurred while fetching the orders', 'error' => $e->getMessage()], 500);
        }
    }
    
}