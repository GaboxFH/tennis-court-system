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

    public function getUserReservations($user_id)
    {
        // return $user_id;
        // $reservations = Reservation::orderBy('created_at', 'DESC')->get();
        $reservations = Reservation::where('host_id',$user_id)->orderBy('start', 'DESC')->get();
        return $reservations;
        // $participants = Reservation_User::where('reservation_id', $res_id)->select('user_id')->get();
        // $participants = Reservation_User::where('reservation_id', $res_id)->select('user_id')->get();

    }

    public function daily($start, $end)
    {
        $reservations = Reservation::where('start', '>', $start)
                            ->where('start', '<', $end)
                            ->select('reservations.start as time', 'reservations.category as courtnumber', 'reservations.num_of_guests as guests', 'reservations.host_id as host')
                            ->get();
        $hosts = array();
        foreach ($reservations as $r) {
            $name = User::where('id', '=', $r->host)
                    ->select('name')
                    ->get();
            $r->host = $name[0]->name;
        }
        return $reservations;
    }

    public function rainout($start, $end)
    {
        $users = Reservation::where('start', '>', $start)
                            ->where('start', '<', $end)
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
    // function hhmmssToMillisec($string){
    //     $time = explode(":", $string);
    
    //     $hour = $time[0] * 60 * 60 * 1000;
    //     $minute = $time[1] * 60 * 1000;
    //     $sec = $time[2] * 1000;
    
    //     $result = $hour + $minute + $sec;
    //     return $result;
    // }

    public function avail_reservations($search_type,$date,$n)
    {

        $duration_options = ["0"=>true,"1"=>true,"2"=>true,"3"=>true];
        $avail_durs = ([
            '1' => $duration_options,'2' => $duration_options,'3' => $duration_options,'4' => $duration_options,'5' => $duration_options,'6' => $duration_options,'7' => $duration_options,'8' => $duration_options,
            '9' => $duration_options,'10' => $duration_options,'11' => $duration_options,'12' => $duration_options,'13' => $duration_options,'14' => $duration_options,'15' => $duration_options,'16' => $duration_options,'17' => $duration_options,
        ]);
        $avail_durs_slots = ([
            '1' => $duration_options,'2' => $duration_options,'3' => $duration_options,'4' => $duration_options,'5' => $duration_options,'6' => $duration_options,'7' => $duration_options,'8' => $duration_options,
            '9' => $duration_options,'10' => $duration_options,'11' => $duration_options,'12' => $duration_options,'13' => $duration_options,'14' => $duration_options,'15' => $duration_options,'16' => $duration_options,'17' => $duration_options,
        ]);

        $timeslot_options = ["0"=>true,"1"=>true,"2"=>true,"3"=>true,"4"=>true,"5"=>true,"6"=>true,"7"=>true,"8"=>true,"9"=>true,"10"=>true,"11"=>true,"12"=>true,"13"=>true,"14"=>true,"15"=>true,"16"=>true,"17"=>true,"18"=>true,"19"=>true,"20"=>true,"21"=>true,"22"=>true,"23"=>true];
        $avail_slots = ([
            '1' => $timeslot_options,'2' => $timeslot_options,'3' => $timeslot_options,'4' => $timeslot_options,'5' => $timeslot_options,'6' => $timeslot_options,'7' => $timeslot_options,'8' => $timeslot_options,
            '9' => $timeslot_options,'10' => $timeslot_options,'11' => $timeslot_options,'12' => $timeslot_options,'13' => $timeslot_options,'14' => $timeslot_options,'15' => $timeslot_options,'16' => $timeslot_options,'17' => $timeslot_options,
        ]);
        $dur_avail_slots = ([
            '1' => $timeslot_options,'2' => $timeslot_options,'3' => $timeslot_options,'4' => $timeslot_options,'5' => $timeslot_options,'6' => $timeslot_options,'7' => $timeslot_options,'8' => $timeslot_options,
            '9' => $timeslot_options,'10' => $timeslot_options,'11' => $timeslot_options,'12' => $timeslot_options,'13' => $timeslot_options,'14' => $timeslot_options,'15' => $timeslot_options,'16' => $timeslot_options,'17' => $timeslot_options,
        ]);

        if($search_type == 0){
            $start_string = explode(":", '08:00:00');

            $hour = $start_string[0] * 60 * 60 * 1000;
            $minute = $start_string[1] * 60 * 1000;
            $sec = $start_string[2] * 1000;
        
            $start_time = $hour + $minute + $sec;

            $start_t_input = $date+ $start_time;
            $halfHr = 30*60*1000;

            $reservations = Reservation::where('start','>',$date)
                            ->where('start','<',($date+24*60*60*1000))
                            ->get();
            foreach($reservations as $res){
                // return $start_t_input;
                $check_time = $start_t_input;
                for($x = 0; $x <= 23; $x++){
                    if($check_time>=$res->start && $check_time<$res->end){
                        $avail_slots[$res->category][$x]=false;
                    }
                    $check_time+=$halfHr;
                }
            }
            for($c = 1; $c <= 17; $c++){
                for($x = 0; $x <= 23; $x++){
                    for($i=0;$i<$n; $i++){
                        if($x+$i>23){
                            $dur_avail_slots[$c][$x]=false;
                        } 
                        else if($avail_slots[$c][$x+$i]==false){
                            $dur_avail_slots[$c][$x]=false;
                        }
                    }
                }
            }
            return $dur_avail_slots;
        } else{
            
            $start_string = explode(":", $n);

            $hour = $start_string[0] * 60 * 60 * 1000;
            $minute = $start_string[1] * 60 * 1000;
            $sec = $start_string[2] * 1000;
        
            $start_time = $hour + $minute + $sec;
            
            $possible_slots = 4;
            // 66600000
            if($start_time==(66600000)){
                $possible_slots = 3;
            } else if($start_time==(68400000)){
                $possible_slots = 2;
            } else if($start_time==(70200000)){
                $possible_slots = 1;
            } else if($start_time==(73800000)){
                $possible_slots = 0;
            }

            $start_t_input = $date+ $start_time;
            $halfHr = 30*60*1000;
            
            // $reservations = Reservation::where('start','>=',$start_t_input)
            //                 ->where('start','<',($start_t_input+2*60*60*1000))
            //                 ->get();
            $reservations = Reservation::where('start','>',$date)
                            ->where('start','<',($date+24*60*60*1000))
                            ->get();
            foreach($reservations as $res){
                // return $start_t_input;
                $check_time = $start_t_input;
                for($x = 0; $x < $possible_slots; $x++){
                    if($check_time>=$res->start && $check_time<$res->end){
                        $avail_durs[$res->category][$x]=false;
                    }
                    $check_time+=$halfHr;
                }
            }
            // return $start_time;
            for($c = 1; $c <= 17; $c++){
                $bust = false;
                for($x = 0; $x < 4; $x++){
                    if($x>($possible_slots-1)){
                        $bust = true;
                    }
                    if(!$bust){
                        if($avail_durs[$c][$x]==false){
                            $bust = true;
                            $avail_durs_slots[$c][$x]=false;
                        } 
                    } else {
                        $avail_durs_slots[$c][$x]=false;
                    }
                }
            }
            return $avail_durs_slots;
            // return $avail_durs;
        } 
        
        return Reservation::orderBy('created_at', 'DESC')->get();
    }

    public function court_play($time)
    {
        return Reservation::where('start', '>', $time)
                            ->join('reservation_user', 'reservations.id', '=', 'reservation_user.reservation_id')
                            ->select('reservations.category as courtnumber', Reservation::raw('sum(TIME_TO_SEC(reservations.duration)) as totaltime'))
                            ->groupBy('reservations.category')
                            ->get();
    }
    public function member_play($time)
    {
        
        return Reservation::where('start', '>', $time)
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

    public function memberStore(Request $request)
    {
        $conflictingReservations = Reservation::where('start', '<', $request->item["end"])
                                            ->where('end', '>', $request->item["start"])
                                            ->where('category', $request->item["category"])
                                            ->count();
        
        if($conflictingReservations){
            return "error";
        } 

        $datetime1 = $request->item["start"]/1000;
        $datetime2 = $request->item["end"]/1000;

        $hours = floor(($datetime2-$datetime1)/60/60);
        $mins = ($datetime2-$datetime1)/60%60;
        $secs = ($datetime2-$datetime1)%60;

        $duration = date("H:i:s", mktime($hours, $mins, $secs));
       
        $newEvent = Reservation::create([
            'name' => $request->item["name"],
            'method' => $request->item["method"],
            'start' => $request->item["start"],
            'end' => $request->item["end"],
            'duration' => $duration,
            'category' => $request->item["category"],
            'num_of_members' => $request->item["num_of_members"],
            'num_of_guests' => $request->item["num_of_guests"],
            'host_id' => $request->item["host_id"],
            'timed' => $request->item["timed"],
        ]);
        
        if(isset($request->item["ordered_participants_ids"])){
            foreach($request->item["ordered_participants_ids"] as $id){
                Reservation_User::create([
                    'reservation_id' => $newEvent->id,
                    'user_id' => $id
                ]);
            }
        }

        return $newEvent;
    }
   
    public function store(Request $request)
    {
        $conflictingReservations = Reservation::where('start', '<', $request->item["end"])
                                            ->where('end', '>', $request->item["start"])
                                            ->where('category', $request->item["category"])
                                            ->count();
        
        if($conflictingReservations){
            return "error";
        } 
        // return $request->item["ordered_participants_ids"];
        // if(isset($request->item["ordered_participants_ids"])){
        //     for($i;$i<$request->item["ordered_participants_ids"]){
                
        //         Reservation_User::create([
        //             'reservation_id' => $existingItem->id,
        //             'user_id' => $formParticipants[$formIndex]
        //         ]);
        //     }
        // }
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
       
        $newEvent = Reservation::create([
            'name' => $request->item["name"],
            'method' => $request->item["method"],
            'start' => $request->item["start"],
            'end' => $request->item["end"],
            'duration' => $duration,
            'category' => $request->item["category"],
            'num_of_members' => $request->item["num_of_members"],
            'num_of_guests' => $request->item["num_of_guests"],
            'host_id' => $request->item["host_id"],
            'timed' => $request->item["timed"],
        ]);
        
        return $newEvent;
    }

    
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

