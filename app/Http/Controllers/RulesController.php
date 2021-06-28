<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rules;

class RulesController extends Controller
{
    //index
    public function index()
    {
        return Rules::get();
    }

    public function updateRules(Request $request)
    {
        $rules = Rules::orderBy('id')->get();
        
        for ($x = 0; $x < count($request->item); $x++) {
            if($x==1){
                if(!preg_match("/^[1-9][0-9]{0,15}$/", $request->item[$x]["value"])){
                    return [
                        'status' => 'error',
                        'message' => 'Invalid Entry',
                    ];
                }
            } else {
                if(!preg_match("/^\d*\.?\d{0,4}$/", $request->item[$x]["value"])){
                    return [
                        'status' => 'error',
                        'message' => 'Invalid Entry',
                    ];
                }
            }
        }
        for ($x = 0; $x < count($rules); $x++) {
            if($x==1){
                $rules[$x]->value = $request->item[$x]["value"];
                $rules[$x]->active = $request->item[$x]["active"];
                $rules[$x]->save();
            } else {
                $rules[$x]->value = round($request->item[$x]["value"], 1);
                $rules[$x]->active = $request->item[$x]["active"];
                $rules[$x]->save();
            }
        }
        return [
            'status' => 'success',
            'message' => 'Club Rules Updated',
        ];
    }
}
