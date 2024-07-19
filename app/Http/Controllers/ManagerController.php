<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\Designs;
use App\Models\Labour;
use App\Models\Log;
use App\Models\Orders;
use App\Models\QuantityUnits;
use App\Models\Reminders;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use stdClass;

class ManagerController extends Controller
{
    private function pageDataToBeEmpty ($pageData) {
        if($pageData->isEmpty()) {$pageData = [];}
        return $pageData;
    }

    // Common method to get user data
    private function getUserData($menuTitle, $sectionName, $title, $pageData = new stdClass())
    {
        $user = User::find(Auth::id());
        $role = $user->role;
        $userName = $user ? $user->name : 'Guest';
        $userId = $user ? $user->id : 'Guest';
        $totalPages = 1;

        $reminder = $user->reminders()
            ->where('is_completed', 0)
            ->orderBy('priority', 'asc')
            ->orderBy('reminder_time', 'asc')
            ->get(); 

        if($title == "DashBoard"){
            $publicSchedules = Schedule::where('visibility', 'public')->get();
            $managerSchedules = Schedule::where('visibility', 'manager')->get();
            $userSchedules = $user->schedule()->get();
            $pageData->Schedules = $publicSchedules->merge($userSchedules);
            $pageData->Schedules = $pageData->Schedules->merge($managerSchedules);
        }else if ($sectionName == 'Reminder' && $title == "List"){
            $pageData->reminders = $user->reminders()->orderBy('created_at', 'desc')->get();
        }else if ($sectionName == 'Design' && $title == "New"){
            $pageData->QuantityUnits = QuantityUnits::all();
        }else if ($title == "Gallery" || ($sectionName == 'Design' && $title == "List")){
            $pageData = Designs::withTrashed()->orderByDesc('id')->get();
        }else if ($title == "Quantity Units"){
            $pageData->QuantityUnits = QuantityUnits::withTrashed()->orderByDesc('id')->get();
        }else if ($title == "Report" ){
            $pageData->orders = Orders::where('user_id',$userId);
            $pageData->customers = Customers::where('user_id',$userId)->get();
        }else if ($sectionName == 'Order' && $title == "New"){
            $pageData->QuantityUnits = QuantityUnits::all();
        }else if ($sectionName == 'Order' && $title == "List"){
            $pageData->Orders = Orders::where('user_id',$user->id)->with('orderItems')->orderByDesc('id')->get();
        }
       
        // dd($pageData->reminders);
        return [
            'title' => $title,
            'menuTitle' => $menuTitle,
            'sectionName' => $sectionName,
            'userName' => $userName,
            'userId' => $userId,
            "role" => $role,
            "user" => $user,
            'pageData' => $pageData,
            'displayReminder' => $reminder
        ];
    }

     public function index()
    {
        $data = $this->getUserData('Home',null, 'DashBoard');
        return view('manager.index', $data);
    }

    public function profile()
   {
       $data = $this->getUserData('General',null, 'Profile');
       return view('manager.profile', $data);
   }

   public function invoice()
  {
      $data = $this->getUserData('General',null, 'Invoice');
      return view('manager.invoice', $data);
  }

   public function reminder()
   {
       $data = $this->getUserData('Settings',"Reminder",'Set');
       return view('manager.reminder.index', $data);
   }

   public function reminder_list()
   {
       $data = $this->getUserData('Settings', "Reminder",'List');
       return view('manager.reminder.list', $data);
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
           return view('manager.reminder.view', $data);
       } catch (ModelNotFoundException $e) {
           return abort(404, 'Reminder not found'); 
       }
   }

   public function reminder_edit(string $encodedId, Request $request) 
   {
       $decodedId = base64_decode($encodedId); 
       try {
            $user = User::find(Auth::id());
            $reminder = $user->reminders()->findOrFail($decodedId);
           try {
               $data = $this->getUserData('Settings', "Reminder",'Edit ', $reminder);
             } catch (Exception $e) {
               return abort(500, 'An error occurred while processing your request.');
             }
           $user_id = Auth::id();
           if($user_id != $reminder->user_id){
            Log::create([
                'message' => 'Unauthorized operation by ' . $user->username . ' while trying to view product.',
                'level' => 'warning',
                'type' => 'security',
                'ip_address' => $request->ip(),
                'context' => 'web',
                'source' => 'view_product',
                'extra_info' => json_encode(['user_agent' => $request->header('User-Agent')])
            ]);
               abort(403, 'You can only edit reminders you created.');
           }
           return view('manager.reminder.edit', $data);
       } catch (ModelNotFoundException $e) {
           return abort(404, 'Reminder not found'); 
       }
   }

   public function Gallery()
   {
       $data = $this->getUserData('Settings', null,'Gallery');
       return view('manager.gallery', $data);
   }

   public function newDesign()
   {
       $data = $this->getUserData('Orders',"Design", 'New');
       return view('manager.design.add', $data);
   }

   public function listDesign()
   {
       $data = $this->getUserData('Orders', "Design", 'List');
       return view('manager.design.list', $data);
   }

   public function viewDesign(string $encodedId) 
   {
       $decodedId = base64_decode($encodedId);

       try {
           $pageData = new stdClass();
           $pageData->design = Designs::findOrFail($decodedId);
           $pageData->QuantityUnits = QuantityUnits::all();
           $data = $this->getUserData('Orders', "Design", 'View', $pageData);
           return view('manager.design.view', $data);
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
           $data = $this->getUserData('Orders', "Design", 'Edit', $pageData);
           return view('manager.design.edit', $data);
       } catch (ModelNotFoundException $e) {
           return abort(404, 'Order not found'); 
       }
   }

   public function QuantityUnits()
   {
       $data = $this->getUserData('Settings',null, 'Quantity Units');
       return view('manager.quantity-units.unit', $data);
   }

   public function QuantityUnitsAdd()
   {
       $data = $this->getUserData('Settings', null,'Quantity Units');
       return view('manager.quantity-units.add', $data);
   }

   public function QuantityUnitsEdit(string $encodedId)
   {
       $decodedId = base64_decode($encodedId); 
       $pageData = new stdClass();
       try {
           $pageData->QuantityUnits = QuantityUnits::all();
           $pageData->ChangedQuantityUnit = QuantityUnits::findOrFail($decodedId);
           try {
               $data = $this->getUserData('Settings',null, 'Quantity Units', $pageData);
             } catch (Exception $e) {
               return abort(500, 'An error occurred while processing your request.');
             }
           return view('manager.quantity-units.edit', $data);
       } catch (ModelNotFoundException $e) {
           return abort(404, 'Quantity Unit not found'); 
       }
   }

   public function Report()
   {
       $data = $this->getUserData('Settings',null, 'Report');
       return view('manager.report', $data);
   }

   public function newOrder()
   {
       $data = $this->getUserData('Orders',"Order" ,  'New');
       return view('common.order.create', $data);
   }

   public function listOrder()
   {
       $data = $this->getUserData('Orders', "Order" , 'List');
       return view('manager.orders.list', $data);
   }

   public function viewOrder(string $encodedId) 
   {
       $decodedId = base64_decode($encodedId);

       try {
           $order = Orders::findOrFail($decodedId);

            if ($order->user_id != Auth::id()) {
                abort(403, 'You can only view orders you created or if an admin gave access.');
            }

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
                   'edit_link' => route('manager.order.Labours',['encodedOrderId'=> $encodedId,"date"=>$date])
               ];
           }

           $pageData->labours = $labourData;
           $pageData->follow_up = Reminders::where('order_id',$order->id)->orderBy('reminder_time')->get();

           $data = $this->getUserData('Orders',"Order" , 'View', $pageData);
           return view('manager.orders.view', $data);
       } catch (ModelNotFoundException $e) {
           return abort(404, 'Order not found'); 
       }
   }

   public function editOrder(string $encodedId) 
   {
       $decodedId = base64_decode($encodedId);

       try {
           $pageData = new stdClass();
           $pageData->order = Orders::findOrFail($decodedId);
           if ($pageData->order->user_id != Auth::id()) {
                abort(403, 'You can only edit orders you created or if an admin gave access.');
           }
           $pageData->QuantityUnits = QuantityUnits::all();
           $data = $this->getUserData('Orders', "Order" , 'Edit', $pageData);
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
           if ($pageData->order->user_id != Auth::id()) {
                abort(403, 'You can only add or update labors for a orders you created or if an admin gave access.');
            }
           $date = $date ?? Carbon::today()->toDateString();
           $pageData->date = $date;
           $pageData->labours = Labour::where('order_id', $decodedOrderId)->where('date', $date)->get();
           $data = $this->getUserData('Orders', "Order" ,'Show Labours', $pageData);
           return view('manager.orders.workers', $data);
       } catch (ModelNotFoundException $e) {
           return abort(404, 'Order not found'); 
       }
   }




}
