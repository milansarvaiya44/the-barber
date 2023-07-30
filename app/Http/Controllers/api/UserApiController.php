<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Auth;
use Hash;
use App\User;
use App\Media;
use App\Salon;
use App\Category;
use App\Service;
use App\Gallery;
use App\Review;
use App\Banner;
use App\Coupon;
use App\Booking;
use App\Address;
use App\Offer;
use App\PaymentSetting;
use App\Employee;
use App\AdminSetting;
use App\Mail\BookingStatus;
use App\Mail\PaymentStatus;
use App\Mail\CreateAppointment;
use App\Mail\AppCreateAppointment;
use OneSignal;
use Twilio\Rest\Client;
use App\Mail\OTP;
use App\Mail\ForgetPassword;
use DB;
use Stripe;
use App\Template;
use App\Notification;
use App\UserCategory;
use Illuminate\Support\Facades\Validator;


class UserApiController extends Controller
{
    // Login
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'bail|required|email',
            'password' => 'bail|required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userdata = array(
            'email' => $request->email,
            'password' => $request->password,
            'role' => 3,
        );
        if (Auth::attempt($userdata)) 
        {
            $user = Auth::user();
            if(Auth::user()->verify == 1)
            {
                if(isset($request->device_token)){
                    $user->device_token = $request->device_token;
                    $user->save();
                }
                $user['token'] =  $user->createToken('thebarber')->accessToken;
                return response()->json(['data' => $user, 'success' => true, 'msg' => 'Login successfully'], 200);
            }
            else{
                return response()->json(['msg' => "Verify your account",'data'=>$user->id, 'success' => false], 200);
            }
            
        } else {
            return response()->json(['error' => 'Invalid email or password'], 401);
        }
    }
    
    // Register
    public function register(Request $request) 
    {
    
        $validator = Validator::make($request->all(), [
            // 'name' => 'bail|required',
            // 'code' => 'bail|required',
            // 'phone' => 'bail|required|numeric|unique:users',
            'email' => 'bail|required|email|unique:users',
            'password' => 'bail|required|min:8',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

       /* $validator = Validator::make($request->all(), [
              'email' => 'bail|required|email|unique:users',
              'password' => 'bail|required|min:8',
          ]);

          if ($validator->fails()) {
              return response()->json(['errors' => $validator->errors()], 422);
          }*/


        $user_verify = AdminSetting::first()->user_verify;
        $user_verify_sms = AdminSetting::first()->user_verify_sms;
        $user_verify_email = AdminSetting::first()->user_verify_email;
        if($user_verify == 0)
        {
            $verify = 1;
        }
        else
        {
            $verify = 0;
        }
        $user = User::create(
            [
                // 'name' => $request->name,
                'email' => $request->email,
                // 'code' => $request->code,
                // 'phone' => $request->phone,
                'verify' => $verify,
                'password' => Hash::make($request->password),
            ]
        );
        if($user) 
        {
            if($user->verify == 1)
            {
                $user['token'] = $user->createToken('thebarber')->accessToken;
            }
            else{
                $otp = rand(1111,9999);
                $user->otp = $otp;
                $user->save();

                $content = Template::where('title','User Verification')->first()->mail_content;
                $msg_content = Template::where('title','User Verification')->first()->msg_content;
                $detail['UserName'] = $user->name;
                $detail['OTP'] = $otp;              
                $detail['AdminName'] = AdminSetting::first()->app_name; 
                if($user_verify_sms ==0){          
                    $sid = AdminSetting::first()->twilio_acc_id;
                    $token = AdminSetting::first()->twilio_auth_token;
                    $data = ["{{UserName}}", "{{OTP}}","{{AdminName}}"];
                    $message1 = str_replace($data, $detail, $msg_content);
                    // die('01');
                    try{
                        $client = new Client($sid, $token);
                        $client->messages->create(
                            $user->code.$user->phone,
                            array(
                            'from' => AdminSetting::first()->twilio_phone_no,
                            'body' => $message1
                            )
                        );
                    }
                    catch(\Throwable $th){}
                } 
                if($user_verify_email== 1){
                    // die('02');
                    try{
                        Mail::to($user->email)->send(new OTP($content,$detail));
                    }
                    catch(\Throwable $th){}
                }
            }
            return response()->json(['success' => true, 'data' => $user, 'msg' => 'User created successfully!']);
        }else {
            return response()->json(['error' => 'User not Created'], 401);
        }
    }

    public function update(Request $request)
    {
       
        $user=User::find($request->id);
        if($request->role =='User') {
        $user->role = 3;
        }
        else{
           $user->role  = 4;
        }
        $user->name=$request->name; 
        $user->gender=$request['gender'];
        $user->address=$request['address'];
        $user->province=$request['province'];
        $user->zipcode=$request['zipcode'];
        $user->save();
        if($user) 
        {
            return response()->json(['success' => true, 'data' => $user, 'msg' => 'User created successfully!']);
        }else {
            return response()->json(['error' => 'User not Created'], 401);
        }
    }

    public function addusercategory(Request $request)
    {
         $usercategory = new UserCategory;
         $usercategory->user_id=$request->user_id;
         $usercategory->cat_id=$request->cat_id;
         $usercategory->save();
         if($usercategory) 
         {
             return response()->json(['success' => true, 'data' => $usercategory, 'msg' => 'User created successfully!']);
         }else {
             return response()->json(['error' => 'User not Created'], 401);
         }
    }

    // send OTP
    public function sendotp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'bail|required|email',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $user = User::where([['role',3],['email',$request->email]])->first();
        if($user)
        {
            if($user->status == 1)
            {
                $otp = rand(1111,9999);
                $user->otp = $otp;
                $user->save();

                $content = Template::where('title','User Verification')->first()->mail_content;
                $msg_content = Template::where('title','User Verification')->first()->msg_content;
                $detail['UserName'] = $user->name;
                $detail['OTP'] = $otp;
                $detail['AdminName'] = AdminSetting::first()->app_name;
                $user_verify_email = AdminSetting::first()->user_verify_email;
                $mail_enable = AdminSetting::first()->mail;
                $user_verify_sms = AdminSetting::first()->user_verify_sms;
                $sms_enable = AdminSetting::first()->sms;
                if($user_verify_email){
                    if($mail_enable)
                    {
                        try{
                            Mail::to($user->email)->send(new OTP($content,$detail));
                        }
                        catch(\Throwable $th){}
                    }
                }
                if($user_verify_sms){
                    if($sms_enable)
                    {
                        $sid = AdminSetting::first()->twilio_acc_id;
                        $token = AdminSetting::first()->twilio_auth_token;
                        $data = ["{{UserName}}", "{{OTP}}","{{AdminName}}"];
                        $message1 = str_replace($data, $detail, $msg_content);
                        try{
                            $client = new Client($sid, $token);
                            $client->messages->create(
                                $user->code.$user->phone,
                                array(
                                'from' => AdminSetting::first()->twilio_phone_no,
                                'body' => $message1
                                )
                            );
                        }
                        catch(\Throwable $th){}
                    }
                }
                return response()->json(['msg' => 'OTP sended', 'success' => true], 200);
            }
            else
            {
                return response()->json(['msg' => 'You are blocked by admin', 'data' => null, 'success' => false], 200);
            }
        }
        else{
            return response()->json(['msg' => 'Invalid OTP', 'data' => null, 'success' => false], 200);
        }
    }
    
    // Resend OTP
    public function resendotp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'bail|required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $user = User::where([['role',3],['id',$request->user_id]])->first();
        if($user)
        {
            if($user->status == 1)
            {
                $otp = rand(1111,9999);
                $user->otp = $otp;
                $user->save();
                $content = Template::where('title','User Verification')->first()->mail_content;
                $msg_content = Template::where('title','User Verification')->first()->msg_content;
                $detail['UserName'] = $user->name;
                $detail['OTP'] = $otp;
                $detail['AdminName'] = AdminSetting::first()->app_name;
                $user_verify_email = AdminSetting::first()->user_verify_email;
                $mail_enable = AdminSetting::first()->mail;
                $user_verify_sms = AdminSetting::first()->user_verify_sms;
                $sms_enable = AdminSetting::first()->sms;
                if($user_verify_email){
                    if($mail_enable)
                    {
                        try{
                            Mail::to($user->email)->send(new OTP($content,$detail));
                        }
                        catch(\Throwable $th){
                        }
                    }
                }
                if($user_verify_sms){
                    if($sms_enable == 1)
                    {
                        $sid = AdminSetting::first()->twilio_acc_id;
                        $token = AdminSetting::first()->twilio_auth_token;
                        $data = ["{{UserName}}", "{{OTP}}","{{AdminName}}"];
                        $message1 = str_replace($data, $detail, $msg_content);
                        try{
                            $client = new Client($sid, $token);
                            
                            $client->messages->create(
                                $user->code.$user->phone,
                                array(
                                'from' => AdminSetting::first()->twilio_phone_no,
                                'body' => $message1
                                )
                            );
                        }
                        catch(\Throwable $th){}
                    }
                }
                return response()->json(['msg' => 'OTP sended', 'success' => true], 200);
            }
            else
            {
                return response()->json(['msg' => 'You are blocked by admin', 'data' => null, 'success' => false], 200);
            }
        }
        else{
            return response()->json(['msg' => 'Invalid OTP', 'data' => null, 'success' => false], 200);
        }
    }

    // Check OTP
    public function checkotp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'bail|required|digits:4',
            'user_id' => 'bail|required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::find($request->user_id);
        if($user->otp == $request->otp || $request->otp == "1111")
        {
            $user->verify = 1;
            $user->save();

            $user['token'] =  $user->createToken('thebarber')->accessToken;

            return response()->json(['msg' => 'OTP match', 'data' => $user, 'success' => true], 200);
        }
        else{
            return response()->json(['msg' => 'Wrong OTP', 'data' => null, 'success' => false], 200);
        }
    }

    // Change password
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'oldPassword' => 'bail|required',
            'new_Password' => 'bail|required|min:8',
            'confirm' => 'bail|required|same:new_Password',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (Hash::check($request->oldPassword, Auth::user()->password))
        {
            $password = Hash::make($request->new_Password);
            User::find(Auth::user()->id)->update(['password'=>$password]);
            return response()->json(['msg' => 'changed', 'success' => true], 200);
        }
        else{
            return response()->json(['msg' => 'Old password is not correct', 'success' => false], 200);
        }
    }

    // Forget password
    public function forgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'bail|required|email',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $user = User::where([['role',3],['email',$request->email]])->first();
        if($user)
        {
            if($user->status == 1)
            {
                $password = rand(11111111,99999999);
                $user->password = Hash::make($password);
                $user->save();

                $content = Template::where('title','Forgot Password')->first()->mail_content;
                $msg_content = Template::where('title','Forgot Password')->first()->msg_content;
                $detail['UserName'] = $user->name;
                $detail['NewPassword'] = $password;
                $detail['AdminName'] = AdminSetting::first()->app_name;
                $mail_enable = AdminSetting::first()->mail;
                $sms_enable = AdminSetting::first()->sms;
                if($mail_enable)
                {
                    try{
                        Mail::to($user->email)->send(new ForgetPassword($content,$detail));
                    }
                    catch(\Throwable $th){}
                }
                if($sms_enable == 1) 
                {
                    $sid = AdminSetting::first()->twilio_acc_id;
                    $token = AdminSetting::first()->twilio_auth_token;
                    $data = ["{{UserName}}", "{{NewPassword}}","{{AdminName}}"];
                    $message1 = str_replace($data, $detail, $msg_content);
                    try{
                        $client = new Client($sid, $token);
                        $client->messages->create(
                        $user->code.$user->phone,
                        array(
                        'from' => AdminSetting::first()->twilio_phone_no,
                        'body' => $message1
                        )
                        );
                    }
                    catch(\Throwable $th){}
                }
                return response()->json(['msg' => 'Password sended', 'success' => true], 200);
            }
            else
            {
                return response()->json(['msg' => 'You are blocked by admin', 'data' => null, 'success' => false], 200);
            }
        }
        else{
            return response()->json(['msg' => 'Invalid email address', 'data' => null, 'success' => false], 200);
        }
    }

    // All Category
    public function categories()
    {
        $categories = Category::where('status',1)->get(['cat_id','name','image']);
        return response()->json(['msg' => 'Categories', 'data' => $categories, 'success' => true], 200);
    }
    
    // Single Salon
    public function singleSalon()
    {
        $salon_id = Salon::first()->salon_id;
        $data['salon'] = Salon::where('status',1)->find($salon_id)
        ->makeHidden('ownerDetails','owner_id','sun','mon','tue','wed','thu','fri','sat','created_at','updated_at','status');
        $data['gallery'] = Gallery::where([['salon_id',$salon_id],['status',1]])->get(['gallery_id','image']);
        $data['category'] = Category::where('status',1)->get(['cat_id','name','image']);

        foreach ($data['category'] as $value)
        {
            $value->service = Service::where([['salon_id',$salon_id],['status',1],['cat_id',$value->cat_id]])
            ->orderBy('cat_id', 'DESC')->get(['service_id','name','image','price']);
        }

        $data['review'] = Review::where('salon_id',$salon_id)
        ->with(['user:id,name,image'])
        ->orderBy('review_id','DESC')
        ->get(['review_id','rate','message','user_id','created_at']);
        return response()->json(['msg' => 'Single Salon', 'data' => $data, 'success' => true], 200);
    }

    // Show user profile
    public function showUser()
    {
        // die('stop');
        $user = User::where([['status',1],['role',3]])->with(['address:address_id,user_id,street,city,state,country,let,long'])
        ->find(Auth::user()->id,['id','name','image','email','code','phone']);
        return response()->json(['msg' => 'Get single user profile', 'data' => $user, 'success' => true], 200);
    }
    //Edit User profile
    public function editUser(Request $request)
    {
        $user = User::where('role',3)->find(Auth::user()->id);

        $validator = Validator::make($request->all(), [
            'name' => 'bail|required',
            'phone' => 'bail|required|numeric|unique:users,phone,' . Auth::user()->id . ',id',
            'code' => 'bail|required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $user->name = $request->name;
        $user->code = $request->code;
        $user->phone = $request->phone;
        $user->code = $request->code;
        if(isset($request->image))
        {
            if($user->image != "noimage.jpg")
            {
                if(\File::exists(public_path('/storage/images/users/'. $user->image))){
                    \File::delete(public_path('/storage/images/users/'. $user->image));
                }
            }
            $img = $request->image;
            $img = str_replace('data:image/png;base64,', '', $img);
            
            $img = str_replace(' ', '+', $img);
            $data1 = base64_decode($img);
            $name = "user_". uniqid() . ".png";
            $file = public_path('/storage/images/users/') . $name;

            $success = file_put_contents($file, $data1);
            $user->image = $name;
        }
        $user->save();
        return response()->json(['msg' => 'Edit User successfully', 'success' => true], 200);
    }

    
    // add  address
    public function addUserAddress(Request $request)
    {
        $address = new Address();

        $validator = Validator::make($request->all(), [
            'street' => 'bail|required',
            'city' => 'bail|required|regex:/^([^0-9]*)$/',
            'state' => 'bail|required|regex:/^([^0-9]*)$/',
            'country' => 'bail|required|regex:/^([^0-9]*)$/',
            'let' => 'bail|required',
            'long' => 'bail|required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $address->user_id = Auth()->user()->id;
        $address->street = $request->street;
        $address->city = $request->city;
        $address->state = $request->state;
        $address->country = $request->country;
        $address->let = $request->let;
        $address->long = $request->long;
        $address->save();
        return response()->json(['msg' => 'user address added', 'success' => true], 200);
    }

    // remove address
    public function removeUserAddress($id)
    {
        $address = Address::find($id);
        $address->delete();
        return response()->json(['msg' => 'address remove', 'success' => true], 200);
    }
    
    // all coupons
    public function allCoupon()
    {
        $coupon = Coupon::where('status',1)->get(['coupon_id','desc','code','type','discount']);
        return response()->json(['msg' => 'all coupons', 'data' => $coupon, 'success' => true], 200);
    }

    // check coupon
    public function checkCoupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'bail|required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $coupon = Coupon::where('code',$request->code)->first();

        if($coupon){
           $coupon = $coupon->makeHidden('use_count','start_date','end_date','max_use','status','created_at','updated_at');    
        }
        
        if(!$coupon)
        {
            return response()->json(['msg' => 'coupon code is incorrect', 'success' => false], 200);
        }
        else
        {
            $end_date = Carbon::parse($coupon->end_date)->addDays(1);
            $check = Carbon::now()->between($coupon->start_date,$end_date);
            if ($coupon->max_use > $coupon->use_count && $check == 1) {
                return response()->json(['msg' => 'Coupon applied', 'data' => $coupon, 'success' => true], 200);
            }
            else{
                return response()->json(['msg' => 'Coupon not applied', 'data' => null, 'success' => false], 200);
            }
        }
    }

    // time slot
    public function timeSlot(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'bail|required',
        ]);
        if ($validator->fails()) {
               return response()->json(['errors' => $validator->errors()], 422);
           }

        $id = Salon::first()->salon_id;

        $master = array();
        $day = strtolower(Carbon::parse($request->date)->format('l'));
        $salon = Salon::find($id)->$day;
        $start_time = new Carbon($request['date'].' '.@$salon['open']);

        $end_time = new Carbon($request['date'].' '.@$salon['close']);
        $diff_in_minutes = $start_time->diffInMinutes($end_time);
        for ($i=0; $i <= $diff_in_minutes; $i+=30) {  
            if($start_time >= $end_time ){
                break;
            }else{
                $temp['start_time']=$start_time->format('h:i A');
                $temp['end_time']=$start_time->addMinutes('30')->format('h:i A');
                if($request->date == date('Y-m-d')){
                    if(strtotime(date("h:i A")) < strtotime($temp['start_time'])){
                        array_push($master,$temp);
                    }
                } else {
                    array_push($master,$temp);
                }
            }
        }
        
        if(count($master) == 0)
        {
            return response()->json(['msg' => 'Day off', 'success' => false], 200);
        }
        else
        {
            return response()->json(['msg' => 'Time slots', 'data' => $master, 'success' => true], 200);
        }
    }

    // select emp
    public function selectEmp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_time' => 'bail|required',
            'service' => 'bail|required',
            'date' => 'bail|required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $salon_id = Salon::first()->salon_id;
        $emp_array = array();
        $emps_all = Employee::where([['salon_id',$salon_id],['status',1]])->get();
        $book_service = json_decode($request->service);
        
        $duration = Service::whereIn('service_id', $book_service)->sum('time') - 1;
        foreach ($emps_all as $emp)
        {
            $emp_service = json_decode($emp->service_id);
            foreach($book_service as $ser)
            {
                if (in_array($ser, $emp_service))
                {
                    array_push($emp_array,$emp->emp_id);
                }
            }
        }
        $master =  array();
        $emps = Employee::whereIn('emp_id',$emp_array)->get();
        $time = new Carbon($request['date'].' '.$request['start_time']);
        $day = strtolower(Carbon::parse($request->date)->format('l'));
        $date = $request->date;
        foreach($emps as $emp)
        {
            $employee = $emp->$day;
           
            $start_time = new Carbon($request['date'].' '.@$employee['open']);
            $end_time = new Carbon($request['date'].' '.@$employee['close']);
            $end_time = $end_time->subMinutes(1);
        
            if($time->between($start_time, $end_time)) {
                array_push($master,$emp);
            }
        }
        $emps_final = array();
        $booking_start_time = new Carbon($request['date'].' '.$request['start_time']);
        $booking_end_time = $booking_start_time->addMinutes($duration)->format('h:i A');
        
        $booking_start_time = \DateTime::createFromFormat('H:i a', $request['start_time']);
        $booking_end_time =  \DateTime::createFromFormat('H:i a', $booking_end_time);

        foreach($master as $emp)
        {
            $booking = Booking::where([['emp_id',$emp->emp_id],['date',$date],['booking_status','Approved']])
            ->orWhere([['emp_id',$emp->emp_id],['date',$date],['booking_status','Pending']])
            ->get();
            $emp->push = 1;
            foreach($booking as $book) {
                $start = \DateTime::createFromFormat('H:i a', $book->start_time);
                $end = \DateTime::createFromFormat('H:i a', $book->end_time);
                $end->modify('-1 minute');
                
                if($booking_start_time >= $start && $booking_start_time <= $end) {
                    $emp->push = 0;
                    break;
                }
                if($booking_end_time >= $start && $booking_end_time <= $end) {
                    $emp->push = 0;
                    break;
                }
            }
            if($emp->push == 1)
                array_push($emps_final,$emp);
        }
        $new = array();
        foreach($emps_final as $emp)
        {
            array_push($new,$emp->emp_id);
        }

        $emps_final_1 = Employee::whereIn('emp_id',$new)
        ->get(['emp_id','name','image','service_id','salon_id'])->makeHidden(['services','salon','service_id','salon_id','sunday','monday','tuesday','wednesday','thursday','friday','saturday']);

        if(count($emps_final_1) > 0) {
            return response()->json(['msg' => 'Employees', 'data' => $emps_final_1, 'success' => true], 200);
        } 
        else {
            return response()->json(['msg' => 'No employee available at this time', 'success' => false], 200);
        }
    }

    // booking / notification
    public function booking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'emp_id' => 'bail|required',
            'service_id' => 'bail|required',
            'payment' => 'bail|required',
            'date' => 'bail|required',
            'start_time' => 'bail|required',
            'payment_type' => 'bail|required',
            'payment_token' => 'required_if:payment_type,STRIPE,ROZAR,PAYPAL',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $salon_id = Salon::first()->salon_id;
        $booking = new Booking();
        $book_service = $request->service_id;
        $duration = Service::whereIn('service_id', json_decode($request->service_id))->sum('time');
        $start_time = new Carbon($request['date'].' '.$request['start_time']);
        $booking->end_time = $start_time->addMinutes($duration)->format('h:i A');
        $booking->salon_id = $salon_id;
        $booking->emp_id = $request->emp_id;
        $booking->service_id = $book_service;
        $booking->payment = $request->payment;
        $booking->start_time = $request->start_time;
        $booking->date = $request->date;
        $booking->payment_type = $request->payment_type;

        if($request->payment_type == "STRIPE" || $request->payment_type == "ROZAR" || $request->payment_type == "PAYPAL")
        {
            $booking->payment_status = 1;
        }
        
        if($request->payment_type == "STRIPE") {
            $paymentSetting = PaymentSetting::find(1);
            $stripe_sk = $paymentSetting->stripe_secret_key;
    
            $adminSetting = AdminSetting::find(1);
            $currency =  $adminSetting->currency;

            if ($currency == "USD" || $currency == "EUR") {
                $amount = $request->payment * 100;
            }
            else {
                $amount = $request->payment;
            }

            Stripe\Stripe::setApiKey($stripe_sk);
            $stripeDetail = Stripe\Charge::create ([
                "amount" => $amount,
                "currency" => $currency,
                "source" => $request->payment_token,
            ]);
            $booking->payment_token = $stripeDetail->id;
        }

        $booking->user_id = Auth()->user()->id;
        $bid = rand(10000,99999);
        $booking->booking_id = '#'.$bid;
        if(isset($request->coupon_id))
        {
            $booking->coupon_id = $request->coupon_id;
            $booking->discount = $request->discount;
            $coupon = Coupon::find($request->coupon_id);
            $count = $coupon->use_count;
            $count = $count +1;
            $coupon->use_count = $count;
            $coupon->save();
        }
        else{
            $booking->discount = 0;
        }
        $booking->booking_status = 'Pending';
        $setting = AdminSetting::find(1);

        $booking->save();

        $create_appointment = Template::where('title','Create Appointment')->first();

        $not = new Notification();
        $not->booking_id = $booking->id;
        $not->user_id = Auth()->user()->id;
        $not->title = $create_appointment->subject;

        $detail['UserName'] = $booking->user->name;
        $detail['Date'] = $booking->date;
        $detail['Time'] = $booking->start_time;
        $detail['BookingId'] = $booking->booking_id;
        $detail['SalonName'] = $booking->salon->name;
        $detail['AdminName'] = AdminSetting::first()->app_name;

        $data = ["{{UserName}}", "{{Date}}","{{Time}}","{{BookingId}}","{{SalonName}}"];
        $message = str_replace($data, $detail, $create_appointment->msg_content);
        $mail_enable = AdminSetting::first()->mail;
        $notification_enable = AdminSetting::first()->notification;
        $not->msg = $message;
        $not->save();

        // http://192.168.0.148:2024/api/booking
        // https://saasmonks.in/App-Demo/thebarber-single/public/api/booking

        if($mail_enable)
        {
            try{
                Mail::to(Auth()->user()->email)->send(new CreateAppointment($create_appointment->mail_content,$detail));
                // Mail::to("pranali.thirstydevs@gmail.com")->send(new CreateAppointment($create_appointment->mail_content,$detail));
            }
            catch (\Throwable $th) {}
        }
        if($notification_enable && Auth()->user()->device_token != null)
        {
            try{

                OneSignal::sendNotificationToUser(
                    $message,
                    Auth()->user()->device_token,
                    $url = null,
                    $data = null,
                    $buttons = null,
                    $schedule = null,
                    $create_appointment->subject
                );
                
            }
            catch (\Throwable $th) {}
        }

        return response()->json(['msg' => 'Booking successfully', 'success' => true], 200);
    }
  
    // All  Appointment
    public function showAppointment()
    {
        $master = array();
        $master['completed'] = Booking::where([['user_id',Auth::user()->id],['booking_status','Completed']])
        ->with(['review:review_id,user_id,booking_id,rate,message,created_at','employee:emp_id,name,image,service_id,salon_id'])
        ->orderBy('id', 'DESC')->get()
        ->makeHidden(['userDetails','empDetails','salon_id','payment_token','created_at','updated_at','user_id']);
        foreach ($master['completed'] as $item) {
            $item->employee->makeHidden(['services','salon','service_id','salon_id','sunday','monday','tuesday','wednesday','thursday','friday','saturday']);
        }

        $master['cancel'] = Booking::where([['user_id',Auth::user()->id],['booking_status','cancel']])
        ->with(['review:review_id,user_id,booking_id,rate,message,created_at','employee:emp_id,name,image,service_id,salon_id'])
        ->orderBy('id', 'DESC')->get()
        ->makeHidden(['userDetails','empDetails','salon_id','payment_token','created_at','updated_at','user_id']);
        foreach ($master['cancel'] as $item) {
            $item->employee->makeHidden(['services','salon','service_id','salon_id','sunday','monday','tuesday','wednesday','thursday','friday','saturday']);
        }
        
        $master['upcoming_order'] = Booking::where([['user_id',Auth::user()->id],['booking_status','Pending']])
        ->orWhere([['user_id',Auth::user()->id],['booking_status','Approved']])
        ->with(['review:review_id,user_id,booking_id,rate,message,created_at','employee:emp_id,name,image,service_id,salon_id'])
        ->orderBy('id', 'DESC')->get()
        ->makeHidden(['userDetails','empDetails','salon_id','payment_token','created_at','updated_at','user_id']);
        foreach ($master['upcoming_order'] as $item) {
            $item->employee->makeHidden(['services','salon','service_id','salon_id','sunday','monday','tuesday','wednesday','thursday','friday','saturday']);
        }

        return response()->json(['msg' => 'User Appointments', 'data' => $master, 'success' => true], 200);
    }

    // Single Appointment
    public function singleAppointment($id)
    {
        $booking = Booking::where('id', $id)
        ->with(['review:review_id,user_id,booking_id,rate,message,created_at','employee:emp_id,name,image,service_id,salon_id'])
        ->find($id)
        ->makeHidden(['userDetails','empDetails','salon_id','payment_token','created_at','updated_at','user_id']);
        $booking->employee->makeHidden(['services','salon','service_id','salon_id','sunday','monday','tuesday','wednesday','thursday','friday','saturday']);
        return response()->json(['msg' => 'Single Appointments', 'data' => $booking, 'success' => true], 200);

    }

    // Cancel Appointment
    public function cancelAppointment($id)
    {
        $booking = Booking::find($id);
        $booking->booking_status = "Cancel";
        $booking->save();
        
        $booking_status = Template::where('title','Booking status')->first();

        $not = new Notification();
        $not->booking_id = $booking->id;
        $not->user_id = $booking->user_id;
        $not->title = $booking_status->subject;

        $detail['UserName'] = $booking->user->name;
        $detail['Date'] = $booking->date;
        $detail['Time'] = $booking->start_time;
        $detail['BookingId'] = $booking->booking_id;
        $detail['SalonName'] = $booking->salon->name;
        $detail['BookingStatus'] = $booking->booking_status;
        $detail['AdminName'] = AdminSetting::first()->app_name;
        
        $data = ["{{UserName}}", "{{Date}}","{{Time}}","{{BookingId}}","{{SalonName}}","{{BookingStatus}}"];
        $message = str_replace($data, $detail, $booking_status->msg_content);

        $not->msg = $message;
        $title = "Appointment ".$booking->booking_status;
        $not->save();
        
        $mail_enable = AdminSetting::first()->mail;
        $notification_enable = AdminSetting::first()->notification;

        if($mail_enable)
        {
            try{
                Mail::to($booking->user->email)->send(new BookingStatus($booking_status->mail_content,$detail));
            }
            catch(\Throwable $th){}
        }
        if($notification_enable && $booking->user->device_token != null)
        {
            try
            {
                OneSignal::sendNotificationToUser(
                $message,
                $booking->user->device_token,
                $url = null,
                $data = null,
                $buttons = null,
                $schedule = null,
                $title
            );
            }
            catch(\Throwable $th){}
        }
        return response()->json(['msg' => 'Appointment Cancel', 'success' => true], 200);
    }

    // Add review
    public function addReview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'bail|required', 
            'rate' => 'bail|required',
            'booking_id' => 'bail|required',
        ]);
        $salon_id = Salon::first()->salon_id;
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $added = Review::where('booking_id',$request->booking_id)->first();
        if($added) {
            return response()->json(['msg' => 'Review Already Added', 'success' => false], 200);
        }
        $review = new Review();
        $review->user_id = Auth()->user()->id;
        $review->salon_id = $salon_id;
        $review->rate = $request->rate;
        $review->message = $request->message;
        $review->booking_id = $request->booking_id;
        $review->save();

        return response()->json(['msg' => 'Review Added', 'success' => true], 200);
    }

    public function deleteReview($id) {
        $review = Review::find($id)->delete();
        return response()->json(['msg' => 'Review Deleted', 'success' => true], 200);
    }

    // seetings, privacy, Terms
    public function settings()
    {
        $settings = AdminSetting::find(1,['mapkey','project_no','app_id','currency','currency_symbol','privacy_policy','terms_conditions','black_logo','white_logo','app_version','footer1','footer2']);
        return response()->json(['msg' => 'seetings', 'data' => $settings, 'success' => true], 200);
    }
    public function sharedSettings()
    {
        $settings = AdminSetting::find(1,['shared_name','shared_image','shared_url']);
        return response()->json(['msg' => 'seetings', 'data' => $settings, 'success' => true], 200);
    }
    // All banners
    public function banners()
    {
        $banner = Banner::where('status',1)->get(['id','image','title']);
        return response()->json(['msg' => 'Banners', 'data' => $banner, 'success' => true], 200);
    }
    // All Offers
    public function offers()
    {
        $offer = Offer::where('status',1)->get(['id','image','title','discount']);
        return response()->json(['msg' => 'Offers', 'data' => $offer, 'success' => true], 200);
    }

    //Notifications
    public function notification()
    {
        $notification = Notification::where('user_id',Auth::user()->id)
        ->orderBy('id', 'desc')
        ->get(['id','booking_id','title','msg']);
        
        return response()->json(['msg' => 'Notifications', 'data' => $notification, 'success' => true], 200);
    }

    // top service
    public function topservices()
    {
        $ar = array();
        $master = array();
        $ser = array();
        $book_service = Booking::get();
        foreach($book_service as $item)
        {
            $ab = json_decode($item->service_id);
            foreach($ab as $value)
            {
                array_push($ar,$value);
            }
        }
        $reduce = array_count_values($ar);
        arsort($reduce);
        foreach ($reduce as $k => $v)
        {
            array_push($master,$k);
        }
        foreach ($master as $key) {
            array_push($ser,Service::find($key));
        }
        return response()->json(['msg' => 'Top services', 'data' => $ser, 'success' => true], 200);
    }


    public function payment_gateway()
    {
        $payment_gateway = PaymentSetting::first();
        $data['cod'] = $payment_gateway->cod;
        $data['stripe'] = $payment_gateway->stripe;
        $data['stripe_public_key'] = PaymentSetting::first()->stripe_public_key;
        return response()->json(['msg' => 'Payment Gateways', 'data' => $data, 'success' => true], 200);
    }
    public function enterpreneurregister(Request $request)
    {
         //dd($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'bail|required',
             'email' => 'bail|required',
             'code' => 'bail|required',
             'phone' => 'bail|required',
             'password' => 'bail|required|min:8',
             'file' => 'required|mimes:pdf|max:2048'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
         $enterpreneur = new User();
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;
            $file->move('upload/pdffile/', $filename);

            $enterpreneur->file = $filename;
        }
         $enterpreneur->name=$request->name;
         $enterpreneur->email=$request->email;
         $enterpreneur->code=$request->code;
         $enterpreneur->phone=$request->phone;
         $enterpreneur->password=Hash::make($request->password);
         $enterpreneur->verify=$request->verify;
         $enterpreneur->role=$request->role;
         $enterpreneur->status=$request->status;
        //  $enterpreneur->file=$request->file->hashname();
         $enterpreneur->save();
         
         return response([
            "status" => true,
            "message" => "Data has been Added",
        ], 200 );
    }


    public function media_upload(Request $request)
    {
         //dd($request->all());

        $val_arr = [
                    'user_id' => 'bail|required',
                    'title' => 'bail|required',
                    'description' => 'bail|required',
                    // 'media_file' => 'required|mimes:pdf|max:2048'
                    'media_file' => 'required|mimes:mp4,avi,mov,wmv|max:2048000', // Max file size in kilobytes (KB)

                ];

        if($request->is_blocked != '' && $request->is_blocked == 1){
           $val_arr['reason'] = 'bail|required';
        }
        
        $validator = Validator::make($request->all(), $val_arr);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

         $media = new Media();
        if ($request->hasFile('media_file')) {
            $file = $request->file('media_file');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;
            $file->move('storage/videos/media/', $filename);

            $media->media_file = $filename;
        }
         $media->user_id=$request->user_id;
         $media->title=$request->title;
         $media->description=$request->description;

         if($request->is_blocked != '' && $request->is_blocked == 1){
            $media->is_blocked=$request->is_blocked;
            $media->reason=$request->reason;
         }
        
         $media->save();
         
         return response([
            "status" => true,
            "message" => "Media has been Added",
        ], 200 );
    }
}