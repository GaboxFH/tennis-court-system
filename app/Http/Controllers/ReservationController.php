<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Reservation_User;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\RainoutMessage;

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

    public function rainout($start, $end)
    {
        $users = Reservation::where('start_datetime', '>', $start)
                            ->where('start_datetime', '<', $end)
                            ->join('reservation_user', 'reservations.id', '=', 'reservation_user.reservation_id')
                            ->join('users', 'reservation_user.user_id', '=', 'users.id')
                            //->select('users.id', 'users.membership_id', 'users.access', 'users.name', 'users.phone', 'users.email', 'users.num_of_notos', 'users.password', 'users.remember_token', 'users.email_verified_at', 'users.created_at', 'users.updated_at')
                            ->select('users.email')
                            ->get();

        foreach ($users as $user) {
            Notification::route('mail', $user->email)->notify(new RainoutMessage());
        }
        return $users;
    }

    public function court_play($year, $month)
    {
        
        return Reservation::whereYear('start_datetime', '=', $year)->whereMonth('start_datetime', '=', $month)
                            ->join('reservation_user', 'reservations.id', '=', 'reservation_user.reservation_id')
                            ->select('reservations.court as courtnumber', Reservation::raw('sum(TIME_TO_SEC(reservations.duration)) as totaltime'))
                            ->groupBy('reservations.court')
                            ->get();
    }
    public function member_play($year, $month)
    {
        
        return Reservation::whereYear('start_datetime', '=', $year)->whereMonth('start_datetime', '=', $month)
                            ->join('reservation_user','reservations.id','=','reservation_user.reservation_id')
                            ->join('users', 'reservation_user.user_id', '=', 'users.id')
                            ->select(Reservation::raw('sum(TIME_TO_SEC(reservations.duration)) as duration'), 'users.name as name')     
                            ->groupBy('users.name')
                            ->get();
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
        // id is not passed in for created events so we must use start and court for this button to work correctly 
        if (!isset($request->item["id"])){
            $existingItem = Reservation::where('start_datetime', $request->item["start"])->where('court', $request->item["category"])->first();
        } else {
            $existingItem = Reservation::find($request->item["id"]);
        }
        
        if($existingItem) {
            $dataIndex = 0;
            $formIndex = 0;

            $existingParticipants = Reservation_User::where('reservation_id', $existingItem->id)->orderBy('user_id')->get();
            $formParticipants = $request->item["ordered_participants_ids"];
            
            if($existingParticipants){
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
                
                $existingItem->title = $request->item["title"];
                $existingItem->method = $request->item["method"];
                $existingItem->user_id = $request->item["host"];
                $existingItem->num_of_members = count($request->item["ordered_participants_ids"]);
                $existingItem->num_of_guests = 0; //hard coded
                $existingItem->save();
                
                return "Update complete";
            }
            
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

