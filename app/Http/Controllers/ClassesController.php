<?php

namespace App\Http\Controllers;

use App\Classes;
use App\Teachers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\ClassesExports;
use PDF;
use Excel;

class ClassesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        return Classes::with('classTeacher')->latest()->paginate(20);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name'=>'required|unique:classes,name,'.$request->id,
            'capacity'=>'required|min:1|max:3'
        ]);

        if($request->class_teacher && $request->class_teacher != ''){
            $class_teacher_id = Teachers::where('name', '=', $request->class_teacher)->first()->id;
            $request->merge(['class_teacher_id'=>$class_teacher_id]);
        }else{
            $request->merge(['class_teacher_id'=>null]);
        }

       $class = new Classes();
       $class->name = $request->name;
       $class->capacity = $request->capacity;
       $class->class_teacher_id = $request->class_teacher_id;
       $class->save();

       return response()->json(['last_record'=>Classes::latest()->get()->first()]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Classes  $classes
     * @return \Illuminate\Http\Response
     */
    public function show(Classes $classes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Classes  $classes
     * @return \Illuminate\Http\Response
     */
    public function edit(Classes $classes, $id)
    {
       //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Classes  $classes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Classes $classes, $id)
    {
        //original record
        $original_record = $classes->find($id)->toJson();

        $this->validate($request,[
            'name'=>'required|unique:classes,name,'.$request->id,
            'capacity'=>'required|min:1|max:3'
        ]);

        if($request->class_teacher && $request->class_teacher != ''){
            $class_teacher_id = Teachers::where('name', '=', $request->class_teacher)->first()->id;
            $request->merge(['class_teacher_id'=>$class_teacher_id]);
        }else{
            $request->merge(['class_teacher_id'=>null]);
        }

        $classes->where('id',$id)->update([
            'name'=>$request->name,
            'capacity'=>$request->capacity,
            'class_teacher_id'=>$request->class_teacher_id,
        ]);

        //updated record
        $updated_record = $classes->first()->toJson();
        return response()->json([
            'message'=>'Classes info updated successfully.',
            'original_record'=>$original_record,
            'updated_record'=>$updated_record
            ]);
    }

    public function downloadPdf()
    {
        $classes = Classes::all();
        $pdf = PDF::loadView('downloads.classes', compact('classes'))->setOptions(['defaultFont'=>'sans-serif'])->setPaper('a4','portrait');
        return $pdf->stream('classes.pdf');
    }

    public function downloadExcel()
    {
        return Excel::download(new ClassesExports, 'classes.xlsx');
    }

    public function downloadCsv()
    {
        return Excel::download(new ClassesExports, 'classes.csv');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Classes  $classes
     * @return \Illuminate\Http\Response
     */
    public function destroy(Classes $classes, $id)
    {
        $deleted_record = $classes->find($id)->toJson();
        $classes->where('id',$id)->delete();
        return response()->json([
            'message'=>'User deleted successfully.',
            'deleted_record'=>$deleted_record
        ]);

    }

    //get class details by id
    public function getById(Classes $classes,$id){
        $classes = Classes::where('id',$id)->get();
        return $classes;
    }

    public function find(Request $request){
        $count = $request->rec_count;
        if ($search = $request->get('q')) {

            $classes = Classes::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%");
            })->paginate($count ? $count : 10);
        }else{
            $classes = Classes::latest()->paginate($count ? $count : 10);
        }

        return $classes;
    }
}
