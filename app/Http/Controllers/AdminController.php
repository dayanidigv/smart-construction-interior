<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\Designs;
use App\Models\Labour;
use App\Models\Log;
use App\Models\Orders;
use App\Models\QuantityUnits;
use App\Models\Reminders;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use stdClass;

class AdminController extends Controller
{

    private function generatePassword($length = 12, $useUppercase = true, $useLowercase = true, $useNumbers = true, $useSymbols = true) {
        $characters = '';
      
        if ($useUppercase) {
          $characters .= strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, floor($length / 4)));
        }
      
        if ($useLowercase) {
          $characters .= strtolower(substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, floor($length / 4)));
        }
      
        if ($useNumbers) {
          $characters .= substr(str_shuffle('0123456789'), 0, floor($length / 4));
        }
      
        if ($useSymbols) {
          $characters .= substr(str_shuffle('!@#$%&?'), 0, floor($length / 4));
        }
      
        $len = strlen($characters);
        if ($len < $length) {
          $characters .= str_shuffle(substr($characters, 0, $len) . ($useUppercase ? 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' : '') . ($useLowercase ? 'abcdefghijklmnopqrstuvwxyz' : '') . ($useNumbers ? '0123456789' : '') . ($useSymbols ? '!@#$%^&*()~-_=+{};:,<.>/?': ''));
        }
      
        return substr(str_shuffle($characters), 0, $length);
      }

    private function pageDataToBeEmpty ($pageData) {
        if($pageData->isEmpty()) {$pageData = [];}
        return $pageData;
    }

    // Common method to get user data
    private function getUserData(string $menuTitle,  $sectionName, string $title, object $pageData = new stdClass()): array
    {
        $user = User::find(Auth::id());
        $role = $user->role;
        $userName = $user ? $user->name : 'Guest';
        $userId = $user ? $user->id : 'Guest';
        $reminder = $user->reminders()
            ->where('is_completed', 0)
            ->get()
            ->sort(function ($a, $b) {
                $aTime = strtotime($a->reminder_time);
                $bTime = strtotime($b->reminder_time);

                if ($aTime == $bTime) {
                    return $a->priority <=> $b->priority;
                }

                return $aTime <=> $bTime;
            });



        if($title == "Dashboard"){
            $pageData->Schedules = $user->schedule()->get();
        }else if ( $sectionName == "Reminder" && $title == "List"){
            $pageData->Reminders = $user->reminders()->orderBy('created_at', 'desc')->get();
        }else if ($sectionName == "Users" && $title == "List"){
            $pageData->users = User::query()->where('role',"!=","developer")->withTrashed()->orderByDesc('id')->get();
        }else if ($title == "Quantity Units"){
            $pageData->QuantityUnits = QuantityUnits::withTrashed()->orderByDesc('id')->get();
        }else if ($sectionName == "Products" && $title == "New"){
            $pageData->QuantityUnits = QuantityUnits::all();
        }else if ($sectionName == "Order" && $title == "New"){
            $pageData->QuantityUnits = QuantityUnits::all();
            $pageData->managers = User::where('role','manager')->get();
        }else if ($sectionName == "Products" && $title == "List"){
            $pageData->Products = $user->products()->get();
        }else if ($sectionName == "Order" && $title == "List"){
            $pageData->Orders = Orders::with('orderItems')->orderByDesc('id')->get();
        }else if ($title == "Invoice"){
            $pageData = Orders::orderByDesc('created_at')->with('orderItems')->get();
        }else if ($sectionName == "Design" && $title == "New"){
            $pageData->QuantityUnits = QuantityUnits::all();
        }else if ($title == "Gallery"){
            $pageData = Designs::all();
        }else if ($sectionName == "Design" && $title == "List"){
            $pageData = Designs::withTrashed()->orderByDesc('id')->get();
        }else if ($title == "Report" ){
            $pageData->orders = Orders::all();
            $pageData->users = User::where('role','!=',"developer")->get();
            $pageData->customers = Customers::all();
        } 
       
       
        return [
            'title' => $title,
            'menuTitle' => $menuTitle,
            'sectionName' => $sectionName,
            'userName' => $userName,
            'userId' => $userId,
            "user" => $user,
            "role" => $role,
            'pageData' => $pageData,
            'displayReminder' => $reminder
        ];
    }

     public function index()
    {
        $data = $this->getUserData('Home',null, 'Dashboard');
        return view('admin.index', $data);
    }

    public function QuantityUnits()
    {
        $data = $this->getUserData('Settings', null ,'Quantity Units');
        return view('admin.quantity-units.unit', $data);
    }

    public function QuantityUnitsAdd()
    {
        $data = $this->getUserData('Settings', null , 'Quantity Units');
        return view('admin.quantity-units.add', $data);
    }

    public function QuantityUnitsEdit(string $encodedId)
    {
        $decodedId = base64_decode($encodedId); 
        $pageData = new stdClass();
        try {
            $pageData->QuantityUnits = QuantityUnits::all();
            $pageData->ChangedQuantityUnit = QuantityUnits::findOrFail($decodedId);
            try {
                $data = $this->getUserData('Settings', null , 'Quantity Units', $pageData);
              } catch (Exception $e) {
                return abort(500, 'An error occurred while processing your request.');
              }
            return view('admin.quantity-units.edit', $data);
        } catch (ModelNotFoundException $e) {
            return abort(404, 'Quantity Unit not found'); 
        }
    }

    public function newProduct()
    {
        $data = $this->getUserData("Orders",'Products', 'New');
        return view('admin.product.add', $data);
    }

    public function listProduct()
    {
        $data = $this->getUserData("Orders",'Products', 'List');
        return view('admin.product.list', $data);
    }

    public function viewProduct(string $encodedId, Request $request) 
    {
        $decodedId = base64_decode($encodedId); 
        try {
            $user = User::find(Auth::id());
            $product = $user->products()->findOrFail($decodedId);
            $data = $this->getUserData("Orders",'Products', 'View', $product);
            $user_id = Auth::id();
            if($user_id != $product->user_id){
                Log::create([
                    'message' => 'Unauthorized operation by ' . $user->username . ' while trying to view product.',
                    'level' => 'warning',
                    'type' => 'security',
                    'ip_address' => $request->ip(),
                    'context' => 'web',
                    'source' => 'view_product',
                    'extra_info' => json_encode(['user_agent' => $request->header('User-Agent')])
                ]);
                abort(403, 'You can only view product you created.');
            }
            return view('admin.product.view', $data);
        } catch (ModelNotFoundException $e) {
            
            return abort(404, 'Customer not found'); 
        }
    }

    public function editProduct(string $encodedId, Request $request)
    {
        $decodedId = base64_decode($encodedId);
        try {
            $user = User::find(Auth::id());
            $product = $user->products()->findOrFail($decodedId);
    
            if ($user->id != $product->user_id) {
                Log::create([
                    'message' => 'Unauthorized operation by ' . $user->username . ' while trying to edit product.',
                    'level' => 'warning',
                    'type' => 'security',
                    'ip_address' => $request->ip(),
                    'context' => 'web',
                    'source' => 'edit_product',
                    'extra_info' => json_encode(['user_agent' => $request->header('User-Agent')])
                ]);
                abort(403, 'You can only edit a product you created.');
            }
    
            $pageData = new stdClass();
            $pageData->product = $product;
            $pageData->QuantityUnits = QuantityUnits::all();
            $data = $this->getUserData("Orders", 'Products', 'Edit', $pageData);
    
            return view('admin.product.edit', $data);
        } catch (ModelNotFoundException $e) {
            
            return abort(404, 'Product not found');
        }
    }

    public function newOrder()
    {
        $data = $this->getUserData('Orders',"Order", 'New');
        return view('common.order.create', $data);
    }

    public function listOrder()
    {
        $data = $this->getUserData('Orders', "Order", 'List');
        return view('admin.orders.list', $data);
    }

    public function viewOrder(string $encodedId) 
    {
        $decodedId = base64_decode($encodedId);

        try {
            $order = Orders::findOrFail($decodedId);

            $pageData = new stdClass();
            $pageData->order = $order;
            $labourData=[];

            $labours = Labour::where('order_id', $order->id)->orderBy('date')->get();
            foreach ($labours as $labour) {
                $date = Carbon::parse($labour->date)->format('Y-m-d');
                if (!isset($labourData[$date])) {
                    $labourData[$date] = [];
                }
                
                $labourData[$date][] = [
                    'labor_category_id' => $labour->labor_category_id,
                    'labor_category_name' => $labour->category->name,
                    'number_of_labors' => $labour->number_of_labors,
                    'per_labor_amount' => $labour->per_labor_amount,
                    'total_amount' => $labour->total_amount,
                    'date' => Carbon::parse($labour->date)->format('jS F Y'),
                    'edit_link' => route('admin.order.Labours',['encodedOrderId'=> $encodedId,"date"=>$date])
                ];
            }

            $pageData->labours = $labourData;
            $pageData->follow_up = Reminders::where('order_id',$order->id)->orderBy('reminder_time')->get();

            $data = $this->getUserData('Orders', "Order", 'View', $pageData);
            return view('admin.orders.view', $data);
        } catch (ModelNotFoundException $e) {
            return abort(404, 'Order not found'); 
        }
    }

    public function editOrder(string $encodedId) 
    {
        $decodedId = base64_decode($encodedId);

        try {
            $pageData = new stdClass();
            $pageData->QuantityUnits = QuantityUnits::all();
            $pageData->order = Orders::findOrFail($decodedId);
            $pageData->managers = User::where('role','manager')->get();
            $data = $this->getUserData('Orders', "Order", 'Edit', $pageData);
            return view('common.order.update', $data);
        } catch (ModelNotFoundException $e) {
            return abort(404, 'Order not found'); 
        }
    }

    public function showLabours($encodedOrderId, $date = null)
    {
        // Decode the order ID
        $decodedOrderId = base64_decode($encodedOrderId);

        try {
            $pageData = new stdClass();
            $pageData->order = Orders::findOrFail($decodedOrderId);
            $date = $date ?? Carbon::today()->toDateString();
            $pageData->date = $date;
            $pageData->labours = Labour::where('order_id', $decodedOrderId)->where('date', $date)->get();
            $data = $this->getUserData('Orders', "Order", 'Show Labours', $pageData);
            return view('admin.orders.workers', $data);
        } catch (ModelNotFoundException $e) {
            return abort(404, 'Order not found'); 
        }
    }

    public function invoiceShow()
    {
        $data = $this->getUserData('Invoice',null , 'Invoice');
        return view('admin.invoice.index', $data);
    }

    public function add_user()
    {
        $data = $this->getUserData('Settings', "Users", 'New');
        return view('admin.add-user', $data);
    }

    public function list_user()
    {
        $data = $this->getUserData('Settings',"Users", 'List');
        return view('admin.user.list', $data);
    }

    public function userDestroy(string $encodedId)
    {
        $decodedId = base64_decode($encodedId); 
        $record = User::find($decodedId);
        $record->delete();
        return redirect()->back()->with('message', 'User soft deleted successfully');
    
    }

    public function userRestore(string $encodedId)
    {
        $decodedId = base64_decode($encodedId); 
        $record = User::withTrashed()->find($decodedId);
        $record->restore();
        return redirect()->back()->with('message', 'User Restore successfully');
    
    }
    
    public function user_store(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'username' => 'required|string|unique:users,username',
            'role' => ['required', 'regex:/^(admin|manager)$/i']
        ]);
    
        // If validation fails, redirect back with errors and input data
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
    
        // Generate a random password for the new user
        $password = $this->generatePassword();
    
        DB::beginTransaction();
    
        try {
            // Create the new user record
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'role' => $request->role,
                'password' => Hash::make($password),
            ]);
    
            DB::commit();
    
            return redirect()->back()->with('message', 'User Create successfully.')->with('username',$request->username)->with('password',$password);
        } catch (Exception $e) {
            DB::rollBack();
            Log::create([
                'message' => 'Failed to send mail.',
                'level' => 'danger',
                'type' => 'error',
                'ip_address' => $request->ip(),
                'context' => 'web',
                'source' => 'create_user',
                'extra_info' => json_encode(['user_agent' => $request->header('User-Agent'), "error" => $e->getMessage()])
            ]);
            return redirect()->back()->with('error', 'Failed to send mail. Please try again later.')->withInput();
        }
    }
    

    public function reminder()
    {
        $data = $this->getUserData('Settings', "Reminder", 'Set');
        return view('admin.reminder.index', $data);
    }

    public function reminder_list()
    {
        $data = $this->getUserData('Settings', "Reminder",'List');
        return view('admin.reminder.list', $data);
    }

    public function reminder_view(string $encodedId) 
    {
        $decodedId = base64_decode($encodedId); 
        try {
            $reminder = User::find(Auth::id())->reminders()->findOrFail($decodedId);
            $data = $this->getUserData('Settings', "Reminder", 'View', $reminder);
            $user_id = Auth::id();
            if($user_id != $reminder->user_id){
                abort(403, 'You can only view reminders you created.');
            }
            return view('admin.reminder.view', $data);
        } catch (ModelNotFoundException $e) {
            return abort(404, 'Customer not found'); 
        }
    }

    public function reminder_edit(string $encodedId, Request $request) 
    {
        $decodedId = base64_decode($encodedId); 
        try {
            $user = User::find(Auth::id());
            $reminder = $user->reminders()->findOrFail($decodedId);
            try {
                $data = $this->getUserData('Settings', "Reminder", 'Edit', $reminder);
              } catch (Exception $e) {
                return abort(500, 'An error occurred while processing your request.');
              }
            $user_id = Auth::id();
            if($user_id != $reminder->user_id){
                Log::create([
                    'message' => 'Unauthorized operation by ' . $user->username . ' while trying to edit reminders',
                    'level' => 'warning',
                    'type' => 'security',
                    'ip_address' => $request->ip(),
                    'context' => 'web',
                    'source' => 'edit_reminder',
                    'extra_info' => json_encode(['user_agent' => $request->header('User-Agent')])
                ]);
                abort(403, 'You can only edit reminders you created.');
            }
            return view('admin.reminder.edit', $data);
        } catch (ModelNotFoundException $e) {
            return abort(404, 'Customer not found'); 
        }
    }

    public function Gallery()
    {
        $data = $this->getUserData('Settings', null,'Gallery');
        return view('admin.gallery', $data);
    }

    public function newDesign()
    {
        $data = $this->getUserData('Orders','Design', 'New');
        return view('admin.design.add', $data);
    }

    public function listDesign()
    {
        $data = $this->getUserData('Orders','Design', 'List');
        return view('admin.design.list', $data);
    }

    public function viewDesign(string $encodedId) 
    {
        $decodedId = base64_decode($encodedId);

        try {
            $pageData = new stdClass();
            $pageData->design = Designs::findOrFail($decodedId);
            $pageData->QuantityUnits = QuantityUnits::all();
            $data = $this->getUserData('Orders','Design', 'View', $pageData);
            return view('admin.design.view', $data);
        } catch (ModelNotFoundException $e) {
            return abort(404, 'Order not found'); 
        }
    }

    public function editDesign(string $encodedId) 
    {
        $decodedId = base64_decode($encodedId);

        try {
            $pageData = new stdClass();
            $pageData->design = Designs::findOrFail($decodedId);
            $pageData->QuantityUnits = QuantityUnits::all();
            $data = $this->getUserData('Orders', 'Design','Edit', $pageData);
            return view('admin.design.edit', $data);
        } catch (ModelNotFoundException $e) {
            return abort(404, 'Order not found'); 
        }
    }

    public function Report()
    {
        $data = $this->getUserData('Orders',null, 'Report');
        return view('admin.report', $data);
    }

}