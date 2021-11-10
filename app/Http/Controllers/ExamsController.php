<?php

namespace App\Http\Controllers;

use App\Exams;
use Illuminate\Http\Request;
use App\Exports\ExamsExports;
use PDF;
use Excel;

class ExamsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        $count =  $request->rec_count;
        return Exams::latest()->paginate($count ? $count : 10);
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
        $this->validate($request, [
            'name'=>'required|min:3|max:30',
            'exam_id'=>'required|unique:exams,exam_id,'.$request->id
        ]);

        $exam = new Exams();
       $exam->name = $request->name;
       $exam->exam_id = $request->exam_id;
       $exam->save();

       return response()->json(['last_record'=>Exams::latest()->get()->first()]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Exams  $exams
     * @return \Illuminate\Http\Response
     */
    public function show(Exams $exams, $id)
    {
        return Exams::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Exams  $exams
     * @return \Illuminate\Http\Response
     */
    public function edit(Exams $exams)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Exams  $exams
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Exams $exams, $id)
    {
        //original record
        $original_record = $exams->find($id)->toJson();

        $this->validate($request, [
            'name'=>'required|min:3|max:30',
            'exam_id'=>'required|unique:exams,exam_id,'.$request->id
        ]);

        $exams->where('id',$id)->update([
            'name'=>$request->name,
            'exam_id'=>$request->exam_id
        ]);

        //updated record
        $updated_record = $exams->first()->toJson();
        return response()->json([
            'message'=>'Exams info updated successfully.',
            'original_record'=>$original_record,
            'updated_record'=>$updated_record
            ]);
    }

    public function find(Request $request){
        $count = $request->rec_count;
        if($search = $request->get('q')){
            $exams = Exams::where(function($query) use ($search){
                $query->where('name', 'LIKE', "%$search%")
                ->orWhere('exam_id','LIKE', "%$search%");
            })->paginate($count ? $count : 10);
        }else{
            $exams = Exams::latest()->paginate($count ? $count : 10);
        }

        return $exams;
    }

    public function downloadPdf()
    {
        $exams = Exams::all();
        $pdf = PDF::loadView('downloads.exams', compact('exams'))->setOptions(['defaultFont'=>'sans-serif'])->setPaper('a4','portrait');
        return $pdf->stream('exams.pdf');
    }

    public function downloadExcel()
    {
        return Excel::download(new ExamsExports, 'exams.xlsx');
    }

    public function downloadCsv()
    {
        return Excel::download(new ExamsExports, 'exams.csv');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Exams  $exams
     * @return \Illuminate\Http\Response
     */
    public function destroy(Exams $exams, $id)
    {
        $deleted_record = $exams->find($id)->toJson();
        $exams->where('id',$id)->delete();
        return response()->json([
            'message'=>'Exam deleted successfully.',
            'deleted_record'=>$deleted_record
        ]);
    }
}
