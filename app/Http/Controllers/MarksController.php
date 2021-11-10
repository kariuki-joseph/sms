<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mark;

class MarksController extends Controller
{
    public function index(Request $request){
        $count =  $request->rec_count;
        return Mark::orderBy('score', 'DESC')->paginate($count ? $count : 30);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'min_score'=>'required',
            'max_score'=>'required',
            'grade'=>'required',
            'remark'=>'required'
        ]);

        $this->insertMarks($request);

       return response()->json([
           'status'=>'success',
           'last_record'=>Mark::latest()->get()->first()
           ]);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'min_score'=>'required',
            'max_score'=>'required',
            'grade'=>'required',
            'remark'=>'required'
        ]);

        $this->insertMarks($request);

    }

    public function insertMarks($request){



        if(Mark::count() < 101){
            $this->m_insert();
            $this->m_update($request);
        }else{
            $this->m_update($request);
        }

    }

    public function m_insert(){
        for($i=0; $i<=100; $i++){
            $mark = new Mark();
            $mark->score = $i;
            $mark->save();
        }
    }
    public function m_update($request){
        $min = $request->min_score;
        $max = $request->max_score;
        Mark::where(function($query) use ($min, $max){
            $query->where( 'score','>=',$min,'and')
            ->where('score','<=',$max);
        })->update([
            'grade'=>$request->grade,
            'remark'=>$request->remark
        ]);
    }
    public function find(Request $request){
        $count = $request->rec_count;
        if ($search = \Request::get('q')) {
            $Mark = Mark::where(function($query) use ($search){
                $query->where('score','LIKE',"%$search%")
                ->orWhere('grade', 'LIKE', "%$search%")
                ->orWhere('remark', 'LIKE', "%$search%");
            })->paginate($count ? $count : 10);
        }else{
            $Mark = Mark::latest()->paginate($count ? $count : 10);
        }

        return $Mark;
    }

    public function destroy(Mark $Mark, $id)
    {
        $deleted_record = $Mark->find($id)->toJson();
        $Mark->where('id',$id)->delete();
        return response()->json([
            'message'=>'Mark record deleted successfully.',
            'deleted_record'=>$deleted_record
        ]);
    }

}
