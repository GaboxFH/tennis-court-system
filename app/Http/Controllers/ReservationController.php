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


    public function court_play($month)
    {

    }
    public function member_play($month)
    {
        // new Date().getMonth()
        
        // return Reservation::orderBy('created_at')->get();
        // User::select('id','name')->get();
        // return Reservation::get();
        $user_info = User::select('id','name')->get();

        return Reservation::where('start_datetime','>=','2021-03-01 00:00:00')
                            ->where('start_datetime','<','2021-04-01 00:00:00')
                            // ->join('Reservation','Reservation.user_id','=','Users.id')
                            // ->groupBy('user_id');
                            ->get();

        // return Reservation::where('start_datetime','<=','2020-04-01')->get();
                            // ->where('start_datetime','<','2020-04-11 00:00:00')
                            

        // return User::select('id','name')->leftJoin();
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
                    $res_host = $participant->user_id;
                } else {
                    $res_participants[$i] = $participant->user_id;
                    $i++;
                }
            }
        }
        return $res_users = ([
            'res_host' => $res_host,
            'res_participants' => $res_participants
        ]);
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
        // (new Date(this.cal_events[i].start).getTime() < this.new_endtime
        // && new Date(this.cal_events[i].end).getTime() >  this.dragEvent.start)
        // $conflictingReservations = Reservation::where('start', '>=', $request->item["start"])
        // ->where('start', '<', $request->item["end"])
        // ->where('end', '>', $request->item["start"])
        // ->where('category', $request->item["category"])
        //                             ->count();
        $conflictingReservations = Reservation::where('start', '<', $request->item["end"])
                                            ->where('end', '>', $request->item["start"])
                                            ->where('category', $request->item["category"])
                                            ->count();
        // $conflictingReservations = Reservation::where('start', '<', $request->item["start"])
        //                             ->count();
        if($conflictingReservations){
            return "error";
        } 
        // $existingItem = Reservation::where('start_datetime', $request->item["start"])
        //                             ->where('court', $request->item["category"])
        //                             ->update(['end_datetime' => $request->item["end"]]);
        // $datetime1 = strtotime($request->item["start"]);
        // $datetime2 = strtotime($request->item["end"]);

        $datetime1 = $request->item["start"]/1000;
        $datetime2 = $request->item["end"]/1000;

        $hours = floor(($datetime2-$datetime1)/60/60);
        $mins = ($datetime2-$datetime1)/60%60;
        $secs = ($datetime2-$datetime1)%60;

        $duration = date("H:i:s", mktime($hours, $mins, $secs));
        // Reservation::create([
        //     'name' => "noah",
        //     'method' => "call",
        //     'start' => 1617631200000,
        //     'end' => 1617633000000,
        //     'duration' => "00:30:00",
        //     'category' => "13",
        //     'num_of_members' => 0,
        //     'num_of_guests' => 0,
        //     'host_id' => 123,
        //     'timed' => 1,
        // ]);
        $newEvent = Reservation::create([
            'name' => $request->item["name"],
            'method' => $request->item["method"],
            'start' => $request->item["start"],
            'end' => $request->item["end"],
            // 'duration' => "00:30:00",
            'duration' => $duration,
            'category' => $request->item["category"],
            'num_of_members' => $request->item["num_of_members"],
            'num_of_guests' => $request->item["num_of_guests"],
            'host_id' => $request->item["host_id"],
            'timed' => $request->item["timed"],
        ]);
        
        return $newEvent;
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
        // id is not passed in for created events so we must use start and court for this button to work correctly 
        // if (!isset($request->item["id"])){
        //     $existingItem = Reservation::where('start_datetime', $request->item["start"])->where('court', $request->item["category"])->first();
        // } else {
        $conflictingReservations = Reservation::where('id', '!=', $request->item["id"])
        ->where('start', '<', $request->item["end"])
        ->where('end', '>', $request->item["start"])
        ->where('category', $request->item["category"])
        ->count();
        // $conflictingReservations = Reservation::where('start', '<', $request->item["start"])
        //                             ->count();
        if($conflictingReservations){
            return "error";
        } 

        $existingItem = Reservation::find($request->item["id"]);
        // }
        
        if($existingItem) {
            $dataIndex = 0;
            $formIndex = 0;
            
            $existingParticipants = Reservation_User::where('reservation_id', $existingItem->id)->orderBy('user_id')->get();
            $checkParticipants = true;

            if(isset($request->item["ordered_participants_ids"])){
                if($request->item["ordered_participants_ids"] == -1){
                    $checkParticipants = false;
                } else {
                    $formParticipants = $request->item["ordered_participants_ids"];
                    $existingItem->num_of_members = count($formParticipants); 
                }
            } else {
                $formParticipants = [];
            } 
            // return "here";
            // return count($formParticipants);
            // return "here";
            if($checkParticipants){
                    // participant replacement algorithm for the Reservation_User table
                while($dataIndex != count($existingParticipants) || $formIndex != count($formParticipants)){
                    if($formIndex == count($formParticipants)) {
                        $existingParticipants[$dataIndex]->delete();
                        $dataIndex++;
                    }
                    else if($dataIndex == count($existingParticipants)) {
                        Reservation_User::create([
                            'reservation_id' => $existingItem->id,
                            'user_id' => $formParticipants[$formIndex]
                        ]);
                        $formIndex++;
                    }
                    else if($existingParticipants[$dataIndex]->user_id < $formParticipants[$formIndex]) {
                        $existingParticipants[$dataIndex]->delete();
                        $dataIndex++;
                    }
                    else if($existingParticipants[$dataIndex]->user_id > $formParticipants[$formIndex]){
                        Reservation_User::create([
                            'reservation_id' => $existingItem->id,
                            'user_id' => $formParticipants[$formIndex]
                        ]);
                        $formIndex++;
                    }
                    else{
                        $dataIndex++;
                        $formIndex++;
                    }
                }
            }    
            

            $datetime1 = $request->item["start"]/1000;
            $datetime2 = $request->item["end"]/1000;

            $hours = floor(($datetime2-$datetime1)/60/60);
            $mins = ($datetime2-$datetime1)/60%60;
            $secs = ($datetime2-$datetime1)%60;

            $duration = date("H:i:s", mktime($hours, $mins, $secs));

            $existingItem->name = $request->item["name"];
            $existingItem->method = $request->item["method"];
            $existingItem->start = $request->item["start"];
            $existingItem->end = $request->item["end"];
            $existingItem->duration = $duration;
            $existingItem->category = $request->item["category"];
            $existingItem->host_id = $request->item["host_id"];
            $existingItem->num_of_guests = $request->item["num_of_guests"];; //hard coded
            $existingItem->save();
            
            return "success";
            
            
        }
        return "Item not found.";
    }

    public function adminupdate(Request $request)
    {
        $existingItem = Reservation::where('start_datetime', $request->item["start"])
                                    ->where('court', $request->item["category"])
                                    ->update(['end_datetime' => $request->item["end"]]);
        return "Event drag";
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

