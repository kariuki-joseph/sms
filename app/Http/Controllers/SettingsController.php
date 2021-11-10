<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Settings;
use App\Exports\SettingsExports;
use PDF;
use Excel;
class SettingsController extends Controller
{

    public function index(Request $request){
        $count =  $request->rec_count;
        return Settings::latest()->paginate($count ? $count : 10);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'sch_name'=>'required',
            'sch_motto'=>'required',
            'sch_vission'=>'required',
            'sch_mission'=>'required',
            'email'=>'required',
            'po_address'=>'required',
            'phone'=>'required',
        ]);

       $settings = new Settings();
       $settings->sch_name = $request->sch_name;
       $settings->sch_motto = $request->sch_motto;
       $settings->sch_vission = $request->sch_vission;
       $settings->sch_mission = $request->sch_mission;
       $settings->logo = $request->logo ? $request->logo : 'logo.png';
       $settings->email = $request->email;
       $settings->po_address = $request->po_address;
       $settings->phone = $request->phone;
       $settings->save();

       return response()->json(['last_record'=>Settings::latest()->get()->first()]);
    }

    public function update(Request $request, Settings $settings, $id)
    {
        //original record
        $original_record = $settings->find($id)->toJson();

        $this->validate($request, [
            'sch_name'=>'required|unique:settings,sch_name,'.$request->id,
            'sch_motto'=>'required',
            'sch_vission'=>'required',
            'sch_mission'=>'required',
            'email'=>'required',
            'po_address'=>'required',
            'phone'=>'required',
        ]);



        $settings->where('id',$id)->update([
            'sch_name'=>$request->sch_name,
            'sch_motto'=>$request->sch_motto,
            'sch_vission'=>$request->sch_vission,
            'sch_mission'=>$request->sch_mission,
            'email'=>$request->email,
            'po_address'=>$request->po_address,
            'phone'=>$request->phone,
        ]);

        //updated record
        $updated_record = $settings->first()->toJson();
        return response()->json([
            'message'=>'Settings info updated successfully.',
            'original_record'=>$original_record,
            'updated_record'=>$updated_record
            ]);
    }

    public function updateLogo(Request $request){
        if(!empty($request->logo) && (strlen($request->logo) > 70)){
            $ext =explode('/', explode(':', substr($request->logo, 0, strpos($request->logo, ';')))[1])[1];
            \Image::make($request->logo)->save(public_path('img/logo/logo.').$ext);

            $update = Settings::where('id', $request->id)->update([
                'logo' => $request->logo
            ]);

            return response()->json(['message'=>'Logo was updated successfully']);
        }else{
            return response()->json(['message'=>'No data was received from this request.']);
        }
    }

    public function find(Request $request){
        $count = $request->rec_count;
        if ($search = \Request::get('q')) {
            $settings = Settings::where(function($query) use ($search){
                $query->where('sch_name','LIKE',"%$search%")
                ->orWhere('email','LIKE',"%$search%")
                ->orWhere('sch_motto','LIKE',"%$search%")
                ->orWhere('sch_vission','LIKE',"%$search%")
                ->orWhere('sch_mission','LIKE',"%$search%");
            })->paginate($count ? $count : 10);
        }else{
            $settings = Settings::latest()->paginate($count ? $count : 10);
        }

        return $settings;
    }

    public function destroy(Settings $settings, $id)
    {
        $deleted_record = $settings->find($id)->toJson();
        $settings->where('id',$id)->delete();
        return response()->json([
            'message'=>'Setting deleted successfully.',
            'deleted_record'=>$deleted_record
        ]);
    }

}
