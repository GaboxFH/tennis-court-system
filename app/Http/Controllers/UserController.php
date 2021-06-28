<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    //
    public function index()
    {
        return User::orderBy('name')->get();
    }

    public function update(Request $request, $id)
    {
        // return $request;
        $existingUser = User::find($id);

        // empty fields
        if(!isset($request->item["f_name"]) || !isset($request->item["l_name"]) 
        || !isset($request->item["email"]) || !isset($request->item["membership_id"])){
            return [
                'status' => 'error',
                'message' => 'Empty Fields',
            ];
        }
        // validate membership_id
        if(!preg_match("/^[1-9][0-9]{0,15}$/", $request->item["membership_id"])){
            return [
                'status' => 'error',
                'message' => 'Invalid Membership ID',
            ];
        }
        // validate phone
        $phone = $request->item["phone"];
        if(isset($phone)){
            if(preg_match("/^[1-9][0-9]{9}$/",$phone)){
                $phone = substr($phone, 0, 3) .'-'.
                        substr($phone, 3, 3) .'-'.
                        substr($phone, 6);
            }
            if(!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $phone)) {
                return [
                    'status' => 'error',
                    'message' => 'Invalid Phone Number',
                ];
            }
        }
        // validate email
        $uniqueEmail = User::where('email',$request->item["email"])->get();
        if(count($uniqueEmail)>1){
            return [
                'status' => 'error',
                'message' => 'Email already in use',
            ];
        } else if(count($uniqueEmail) == 1 && $uniqueEmail[0]->id != $request->item["id"]){
            return [
                'status' => 'error',
                'message' => 'Email already in use',
            ];
        }

        if($existingUser){
            $existingUser->name = $request->item["f_name"] . " " . $request->item["l_name"];
            $existingUser->f_name = $request->item["f_name"];
            $existingUser->l_name = $request->item["l_name"];
            $existingUser->access = $request->item["access"];
            $existingUser->membership_id = $request->item["membership_id"];
            $existingUser->email = $request->item["email"];
            $existingUser->phone = $phone;
            $existingUser->save();
        } else{
            return [
                'status' => 'error',
                'message' => 'User does not exist',
            ];
        }

        return [
            'status' => 'success',
            'message' => 'User Updated',
        ];
    }

    public function store(Request $request)
    {   
        // empty fields
        if(!isset($request->item["f_name"]) || !isset($request->item["l_name"]) 
        || !isset($request->item["email"]) || !isset($request->item["membership_id"])){
            return [
                'status' => 'error',
                'message' => 'Empty Fields',
            ];
        }
        // validate membership_id
        if(!preg_match("/^[1-9][0-9]{0,15}$/", $request->item["membership_id"])){
            return [
                'status' => 'error',
                'message' => 'Invalid Membership ID',
            ];
        }
        // validate phone
        $phone = $request->item["phone"];
        if(isset($phone)){
            if(preg_match("/^[1-9][0-9]{9}$/",$phone)){
                $phone = substr($phone, 0, 3) .'-'.
                        substr($phone, 3, 3) .'-'.
                        substr($phone, 6);
            }
            if(!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $phone)) {
                return [
                    'status' => 'error',
                    'message' => 'Invalid Phone Number',
                ];
            }
        }
        // validate email
        $uniqueEmail = User::where('email',$request->item["email"])->get();
        if(count($uniqueEmail)>=1){
            return [
                'status' => 'error',
                'message' => 'Email already in use',
            ];
        } 
        
        // $data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';
        // $pass = substr(str_shuffle($data), 0, 16);
        // 'password' => Hash::make($pass),

        User::create([
            'membership_id' => $request->item["membership_id"],
            'access' => $request->item["access"],
            'name' => $request->item["f_name"] . " " . $request->item["l_name"],
            'f_name' => $request->item["f_name"],
            'l_name' => $request->item["l_name"],
            'phone' => $phone,
            'email' => $request->item["email"],
            // 'password' => Hash::make($pass),
        ]);

        return [
            'status' => 'success',
            'message' => 'User Created',
        ];

        
    }

    public function updatePass(Request $request){

        if(!isset($request->item["id"]) || !isset($request->item["pass"])){
            return [
                'status' => 'error',
                'message' => 'Empty Field',
            ];
        }
        if(!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/", $request->item["pass"])) {
            return [
                'status' => 'error',
                'message' => 'Minimum eight characters, at least one letter and one number:',
            ];
        }
        $existingUser = User::find($request->item["id"]);
        if($existingUser){
            $existingUser->password = Hash::make($request->item["pass"]);
            $existingUser->save();

            return [
                'status' => 'success',
                'message' => 'Password Changed',
            ];
        }
        return [
            'status' => 'error',
            'message' => 'User Not Found',
        ];
        // Hash::make($pass)
    }

    public function destroy($id)
    {
        $existingUser = User::find($id);

        if($existingUser){
            $existingUser->delete();
            return [
                'status' => 'success',
                'message' => 'User Deleted',
            ];
        }

        return [
            'status' => 'error',
            'message' => 'User Not Found',
        ];
    }
}


// User::create([
//     'membership_id' => 123456,
//     'access' => 'Admin',
//     'name' => 'Noah Smith',
//     'f_name' => 'Noah',
//     'l_name' => 'Smith',
//     'phone' => '7278716624',
//     'email' => 'nosmith16@gmail.com',
//     'password' => Hash::make('password123')
// ]);


// User::create([
//     'membership_id' => 4912,
//     'access' => 'Single',
//     'name' => 'Trish Smith',
//     'f_name' => 'Trish',
//     'l_name' => 'Smith',
//     'phone' => '7274036624',
//     'email' => 't@gmail.com',
//     'password' => Hash::make('password123')
// ]);

