<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Action;

class ActionController extends Controller
{
    public function index()
    {
        return Action::orderBy('created_at', 'DESC')->get();
    }

    public function store(Request $request)
    {   
        Action::create([
            'reservation_id' => $request->item["reservation_id"],
            'user_id' => $request->item["user_id"],
            'type' => $request->item["type"],
            'curr_start_datetime' => $request->item["curr_start_datetime"],
            'curr_end_datetime' => $request->item["curr_end_datetime"],
            'curr_court' => $request->item["curr_court"]
        ]);

        return "Action not created";
    }

    public function update(Request $request)
    {
        // $existingUser = Action::find($id);

        // if($existingUser){
        //     $existingUser->membership_id = $request->item["membership_id"];
        //     $existingUser->name = $request->item["name"];
        //     $existingUser->phone = $request->item["phone"];
        //     $existingUser->email = $request->item["email"];
        //     $existingUser->save();
        // }

        return "Action not found.";
    }

    public function destroy(Request $request)
    {
        // $existingUser = Action::find($id);

        // if($existingUser){
        //     $existingUser->delete();
        //     return "Item successfully deleted.";
        // }

        return "Action not found.";
    }

}

// Schema::create('actions', function (Blueprint $table) {
//     $table->id();
//     $table->integer('reservation_id');
//     $table->integer('user_id');
//     $table->string('type');
//     $table->timestamps();
//     $table->datetime('curr_start_datetime');
//     $table->datetime('curr_end_datetime');
//     $table->integer('curr_court');
//     $table->datetime('prev_start_datetime')->nullable();
//     $table->datetime('prev_end_datetime')->nullable();
//     $table->integer('prev_court')->nullable();
//     $table->integer('new_user_id')->nullable();
//     $table->integer('deleted_user_id')->nullable();
// });

// Action::create([
//     'reservation_id' => "1",
//     'user_id' => "1",
//     'type' => "create",
//     'curr_start_datetime' => "2021-03-25 09:00:00",
//     'curr_end_datetime' => "2021-03-25 11:00:00",
//     'curr_court' => "3"
// ]);