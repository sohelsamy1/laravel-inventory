<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\OTPMail;
use App\Helper\JWTToken;
use Illuminate\Http\Request;
use PHPUnit\Runner\Exception;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function userRegistration(Request $request){
        try{
            User::create([
           'first_name' => $request->first_name,
           'last_name' => $request->last_name,
           'email' => $request->email,
           'mobile' => $request->mobile,
           'password' => $request->password,
            ]);
            return response()->json([
                'status' => 'success',
                 'message' => 'User Registration successful'
            ], 200);
        }catch(\Throwable $e){
            return response()->json([
           'status' => 'failed',
           'message' => 'User Registration failed'
            ], 200);

       }
     }


       public function userLogin(Request $request){
        $user= User::where(['email' => $request->email, 'password' => $request->password])->select('id')->first();

        if($user !== null){
         $token = JWTToken::createToken($request->email, $user->id);
         return response()->json([
                'status' => 'success',
                 'message' => 'User Login successful'
         ], 200)->cookie('token', $token, time() + 60 * 60 * 24);
        }else{
            return response()->json([
                'status' => 'failed',
                 'message' => 'User Login failed'
            ]);
        }


    }

     public function logout(){
        return response()->json([
            'status' => 'success',
            'message' => 'User Logout successful'
        ])->cookie('token', null, -1);
     }

     public function sendOTP(Request $request){
        $email = $request->email;
        $otp = rand(1000, 9999);
        $user = User::where('email', $email)->first();

        if($user !== null){
            //send otp to the email address
       Mail::to($email)->send(new OTPMail($otp));
       User::where('email', $email)->update(['otp' => $otp]);

          return response()->json([
            'status' => 'success',
            'message' => 'OTP sent successfully'
        ], 200);

        }else{
            return response()->json([
            'status' => 'failed',
            'message' => 'Unable to send OTP'
        ], 200);
      }
     }

     public function verifyOtp(Request $request){
         $email = $request->email;
         $otp = $request->otp;

         $user = User::where('email', $email)->first();

         if($user !== null){
            //update otp to 0
            User::where('email', $email)->update(['otp' => 0]);
            $token = JWTToken::createTokenForResetPassword($email);

            return response()->json([
                 'status' => 'success',
                'message' => 'OTP verified successfully'
            ])->cookie('token', $token, time() + 60 * 60 * 24);
         }else{
            return response()->json([
                'status' => 'failed',
                'message' => 'Unable to send OTP'
            ]);
         }
        }



        public function resetPassword(Request $request){

        try{
          $email = $request->header('email');
          $password = $request->password;
          User::where('email', $email)->update(['password' => $password]);
            return response()->json([
                'status' => 'success',
                'message' => 'Password Reset successfully'
            ], 200);

           }catch(\Throwable $e){
        return response()->json([
                'status' => 'failed',
                'message' => 'Unable to reset password'
         ]);
        }
       }

    public function userProfile(Request $request){
        $email = $request->header('email');
        $user = User::where('email', $email)->first();
        return response()->json([
                'status' => 'success',
                'message' => 'User Profile',
                'data' => $user
         ], 200);
        }

        //user profile update
    public function updateUserProfile(Request $request){

      try{
         $email = $request->header('email');
       $first_name = $request->first_name;
       $last_name = $request->last_name;
       $mobile = $request->mobile;
       $password = $request->password;

       User::where('email', $email)->update([
             'first_name' => $first_name,
              'last_name' => $last_name,
              'mobile' => $mobile,
              'password' => $password
       ]);

         return response()->json([
             'status' => 'success',
                'message' => 'User Profile update successfully',
         ], 200);

        }catch(Exception $e){
            return response()->json([
                'status' => 'failed',
                'message' => 'Unable to update your profile',
            ], 200);
        }
        }

    //       public function restPasswordPage(){
    //     return view('pages.auth.reset-pass-page');
    // }

    //        public function sendOtpPage(){
    //     return view('pages.auth.send-otp-page');
    // }

    //          public function verifyOtpPage(){
    //     return view('pages.auth.verify-otp-page');
    // }

    //        public function profilePage(){
    //     return view('pages.dashboard.profile-page');
    // }


}
