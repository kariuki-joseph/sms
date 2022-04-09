<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserType;

class UserTypeController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        return UserType::latest()->get();
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
            'name'=>'required|min:3|unique:user_types,name,'.$request->id,
        ]);

        $user_type = new UserType();
        $user_type->name = $request->name;
        $user_type->save();

        return response()->json([
            'status'=>'success',
            'last_record'=>UserType::latest()->get()->first()
            ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\UserType  $user_types
     * @return \Illuminate\Http\Response
     */
    public function show(UserType $user_types, $id)
    {
        return UserType::where('id',$id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UserType  $user_types
     * @return \Illuminate\Http\Response
     */
    public function edit(UserType $user_types)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UserType  $user_types
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserType $user_types, $id)
    {
        //original record
        $original_record = $user_types->find($id)->toJson();

        $this->validate($request,[
            'name'=>'required|min:3|unique:user_types,name,'.$request->id,
        ]);

        $user_types->where('id', $id)->update($request->all());
        //updated record
        $updated_record = $user_types->first()->toJson();
        return response()->json([
            'message'=>'User types info updated successfully.',
            'original_record'=>$original_record,
            'updated_record'=>$updated_record
            ]);
    }
     public function find(Request $request)
     {
         $count = $request->rec_count;
         if($search = $request->get('q')){
             $user_types = UserType::where(function($query) use ($search){
                $query->where('name', 'LIKE', "%$search%");
             })->paginate($count ? $count : 20);
         }else{
             $user_types = UserType::latest()->paginate($count ? $count : 20);
         }

         return $user_types;
     }

     public function downloadPdf()
    {
        $user_types = UserType::all();
        $pdf = PDF::loadView('downloads.user_types', compact('user_types'))->setOptions(['defaultFont'=>'sans-serif'])->setPaper('a4','portrait');
        return $pdf->stream('user_types.pdf');
    }

    public function downloadExcel()
    {
        return Excel::download(new UserTypeExports, 'user_types.xlsx');
    }

    public function downloadCsv()
    {
        return Excel::download(new UserTypeExports, 'user_types.csv');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserType  $user_types
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserType $user_types, $id)
    {
        $user_types->where('id', $id)->delete();
        return response()->json(['status'=>'success']);
    }

}
