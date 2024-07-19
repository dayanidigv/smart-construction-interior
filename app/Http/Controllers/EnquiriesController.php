<?php

namespace App\Http\Controllers;

use App\Models\CustomerCategory;
use App\Models\Customers;
use App\Models\Enquiries;
use App\Models\Invoices;
use App\Models\Log;
use App\Models\Orders;
use App\Models\Reminders;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use stdClass;

class EnquiriesController extends Controller
{
    protected function auth($role)
    {
        if (!in_array($role, ['admin', 'manager'])) {
            abort(404); 
        }
        if (!Auth::check() || Auth::user()->role != $role) {
            abort(403, 'Unauthorized action.');
        }
    }


    // Common method to get user data
    private function getUserData(string $menuTitle, $sectionName, string $title, object $pageData = null): array
    {
        $pageData = $pageData ?: new stdClass();
        $user = User::find(Auth::id());
        $userName = $user ? $user->name : 'Guest';
        $userId = $user ? $user->id : 'Guest';
        $reminder = $user ? $user->reminders()
            ->where('is_completed', 0)
            ->orderBy('priority', 'asc')
            ->orderBy('reminder_time', 'asc')
            ->get() : collect();
        return [
            'title' => $title,
            'role' =>  $user->role,
            'menuTitle' => $menuTitle,
            'sectionName' => $sectionName,
            'userName' => $userName,
            'userId' => $userId,
            "user" => $user,
            'pageData' => $pageData,
            'displayReminder' => $reminder
        ];
    }

    public function new(string $role){
        $this->auth($role);
        $data = $this->getUserData('Orders', "Enquiries", "New");
        return view('common.enquiries.new',$data);
    }
    public function store(Request $request, string $role)
    {
        $this->auth($role);
    
        try {
            $validator = Validator::make($request->all(), [
                'customer_category' => 'required|string',
                'customer' => 'required|numeric',
                'description' => 'nullable|string',
                'site_status' => 'required|string',
                'type_of_work' => 'required|string',
                'status' => 'required|string',
                'note' => 'nullable|array',
                'note.*' => 'nullable|string',
                'follow_date' => 'nullable|array',
                'follow_date.*' => 'nullable|date',
                'priority' => 'nullable|array',
                'priority.*' => 'nullable|numeric',
            ]);
    
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
    
            DB::beginTransaction();
    
            $userID = Auth::id();
    
            // Find the customer
            $customer = Customers::find($request->customer);
            if (!$customer) {
                throw new \Exception("Customer not found.");
            }
    
            $category = CustomerCategory::firstOrCreate(['name' => $request->customer_category]);
    
            // Create a new enquiry instance
            $enquiry = Enquiries::create([
                'customer_category_id' => $category->id,
                'description' => $request->description,
                'site_status' => $request->site_status,
                'type_of_work' => $request->type_of_work,
                'status' => $request->status,
                'user_id' => $userID,
                'creator_id' => $userID,
                'customer_id' => $request->customer
            ]);
    
            $priorityLevels = [
                "1" => 'Danger',
                "2" => 'Warning',
                "3" => 'Success'
            ];
    
            // Add new follow-ups
            $this->handleNewFollowUps($request, $enquiry, $customer, $priorityLevels, $userID);
        
            DB::commit();
    
            return redirect()->route('enquiries.list', ['role' => $role])->with('message', 'Enquiry created successfully.');
        } catch (QueryException $e) {
            DB::rollBack();
            return $this->handleException($request, 'Failed to create Enquiry.', $e);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($request, 'Failed to create Enquiry.', $e);
        }
    }
    
    public function update(string $role, string $encodedId, Request $request)
    {
        $this->auth($role);

        try {
            $decodedId = base64_decode($encodedId);

            $validator = Validator::make($request->all(), [
                'customer_category' => 'required|string',
                'customer' => 'required|numeric',
                'description' => 'nullable|string',
                'site_status' => 'required|string',
                'type_of_work' => 'required|string',
                'status' => 'required|string',
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
                'note' => 'nullable|array',
                'note.*' => 'nullable|string',
                'follow_date' => 'nullable|array',
                'follow_date.*' => 'nullable|date',
                'priority' => 'nullable|array',
                'priority.*' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();

            $userID = Auth::id();

            // Find the customer
            $customer = Customers::find($request->customer);
            if (!$customer) {
                throw new \Exception("Customer not found.");
            }

            $enquiry = Enquiries::findOrFail($decodedId);

            $category = CustomerCategory::firstOrCreate(['name' => $request->customer_category]);

            $enquiry->update([
                'customer_category' => $category->name,
                'description' => $request->description,
                'site_status' => $request->site_status,
                'type_of_work' => $request->type_of_work,
                'status' => $request->status,
                'customer_id' => $request->customer,
            ]);

            $priorityLevels = [
                "1" => 'Danger',
                "2" => 'Warning',
                "3" => 'Success'
            ];



            // Update or delete existing follow-ups
            $this->handleExistingFollowUps($request, $enquiry, $customer, $priorityLevels);

            // Add new follow-ups
            $this->handleNewFollowUps($request, $enquiry, $customer, $priorityLevels, $userID);

            // Check and Convert to Order
            $order = $this->handleConvertToOrder( $enquiry, $customer, $userID);
           
           DB::commit();

           if ($order) {
                $encodedId = base64_encode($order->id);
                if (Auth::check() && Auth::user()->role === "admin") {
                    return redirect()->route('admin.edit.order', ['encodedId' => $encodedId])->with('message', 'Enquiry to Order Convert successfully.');
                } else {
                    return redirect()->route('manager.edit.order', ['encodedId' => $encodedId])->with('message', 'Enquiry to Order Convert successfully.');
                }
            }

            return redirect()->back()->with('message', 'Enquiry updated successfully.');
        } catch (QueryException $e) {
            DB::rollBack();
            return $this->handleException($request, 'Failed to update Enquiry.', $e);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($request, 'Failed to update Enquiry.', $e);
        }
    }

    public function viewEnqury(string $role, string $encodedId, Request $request)
    {
        
        $this->auth($role);
        
        try {
            $pageData =  new stdClass();
            $decodedId = base64_decode($encodedId);
            $pageData->enquiry = Enquiries::findOrFail($decodedId);
            $pageData->followup = Reminders::where('enquiry_id',$decodedId)->get();
            $data = $this->getUserData('Orders', "Enquiries", "View", $pageData);
            return view('common.enquiries.view', $data);
        } catch (QueryException $e) {
            return $this->handleException($request, 'Failed to retrieve Enquiry.', $e);
        } catch (\Exception $e) {
            return $this->handleException($request, 'Failed to retrieve Enquiry.', $e);
        }
    }

    public function edit(string $role, string $encodedId, Request $request)
    {
        $this->auth($role);
        try {
            $pageData =  new stdClass();
            $decodedId = base64_decode($encodedId);
            $pageData->enquiry = Enquiries::findOrFail($decodedId);
            $pageData->followup = Reminders::where('enquiry_id',$decodedId)->get();
            $data = $this->getUserData('Orders', "Enquiries", "Edit", $pageData);
            return view('common.enquiries.edit', $data);
        } catch (QueryException $e) {
            return $this->handleException($request, 'Failed to retrieve Enquiry.', $e);
        } catch (\Exception $e) {
            return $this->handleException($request, 'Failed to retrieve Enquiry.', $e);
        }
    }

    public function list(string $role, Request $request){
        
        $this->auth($role);
        
        try {
            if($role == 'admin'){
                $enquiries = Enquiries::orderBy('id', 'desc')->where('status', "!=", "confirmed")->get();
            }else{
                $enquiries = Enquiries::where('user_id', Auth::user()->id)->where('status', "!=", "confirmed")->get();
            }
            $data = $this->getUserData('Orders', "Enquiries", "List", $enquiries);
            return view('common.enquiries.list', $data);
        } catch (QueryException $e) {
            return $this->handleException($request, 'Failed to retrieve Enquiries.', $e);
        } catch (\Exception $e) {
            return $this->handleException($request, 'Failed to retrieve Enquiries.', $e);
        }

    }

    public function destroy(string $role, string $encodedId, Request $request)
    {
        try {
            $decodedId = decrypt($encodedId);
            $enquiry = Enquiries::findOrFail($decodedId);
            $enquiry->delete();

            if ($request->input('returnType') === 'json') {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Enquiry deleted successfully.',
                ]);
            }

            return redirect()->back()->with('success', 'Enquiry deleted successfully.');
        } catch (QueryException $e) {
            return $this->handleException($request, 'Failed to delete Enquiry.', $e);
        } catch (\Exception $e) {
            return $this->handleException($request, 'Failed to delete Enquiry.', $e);
        }
    }

    // Handle Convert To Order Function
    private function handleConvertToOrder( $enquiry, $customer, $userID)
    {
        if ( $enquiry->status === 'confirmed') {
            // Create new order
            $order = Orders::create([
                'user_id' => $userID,
                'description' => $enquiry->description,
                'creator_id' => $userID,
                'enquiry_id' => $enquiry->id,
                'name' => 'Order for ' . $customer->name,
                'type' => $enquiry->type_of_work,
                'customer_id' => $customer->id,
                'is_set_approved' => Auth::check() && Auth::user()->role === "admin" ? 1 : 0,
                'is_approved' => Auth::check() && Auth::user()->role === "admin" ? 1 : 0,
            ]);

            $latestInvoice = Invoices::orderBy('id', 'desc')->first();
            $nextInvoiceNumber = $latestInvoice ? ((int) substr($latestInvoice->invoice_number, 5)) + 1 : 1;
            $formattedInvoiceNumber = '#INV-' . str_pad($nextInvoiceNumber, 5, '0', STR_PAD_LEFT);

            // Create invoice
            $invoice = Invoices::create([
                'order_id' => $order->id,
                'user_id' => $userID,
                'customer_id' => $customer->id,
                'invoice_number' => $formattedInvoiceNumber,
                'terms_and_conditions' => '1. In case of changes in design rate will be changed\n2. Extra works cause extra charges.',
            ]);

            return $order;
        }
        return null;
    }

    private function handleExistingFollowUps(Request $request, $enquiry, $customer, $priorityLevels)
    {
        if (isset($request->alt_followup_id) && is_array($request->alt_followup_id)) {
            foreach ($request->alt_followup_id as $index => $alreadyFollowUpId) {
                $schedule = Schedule::find($alreadyFollowUpId);
                if ($schedule) {
                    $reminder = Reminders::where('enquiry_id', $enquiry->id)
                        ->where('title', $schedule->title)
                        ->where('description', $schedule->description)
                        ->first();

                    if (isset($request->is_followup_delete) && in_array($alreadyFollowUpId, $request->is_followup_delete)) {
                        if ($reminder) {
                            $reminder->delete();
                        }
                        $schedule->delete();
                    } else {
                        $this->updateFollowUp($schedule, $reminder, $request, $index, $enquiry, $customer, $priorityLevels);
                    }
                }
            }
        }
    }

    private function updateFollowUp($schedule, $reminder, Request $request, $index, $enquiry, $customer, $priorityLevels)
    {
        $additionalNote = $request->alt_note[$index] ?? '';
        $newDescription = 'Follow-up needed for enquiry with customer ' . $customer->name . ". Additional note: " . $additionalNote;
    
        $priorityLevel = $priorityLevels[$request->alt_follow_priority[$index]] ?? 'Warning';
    
        // Update schedule description and level
        $schedule->update([
            'description' => $newDescription,
            'level' => $priorityLevel,
        ]);
    
        if (isset($request->alt_follow_date[$index])) {
            $newFollowDate = Carbon::parse($request->alt_follow_date[$index])->format('Y-m-d 00:00:00');
            $schedule->update(['start' => $newFollowDate]);
        }
    
        if ($reminder) {
            $reminder->update([
                'description' => $newDescription,
                'priority' => $request->alt_follow_priority[$index],
            ]);
    
            if (isset($request->alt_follow_date[$index])) {
                $newReminderTime = Carbon::parse($request->alt_follow_date[$index])->format('Y-m-d 09:00:00');
                $reminder->update(['reminder_time' => $newReminderTime]);
            }
        }
    }
    
    private function handleNewFollowUps(Request $request, $enquiry, $customer, $priorityLevels, $userID)
    {
        if (isset($request->follow_date) && is_array($request->follow_date)) {
            foreach ($request->follow_date as $index => $followDate) {
                $note = $request->note[$index] ?? '';
                $priority = $request->priority[$index] ?? 2; // Default to 'Warning' level if priority is not set

                // Create new schedule
                $schedule = new Schedule();
                $schedule->user_id = $userID;
                $schedule->enquiry_id = $enquiry->id;
                $schedule->title = 'Enquiry Follow-up Reminder';
                $schedule->description = 'Follow-up needed for enquiry with customer ' . $customer->name . ". Additional note: " . $note;
                $schedule->start = Carbon::parse($followDate)->format('Y-m-d 00:00:00');
                $schedule->level = $priorityLevels[$priority] ?? 'Warning';
                $schedule->save();

                // Create new reminder
                $reminder = new Reminders();
                $reminder->user_id = $userID;
                $reminder->enquiry_id = $enquiry->id;
                $reminder->title = 'Enquiry Follow-up Reminder';
                $reminder->description = 'Follow-up needed for enquiry with customer ' . $customer->name . ". Additional note: " . $note;
                $reminder->reminder_time = Carbon::parse($followDate)->format('Y-m-d 09:00:00');
                $reminder->priority = $priority;
                $reminder->save();
            }
        }
    }

    private function handleException(Request $request, string $message, \Exception $exception)
    {
        $this->logError($request, $message, $exception);

        if ($request->input('returnType') === 'json') {
            return response()->json([
                'status' => 'error',
                'message' => $message . ': ' . $exception->getMessage(),
            ], 500);
        }

        return redirect()->back()->with('error', $message . ': ' . $exception->getMessage());
    }

    private function logError(Request $request, string $message, \Exception $exception)
    {
        Log::create([
            'message' => $message,
            'level' => 'warning',
            'type' => 'error',
            'ip_address' => $request->ip(),
            'context' => 'web',
            'source' => 'enquiries_controller',
            'extra_info' => json_encode([
                'user_agent' => $request->header('User-Agent'),
                'error' => $exception->getMessage(),
            ]),
        ]);
    }
}