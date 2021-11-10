<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Subjects;

class SubjectsController extends Controller
{
    public function index(Request $request){
        $count =  $request->rec_count;
        return Subjects::latest()->paginate($count ? $count : 10);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'=>'required',
            'subject_id'=>'required|min:1|max:10|unique:subjects'
        ]);

       $subject = new Subjects();
       $subject->name = $request->name;
       $subject->subject_id = $request->subject_id;
       $subject->save();

       return response()->json([
           'status'=>'success',
           'last_record'=>Subjects::latest()->get()->first()
           ]);
    }

    public function update(Request $request, Subjects $subjects, $id)
    {
        //original record
        $original_record = $subjects->find($id)->toJson();

        $subject = Subjects::findOrFail($request->id);
        $this->validate($request, [
            'name'=>'required',
            'subject_id'=>'required|min:1|max:9|unique:subjects,subject_id,'.$request->id
        ]);

        $subjects->where('id',$id)->update([
            'name'=>$request->name,
            'subject_id'=>$request->subject_id
        ]);

        //updated record
        $updated_record = $subjects->first()->toJson();
        return response()->json([
            'message'=>'Subject info updated successfully.',
            'original_record'=>$original_record,
            'updated_record'=>$updated_record
            ]);
    }

    public function find(Request $request){
        $count = $request->rec_count;
        if ($search = \Request::get('q')) {
            $subjects = Subjects::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%")
                ->orWhere('subject_id', 'LIKE', "%$search%");
            })->paginate($count ? $count : 10);
        }else{
            $subjects = Subjects::latest()->paginate($count ? $count : 10);
        }

        return $subjects;
    }

    public function downloadPdf()
    {
        $subjects = Subjects::all();
        $pdf = PDF::loadView('downloads.subjects', compact('subjects'))->setOptions(['defaultFont'=>'sans-serif'])->setPaper('a4','portrait');
        return $pdf->stream('subjects.pdf');
    }

    public function downloadExcel()
    {
        return Excel::download(new SubjectsExports, 'subjects.xlsx');
    }

    public function downloadCsv()
    {
        return Excel::download(new SubjectsExports, 'subjects.csv');
    }

    public function destroy(Subjects $subjects, $id)
    {
        $deleted_record = $subject->find($id)->toJson();
        $subject->where('id',$id)->delete();
        return response()->json([
            'message'=>'Subject deleted successfully.',
            'deleted_record'=>$deleted_record
        ]);
    }

}
