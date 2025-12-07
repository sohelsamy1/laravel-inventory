<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\OTPMail;
use App\Helper\JWTToken;
use Illuminate\Http\Request;
use PHPUnit\Runner\Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{

        public function userloginPage(){
        return view('pages.auth.loging-page');
    }

        public function userRegistrationPage(){
        return view('pages.auth.registration-page');
    }
        public function sendOtpPage(){
        return view('pages.auth.send-otp-page');
    }

          public function restPasswordPage(){
        return view('pages.auth.reset-pass-page');
    }

             public function verifyOtpPage(){
        return view('pages.auth.verify-otp-page');
    }

           public function profilePage(){
        return view('pages.dashboard.profile-page');
    }


     public function userRegistration(Request $request){

        try{
            $validator = Validator::make($request->all(), [
                'firstNmae' => 'required|string|max:255',
                'lastNmae' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:3',
                'mobile' => 'required|string|max:15'
           ]);

           if ($validator->fails()) {
           return response()->json([
            'status' => 'fails',
            'message' => 'validation error',
            'errors' => $validator->errors()
           ], 422);
        }

            User::create([
           'first_name' => $request->first_name,
           'last_name' => $request->last_name,
           'email' => $request->email,
           'mobile' => $request->mobile,
           'password' => bcrypt($request->password),
            ]);

            return response()->json([
                'status' => 'success',
                 'message' => 'User Registration successful'
            ], 200);

        }catch(\Exception $e){
            return response()->json([
           'status' => 'fail',
           'message' => 'User Registration failed',
           'error'  => $e->getMessage()
            ], 500);

       }
     }


        // Login
     public function userLogin(Request $request)
     {
        // dd($request->all());
        try {
            // Step 1: Validate input
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:3'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Validation Error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Step 2: Check user existence
            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                // Step 3: Generate token
                $token = JWTToken::createToken($user->email, $user->id);

                return response()->json([
                    'status' => 'success',
                    'message' => 'User Login successful'
                ], 200)->cookie('token', $token, 60 * 24); // 1 day
            } else {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Invalid email or password'
                ], 401);
            }

        } catch (\Throwable $e) {
        //    Log::error($e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                // 'message' => $e->getMessage(),
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
     }


   //Logout
     public function logout(){
        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'User Logout successful'
        // ])->cookie('token', null, -1);
        return redirect('/userLogin')
        ->withCookie(Cookie('token', null, -1));
     }



    public function sendOTP(Request $request)
    {
        try {
            // Step 1: Validate email field
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Validation Error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Step 2: Generate OTP and send mail
            $otp = rand(1000, 9999);
            $email = $request->email;

            Mail::to($email)->send(new OTPMail($otp));

            User::where('email', $email)->update(['otp' => $otp]);

            return response()->json([
                'status' => 'success',
                'message' => 'OTP sent successfully'
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }

    //Verify OTP
    public function verifyOTP(Request $request)
    {
        try {
            // Step 1: Validate input
            $validator = Validator::make($request->all(), [
                'otp' => 'required|digits:4'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Validation Error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Step 2: Find user with correct OTP
            $user = User::where([
                'email' => $request->email,
                'otp' => $request->otp
            ])->first();

            if ($user) {
                // OTP matched, reset OTP and generate token
                User::where('email', $request->email)->update(['otp' => 0]);

                $token = JWTToken::createTokenForResetPassword($request->email);

                return response()->json([
                    'status' => 'success',
                    'message' => 'OTP verified successfully',
                ], 200)->cookie('token', $token, 60 * 5); // 24 hours
            } else {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Invalid OTP or email'
                ], 401);
            }

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }


      // Reset Password
        public function resetPassword(Request $request)
        {
            try {
                // Validate request
                $validator = Validator::make($request->all(), [
                    'password' => 'required|min:3|confirmed'
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => 'fail',
                        'message' => 'Validation Error',
                        'errors' => $validator->errors()
                    ], 422);
                }

                $email = $request->header('email');
                $password = $request->password;

                User::where('email', $email)->update([
                    'password' => Hash::make($password)
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Password reset successfully'
                ], 200);

            } catch (\Throwable $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unable to reset password',
                    'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
                ], 500);
            }
        }

      //userProfile
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



}
