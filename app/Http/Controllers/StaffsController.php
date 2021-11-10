<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Staffs;
use App\UserType;
use App\Custom\Doc;


class StaffsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        //$this->authorize('isAdmin');
        if($request->rec_count == 'all' && $request->get('staff_type') != null){
            $staffTypeId =UserType::where('name',$request->staff_type)->first()->id;
            return Staffs::where('staff_type_id', $staffTypeId)->latest()->with('staffTypes')->paginate(Staffs::count());
        }else if($request->rec_count){
            if($request->staff_type != null){
                //get staff type id
                $staffTypeId =UserType::where('name',$request->staff_type)->first()->id;
                $count =  $request->rec_count;
                return Staffs::where('staff_type_id', $staffTypeId)->with('staffTypes')->latest()->paginate($count ? $count : 10);

            }else{
                return Staffs::with('staffTypes')->latest()->paginate(50);
            }
        }else{
            return response()->json([
                'status'=>'fail',
                'data'=>[],
                'msg'=>'Passed incomplete parameters to the request. Please make sure that all the required parameters are available.'
            ]);
        }
    }

    public function find(){
        $count = \Request::get('rec_count');
        if($search = \Request::get('q')){
            $staffs = Staffs::where(function($query) use ($search){
                $query->where('name', 'LIKE', "%$search%")
                ->orWhere('email', 'LIKE', "%$search%")
                ->orWhere('tsc', 'LIKE', "%$search%")
                ->orWhere('birth_certificate_number', 'LIKE', "%$search%")
                ->orWhere('staff_type', 'LIKE', "%$search%")
                ->orWhere('phone', 'LIKE', "%$search%")
                ->orWhere('id_number', 'LIKE', "%$search%");
            })->paginate($count ? $count : 10);
        }else{
            $staffs = Staffs::latest()->paginate($count ? $count : 10);
        }

        return $staffs;
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
            'birth_certificate_number'=>'required|unique:staffs',
            'id_number'=>'required|unique:staffs',
            'phone'=>'required|min:9|max:13',
            'email'=>'required|unique:staffs|string|max:191',
            'staff_type'=>'required|string',
            'kra'=>'required|unique:staffs',
            'nhif'=>'required|unique:staffs',
            'tsc'=>'required|unique:staffs',
            'location'=>'required|string|min:3',
            'gross_salary'=>'required'
        ]);

        // get staff type id
        $staffTypeId =UserType::where('name', '=', $request->staff_type)->first()->id;
        // uploads passports, id cards and birth certificates
        // passport upload
        if($request->passport != (null && "")){
            $ext = explode('/',explode(':',substr($request->passport, 0, strpos($request->passport, ';')))[1])[1];
            $filename = 'docs/staffs/passports/'.time().".".$ext;
            $upload = (new Doc($request->passport))->save($filename);

            if(json_decode($upload)->status == 'success'){
                $passport = $filename;
            }else{
                $passport = "null";
            }
        }else{
            $passport = "null";
        }

        //id card upload
        if($request->id_card != (null && "")){
            $filename ='docs/staffs/id_cards/'.time().".pdf";
            $upload = (new Doc($request->id_card))->save($filename);
            if(json_decode($upload)->status == 'success'){
                $id_card = $filename;
            }else{
                $id_card = "null";
            }
        }else{
            $id_card = "null";
        }

        //birth certificate upload
        if($request->birth_certificate != (null && "")){
            $filename = 'docs/staffs/birth_certificates/'.time().".pdf";
            $upload = (new Doc($request->birth_certificate))->save($filename);
            if(json_decode($upload)->status == 'success'){
                $birth_certificate = $filename;
            }else{
                $birth_certificate = "null";
            }
        }else{
            $birth_certificate = "null";
        }

        $staff = Staffs::create([
            'name'=>$request->name,
            'birth_certificate_number'=>$request->birth_certificate_number,
            'id_number'=>$request->id_number,
            'phone'=>$request->phone,
            'email'=>$request->email,
            'staff_type_id'=>$staffTypeId,
            'kra'=>$request->kra,
            'gross_salary'=>$request->gross_salary,
            'nhif'=>$request->nhif,
            'tsc'=>$request->tsc,
            'location'=>$request->location,
        ]);

        //staff documents
        $staff->documents()->createMany([
            [
                'name'=>'passport_photo',
                'url'=>$passport,
            ],
            [
                'name'=>'ID Card',
                'url'=>$id_card,
            ],
            [
                'name'=>'birth_certificate',
                'url'=>$birth_certificate,
            ]
        ]);


        return response()->json(['last_record'=>Staffs::latest()->get()->first()]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Staffs::with(['staffType','documents'])->find($id);
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

        $staff = Staffs::findOrFail($id);
        //original record
        $original_record = $staff->first();

        $this->validate($request,[
            'name'=>'required|string|max:191',
            'birth_certificate_number'=>'required|unique:staffs,birth_certificate_number,'.$staff->id,
            'id_number'=>'required|unique:staffs,id_number,'.$staff->id,
            'phone'=>'required|min:9|max:13',
            'email'=>'required|string|max:191|unique:staffs,email,'.$staff->id,
            'staff_type'=>'required|string',
            'kra'=>'required|unique:staffs,kra,'.$staff->id,
            'nhif'=>'required|unique:staffs,nhif,'.$staff->id,
            'tsc'=>'required|unique:staffs,tsc,'.$staff->id,
            'location'=>'required|string|min:3',
            'gross_salary'=>'required'
        ]);
        //get staff type id
        $staffTypeId = UserType::where('name', '=', $request->staff_type)->first()->id;

         //uploads passports, id cards and birth certificates
         $originalPassport = json_decode($original_record)->passport;
         $originalBirthCertificate = json_decode($original_record)->birth_certificate;
         $originalIdCard = json_decode($original_record)->id_card;

         //passport upload
        if($request->passport != (null && "")){
            if($originalPassport == 'null'){
                $ext = explode('/',explode(':',substr($request->passport, 0, strpos($request->passport, ';')))[1])[1];
                $filename = 'docs/staffs/passports/'.time().$ext;
            }else{
                $filename = $originalPassport;
            }
            $upload = (new Doc($request->passport))->save($filename);
            if(json_decode($upload)->status == 'success'){
                $passport = $filename;
            }else{
                $passport = $originalPassport;
            }
        }else{
            $passport = $originalPassport;
        }

        //id card upload
        if($request->id_card != (null && "")){
            if($originalIdCard == 'null'){
                $filename = 'docs/staffs/id_cards/'.time().".pdf";
            }else{
                $filename  = $originalIdCard;
            }

            $upload = (new Doc($request->id_card))->save($filename);
            if(json_decode($upload)->status == 'success'){
                $id_card = $filename;
            }else{
                $id_card = $originalIdCard;
            }
        }else{
            $id_card = $originalIdCard;
        }

        //birth certificate upload
        if($request->birth_certificate != (null && "")){
            if($originalBirthCertificate == "null"){
                $filename = 'docs/staffs/birth_certificates/'.time().'pdf';
            }else{
                $filename = $originalBirthCertificate;
            }
            $upload = (new Doc($request->birth_certificate))->save($filename);
            if(json_decode($upload)->status == 'success'){
                $birth_certificate = $filename;
            }else{
                $birth_certificate = $originalBirthCertificate;
            }
        }else{
            $birth_certificate = $originalBirthCertificate;
        }

       $staff->update([
        'name'=>$request->name,
        'birth_certificate_number'=>$request->birth_certificate_number,
        'id_number'=>$request->id_number,
        'phone'=>$request->phone,
        'email'=>$request->email,
        'staff_type_id'=>$staffTypeId,
        'kra'=>$request->kra,
        'gross_salary'=>$request->gross_salary,
        'nhif'=>$request->nhif,
        'tsc'=>$request->tsc,
        'location'=>$request->location,
        ]);

        //staff documents update
        $staff->documents()->where('name','Passport Photo',)->update([
            'url'=>$passport,
            'user_type'=>'staffs'
        ]);
        $staff->documents()->where('name', 'ID Card')->update([
            'url'=>$id_card,
            'user_type'=>'staffs'
        ]);
        $staff->documents()->where('name', 'Birth Certificate')->update([
            'url'=>$birth_certificate,
            'user_type'=>'staffs'
        ]);


        //updated record
        $updated_record = $staff->first();
        return response()->json([
            'message'=>'Staff info updated successfully.',
            'original_record'=>$original_record,
            'updated_record'=>$updated_record
            ]);
    }

    public function downloadPdf()
    {
        $staffs = Staffs::all();
        $pdf = PDF::loadView('downloads.staffs', compact('staffs'))->setOptions(['defaultFont'=>'sans-serif'])->setPaper('a4','portrait');
        return $pdf->stream('staffs.pdf');
    }

    public function downloadExcel()
    {
        return Excel::download(new StaffsExports, 'staffs.xlsx');
    }

    public function downloadCsv()
    {
        return Excel::download(new StaffsExports, 'staffs.csv');
    }

    //nesting documents uploaded
    public function uploadDocuments(Request $request){
        //passport upload
        if($request->passport != (null && "")){
            $uploadStatus = $this->uploadPhoto($request->passport, 'docs/staffs/passports');
            if(json_decode($uploadStatus)->status == 'success'){
                $passport = json_decode($uploadStatus)->fileName;
            }else{
                $passport = "null";
            }
        }else{
            $passport = "null";
        }

        //id card upload
        if($request->id_card != (null && "")){
            $uploadStatus = $this->uploadPhoto($request->id_card, 'docs/staffs/id_cards');
            if(json_decode($uploadStatus)->status == 'success'){
                $id_card = json_decode($uploadStatus)->fileName;
            }else{
                $id_card = "null";
            }
        }else{
            $id_card = "null";
        }

        //birth certificate upload
        if($request->birth_certificate != (null && "")){
            $uploadStatus = $this->uploadPhoto($request->passport, 'docs/staffs/birth_certificates');
            if(json_decode($uploadStatus)->status == 'success'){
                $birth_certificate = json_decode($uploadStatus)->fileName;
            }else{
                $birth_certificate = "null";
            }
        }else{
            $birth_certificate = "null";
        }
    }

    //upload PDF document

    function uploadPDF($base_64file, $folder){
        if(strlen($base_64file) < 100){
            $success['status']=false;
            return json_encode($success);;
        }else{
            $name = time().".".explode('/',explode(':',substr($base_64file, 0, strpos($base_64file, ';')))[1])[1];

            $fileExact = explode(',', $base_64file)[1];
            $file = base64_decode($fileExact);
            $location = fopen(public_path($folder."/".$name), "a");
            $success = fwrite($location, $file);
            fclose($location);

            if($success){
                return json_encode(array(
                    'status'=>'success',
                    'message'=>'Your Imag was successfully uploaded.',
                    'fileName'=>$name
                ));
            }else{
               return json_encode(array(
                   'status'=>'fail',
                   'message'=>'Unble to upload your image. Please try again.'
               ));
            }
        }
    }

    function uploadPhoto($photo, $folder){
        if(strlen($photo) < 100){
            $success['status']=false;
            return json_encode($success);;
        }else{
            $name = time().".".explode('/',explode(':',substr($photo, 0, strpos($photo, ';')))[1])[1];
            $success = \Image::make($photo)->save(public_path($folder.'/').$name);

            if($success){
                return json_encode(array(
                    'status'=>'success',
                    'message'=>'Your Image was successfully uploaded.',
                    'fileName'=>$name
                ));
            }else{
               return json_encode(array(
                   'status'=>'fail',
                   'message'=>'Unable to upload your image.'
               ));
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Staffs $staffs, $id)
    {
        // $this->authorize('isAdmin');

        $deleted_record = $staffs->find($id);
        $staffs->where('id',$id)->delete();
        return response()->json([
            'message'=>'Staff deleted successfully.',
            'deleted_record'=>$deleted_record
        ]);
    }

}
