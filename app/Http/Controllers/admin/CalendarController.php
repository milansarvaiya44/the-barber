<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use MaddHatter\LaravelFullcalendar\Facades\Calendar;
use App\Booking;
use App\Salon;
use App\Service;
use App\User;
use App\Employee;
use DateTime;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        // if(isset($salon_id))
        // {
            $salon_id = Salon::where('owner_id',Auth()->user()->id)->first()->salon_id;
          
            $booking = Booking::where('salon_id',$salon_id)->get();
           
            $event = [];
          
            foreach ($booking as $row)
            {
              
                $start_time = strtolower(Carbon::parse($row->start_time)->format('H:i'));
                $end_time = strtolower(Carbon::parse($row->end_time)->format('H:i'));
    
                $_start = date('Y-m-d H:i:s', strtotime("$row->date $start_time"));
                $_end = date('Y-m-d H:i:s', strtotime("$row->date $end_time"));
    
                if ($row->booking_status == "Cancelled")
                {
                    $bgColor = "rgba(251, 175, 190, .5)";
                    $textColor = "#b3092b";
                }
                else if($row->booking_status == "Pending")
                {
                    $bgColor = "rgba(203, 210, 246, .5)";
                    $textColor = "#2236a8";
                }
                else if($row->booking_status == "Approved")
                {
                    $bgColor = "rgba(136, 230, 247, .5)";
                    $textColor = "#05879e";
                }
                else if($row->booking_status == "Completed")
                {
                    $bgColor = "rgba(147, 231, 195, .5)";
                    $textColor = "#1a8a59";
                }
                else{
                    $bgColor = "rgba(11, 11, 11, .5)";
                    $textColor = "#111111";
                }
                // return $row;
                $event[] = Calendar::event(
                // $row->user->name,
                   $row->user['name'],
                    false,
                    $_start,
                    $_end,
                    $row->id,
                    [
                        'color' => $bgColor,
                        'textColor' => $textColor,
                    ]
                );
            }
            $calendar = Calendar::addEvents($event)->setCallbacks(['eventClick' => 'eventClicked']);
        // }
        // else
        // {
        //     $booking = Booking::get();           
        //     $event = [];
        //     $start_time = strtolower(Carbon::parse()->format('H:i'));
        //     $end_time = strtolower(Carbon::parse()->format('H:i'));

        //     $_start = date('Y-m-d H:i:s', strtotime("$start_time"));
        //     $_end = date('Y-m-d H:i:s', strtotime("$end_time"));
        //     $bgColor = "rgba(11, 11, 11, .5)";
        //     $textColor = "#111111";
        //     $event[] = Calendar::event(
        //         'demo',
        //         false,
        //         $_start,
        //         $_end,
        //         [
        //             'color' => $bgColor,
        //             'textColor' => $textColor,
        //         ]
        //     );
        
        // $calendar = Calendar::addEvents($event)->setCallbacks(['eventClick' => 'eventClicked']);         
            
        // }
        $salon = Salon::where('owner_id', Auth()->user()->id)->first();
        $users = User::where([['status',1],['role',3]])->get();
        $enterpreneur=User::where([['status',0],['role',4]])->get();
        $emps = Employee::where([['status',1],['salon_id',$salon->salon_id]])->get();
        if(isset($salon->salon_id))
        {
            $services = Service::where([['salon_id',$salon->salon_id],['isdelete',0]])->get();
        }
        else
        {
            $services = Service::get();
        }
       

        return view('admin.pages.calendar', compact('booking','calendar','emps','services','users','enterpreneur'));
    }
   
}
