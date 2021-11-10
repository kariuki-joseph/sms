<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\HousesExports;
use App\Houses;
use PDF;
use Excel;

class HousesController extends Controller
{
    public function index(Request $request){
        $count =  $request->rec_count;
        return Houses::latest()->paginate($count ? $count : 10);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'=>'required|unique:houses,name,'.$request->id,
            'capacity'=>'required|min:1|max:3'
        ]);

       $house = new Houses();
       $house->name = $request->name;
       $house->capacity = $request->capacity;
       $house->save();

       return response()->json(['last_record'=>Houses::latest()->get()->first()]);
    }

    public function update(Request $request, Houses $houses, $id)
    {
        //original record
        $original_record = $houses->find($id)->toJson();

        $this->validate($request, [
            'name'=>'required|unique:houses,name,'.$request->id,
            'capacity'=>'required|min:1|max:3'
        ]);

        $houses->where('id',$id)->update([
            'name'=>$request->name,
            'capacity'=>$request->capacity
        ]);

        //updated record
        $updated_record = $houses->first()->toJson();
        return response()->json([
            'message'=>'House info updated successfully.',
            'original_record'=>$original_record,
            'updated_record'=>$updated_record
            ]);
    }

    public function find(Request $request){
        $count = $request->rec_count;
        if ($search = \Request::get('q')) {
            $houses = Houses::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%");
            })->paginate($count ? $count : 10);
        }else{
            $houses = Houses::latest()->paginate($count ? $count : 10);
        }

        return $houses;
    }

    public function downloadPdf()
    {
        $houses = Houses::all();
        $pdf = PDF::loadView('downloads.houses', compact('houses'))->setOptions(['defaultFont'=>'sans-serif'])->setPaper('a4','portrait');
        return $pdf->stream('houses.pdf');
    }

    public function downloadExcel()
    {
        return Excel::download(new HousesExports, 'houses.xlsx');
    }

    public function downloadCsv()
    {
        return Excel::download(new HousesExports, 'houses.csv');
    }

    public function destroy(Houses $houses, $id)
    {
        $deleted_record = $houses->find($id)->toJson();
        $houses->where('id',$id)->delete();
        return response()->json([
            'message'=>'House deleted successfully.',
            'deleted_record'=>$deleted_record
        ]);
    }
}

