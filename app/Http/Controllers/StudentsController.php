<?php

namespace App\Http\Controllers;

use App\Students;
use App\Parents;
use Illuminate\Http\Request;
use App\Custom\Doc;
use App\Fees;

class StudentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        //$this->authorize('isAdmin');
        return Students::with(['class', 'parents'])->latest()->get();
    }

    public function find()
    {
        $count = \Request::get('rec_count');
        if($search = \Request::get('q')){
            $students = Students::where(function($query) use ($search){
                $query->where('name', 'LIKE', "%$search%")
                ->orWhere('adm_number','LIKE',"%$search%");
            })->with(['class','parents'])->paginate($count ? $count : 10);
        }else{
            $students = Students::latest()->with(['class','parents', 'documents'])->paginate($count ? $count : 10);
        }

        return $students;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //student details validation
        //more validation logic handled in the front end
        $this->validate($request,[
            'name'=>'required|string|max:40',
            'class_id'=>'required',
            'birth_cert_number'=>'required',
            'nemis_number'=>'required',
            'gender'=>'required',
            'dob'=>'required',
            'location'=>'required',
            'previous_school'=>'min:5|max:50',
        ]);
        
        
        $adm_number = $this->getLastAdm();

        //images insert
        if($request->passport_photo && $request->passport_photo !="" ){
            $ext = explode('/',explode(':',substr($request->passport_photo, 0, strpos($request->passport_photo, ';')))[1])[1];
            $filename = 'docs/students/passports/'.time().".".$ext;
            $upload = (new Doc($request->passport_photo))->save($filename);

            if(json_decode($upload)->status == 'success'){
                $passportPhoto = $filename;
            }else{
                $passportPhoto = "docs/students/passports/default.png";
            }

        }else{
            $passportPhoto = "docs/students/passports/default.png";
        }
        //birth cert
        if($request->birth_certificate && $request->birth_certificate !="" ){
            $filename  = "docs/students/birth_certificates/".time().'.pdf';
            $upload = (new Doc($request->birth_certificate))->save($filename);

            if(json_decode($upload)->status == 'success'){
                $birthCertificate = $filename;
            }else{
                $birthCertificate = "docs/students/birth_certificates/default.pdf";
            }
        }else{
            $birthCertificate = "docs/students/birth_certificates/default.pdf";
        }

        //into students table
        $student = new Students();
        $student->adm_number=$adm_number;
        $student->name=$request->name;
        $student->birth_cert_number=$request->birth_cert_number;
        $student->nemis_number=$request->nemis_number;
        $student->medical=$request->medical;
        $student->class_id=$request->class_id;
        $student->gender=$request->gender;
        $student->dob=$request->dob;
        $student->location=$request->location;
        $student->previous_school=$request->previous_school;
        $student->save();
        //into documents table
        
        $student->documents()->createMany([
            [
                'name'=>'birth_certificate',
                'url'=>$birthCertificate,
            ],
            [
                'name'=>'passport_photo',
                'url'=>$passportPhoto,
            ]
        ]);
        //into parents table
        $student->parents()->create([
                'mother_name'=>$request->mother_name,
                'mother_contact'=>$request->mother_contact,
                'father_name'=>$request->father_name,
                'father_contact'=>$request->father_contact,
                'guardian_name'=>$request->guardian_name,
                'guardian_contact'=>$request->guardian_contact
            ]);

     return response()->json([
         'status'=>'Students data successfully saved!',
         'last_record'=>Students::latest()->get()->first()
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
        return Students::with(['class', 'parents', 'documents'])->find($id);
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
        $student = Students::findOrFail($id);
        //original record
        $original_record = $student->first();
        $this->validate($request,[
            'name'=>'required|string|max:40',
            // 'adm_number'=>'integer|unique:students,adm_number,'.$request->id,
            'class_id'=>'required',
            'gender'=>'required',
            'birth_cert_number'=>'required',
            'nemis_number'=>'required',
            'dob'=>'required',
            'location'=>'required',
            'previous_school'=>'required|min:5|max:50',
        ]);
        /*
        //images insert
        $originalPassport = json_decode($original_record)->passport_photo;
        $originalBirthCertificate = json_decode($original_record)->birth_certificate;

        if($request->passport_photo && $request->passport_photo !="" ){
            $ext = explode('/',explode(':',substr($request->passport_photo, 0, strpos($request->passport_photo, ';')))[1])[1];
            $filename = 'docs/students/passports/'.time().'.'.$ext;
            $upload = (new Doc($request->passport_photo))->save($filename);
            if(json_decode($upload)->status == 'success'){
                $passportPhoto = $filename;
            }else{
                $passportPhoto = $originalPassport;
            }
        }else{
            $passportPhoto = $originalPassport;
        }

        if($request->birth_certificate && $request->birth_certificate !="" ){
            $filename = "docs/students/birth_certificates/".time().'.pdf';
            $upload = (new Doc($request->birth_certificate))->save($filename);
            if(json_decode($upload)->status == 'success'){
                $birthCertificate = $filename;
            }else{
                $birthCertificate = $originalBirthCertificate;
            }
        }else{
            $birthCertificate = $originalBirthCertificate;
        }

        */
        $student->update([
            'name'=>$request->name,
            'adm_number'=>$request->adm_number,
            'birth_cert_number'=>$request->birth_cert_number,
            'nemis_number'=>$request->nemis_number,
            'medical'=>$request->medical,
            'class_id'=>$request->class_id,
            'gender'=>$request->gender,
            'dob'=>$request->dob,
            'location'=>$request->location,
            'previous_school'=>$request->previous_school
            ]);

             //update parents table
        $student->parents()->update([
            'mother_name'=>$request->mother_name,
            'mother_contact'=>$request->mother_contact,
            'father_name'=>$request->father_name,
            'father_contact'=>$request->father_contact,
            'guardian_name'=>$request->guardian_name,
            'guardian_contact'=>$request->guardian_contact
        ]);

        //updated record
        $updated_record = $student->first();
        return response()->json([
            'message'=>'Students info updated successfully.',
            'original_record'=>$original_record,
            'updated_record'=>$updated_record
            ]);
    }

    //get admission number
    public function getLastAdm(){
        if(Students::count() > 0){
            $student = Students::orderBy('adm_number', 'DESC')->first();
            $adm = $student->adm_number +1;
        }else{
            $adm = 0001;
        }
        return $adm;
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Students $student, $id)
    {
        // $this->authorize('isAdmin');

        $stud = $student->find($id);
        $stud->delete();
        return response()->json([
            'message'=>'Student deleted successfully.',
            'deleted_record'=>$stud
        ]);
    }
}
