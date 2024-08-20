<?php

namespace App\Http\Controllers;
use App\Models\Designs;
use App\Models\RateHistory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Models\Categories;
use App\Models\Customers;
use App\Models\Invoices;
use App\Models\Log as ModelsLog;
use App\Models\OrderItems;
use App\Models\Orders;
use App\Models\PaymentHistory;
use App\Models\Reminders;
use App\Models\Schedule;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrdersController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'customer' => 'required|integer|exists:customers,id',
            'location' => 'required|string|max:255',
            'type' => 'required|in:Interior,Exterior,Both',
            'order_starting_date' => 'required|date',
            'order_ending_date' => 'nullable|date|after_or_equal:order_starting_date',
            'created_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:created_date',
            'estimated_cost' => 'nullable|numeric',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'advance_pay_amount' => 'nullable|numeric|min:0',
            'payment_status' => 'required|string',
            'terms_and_conditions' => 'nullable|string',
            'note' => 'nullable|array',
            'note.*' => 'nullable|string',
            'follow_date' => 'nullable|array',
            'follow_date.*' => 'nullable|date',
            'alt_follow_priority' => 'nullable|array',
            'alt_follow_priority.*' => 'nullable|string|in:1,2,3',
            'category' => 'nullable|array',
            'category.*' => 'string|max:255',
            'sub_category' => 'nullable|array',
            'sub_category.*' => 'string|max:255',

            'design' => 'nullable|array',
            'design.*' => 'nullable|integer|exists:designs,id',

            'rate_per' => 'nullable|array',
            'rate_per.*' => 'nullable|numeric|min:0',

            'sub_total' => 'nullable|array',
            'sub_total.*' => 'nullable|numeric|min:0',

            'order_item_quantity' => 'nullable|array',
            'order_item_quantity.*' => 'integer|min:1',
            
            'payment_date' => 'nullable|array',
            'payment_date.*' => 'nullable|date',
            
            'payment_amount' => 'nullable|array',
            'payment_amount.*' => 'nullable|numeric|min:0',
            
            'payment_method' => 'nullable|array',
            'payment_method.*' => 'nullable|string',
            'manage_access' => 'nullable|string',
            
            
            'length' => 'nullable|array',
            'length.*' => 'nullable',
            
            'breadth' => 'nullable|array',
            'breadth.*' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            $user_id = Auth::id();

           // Find the customer
            $customer = Customers::find($request->customer);

            // Create new order
            $order = Orders::create([
                'user_id' => $user_id,
                'creator_id' => $user_id,
                'name' => $request->name ?? 'Order for ' . $customer->name,
                'location' => $request->location,
                'type' => $request->type,
                'customer_id' => $request->customer,
                'start_date' => $request->order_starting_date,
                'end_date' => $request->order_ending_date,
                'is_set_approved' => Auth::check() && Auth::user()->role === "admin" ? 1 : 0,
                'is_approved' => Auth::check() && Auth::user()->role === "admin" ? 1 : 0,
            ]);

            if (isset($request->manage_access)) {
                $order->update([
                    'user_id' => $request->manage_access && $request->manage_access == "only-for-me" ? $user_id : $request->manage_access
                ]);
            }

            
            $priorityLevels = [
                1 => 'Danger',
                2 => 'Warning',
                3 => 'Success'
            ];

            // Handle follow-ups
            if (isset($request->follow_date)) {
                foreach ($request->follow_date as $index => $followDate) {
                    $note = $request->note[$index] ?? '';

                    // Create schedule
                    $schedule = new Schedule();
                    $schedule->user_id = $user_id;
                    $schedule->order_id = $order->id;
                    $schedule->title = 'Follow-up Reminder';
                    $schedule->description = 'Follow-up needed for order with customer ' . $customer->name . ". Additional note: " . $note;
                    $schedule->start = Carbon::parse($followDate)->format('Y-m-d 00:00:00');
                    
                    $schedule->level = $priorityLevels[$request->follow_priority[$index]] ?? 'Warning';
                    
                    $schedule->save();

                    // Create reminder
                    $reminder = new Reminders();
                    $reminder->user_id = $user_id;
                    $reminder->order_id = $order->id;
                    $reminder->title = 'Follow-up Reminder';
                    $reminder->description = 'Follow-up needed for order with customer ' . $customer->name . ". Additional note: " . $note;
                    $reminder->reminder_time = Carbon::parse($followDate)->format('Y-m-d 09:00:00');
                    $reminder->priority = $request->follow_priority[$index];
                    $reminder->save();
                }
            }

            // Handle order items
            if (isset($request->category)) {
                for ($i = 0; $i < count($request->category); $i++) {
    
                    // Find the category
                    $category = Categories::findOrFail($request->category[$i]);

                    // Find the subcategory
                    $subCategory = Categories::findOrFail($request->sub_category[$i]);
            
                    $design= null;
                    if (isset($request->design[$i])){   
                        // Find the design
                        $design = Designs::findOrFail($request->design[$i]);
                    }

                    $rate_per = $request->rate_per[$i] ?? 0;

                    $rate_per = $request->rate_per[$i] ?? 0;
                    $sub_total = $request->sub_total[$i];
                    $discount_percentage = $request->discount_percentage ?? 0;
                    $discount_amount = $sub_total * ($discount_percentage / 100);
                    $total = $sub_total - $discount_amount;

                    $length = $request->length[$i] ?? 0;
                    $breadth = $request->breadth[$i] ?? 0;
                    $dimension = $length . 'x' . $breadth;

                    // Create order item
                    DB::table('order_items')->insert([
                        'order_id' => $order->id,
                        'category_id' => $subCategory->id,
                        'design_id' =>  $design ? $design->id : null,
                        'quantity' => $request->order_item_quantity[$i] ?? 0,
                        'dimension' => $dimension,
                        'length' => $length,
                        'breadth' => $breadth,
                        'rate_per' => $rate_per,
                        'sub_total' => $sub_total,
                        'discount_percentage' => $discount_percentage,
                        'discount_amount' => $discount_amount,
                        'total' => $total,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    

                }
            }

            // Create payment history
            if (isset($request->payment_date)) {
                foreach ($request->payment_date as $index => $paymentDate) {
                    $paymentHistory = new PaymentHistory();
                    $paymentHistory->order_id = $order->id;
                    $paymentHistory->payment_date = $paymentDate;
                    $paymentHistory->amount = $request->paid_amount[$index] ?? 0;
                    $paymentHistory->payment_method = $request->payment_method[$index] ?? '';
                    $paymentHistory->save();
                }
            }

            $latestInvoice = Invoices::orderBy('id', 'desc')->first();
            $nextInvoiceNumber = $latestInvoice ? ((int) substr($latestInvoice->invoice_number, 5)) + 1 : 1;
            $formattedInvoiceNumber = '#INV-' . str_pad($nextInvoiceNumber, 5, '0', STR_PAD_LEFT);

            $sub_total = $order->orderItems->sum('sub_total');
            $discountAmount = $sub_total * ($request->discount_percentage / 100);
            $totalAfterDiscount = $sub_total - $discountAmount;

            $balanceAmount = $totalAfterDiscount - $order->paymentHistory->sum('amount');


            // Create invoice
            $invoice = Invoices::create([
                'order_id' => $order->id,
                'user_id' => $user_id,
                'customer_id' => $customer->id,
                'invoice_number' => $formattedInvoiceNumber,
                'created_date' => $request->created_date,
                'due_date' => $request->due_date ?? null,
                'discount_percentage' => $request->discount_percentage ?? 0,
                'discount_amount' => $discountAmount ?? 0,
                'advance_pay_amount' => $request->advance_pay_amount ?? 0,
                'payment_status' => $request->payment_status ?? 'not confirmed',
                'sub_total_amount' => $sub_total ?? 0,
                'total_amount' => $totalAfterDiscount ?? 0,
                'balance_amount' => $balanceAmount ?? 0,
                'terms_and_conditions' => $request->terms_and_conditions ?? '',
            ]);
            
            // Commit the transaction
            DB::commit();

            return redirect()->back()->with('message', 'Order created successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed:', ['error' => $e->getMessage()]);
            ModelsLog::create([
                'message' => 'Order creation failed',
                'level' => 'danger',
                'type' => 'error',
                'ip_address' => $request->ip(),
                'context' => 'web',
                'source' => 'order_create_form',
                'extra_info' =>json_encode( ['user_agent' => $request->header('User-Agent'),'error_message' => $e])
            ]);
            return back()->with('error', 'Failed to create order. Please try again later.');
        }
    }

    public function update(Request $request, string $encodedId){

         $request->validate([
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'customer' => 'required|integer|exists:customers,id',
            'location' => 'required|string|max:255',
            'type' => 'required|in:Interior,Exterior,Both',
            'order_starting_date' => 'required|date',
            'order_ending_date' => 'nullable|date|after_or_equal:order_starting_date',
            'status' => 'required|string',
            'invoice_id' => "required|integer|exists:invoices,id",
            'created_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:created_date',
            'estimated_cost' => 'nullable|numeric',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'advance_pay_amount' => 'nullable|numeric|min:0',
            'payment_status' => 'required|string',
            'terms_and_conditions' => 'nullable|string',
            
            'alt_payment_history_id' => 'nullable|array',
            'alt_payment_history_id.*' => 'nullable|integer|exists:payment_history,id',
            
            'alt_order_item' => 'nullable|array',
            'alt_order_item.*' => 'nullable|integer|exists:order_items,id',
            
            'alt_category_id' => 'nullable|array',
            'alt_category_id.*' => 'nullable|integer|exists:categories,id',
            
            'alt_sub_category_id' => 'nullable|array',
            'alt_sub_category_id.*' => 'nullable|integer|exists:categories,id',
            
            'alt_design' => 'nullable|array',
            'alt_design.*' => 'nullable|integer|exists:designs,id',
            
            'alt_order_item_quantity' => 'nullable|array',
            'alt_order_item_quantity.*' => 'nullable|numeric|min:0',
            
            'alt_rate_per' => 'nullable|array',
            'alt_rate_per.*' => 'nullable|numeric|min:0',
            
            'alt_sub_total' => 'nullable|array',
            'alt_sub_total.*' => 'nullable|numeric|min:0',
            
            'alt_category' => 'nullable|array',
            'alt_category.*' => 'nullable|string|max:255',
            
            'alt_sub_category' => 'nullable|array',
            'alt_sub_category.*' => 'nullable|string|max:255',
            
            
            'alt_length' => 'nullable|array',
            'alt_length.*' => 'nullable',
            
            'alt_breadth' => 'nullable|array',
            'alt_breadth.*' => 'nullable',
            
            'is_order_item_delete' => 'nullable|array',
            'is_order_item_delete.*' => 'nullable|integer|exists:order_items,id',
            
            'alt_followup_id' => 'nullable|array',
            'alt_followup_id.*' => 'nullable|integer|exists:schedule,id',
            
            'alt_follow_date' => 'nullable|array',
            'alt_follow_date.*' => 'nullable|date',
            
            'alt_follow_priority' => 'nullable|array',
            'alt_follow_priority.*' => 'nullable|string|in:1,2,3',
            
            'is_followup_delete' => 'nullable|array',
            'is_followup_delete.*' => 'nullable|integer|exists:schedule,id',
            
            'alt_note' => 'nullable|array',
            'alt_note.*' => 'nullable|string|max:255',
            
            'alt_payment_date' => 'nullable|array',
            'alt_payment_date.*' => 'nullable|date',
            
            'alt_payment_amount' => 'nullable|array',
            'alt_payment_amount.*' => 'nullable|numeric|min:0',
            
            'alt_payment_method' => 'nullable|array',
            'alt_payment_method.*' => 'nullable|string',
            
            'is_payment_history_delete' => 'nullable|array',
            'is_payment_history_delete.*' => 'nullable|integer|exists:payment_history,id',
            
            'note' => 'nullable|array',
            'note.*' => 'nullable|string|max:255',
            
            'follow_date' => 'nullable|array',
            'follow_date.*' => 'nullable|date',
            
            'follow_priority' => 'nullable|array',
            'follow_priority.*' => 'nullable|in:1,2,3',
            
            'payment_date' => 'nullable|array',
            'payment_date.*' => 'nullable|date',
            
            'payment_amount' => 'nullable|array',
            'payment_amount.*' => 'nullable|numeric|min:0',
            
            'payment_method' => 'nullable|array',
            'payment_method.*' => 'nullable|string',
            
            'category' => 'nullable|array',
            'category.*' => 'nullable|string|max:255',
            
            'sub_category' => 'nullable|array',
            'sub_category.*' => 'nullable|string|max:255',

            'design' => 'nullable|array',
            'design.*' => 'nullable|integer|exists:designs,id',

            'rate_per' => 'nullable|array',
            'rate_per.*' => 'nullable|numeric|min:0',

            'sub_total' => 'nullable|array',
            'sub_total.*' => 'nullable|numeric|min:0',
            
            'length' => 'nullable|array',
            'length.*' => 'nullable',

            'breadth' => 'nullable|array',
            'breadth.*' => 'nullable',
            
            'order_item_quantity' => 'nullable|array',
            'order_item_quantity.*' => 'nullable|integer|min:1',

            'manage_access' => 'nullable|string',
        ]);

        try {

            DB::beginTransaction();

            $decodeID = base64_decode($encodedId);

            // Find customer
            $customer = Customers::find($request->customer);

            // Find the order
            $order = Orders::findOrFail($decodeID);

            // Update order fields
            if(isset($request->manage_access)){
                $order->user_id = $request->manage_access == "only-for-me" ? $order->creator_id : $request->manage_access;
            }
            $order->location = $request->location;
            $order->type = $request->type;
            $order->start_date = $request->order_starting_date;
            $order->status = $request->status;
            $order->end_date = $request->order_ending_date ?? null;
            $order->save();

            // Update customer-related details if customer changes
            if ($order->customer_id !== (int) $request->customer) {
                $order->customer_id = $request->customer;
                $order->name = $request->name ?? 'Order for ' . $customer->name;
                $order->save();
            }
            
            // Update or delete existing order items
            if (isset($request->alt_order_item_id)) {
                foreach ($request->alt_order_item_id as $index => $alreadyOrderId) {
                    $orderItem = OrderItems::find($alreadyOrderId);
    
                    if ($orderItem) {
                        // Handle deletion of order item
                        if (isset($request->is_order_item_delete) && in_array($alreadyOrderId, $request->is_order_item_delete)) {
                            $orderItem->delete();
                        } else {
                            // Update category and sub-category
                            $newSubCategory = $request->alt_sub_category[$index];
                            $newCategory = $request->alt_category[$index];

                            // Find the new main category
                            $category = Categories::withTrashed()->findOrFail($newCategory);
                            $subCategory = Categories::withTrashed()->findOrFail($newSubCategory);
                            $orderItem->update(['category_id' => $subCategory->id]);
    
                            // Update Design, quantity, and total
                            if (isset($request->alt_design[$index])) {
                                $orderItem->update(['design_id' => $request->alt_design[$index]]);
                            }
    
                            if (isset($request->alt_order_item_quantity[$index])) {
                                $quantity = $request->alt_order_item_quantity[$index];
                                $rate_per = $request->alt_rate_per[$index];
                                $sub_total = $request->alt_sub_total[$index];
                                $discount_percentage = $request->discount_percentage ?? 0;
                                $discount_amount = $sub_total * ($discount_percentage / 100);
                                $total = $sub_total - $discount_amount;
    
                                $length = $request->alt_length[$index] ?? 0;
                                $breadth = $request->alt_breadth[$index] ?? 0;
                                $dimension = $length . 'x' . $breadth;

                                $orderItem->update([
                                    'quantity' => $quantity,
                                    'rate_per' => $rate_per,
                                    'sub_total' => $sub_total,
                                    'dimension' => $dimension,
                                    'length' => $length,
                                    'breadth' => $breadth,
                                    'discount_percentage' => $discount_percentage,
                                    'discount_amount' => $discount_amount,
                                    'total' => $total,
                                ]);
                            }
                        }
                    }
                }
            }

            // Create new order items
            if (isset($request->category)) {
                for ($i = 0; $i < count($request->category); $i++) {

                    // Find or create the category
                    $category = Categories::findOrFail($request->category[$i]);

                    // Find or create the subcategory
                    $subCategory = Categories::findOrFail($request->sub_category[$i]);

                    $design= null;
                    if (isset($request->design[$i])){   
                        // Find the design
                        $design = Designs::findOrFail($request->design[$i]);
                    }
                    $rate_per = $request->rate_per[$i] ?? 0;
                    $sub_total = $request->sub_total[$i];
                    $discount_percentage = $request->discount_percentage ?? 0;
                    $discount_amount = $sub_total * ($discount_percentage / 100);
                    $total = $sub_total - $discount_amount;
                    $dimension = $request->length[$i] . 'x' .$request->breadth[$i];

                     // Create order item
                     DB::table('order_items')->insert([
                        'order_id' => $order->id,
                        'category_id' => $subCategory->id,
                        'design_id' =>  $design ? $design->id : null,
                        'quantity' => $request->order_item_quantity[$i],
                        'dimension' => $dimension,
                        'length' => $request->length[$i],
                        'breadth' => $request->breadth[$i],
                        'rate_per' => $rate_per,
                        'sub_total' => $sub_total,
                        'discount_percentage' => $discount_percentage,
                        'discount_amount' => $discount_amount,
                        'total' => $total,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            
            $priorityLevels = [
                1 => 'Danger',
                2 => 'Warning',
                3 => 'Success'
            ];

            // Update or delete follow-ups
            if (isset($request->alt_followup_id)) {
                foreach ($request->alt_followup_id as $index => $alreadyFollowUpId) {
                    $schedule = Schedule::find($alreadyFollowUpId);
                    if ($schedule) {
                        $reminder = Reminders::where('order_id', $order->id)->where('title', $schedule->title)
                        ->where('description', $schedule->description)
                        ->first();
    
                        if (isset($request->is_followup_delete) && in_array($alreadyFollowUpId, $request->is_followup_delete)) {
                            if ($reminder) {
                                $reminder->delete();
                            }
                            $schedule->delete();
                        } else {
                            $additionalNote = $request->alt_note[$index] ?? '';
                            $newDescription = 'Follow-up needed for order with customer ' . $customer->name . ". Additional note: " . $additionalNote;
                            $newFollowDate = Carbon::parse($request->alt_follow_date[$index])->format('Y-m-d');
    
                            $reminder = Reminders::where('order_id', $order->id)->where('title', $schedule->title)->where('description', $schedule->description)->first();

                            if ($schedule->description !== $newDescription) {
                                $schedule->update(['description' => $newDescription]);
                                if ($reminder) {
                                    $reminder->update(['description' => $newDescription]);
                                }
                            }
            
                            if (Carbon::parse($schedule->start)->format('Y-m-d') !== $newFollowDate) {
                                $schedule->update(['start' => "$newFollowDate 00:00:00"]);
                                if ($reminder) {
                                    $reminder->update(['reminder_time' => "$newFollowDate 09:00:00"]);
                                }
                            }

                            if($request->alt_follow_priority[$index]){
                                $schedule->level = $priorityLevels[$request->alt_follow_priority[$index]] ?? 'Warning';
                                if ($reminder) {
                                    $reminder->update(['priority' => $request->alt_follow_priority[$index]]);
                                }
                            }
                        }
                    }
                }
            }

            // Create new follow-ups
            if (isset($request->follow_date)) {
                foreach ($request->follow_date as $index => $followDate) {
                    $note = $request->note[$index] ?? '';
    
                    $schedule = new Schedule();
                    $schedule->user_id = Auth::id();
                    $schedule->order_id = $order->id;
                    $schedule->title = 'Follow-up Reminder';
                    $schedule->description = 'Follow-up needed for order with customer ' . $customer->name . ". Additional note: " . $note;
                    $schedule->start = "$followDate 00:00:00";
                    $schedule->level = $priorityLevels[$request->follow_priority[$index]] ?? 'Warning';
                    $schedule->save();
    
                    $reminder = new Reminders();
                    $reminder->user_id = Auth::id();
                    $reminder->order_id  = $order->id;
                    $reminder->title = 'Follow-up Reminder';
                    $reminder->description = 'Follow-up needed for order with customer ' . $customer->name . ". Additional note: " . $note;
                    $reminder->reminder_time = "$followDate 09:00:00";
                    $reminder->priority = $request->follow_priority[$index];
                    $reminder->save();
                }
            }

            // Update or delete payment history
            if (isset($request->alt_payment_history_id)) {
                foreach ($request->alt_payment_history_id as $index => $alreadyPaymentHistoryId) {
                    $paymentHistory = PaymentHistory::find($alreadyPaymentHistoryId);
                    if ($paymentHistory) {
                        if (isset($request->is_payment_history_delete) && in_array($alreadyPaymentHistoryId, $request->is_payment_history_delete)) {
                            $paymentHistory->delete();
                        } else {
                            // Update payment history details
                            $paymentHistory->update([
                                'payment_date' => $request->alt_payment_date[$index] ?? $paymentHistory->payment_date,
                                'amount' => $request->alt_payment_amount[$index] ?? $paymentHistory->amount,
                                'payment_method' => $request->alt_payment_method[$index] ?? $paymentHistory->payment_method,
                            ]);
                        }
                    }
                }
            }

            // Create new payment history
            if (isset($request->payment_date)) {
                foreach ($request->payment_date as $index => $paymentDate) {
                    $paymentHistory = new PaymentHistory();
                    $paymentHistory->order_id = $order->id;
                    $paymentHistory->payment_date = $paymentDate;
                    $paymentHistory->amount = $request->paid_amount[$index] ?? '';
                    $paymentHistory->payment_method = $request->payment_method[$index] ?? '';
                    $paymentHistory->save();
                }
            }

            // Update invoice
            if (isset($request->invoice_id)) {
                $invoice = Invoices::find($request->invoice_id);

                if ($invoice) {
                    // Calculate invoice details
                    $sub_total = $order->orderItems->sum('sub_total');
                    $discountAmount = $sub_total * ($request->discount_percentage / 100);
                    $totalAfterDiscount = $sub_total - $discountAmount;

                    $balanceAmount = $totalAfterDiscount - $order->paymentHistory->sum('amount');

                    $invoice->update([
                        'created_date' => $request->created_date,
                        'due_date' => $request->due_date,
                        'sub_total_amount' => $sub_total,
                        'discount_amount' => $discountAmount,
                        'discount_percentage' => $request->discount_percentage,
                        'advance_pay_amount' => $request->advance_pay_amount,
                        'payment_status' => $request->payment_status,
                        'terms_and_conditions' => $request->terms_and_conditions,
                        'total_amount' => $totalAfterDiscount,
                        'balance_amount' => $balanceAmount,
                    ]);
                }
            }

            // Commit the transaction
            DB::commit();
            return back()->with('message', 'Order updated successfully.');
            
        } catch (Exception $e) {
            DB::rollBack();
            ModelsLog::create([
                'message' => 'Failed to update order.',
                'level' => 'danger',
                'type' => 'error',
                'ip_address' => $request->ip(),
                'context' => 'web',
                'source' => 'order_update_form',
                'extra_info' => json_encode(['user_agent' => $request->header('User-Agent'),'error_message' => $e])
            ]);
            return back()->with('error', 'Failed to update order. Please try again later. ' . $e);
        }
    }
    
    public function destroy(string $encodedId, Request $request) 
    {
        $decodedId = base64_decode($encodedId); 
        $order = Orders::findOrFail($decodedId);

        try {
            DB::beginTransaction();
            foreach ($order->orderItems as $orderItem) {
                $orderItem->delete();
            }
            foreach ($order->followup()->get() as $followup) {
                $followup->delete();
            }
            foreach ($order->followupReminders()->get() as $followup) {
                $followup->delete();
            }
            foreach ($order->paymentHistory()->get() as $paymentHistory) {
                $paymentHistory->delete();
            }
            foreach ($order->labours()->get() as $labour) {
                $labour->delete();
            }
            $order->delete();
            DB::commit();

            return back()->with('message', 'Order deleted successfully.');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return abort(404, 'Order not found'); 
        } catch (Exception $e) {
            DB::rollBack();
            ModelsLog::create([
                'message' => 'An error occurred while deleting the order.',
                'level' => 'danger',
                'type' => 'error',
                'context' => 'web',
                'source' => 'order_delete_form',
                'extra_info' => json_encode(['user_agent' => $request->header('User-Agent'),'error_message' => $e])
            ]);
            return back()->with('error', 'An error occurred while deleting the order.');
        }
    }
    
    public function setApproved(string $encodedId) 
    {
        $decodedId = base64_decode($encodedId); 
        $order = Orders::findOrFail($decodedId);

        try {
            DB::beginTransaction();
            $order->update(['is_set_approved' => 1]); 
            DB::commit();
            return back()->with('message', 'Order approval requested successfully.');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return abort(404, 'Order not found'); 
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while requesting order approval.');
        }
    }
    public function isApproved(string $encodedId) 
    {
        $decodedId = base64_decode($encodedId); 
        $order = Orders::findOrFail($decodedId);
        try {
            if (Auth::user()->role === "admin") {
                DB::beginTransaction();
                $order->update(['is_approved' => 1]); 
                DB::commit();
                return back()->with('message', 'Order approved successfully.');
            } else {
                return abort(403, 'Unauthorized action'); 
            }
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return abort(404, 'Order not found'); 
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while approving the order.');
        }
    }

}
