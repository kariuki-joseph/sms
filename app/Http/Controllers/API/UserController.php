<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        //$this->authorize('isAdmin');
        $count =  $request->rec_count;
        return User::latest()->paginate($count ? $count : 10);
    }

    public function search()
    {
        $count = \Request::get('rec_count');
        if($search = \Request::get('q')){
            $users = User::where(function($query) use ($search){
                $query->where('name', 'LIKE', "%$search%")
                ->orWhere('email', 'LIKE', "%$search%");
            })->paginate($count ? $count : 10);
        }else{
            $users = User::latest()->paginate($count ? $count : 10);
        }

        return $users;
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
            'name'=>'required|string|max:191',
            'email'=>'required|unique:users|string|max:191',
            'password'=>'required|string|min:6'
        ]);

        return User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'user_type'=>$request->user_type,
            'photo'=>$request->photo
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    public function profile()
    {
        return auth()->user();
    }


    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $file = $request->image;

        //fileName
    $name = time().".".explode('/',explode(':',substr($request->doc, 0, strpos($request->doc, ';')))[1])[1];

    $success = \Image::make($request->doc)->save(public_path('docs/').$name);

        $this->validate($request,[
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email,'.$user->id,
            'password' => 'sometimes|required|min:6'
        ]);
            $currentPhoto = $user->photo;
        if($request->photo != $currentPhoto){
            $name = time().'.' . explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];

            \Image::make($request->photo)->save(public_path('img/profile/').$name);
            $request->merge(['photo' => $name]);

            $userPhoto = public_path('img/profile/').$currentPhoto;
            if(file_exists($userPhoto)){
                @unlink($userPhoto);
            }

        }

        if(!empty($request->password)){
            $request->merge(['password' => Hash::make($request['password'])]);
        }
        $user->update($request->all());
        return ['message'=>$request->photo];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // $this->authorize('isAdmin');

        $user = User::findOrFail($id);
        $this->validate($request,[
            'name'=>'required|string|max:191',
            'email'=>'required|string|email|max:191|unique:users,email,'.$user->id,
            'password'=>'sometimes|min:6'
        ]);

        $user->update($request->all());

        return ['message'=>'Info updated successfully.'];
    }

    public function downloadPdf()
    {
        $users = User::all();
        $pdf = PDF::loadView('downloads.users', compact('users'))->setOptions(['defaultFont'=>'sans-serif'])->setPaper('a4','portrait');
        return $pdf->stream('users.pdf');
    }

    public function downloadExcel()
    {
        return Excel::download(new UsersExports, 'users.xlsx');
    }

    public function downloadCsv()
    {
        return Excel::download(new UsersExports, 'users.csv');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $this->authorize('isAdmin');

        $user = User::findOrFail($id);
        $deleted_record = $user->first()->toJson();
        $user->delete();
        return response()->json([
            'message'=>'User deleted successfully.',
            'deleted_record'=>$deleted_record
        ]);
    }
}
