<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Reminders;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class reminderController extends Controller
{
    public function store(Request $request){  
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'password' => 'nullable|string',
            'reminder_time' => 'required|date_format:Y-m-d\TH:i|after:now',
            'priority' => 'required',
        ]);
    
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user_id = Auth::id();

        try {
            $reminders = new Reminders();
            $reminders->user_id = $user_id;
            $reminders->title = $request->title;
            $reminders->description = $request->description;
            $reminders->reminder_time = $request->reminder_time;
            $reminders->priority = $request->priority;
            $reminders->save();
            // return back()->with('message', 'Reminder set successfully!');

            $user = User::find($user_id);

            if($user->role == 'admin'){
                return redirect()->route('admin.reminder.list')->with('message', 'Reminder set successfully!');
            }else{
                return redirect()->route('manager.reminder.list')->with('message', 'Reminder set successfully!');
            }

            } catch (\Exception $e) {
            Log::create([
                'message' => 'Error setting reminder',
                'level' => 'warning',
                'type' => 'error',
                'ip_address' => $request->ip(),
                'context' => 'web',
                'source' => 'setting_reminder',
                'extra_info' => json_encode(['user_agent' => $request->header('User-Agent'),'error'=>$e])
            ]);
            return back()->with('error', 'Error setting reminder ');
        }

    }

    public function update(Request $request, $encodedId){
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'password' => 'nullable|string',
            'reminder_time' => 'required|date_format:Y-m-d\TH:i|after:now',
            'priority' => 'required|in:1,2,3',
        ]);
    
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $decodedId = base64_decode($encodedId); 

        try {
            $reminder = Reminders::findOrFail($decodedId);
            $user_id = Auth::id();
            if($user_id != $reminder->user_id){
                abort(403, 'You can only update reminders you created.');
            }
            $reminder->title = $request->title;
            $reminder->description = $request->description;
            $reminder->reminder_time = $request->reminder_time;
            $reminder->priority = $request->priority;
            $reminder->is_completed = 0;
            $reminder->save(); 
            return back()->with('message', 'Reminder Updated successfully!');
          } catch (ModelNotFoundException $e) {
            return back()->with('error', 'Reminder not found.');
          } catch (\Exception $e) {
            Log::create([
                'message' => 'Error updating reminder',
                'level' => 'warning',
                'type' => 'error',
                'ip_address' => $request->ip(),
                'context' => 'web',
                'source' => 'update_reminder',
                'extra_info' => json_encode(['user_agent' => $request->header('User-Agent'),'error'=>$e])
            ]);
            return back()->with('error', 'Error updating reminder.');
          }
    }

    public function destroy($encodedId, Request $request)
    {

        $decodedId = base64_decode($encodedId); 
        try {
            $reminder = Reminders::findOrFail($decodedId);
            $user_id = Auth::id();
            
            if ($user_id != $reminder->user_id) {
                Log::create([
                    'message' => 'Error delete reminder',
                    'level' => 'warning',
                    'type' => 'security',
                    'ip_address' => $request->ip(),
                    'context' => 'web',
                    'source' => 'delete_reminder',
                    'extra_info' => json_encode(['user_agent' => $request->header('User-Agent')])
                ]);
            abort(403, 'You can only delete reminders you created.');
            }
            
            $reminder->delete();
            return back()->with('message', 'Reminder deleted successfully!');
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'Reminder not found.');
        } catch (\Exception $e) {
            Log::create([
                'message' => 'Error delete reminder',
                'level' => 'warning',
                'type' => 'error',
                'ip_address' => $request->ip(),
                'context' => 'web',
                'source' => 'delete_reminder',
                'extra_info' => json_encode(['user_agent' => $request->header('User-Agent'),'error'=>$e])
            ]);
            return back()->with('error', 'Error deleting reminder.');
        }
    }
    
    public function is_completed(Request $request)
    {
        $decodedId = base64_decode($request->id);
    
        try {
            $reminder = Reminders::findOrFail($decodedId);
    
            $user_id = Auth::id();
    
            if ($user_id != $reminder->user_id) {
                abort(403, 'You can only update reminders you created.');
            }
    
            $reminder->is_completed = 1;
    
            $originalReminderTime = $reminder->reminder_time;
    
            $reminder->reminder_time = $originalReminderTime;
    
            $reminder->save();
    
            return response()->json(['success' => true]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Reminder not found'], 404);
        } catch (\Exception $e) {
            Log::create([
                'message' => 'Error updating reminder',
                'level' => 'danger',
                'type' => 'error',
                'ip_address' => $request->ip(),
                'context' => 'web',
                'source' => 'update_reminder',
                'extra_info' => json_encode(['user_agent' => $request->header('User-Agent')])
            ]);
            return response()->json(['success' => false, 'message' => 'Error updating reminder.'], 500);
        }
    }
    
}
