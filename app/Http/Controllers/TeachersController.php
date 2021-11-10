<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Teachers;

class TeachersController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        if($request->tag && $request->tag == 'available'){
            return Teachers::all(['id','name']);
        }

        $count =  $request->rec_count;
        return Teachers::latest()->paginate($count ? $count : 10);
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
        $this->validate($request,[
            'name'=>'required|min:3|unique:teachers,name,'.$request->id,
            'email'=>'sometimes|required|min:3|email',
            'phone'=>'required'
        ]);

        $teacher = new Teachers();
        $teacher->name = $request->name;
        $teacher->email = $request->email;
        $teacher->phone = $request->phone;
        $teacher->save();

        return response()->json([
            'status'=>'success',
            'last_record'=>Teachers::latest()->get()->first()
            ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Teachers  $teachers
     * @return \Illuminate\Http\Response
     */
    public function show(Teachers $teachers, $id)
    {
        return Teachers::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Teachers  $teachers
     * @return \Illuminate\Http\Response
     */
    public function edit(Teachers $teachers)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Teachers  $teachers
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Teachers $teachers, $id)
    {
        //original record
        $original_record = $teachers->find($id)->toJson();

        $this->validate($request,[
            'name'=>'required|min:3|unique:teachers,name,'.$request->id,
            'teacher'=>'required|min:3'
        ]);

        $teachers->where('id', $id)->update($request->all());
        //updated record
        $updated_record = $teachers->first()->toJson();
        return response()->json([
            'message'=>'Teachers info updated successfully.',
            'original_record'=>$original_record,
            'updated_record'=>$updated_record
            ]);

    }
     public function find(Request $request)
     {
         $count = $request->rec_count;
         if($search = $request->get('q')){
             $teachers = Teachers::where(function($query) use ($search){
                $query->where('name', 'LIKE', "%$search%")
                ->orWhere('teacher', 'LIKE', "%$search%");
             })->paginate($count ? $count : 10);
         }else{
             $teachers = Teachers::latest()->paginate($count ? $count : 10);
         }

         return $teachers;
     }

     public function downloadPdf()
    {
        $teachers = Teachers::all();
        $pdf = PDF::loadView('downloads.teachers', compact('teachers'))->setOptions(['defaultFont'=>'sans-serif'])->setPaper('a4','portrait');
        return $pdf->stream('teachers.pdf');
    }

    public function downloadExcel()
    {
        return Excel::download(new TeachersExports, 'teachers.xlsx');
    }

    public function downloadCsv()
    {
        return Excel::download(new TeachersExports, 'teachers.csv');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Teachers  $teachers
     * @return \Illuminate\Http\Response
     */
    public function destroy(Teachers $teachers, $id)
    {
        $teachers->where('id', $id)->delete();
        return response()->json(['status'=>'success']);
    }

}
