<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Reservation_User;
use App\Models\User;
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

    public function reservation_users($res_id, $user_id)
    {        
        $res_host = "";
        $res_participants = [];
        $i = 0;
        $participants = Reservation_User::where('reservation_id', $res_id)->select('user_id')->get();
        if($participants){
            foreach($participants as $participant){
                if($participant->user_id == $user_id){
                    // $res_host = User::where('id', $participant->user_id)->value('name');
                    $res_host = $participant->user_id;
                } else {
                    // $res_participants[$i] = User::where('id', $participant->user_id)->value('name');
                    $res_participants[$i] = $participant->user_id;
                    $i++;
                }
            }
        }
        return $res_users = ([
            'res_host' => $res_host,
            'res_participants' => $res_participants
        ]);
        // return $res_users;
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
        //return "whoop";

        if($existingItem) {
            $dataIndex = 0;
            $formIndex = 0;
            
            $formParticipants = $request->item["ordered_participants_ids"];
            $existingParticipants = Reservation_User::where('reservation_id', $request->item["id"])->orderBy('user_id')->get();
            
            // if(count($existingParticipants)==0){
            //     $existingItem->user_id = 50;
            //     // while($formIndex != count($formParticipants)){
            //     //     Reservation_User::create([
            //     //         'reservation_id' => $request->item["id"],
            //     //         'user_id' => $formParticipants[$i]
            //     //     ]);
            //     //     $formIndex++;
            //     // }
            //     $existingItem->save();
            // } else 
            if($existingParticipants){

                // $sum = $existingParticipants[0]->user_id;
                // for($i=0; $i<count($existingParticipants); $i++){
                //     Reservation_User::create([
                //         'reservation_id' => $request->item["id"],
                //         'user_id' => $existingParticipants[$i]->user_id
                //     ]);
                //     // $sum = $sum + $existingParticipants[0];
                // }
                // for($i=0; $i<count($formParticipants); $i++){
                //     Reservation_User::create([
                //         'reservation_id' => $request->item["id"],
                //         'user_id' => $formParticipants[$i]
                //     ]);
                // }
                
                // $temp_test = Reservation_User::where('reservation_id', $request->item["id"])->select('user_id')->orderBy('user_id')->get();(73);
                // $temp_test->delete();
                // $existingParticipants[3]->delete();
                // participant replacement algorithm for the Reservation_User table
                while($dataIndex != count($existingParticipants) || $formIndex != count($formParticipants)){
                    if($formIndex == count($formParticipants)) {
                    // if($existingParticipants[$dataIndex]->user_id < $formParticipants[$formIndex] || $formIndex == count($request->item["ordered_participants_ids"])){
                    //     // delete where 'user_id', $existingParticipants[$dataIndex]->user_id
                        // $existingItem = Reservation::find($id);

                        // if($existingItem){
                        //     $existingItem->delete();
                        //     return "Item successfully deleted.";
                        // }
                        // $delete = Reservation_User::find($existingParticipants[$dataIndex]->user_id);
                        // if($delete){
                        //     $delete->delete();
                        //     // return "Item successfully deleted.";
                        // }
                        $existingParticipants[$dataIndex]->delete();
                        $dataIndex++;
                    }
                    
                    else if($dataIndex == count($existingParticipants)) {
                    // else if($existingParticipants[$dataIndex] > $formParticipants[$formIndex] || $dataIndex == count($existingParticipants)){
                    //     // create Reservation_User
                    //     //  'user_id', $existingParticipants[$dataIndex]->id
                    //     //  'reservation_id', $request->item["id"]
                        Reservation_User::create([
                            'reservation_id' => $request->item["id"],
                            'user_id' => $formParticipants[$formIndex]
                        ]);
                        $formIndex++;
                    }
                    else if($existingParticipants[$dataIndex]->user_id < $formParticipants[$formIndex]) {
                        // delete
                        $existingParticipants[$dataIndex]->delete();
                        $dataIndex++;
                    }
                    else if($existingParticipants[$dataIndex]->user_id > $formParticipants[$formIndex]){
                        // add
                        Reservation_User::create([
                            'reservation_id' => $request->item["id"],
                            'user_id' => $formParticipants[$formIndex]
                        ]);
                        $formIndex++;
                    }
                    else{
                        $dataIndex++;
                        $formIndex++;
                    }
                }
                
                
                // for ($i = 0; $i < count($existingParticipants); $i++){

                // }
                // if(count($existingParticipants))
                $existingItem->method = $request->item["method"];
                $existingItem->user_id = $request->item["host"];
                // $existingItem->user_id = 14;
                $existingItem->title = $request->item["title"];

                $existingItem->num_of_members = count($request->item["ordered_participants_ids"]);
                // $existingItem->num_of_members = 13;
                $existingItem->num_of_guests = count($existingParticipants);

    //             $existingItem->method = $request->item["method"];
    //             $existingItem->start_datetime = $request->item["start"];
    //             $existingItem->end_datetime = $request->item["end"];
    //             $existingItem->court = $request->item["category"];
    //             $existingItem->num_of_members = $request->item["num_of_members"];
    //             $existingItem->num_of_guests = $request->item["num_of_guests"];
    //             $existingItem->user_id = $request->item["user_id"];
                $existingItem->save();
            }
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

