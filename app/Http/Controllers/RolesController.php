<?php

namespace App\Http\Controllers;

use App\Roles;
use Illuminate\Http\Request;
use PDF;
use Excel;
use App\Exports\RolesExports;
class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        $count =  $request->rec_count;
        return Roles::latest()->paginate($count ? $count : 10);
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
            'name'=>'required|min:3|unique:roles,name,'.$request->id,
            'role'=>'required|min:3'
        ]);

        $role = new Roles();
        $role->name = $request->name;
        $role->role = $request->role;
        $role->save();

        return response()->json(['last_record'=>Roles::latest()->get()->first()]);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Roles  $roles
     * @return \Illuminate\Http\Response
     */
    public function show(Roles $roles, $id)
    {
        return Roles::where('id',$id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Roles  $roles
     * @return \Illuminate\Http\Response
     */
    public function edit(Roles $roles)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Roles  $roles
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Roles $roles, $id)
    {
        //original record
        $original_record = $roles->find($id)->toJson();

        $this->validate($request,[
            'name'=>'required|min:3|unique:roles,name,'.$request->id,
            'role'=>'required|min:3'
        ]);

        $roles->where('id', $id)->update($request->all());
        //updated record
        $updated_record = $roles->first()->toJson();
        return response()->json([
            'message'=>'Roles info updated successfully.',
            'original_record'=>$original_record,
            'updated_record'=>$updated_record
            ]);
    }
     public function find(Request $request)
     {
         $count = $request->rec_count;
         if($search = $request->get('q')){
             $roles = Roles::where(function($query) use ($search){
                $query->where('name', 'LIKE', "%$search%")
                ->orWhere('role', 'LIKE', "%$search%");
             })->paginate($count ? $count : 10);
         }else{
             $roles = Roles::latest()->paginate($count ? $count : 10);
         }

         return $roles;
     }

     public function downloadPdf()
    {
        $roles = Roles::all();
        $pdf = PDF::loadView('downloads.roles', compact('roles'))->setOptions(['defaultFont'=>'sans-serif'])->setPaper('a4','portrait');
        return $pdf->stream('roles.pdf');
    }

    public function downloadExcel()
    {
        return Excel::download(new RolesExports, 'roles.xlsx');
    }

    public function downloadCsv()
    {
        return Excel::download(new RolesExports, 'roles.csv');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Roles  $roles
     * @return \Illuminate\Http\Response
     */
    public function destroy(Roles $roles, $id)
    {
        $deleted_record = $roles->find($id)->toJson();
        $roles->where('id',$id)->delete();
        return response()->json([
            'message'=>'Role deleted successfully.',
            'deleted_record'=>$deleted_record
        ]);
    }
}
