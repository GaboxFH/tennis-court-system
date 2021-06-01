<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;


class CategoryController extends Controller
{
    //
    public function index()
    {
        return Category::get();
    }

    public function store(Request $request)
    {   
        if(!isset($request->item)){
            return [
                'status' => 'error',
                'message' => 'Text Field Blank',
            ];  
        }

        $conflictingCategory = Category::where('name', $request->item)->count();
        if($conflictingCategory != 0){
            return [
                'status' => 'error',
                'message' => 'Category Already Created',
            ];  
        }

        Category::create([
            'name' => $request->item,
            'color' => 0
        ]);

        return [
            'status' => 'success',
            'message' => 'Category Added',
        ];  
    }

    public function update(Request $request)
    {
        $existingCat = Category::where('id', $request->item["id"])
                                ->update(['color' => $request->item["color"]]);
        return [
            'status' => 'success',
            'message' => 'Color Changed',
        ];  
    }

    // public function update(Request $request, $id)
    // {
    //     $existingUser = User::find($id);

    //     if($existingUser){
    //         $existingUser->membership_id = $request->item["membership_id"];
    //         $existingUser->name = $request->item["name"];
    //         $existingUser->phone = $request->item["phone"];
    //         $existingUser->email = $request->item["email"];
    //         $existingUser->save();
    //     }

    //     return "Item not found.";
    // }

    public function destroy($id)
    {
        $existingCat = Category::find($id);

        if($existingCat){
            $existingCat->delete();
            return [
                'status' => 'success',
                'message' => 'Category Deleted',
            ];  
        }

        return [
            'status' => 'error',
            'message' => 'Category Not Found',
        ]; 
    }
}
