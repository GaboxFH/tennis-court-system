<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Reservation_User;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Reservation::orderBy('created_at', 'DESC')->get();
    }

    public function reservation_users(Request $request)
    {
        return Reservation_User::where('reservation_id', $request->item["id"]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Reservation::create([
            'title' => $request->item["title"],
            'method' => $request->item["method"],
            'start_datetime' => $request->item["start"],
            'end_datetime' => $request->item["end"],
            'court' => $request->item["category"],
            'num_of_members' => $request->item["num_of_members"],
            'num_of_guests' => $request->item["num_of_guests"],
            'user_id' => $request->item["user_id"],
        ]);
        
        return "Reservation not created";
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function show(Reservation $reservation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function edit(Reservation $reservation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $existingItem = Reservation::find($request->item["id"]);

        if($existingItem){
//            $existingItem->user_id = $request->item["user_id"];
            $existingItem->title = $request->item["title"];
            $existingItem->method = $request->item["method"];
            $existingItem->start_datetime = $request->item["start"];
            $existingItem->end_datetime = $request->item["end"];
            $existingItem->court = $request->item["category"];
            $existingItem->num_of_members = $request->item["num_of_members"];
            $existingItem->num_of_guests = $request->item["num_of_guests"];
            $existingItem->user_id = $request->item["user_id"];
            $existingItem->save();
        }

        return "Item not found.";

        // Reservation::create([
        //     'title' => $request->item["title"],
        //     'method' => $request->item["method"],
        //     'start_datetime' => $request->item["start"],
        //     'end_datetime' => $request->item["end"],
        //     'court' => $request->item["category"],
        //     'num_of_members' => $request->item["num_of_members"],
        //     'num_of_guests' => $request->item["num_of_guests"],
        //     'user_id' => $request->item["user_id"],
        // ]);
    }

    public function adminupdate(Request $request)
    {
        $existingItem = Reservation::where('start_datetime', $request->item["start"])
                                    ->where('court', $request->item["category"])
                                    ->update(['end_datetime' => $request->item["end"]]);

//         if($existingItem){
// //            $existingItem->user_id = $request->item["user_id"];
//             $existingItem->title = $request->item["title"];
//             $existingItem->method = $request->item["method"];
//             $existingItem->start_datetime = $request->item["start"];
//             $existingItem->end_datetime = $request->item["end"];
//             $existingItem->court = $request->item["category"];
//             $existingItem->num_of_members = $request->item["num_of_members"];
//             $existingItem->num_of_guests = $request->item["num_of_guests"];
//             $existingItem->user_id = $request->item["user_id"];
//             $existingItem->save();
//         }

        return "Item not found.";

        // Reservation::create([
        //     'title' => $request->item["title"],
        //     'method' => $request->item["method"],
        //     'start_datetime' => $request->item["start"],
        //     'end_datetime' => $request->item["end"],
        //     'court' => $request->item["category"],
        //     'num_of_members' => $request->item["num_of_members"],
        //     'num_of_guests' => $request->item["num_of_guests"],
        //     'user_id' => $request->item["user_id"],
        // ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $existingItem = Reservation::find($id);

        if($existingItem){
            $existingItem->delete();
            return "Item successfully deleted.";
        }

        return "Item not found.";
    }
}


// Schema::create('reservations', function (Blueprint $table) {
//     $table->id();
//     $table->string('method');
//     $table->datetime('start_datetime');
//     $table->datetime('end_datetime');
//     $table->integer('court');
//     $table->integer('num_of_members');
//     $table->integer('num_of_guests');
//     $table->integer('user_id');
//     $table->timestamps();
// });

// Online (Member)
// Online (Member)
// Call (admin)
// Walk in (admin)
// Tennis Pro (TP)

// Reservation::create([
//     'title' => "Kenia Rangel",
//     'method' => "Call",
//     'start_datetime' => "2021-03-26 10:00:00",
//     'end_datetime' => "2021-03-26 12:00:00",
//     'court' => 3,
//     'num_of_members' => 2,
//     'num_of_guests' => 0,
//     'user_id' => 2,
// ]);

