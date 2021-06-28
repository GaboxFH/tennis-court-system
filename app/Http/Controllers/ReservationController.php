<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Reservation_User;
use App\Models\Guests;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\RainoutMessage;
use Illuminate\Support\Facades\DB;


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

    public function guests(){
        return Guests::orderBy('created_at', 'DESC')->get();
    }

    public function curr_reservations($time){
        // return $time;
        sleep(1);
        $previous = Reservation::where('timed',1)
            ->where('end','<', $time)
            ->orderBy('date', 'DESC')
            ->orderBy('end', 'DESC')
            ->orderBy('category')
            ->get();

        for($x = 0; $x < count($previous); $x++){
            $sql = "select users.name
            from users 
            INNER JOIN reservation_user ON users.id=reservation_user.user_id
            where reservation_id = " . $previous[$x]->id;
            $names = DB::select($sql);
            $List = array_column($names,'name');
            $previous[$x]->ppts = implode(', ', $List);
        }    


        $current = Reservation::where('timed',1)
            ->where('start','<=', $time)
            ->where('end','>=', $time)
            ->orderBy('start', 'DESC')
            ->get();
        for($x = 0; $x < count($current); $x++){
            $sql = "select users.name
            from users 
            INNER JOIN reservation_user ON users.id=reservation_user.user_id
            where reservation_id = " . $current[$x]->id;
            
            $names = DB::select($sql);
            $List = array_column($names,'name');
            $current[$x]->ppts = implode(', ', $List);
        }    

        $future = Reservation::where('timed',1)
            ->where('start','>', $time)
            ->orderBy('date')
            ->orderBy('start')
            ->get();

        for($x = 0; $x < count($future); $x++){
            // $previous = Reservation_User::where('reservation_id',$test_res->id)->select('user_id')->get();
            // $sql = "select users.name
            // from users 
            // INNER JOIN reservation_user ON users.id=reservation_user.user_id
            // where reservation_id = " . $future[$x]->id;
            $sql = "SELECT
                users.name,
                users.id
            FROM
                users
                INNER JOIN reservation_user ON users.id = reservation_user.user_id
            WHERE
                reservation_id = " . $future[$x]->id . "
            UNION
            SELECT
                name,
                0 AS id
            FROM
                guests
            WHERE
                reservation_id = " . $future[$x]->id;


            $names = DB::select($sql);
            // $future[$x]->ppts
            for($i=0; $i<count($names); $i++){
                if($names[$i]->id == $future[$x]->host_id){
                    $host = $names[$i];
                    unset($names[$i]);
                    array_unshift($names,$host);
                } else if($names[$i]->id == 0){
                    $names[$i]->name = $names[$i]->name . "**";
                }
            }

            $List = array_column($names,'name');
            $future[$x]->ppts = implode(', ', $List);
        }    


        return [
            "previous" => $previous,
            "current" => $current,
            "future" => $future
        ];
    }

    public function getUserReservations($user_id,$time)
    {
        $sql = "SELECT reservations.*
                FROM (
                    SELECT
                        reservation_user.reservation_id,
                        reservation_user.user_id
                    FROM
                        users
                        INNER JOIN reservation_user ON users.id = reservation_user.user_id
                    WHERE
                        reservation_user.user_id = " . $user_id . "
                    ) AS T
                    INNER JOIN reservations ON T.reservation_id = reservations.id
                    WHERE
                    END < " . $time . "
                    ORDER BY
                        date DESC,
                        END DESC, 
                        category";

        $previous = DB::select($sql);
        // $previous = Reservation::where('timed',1)
        // ->where('host_id', $user_id)
        // ->where('end','<', $time)
        //     ->orderBy('date', 'DESC')
        //     ->orderBy('end', 'DESC')
        //     ->orderBy('category')
        //     ->get();
        for($x = 0; $x < count($previous); $x++){
            $sql = "select users.name
            from users 
            INNER JOIN reservation_user ON users.id=reservation_user.user_id
            where reservation_id = " . $previous[$x]->id;
            $names = DB::select($sql);
            $List = array_column($names,'name');
            $previous[$x]->ppts = implode(', ', $List);
        }


        $sql = "SELECT reservations.*
                FROM (
                    SELECT
                        reservation_user.reservation_id,
                        reservation_user.user_id
                    FROM
                        users
                        INNER JOIN reservation_user ON users.id = reservation_user.user_id
                    WHERE
                        reservation_user.user_id = " . $user_id . "
                    ) AS T
                    INNER JOIN reservations ON T.reservation_id = reservations.id
                    WHERE
                    END >= " . $time . "
                    ORDER BY
                        date,
                        END, 
                        category";

        $future = DB::select($sql);

        // $future = Reservation::where('timed',1)
        //     ->where('host_id', $user_id)
        //     ->where('end','>', $time)
        //         ->orderBy('date')
        //         ->orderBy('start')
        //         ->orderBy('category')
        //         ->get();
        for($x = 0; $x < count($future); $x++){
            $sql = "select users.name
            from users 
            INNER JOIN reservation_user ON users.id=reservation_user.user_id
            where reservation_id = " . $future[$x]->id;
            $names = DB::select($sql);
            $List = array_column($names,'name');
            $future[$x]->ppts = implode(', ', $List);
        }    

        return [
            "previous" => $previous,
            "future" => $future     
        ];
        // return $user_id;
        // $reservations = Reservation::orderBy('created_at', 'DESC')->get();
        // $reservations = Reservation::where('host_id',$user_id)->orderBy('start', 'DESC')->get();
        // return $reservations;
        // $participants = Reservation_User::where('reservation_id', $res_id)->select('user_id')->get();
        // $participants = Reservation_User::where('reservation_id', $res_id)->select('user_id')->get();

    }
    
    public function getClosed(){
        return Reservation::where('timed',0)->orderBy('created_at', 'DESC')->get();
    }

    public function getEvents($date){
        // return "hi";
        $events = Reservation::where('timed',1)->where('date',$date)->orderBy('created_at', 'DESC')->get();
        $closed_court_periods = Reservation::where('timed',0)->where('date',$date)->orderBy('created_at', 'DESC')->get();
        return [
            'events' => $events,
            'closed_court_periods' => $closed_court_periods,
        ];
    }

    public function getGuests($id){
        return Guests::where('user_id',$id)->get();
    }

    public function reservation_guests($res_id){
        return Guests::where('reservation_id',$res_id)->select('name')->get();
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

    public function closeOptions(Request $request){
        if(count($request->item['courts'])==0 || !isset($request->item['start']) || !isset($request->item['end'])){
            return [
                'status' => 'error',
                'message' => 'Missing required fields',
            ];
        } 
        else if($request->item['start'] > $request->item['end']) {
            return [
                'status' => 'error',
                'message' => 'Invalid Time Range',
            ];
        }
        // return count($request->item['courts']);
        if(count($request->item['courts']) > 1){
            $sql = "select * from reservations where category IN (". implode(', ', $request->item['courts']) .")  
            and (reoccur_id is null or reoccur_id!=0)
            and timed = 1
            and start < ". $request->item['end'] . "
            and end > ". $request->item['start'];

        } else {
            $sql = "select * from reservations where category IN (". $request->item['courts'][0] .")  
            and (reoccur_id is null or reoccur_id!=0)
            and timed = 1
            and start < ". $request->item['end'] . "
            and end > ". $request->item['start'];
        }
        // return $sql;
        $conflictingReservations = DB::select($sql);

        // $conflictingReservations = Reservation::where('start', '<', $request->item['end'])
        //                                         ->where('end', '>', $request->item['start'])
        //                                         ->count();


        return [
            'status' => 'success',
            'message' => null,
            'reservations' => count($conflictingReservations)
        ];
    }

    public function closeCourts(Request $request){
        // duration
        // sleep(2);
        $datetime1 = $request->item['start']/1000;
        $datetime2 = $request->item['end']/1000;

        $hours = floor(($datetime2-$datetime1)/60/60);
        $mins = ($datetime2-$datetime1)/60%60;
        $secs = ($datetime2-$datetime1)%60;

        $duration = date("H:i:s", mktime($hours, $mins, $secs));

        $groupId = 0;
        foreach($request->item['courts'] as $courtNum){
            $conflictingReservations = Reservation::where('start', '<', $request->item['end'])
            ->where('end', '>', $request->item['start'])
            ->where('category', $courtNum)
            ->where('timed', 1)
            ->get();

            
            

            $closedRecord = Reservation::create([
                'name' => $request->item['reason'],
                'method' => "Closed",
                'date' => $request->item['date'],
                'start' => $request->item['start'],
                'end' => $request->item['end'],
                'duration' => $duration,
                'category' => $courtNum,
                'num_of_members' => 0,
                'num_of_guests' => 0,
                // 'host_id' => $request->item["host_id"],
                'reoccur_id' => $groupId,
                'timed' => 0,
            ]);
            if($groupId==0){
                Reservation::where('id', $closedRecord->id)->update(['reoccur_id' => $closedRecord->id]);
                $groupId = $closedRecord->id;
            }

            foreach($conflictingReservations as $res){
                $res->reoccur_id = 0;
                // $res->reoccur_id = $groupId;
                $res->save();
                // $res->delete();
            }
        }
        return [
            'status' => 'success',
            'message' => 'Courts Closed',
        ];
    }

    public function getClosure($closure_id){
        $closure_courts = Reservation::where('reoccur_id', $closure_id)->get();
        
        $courts = array();
        if($closure_courts->count() > 0){
            // return $closure_courts[0].start;
            $start = $closure_courts[0]->start;
            $end = $closure_courts[0]->end;
            foreach($closure_courts as $court){
                $courts[] = $court->category;
            }
        }else {
            return [
                'status' => 'error',
                'message' => 'Closure not found'
            ];
        }
        return [
            'courts' => $courts,
            'start' => $start,
            'end' => $end,
        ];
    }

    public function deleteClosure($closure_id){        
        $closure_period = Reservation::where('reoccur_id', $closure_id)->get();
        $courts = array();
        if($closure_period->count() > 0){
            $start = $closure_period[0]->start;
            $end = $closure_period[0]->end;
            foreach($closure_period as $court){
                $courts[] = $court->category;
            }
        } else {
            return [
                'status' => 'error',
                'message' => 'Closure not found'
            ];
        }
        $closure_info = [
            'courts' => $courts,
            'start' => $start,
            'end' => $end,
        ];
        // return $closure_info['courts'];
        // return implode(', ', $closure_info['courts']);
        if(count($closure_info['courts']) > 1){
            $sql = "select * from reservations where category IN (". implode(', ', $closure_info['courts']) .")  
            and reoccur_id=0 and start
            and start < ". $closure_info['end'] . "
            and end > ". $closure_info['start'];
        } else {
            $sql = "select * from reservations where category IN (". $closure_info['courts'][0] .")  
            and reoccur_id=0 and start
            and start < ". $closure_info['end'] . "
            and end > ". $closure_info['start']; 
        }

        // $sql = "select * from reservations";
        // $closed_courts = Reservation::selectRaw('price * ? as price_with_tax', [1.0825])
        // $closed_courts = DB::select('select * from reservations where reoccur_id = ?', [0]);
        // $closed_courts = DB::select('select * from reservations where reoccur_id = ?', [0]);
        // $closed_courts = DB::select('select * from reservations');
        $closed_courts = DB::select($sql);
        
        // return [
        //     'noah' => $closed_courts[0],
        //     'status' => 'success',
        //     'message' => 'Courts Re-opened'
        // ];

        $closed_court_periods = Reservation::where('timed', 0)
        ->where('reoccur_id','!=', $closure_id)
        ->get();

        // return $closed_courts;
        foreach($closed_courts as $res){
            $reopen = true;
            foreach($closed_court_periods as $check){
                
                if($res->category == $check->category 
                && $res->start < $check->end
                && $res->end > $check->start){
                    // return [
                    //     'status' => $res,
                    //     'message' => $check
                    // ];
                    $reopen = false;
                }
            }
            if($reopen){
                $res_update = Reservation::where('id', $res->id)->update(['reoccur_id' => null]);
            }
        }
        // $closed_courts = Reservation::where('reoccur_id',0)
        // ->where('start','<',$closure_info['end'])
        // ->where('end','>',$closure_info['start'])
        // ->get();

        // return $closed_courts;
        // $closure_info = getClosure($closure_id);
        // return $closure_info;
        // $reopen = Reservation::where('reoccur_id', 0)->where('start','<',$closure_info->end)
        // SQL category for item is equal to any element in array 'courts'

        foreach($closure_period as $event){
            // $count += 1;
            $event->delete();
        }
        

        return [
            'status' => 'success',
            'message' => 'Courts Re-opened'
        ];
    }

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
        $res_host = null;
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
    public function deleteReoccur($id){
        
        $existingItem = Reservation::find($id);

        $futureEvents = Reservation::where('reoccur_id', $existingItem->reoccur_id)
        ->where('start', '>=', $existingItem->start)
        ->get();
        // $count = 0;
        foreach($futureEvents as $event){
            // $count += 1;
            $event->delete();
        }
        // return $existingItem;
        return [
            'status' => 'success',
            'message' => 'Reoccuring Events Deleted',
        ];
    
        // return [
        //     'status' => 'error',
        //     'message' => 'bad option',
        // ]; 
    }

    public function storeReoccur(Request $request){
        if($request->item["reoccur_type"] == "weekly"){

            $currentTime = (int)(microtime(true)*1000);

            if($currentTime>$request->item["start"]){
                return 
                [
                    'status' => 'error',
                    'message' => 'Cannot start reoccur from past event',
                ];
            }
            $st_seconds = $request->item["start"] / 1000;
            $st_datetime = date("Y-m-d H:i:s", $st_seconds);
            $st_weeklater = $st_datetime;
            
            $en_seconds = $request->item["end"] / 1000;
            $en_datetime = date("Y-m-d H:i:s", $en_seconds);
            $en_weeklater = $en_datetime;

            $num_of_weeks = 200;
            for ($x = 0; $x < $num_of_weeks; $x++) {
                $st_weeklater = date("Y-m-d H:i:s", strtotime("+7 day", strtotime($st_weeklater)));
                $en_weeklater = date("Y-m-d H:i:s", strtotime("+7 day", strtotime($en_weeklater)));
                $st_new = strtotime($st_weeklater) * 1000;
                $en_new = strtotime($en_weeklater) * 1000;

            
                $conflictingReservations = Reservation::where('start', '<', $en_new)
                                                ->where('end', '>', $st_new)
                                                ->where('category', $request->item["category"])
                                                ->count();
            
                if($conflictingReservations){
                    $numberofsecs = $st_new/1000;
                    return 
                    [
                        'status' => 'error',
                        'message' => 'Time Conflict on '.date('m/d/Y', $numberofsecs),
                    ];
                } 
            }

            $st_seconds = $request->item["start"] / 1000;
            $st_datetime = date("Y-m-d H:i:s", $st_seconds);
            $st_weeklater = $st_datetime;
            
            $en_seconds = $request->item["end"] / 1000;
            $en_datetime = date("Y-m-d H:i:s", $en_seconds);
            $en_weeklater = $en_datetime;
            
            for ($x = 0; $x < $num_of_weeks; $x++) {
                $st_weeklater = date("Y-m-d H:i:s", strtotime("+7 day", strtotime($st_weeklater)));
                $en_weeklater = date("Y-m-d H:i:s", strtotime("+7 day", strtotime($en_weeklater)));
                $st_new = strtotime($st_weeklater) * 1000;
                $en_new = strtotime($en_weeklater) * 1000;

                // duration
                $datetime1 = $st_new/1000;
                $datetime2 = $en_new/1000;

                $hours = floor(($datetime2-$datetime1)/60/60);
                $mins = ($datetime2-$datetime1)/60%60;
                $secs = ($datetime2-$datetime1)%60;

                $duration = date("H:i:s", mktime($hours, $mins, $secs));
                Reservation::create([
                    'name' => $request->item["name"],
                    'method' => $request->item["method"],
                    'date' => date('Y-m-d', ($st_new/1000)),
                    'start' => $st_new,
                    'end' => $en_new,
                    'duration' => $duration,
                    'category' => $request->item["category"],
                    'num_of_members' => $request->item["num_of_members"],
                    'num_of_guests' => $request->item["num_of_guests"],
                    'host_id' => $request->item["host_id"],
                    'reoccur_id' => $request->item["id"],
                    'timed' => $request->item["timed"],
                ]);

            }
            
            $existingItem = Reservation::where('id', $request->item["id"])->update(['reoccur_id' => $request->item["id"]]);

            return [
                'status' => 'success',
                'message' => 'Event Repeated Weekly',
            ];
        } 
        return [
            'status' => 'error',
            'message' => 'bad option',
        ];
    }

    public function memberStore(Request $request)
    {   
        $currentTime = (int)(microtime(true)*1000);

        if($currentTime>$request->item["start"]){
            return [
                'status' => 'error',
                'message' => 'Invalid Start Time',
            ];
        }
        // return $request;
        $num_of_guests = 0;
        $guest_list = $request->item["guests"];
        if(count($guest_list)>0){
            $num_of_guests = count($guest_list);
            for($i=0; $i<count($guest_list); $i++){
                if(is_null($guest_list[$i])){
                    return [
                        'status' => 'error',
                        'message' => 'All Fields Required',
                    ];
                } else{
                    if(isset($guest_list[$i]["name"])){
                        $guest_list[$i] = $guest_list[$i]["name"];
                    }
                }
            }
        }
        
        $conflictingReservations = Reservation::where('start', '<', $request->item["end"])
                                            ->where('end', '>', $request->item["start"])
                                            ->where('category', $request->item["category"])
                                            ->count();
        
        if($conflictingReservations){
            return [
                'status' => 'error',
                'message' => 'Time Conflict',
            ];
        } 

        $datetime1 = $request->item["start"]/1000;
        $datetime2 = $request->item["end"]/1000;

        $hours = floor(($datetime2-$datetime1)/60/60);
        $mins = ($datetime2-$datetime1)/60%60;
        $secs = ($datetime2-$datetime1)%60;

        $duration = date("H:i:s", mktime($hours, $mins, $secs));
       
        $eventTitle = null;
        if(isset($request->item["host_id"])){
            $host = User::where('id', $request->item["host_id"])->first();
            if($host->access == "Single" || $host->access == "Family"){
                $eventTitle = $host->name . "'s Event";
            } else if($host->access == "Tennis Pro"){
                $eventTitle = $host->name;
            } else {
                $eventTitle = $request->item["method"];
            }
        } else {
            $eventTitle = $request->item["method"];
        }

        $newEvent = Reservation::create([
            // 'name' => $request->item["name"],
            'name' => $eventTitle,
            'method' => $request->item["method"],
            'date' => $request->item["date"],
            'start' => $request->item["start"],
            'end' => $request->item["end"],
            'duration' => $duration,
            'category' => $request->item["category"],
            'num_of_members' => $request->item["num_of_members"],
            'num_of_guests' => $num_of_guests,
            'host_id' => $request->item["host_id"],
            'timed' => $request->item["timed"],
        ]);
        
        // add rows to reservation_users (participants (members))
        if(isset($request->item["ordered_participants_ids"])){
            foreach($request->item["ordered_participants_ids"] as $id){
                Reservation_User::create([
                    'reservation_id' => $newEvent->id,
                    'user_id' => $id
                ]);
            }
        }

        // add rows to guests (guests (non-member))
        if(count($guest_list)>0){
            foreach($guest_list as $fields){
                Guests::create([
                    'name' => $fields,
                    'user_id' => $request->item["host_id"],
                    'reservation_id' => $newEvent->id
                ]);
            }
        }
        return [
            'status' => 'success',
            'message' => 'Reservation Created',
        ];
    }
   
    public function adminStore(Request $request)
    {
        if(!isset($request->item["category"]) 
        || !isset($request->item["start"])
        || !isset($request->item["end"])        
        ){
            return [
                'status' => 'error',
                'message' => 'Missing Fields',
            ];
        }

        $currentTime = (int)(microtime(true)*1000);

        if($request->item["start"]>$request->item["end"]
        || $currentTime>$request->item["start"]){
            return [
                'status' => 'error',
                'message' => 'Invalid Start Time',
            ];
        }
        // return $request;
        $num_of_guests = 0;
        $guest_list = $request->item["guests"];
        if(count($guest_list)>0){
            $num_of_guests = count($guest_list);
            for($i=0; $i<count($guest_list); $i++){
                if(is_null($guest_list[$i])){
                    return [
                        'status' => 'error',
                        'message' => 'All Fields Required',
                    ];
                } else{
                    if(isset($guest_list[$i]["name"])){
                        $guest_list[$i] = $guest_list[$i]["name"];
                    }
                }
            }
        }
        
        $conflictingReservations = Reservation::where('start', '<', $request->item["end"])
                                            ->where('end', '>', $request->item["start"])
                                            ->where('category', $request->item["category"])
                                            ->count();
        
        if($conflictingReservations){
            return [
                'status' => 'error',
                'message' => 'Time Conflict',
            ];
        } 

        $datetime1 = $request->item["start"]/1000;
        $datetime2 = $request->item["end"]/1000;

        $hours = floor(($datetime2-$datetime1)/60/60);
        $mins = ($datetime2-$datetime1)/60%60;
        $secs = ($datetime2-$datetime1)%60;

        $duration = date("H:i:s", mktime($hours, $mins, $secs));
       
        $eventTitle = null;
        if(isset($request->item["host_id"])){
            $host = User::where('id', $request->item["host_id"])->first();
            if($host->access == "Single" || $host->access == "Family"){
                $eventTitle = $host->name . "'s Event";
            } else if($host->access == "Tennis Pro"){
                $eventTitle = $host->name;
            } else {
                $eventTitle = $request->item["method"];
            }
        } else {
            $eventTitle = $request->item["method"];
        }

        $newEvent = Reservation::create([
            // 'name' => $request->item["name"],
            'name' => $eventTitle,
            'method' => $request->item["method"],
            'date' => $request->item["date"],
            'start' => $request->item["start"],
            'end' => $request->item["end"],
            'duration' => $duration,
            'category' => $request->item["category"],
            'num_of_members' => $request->item["num_of_members"],
            'num_of_guests' => $num_of_guests,
            'host_id' => $request->item["host_id"],
            'timed' => $request->item["timed"],
        ]);
        
        // add rows to reservation_users (participants (members))
        if(isset($request->item["ordered_participants_ids"])){
            foreach($request->item["ordered_participants_ids"] as $id){
                Reservation_User::create([
                    'reservation_id' => $newEvent->id,
                    'user_id' => $id
                ]);
            }
        }

        // add rows to guests (guests (non-member))
        if(count($guest_list)>0){
            foreach($guest_list as $fields){
                Guests::create([
                    'name' => $fields,
                    'user_id' => $request->item["host_id"],
                    'reservation_id' => $newEvent->id
                ]);
            }
        }
        return [
            'status' => 'success',
            'message' => 'Reservation Created',
        ];
    }
   

    public function store(Request $request)
    {
        $currentTime = (int)(microtime(true)*1000);

        if($currentTime>$request->item["start"]){
            return "error";
        }
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
        
        // return date('Y-m-d',strtotime($request->item["date"]));
        // if host is member (single / family)
        // Event title = category unless host is member
        $eventTitle = null;
        if(isset($request->item["host_id"])){
            $host = User::where('id', $request->item["host_id"])->first();
            if($host->access == "Single" || $host->access == "Family"){
                $eventTitle = $host->name . "'s Event";
            } else if($host->access == "Tennis Pro"){
                $eventTitle = $host->name;
            } else {
                $eventTitle = $request->item["method"];
            }
        } else {
            $eventTitle = $request->item["method"];
        }

        $newEvent = Reservation::create([
            'name' => $eventTitle,
            'method' => $request->item["method"],
            'date' => $request->item["date"],
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
        if($conflictingReservations || $request->item["start"]>$request->item["end"]){
            return "error";
        } 
        if(!isset($request->item["method"])){
            return "error2";
        }

        $existingItem = Reservation::find($request->item["id"]);
        // }

        if($existingItem) {
            // update algorithm for guests
            // $guestDataIndex = 0;
            // $guestFormIndex = 0;
            $guest_host_id = 0;
            if(isset($request->item['host_id'])){
                $guest_host_id = $request->item['host_id'];
            }


            $existingGuests = Guests::where('reservation_id', $existingItem->id)->orderBy('name')->get();
            $checkGuests = true;
            
            foreach($existingGuests as $guest){
                $guest->delete();
            }
            if(isset($request->item["guest_names"])){
                foreach($request->item["guest_names"] as $guest_name){
                    if(is_null($guest_name)){
                        return "error2";
                    } else{
                        if(isset($guest_name["name"])){
                            $guest_name = $guest_name["name"];
                        }
                    }
                    Guests::create([
                        'name' => $guest_name,
                        'user_id' => $guest_host_id,
                        'reservation_id' => $existingItem->id
                    ]);
                }
            }

            // if(isset($request->item["guest_names"])){
            //     // if($request->item["ordered_participants_ids"] == -1){
            //     $checkGuests = false;
            //     // } else {
            //     $formGuests = $request->item["ordered_participants_ids"];
            //     $existingItem->num_of_guests = count($formGuests); 
            // } else {
            //     $formGuests = [];
            // } 

            // if($checkGuests){
            //     // participant replacement algorithm for the Reservation_User table
            //     while($guestDataIndex != count($existingGuests) || $guestFormIndex != count($formGuests)){
            //         if($guestFormIndex == count($formGuests)) {
            //             $existingGuests[$guestDataIndex]->delete();
            //             $guestDataIndex++;
            //         }
            //         else if($guestDataIndex == count($existingGuests)) {
            //             Guests::create([
            //                 'reservation_id' => $existingItem->id,
            //                 'user_id' => $formGuests[$guestFormIndex]
            //             ]);
            //             $guestFormIndex++;
            //         }
            //         else if($existingGuests[$guestDataIndex]->user_id < $formGuests[$guestFormIndex]) {
            //             $existingGuests[$guestDataIndex]->delete();
            //             $guestDataIndex++;
            //         }
            //         else if($existingGuests[$guestDataIndex]->user_id > $formGuests[$guestFormIndex]){
            //             Guests::create([
            //                 'reservation_id' => $existingItem->id,
            //                 'user_id' => $formGuests[$guestFormIndex]
            //             ]);
            //             $guestFormIndex++;
            //         }
            //         else{
            //             $guestDataIndex++;
            //             $guestFormIndex++;
            //         }
            //     }
            // }


            // update algorithm for participants
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

            $eventTitle = null;
            if(isset($request->item["host_id"])){
                $host = User::where('id', $request->item["host_id"])->first();
                if($host->access == "Single" || $host->access == "Family"){
                    $eventTitle = $host->name . "'s Event";
                } else if($host->access == "Tennis Pro"){
                    $eventTitle = $host->name;
                } else {
                    $eventTitle = $request->item["method"];
                }
            } else {
                $eventTitle = $request->item["method"];
            }
            if($existingItem->reoccur_id == 0){
                $existingItem->reoccur_id = null;
            }

            // $existingItem->name = $request->item["method"];
            $existingItem->name = $eventTitle;
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

