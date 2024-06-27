<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

          // Common method to get user data
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

        if($sectionName == "Dashboard"){
            $pageData->Schedules = $user->schedule()->get();
        }
       
       
        return [
            'title' => $title,
            'sectionName' => $sectionName,
            'userName' => $userName,
            'userId' => $userId,
            "user" => $user,
            'pageData' => $pageData,
            'displayReminder' => $reminder
        ];
    }

    
    public function index()
    {
        $data = $this->getUserData('Dashboard', 'Index');
        return view('admin.index', $data);
    }

}
