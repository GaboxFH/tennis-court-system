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
        return User::orderBy('name', 'DESC')->get();
    }

    public function update(Request $request, $id)
    {
        $existingUser = User::find($id);

        if($existingUser){
            $existingUser->membership_id = $request->item["membership_id"];
            $existingUser->name = $request->item["name"];
            $existingUser->phone = $request->item["phone"];
            $existingUser->email = $request->item["email"];
            $existingUser->save();
        }

        return "Item not found.";
    }

    public function store(Request $request)
    {   
        // request()->validate([
        //     'name' => 'required',
        //     'phone' => 'required',
        //     'email' => ['min:7', 'max:10']
        // ]);
        
        User::create([
            'membership_id' => $request->item["membership_id"],
            'access' => $request->item["access"],
            'name' => $request->item["name"],
            'phone' => $request->item["phone"],
            'email' => $request->item["email"],
            'password' => Hash::make($request->item["password"]),
        ]);
        // return back();
        // return redirect()->to('/users'); 


        // return view('/users');
        // return redirect(route('/home'));
        return "User not created";
    }

    public function destroy($id)
    {
        $existingUser = User::find($id);

        if($existingUser){
            $existingUser->delete();
            return "Item successfully deleted.";
        }

        return "Item not found.";
    }
}


// User::create([
//     'membership_id' => 5915,
//     'access' => 'Admin',
//     'name' => 'Noah Smith',
//     'phone' => '7278716624',
//     'email' => 'nosmith16@gmail.com',
//     'password' => Hash::make('password123')
// ]);

