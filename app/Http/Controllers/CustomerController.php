<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\Log;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use stdClass;

class CustomerController extends Controller
{
    private function getUserData(string $sectionName, string $title, object $pageData = new stdClass()): array
    {
        $user = User::find(Auth::id());
        $userName = $user ? $user->name : 'Guest';
        $userId = $user ? $user->id : 'Guest';
        $reminder = $user->reminders()
            ->where('is_completed', 0)
            ->orderBy('priority', 'asc')
            ->orderBy('reminder_time', 'asc')
            ->get();

        if ($title === "Index") {
            $pageData->Schedules = $user->schedule()->get();
        }else if ($title == "List Customer"){
            $pageData->Customers =  $user->customers()->get();
        }else if ($title == "List all Customers"){
            
            $customers = Customers::withTrashed()->orderByDesc('id')->get(); 
            $customersWithUserDetails = [];
            
            foreach ($customers as $customer) {
                $user = User::find($customer->user_id);
            
    
                if ($user) {
                    $customersWithUserDetails[] = [
                        "id" => $customer->id, 
                        'customer_name' => $customer->name, 
                        'username' => $user->name,
                        'userrole' => $user->role,
                        'deleted_at' => $customer->deleted_at,
                    ];
                }
            }
            
            $pageData->customers = $customersWithUserDetails ?: [];
            
            
        }

        return [
            'title' => $title,
            'sectionName' => $sectionName,
            'userName' => $userName,
            'user' => $user,
            'pageData' => $pageData,
            'displayReminder' => $reminder
        ];
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|unique:customers,email|max:255',
            'phone' => 'required|numeric|digits:10|unique:customers,phone',
            'address' => 'required|string|max:250',
        ]);
        

        if ($validator->fails()) {
            if ($request->input('returnType') === 'json') {
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
            }
            return back()->withErrors($validator)->withInput();
        }

        $user_id = Auth::id();

        try {
            $customer = Customers::create([
                "user_id" => $user_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            if ($request->input('returnType') === 'json') {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Customer created successfully.',
                    'customer' => $customer,
                ], 200);
            }

            return redirect()->back()->with('message', 'Customer created successfully.');
        } catch (Exception $e) {
            if ($request->input('returnType') === 'json') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to create customer. Please try again later.',
                ], 500);
            }
            Log::create([
                'message' => 'Failed to create customer.',
                'level' => 'warning',
                'type' => 'error',
                'ip_address' => $request->ip(),
                'context' => 'web',
                'source' => 'create_customer',
                'extra_info' => json_encode(['user_agent' => $request->header('User-Agent'),'error'=>$e])
            ]);
            return redirect()->back()->with('error', 'Failed to create customer. Please try again later.')->withInput();
        }
    }
    public function update(Request $request, string $encodedId)
    {
        $decodedId = base64_decode($encodedId); 
    
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'string',
                'email',
                Rule::unique('customers')->ignore($decodedId),
            ],
            'phone' => [
                'required',
                'numeric',
                'digits:10',
                Rule::unique('customers')->ignore($decodedId),
            ],
            'address' => 'required|string|max:250',
        ]);
    
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
    
        try {
            $customer = Customers::findOrFail($decodedId);
            $customer->name = $request->input('name');
            $customer->email = $request->input('email');
            $customer->phone = $request->input('phone');
            $customer->address = $request->input('address');
            $customer->save();
    
            return redirect()->back()->with('message', 'Customer updated successfully.');
        } catch (Exception $e) {
            Log::create([
                'message' => 'Failed to update customer.',
                'level' => 'warning',
                'type' => 'error',
                'ip_address' => $request->ip(),
                'context' => 'web',
                'source' => 'update_customer',
                'extra_info' => json_encode([
                    'user_agent' => $request->header('User-Agent'),
                    'error' => $e->getMessage()
                ])
            ]);
    
            return redirect()->back()->with('error', 'Failed to update customer. Please try again later.')->withInput();
        }
    }
    public function destroy(string $encodedId) 
    {
        // Decode the encoded ID
        $decodedId = base64_decode($encodedId); 
        
        try {
            // Find the customer by the decoded ID
            $customer = Customers::findOrFail($decodedId);
            
            // Soft delete the customer
            $customer->delete();
            
            // Return a success message
            return back()->with('message', 'Customer deleted successfully.');
        } catch (ModelNotFoundException $e) {
            // If the customer is not found, return a 404 error
            return abort(404, 'Customer not found'); 
        }
    }

    public function restore(string $encodedId) 
    {
        // Decode the encoded ID
        $decodedId = base64_decode($encodedId); 
        
        try {
            // Find the customer by the decoded ID, including those that are soft deleted
            $customer = Customers::withTrashed()->findOrFail($decodedId);
            
            // Restore the soft deleted customer
            $customer->restore();
            
            // Return a success message
            return back()->with('message', 'Customer restored successfully.');
        } catch (ModelNotFoundException $e) {
            // If the customer is not found, return a 404 error
            return abort(404, 'Customer not found'); 
        }
    }

    public function add()
    {
        $data = $this->getUserData('Customer', 'Add Customer');
        return view('manager.customer.store', $data);
    }

    public function list()
    {

        $data = $this->getUserData('Customer', 'List Customer');
        return view('manager.customer.list', $data);
    }

    public function view(string $encodedId) 
    {
        $decodedId = base64_decode($encodedId); 
        try {
            $customer = User::find(Auth::id())->customers()->findOrFail($decodedId);
            $data = $this->getUserData('Customer', 'View Customer', $customer);
            return view('manager.customer.view', $data);
        } catch (ModelNotFoundException $e) {
            return abort(404, 'Customer not found'); 
        }
    }

    public function edit(string $encodedId) 
    {
        $decodedId = base64_decode($encodedId); 
        try {
            $customer = User::find(Auth::id())->customers()->findOrFail($decodedId);
            $data = $this->getUserData('Customer', 'Edit Customer', $customer);
            return view('manager.customer.edit', $data);
        } catch (ModelNotFoundException $e) {
            return abort(404, 'Customer not found'); 
        }
    }

    public function admin_add()
    {
        $data = $this->getUserData('Customer', 'Add Customer');
        return view('admin.customer.store', $data);
    }

    public function admin_list()
    {

        $data = $this->getUserData('Customer', 'List Customer');
        return view('admin.customer.list', $data);
    }

    public function admin_view(string $encodedId) 
    {
        $decodedId = base64_decode($encodedId); 
        try {
            $customer = User::find(Auth::id())->customers()->findOrFail($decodedId);
            $data = $this->getUserData('Customer', 'View Customer', $customer);
            return view('admin.customer.view', $data);
        } catch (ModelNotFoundException $e) {
            return abort(404, 'Customer not found'); 
        }
    }

    public function admin_edit(string $encodedId) 
    {
        $decodedId = base64_decode($encodedId); 
        try {
            $customer = Customers::findOrFail($decodedId);
            $data = $this->getUserData('Customer', 'Edit Customer', $customer);
            return view('admin.customer.edit', $data);
        } catch (ModelNotFoundException $e) {
            return abort(404, 'Customer not found'); 
        }
    }

    public function admin_list_all()
    {

        $data = $this->getUserData('Customer', 'List all Customers');
        return view('admin.customer.list-all', $data);
    }
    
    public function admin_view_all(string $encodedId) 
    {
        $decodedId = base64_decode($encodedId); 
        try {
            $pageData = new stdClass();
            $customer = Customers::findOrFail($decodedId);
            $user = User::find($customer->user_id);
            $customersWithUsernames = [
                "id"=> $customer->id, 
                'name' => $customer->name, 
                'phone' => $customer->phone, 
                'email' => $customer->email,
                'address' => $customer->address,
                'created_at' => $customer->created_at,
                'creater_name' => $user->name,
                'creater_email' => $user->email,
                'creater_role' => $user->role,
            ];

            $pageData->customer = $customersWithUsernames;

            $data = $this->getUserData('Customer', 'View all Customer', $pageData);
            return view('admin.customer.view-all', $data);
        } catch (ModelNotFoundException $e) {
            return abort(404, 'Customer not found'); 
        }
    }
}