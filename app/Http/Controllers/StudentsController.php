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
        // if($request->rec_count == 'all'){
        //     return Students::with(['class', 'parents'])->latest()->paginate(Students::count());
        // }else{
        //     $count =  $request->rec_count;
        //     return Students::with(['class', 'parents'])->latest()->paginate($count ? $count : 10);
        // }

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
        $this->validate($request,[
            'name'=>'required|string|max:191',
            'class.name'=>'required',
            'birth_cert_number'=>'required',
            'nemis_number'=>'required',
            'gender'=>'required',
            'dob'=>'required',
            'location'=>'required',
            'previous_school'=>'required|min:5|max:191',
        ]);
        
        $admission_class = collect($request->class)->get('name');
        $class_id = \App\Classes::where('name','=',$admission_class)->get('id')->first()->id;
        $adm_number = $this->getLastAdm();

        //images insert
        if($request->passport_photo && $request->passport_photo !="" ){
            $ext = explode('/',explode(':',substr($request->passport_photo, 0, strpos($request->passport_photo, ';')))[1])[1];
            $filename = 'docs/students/passports/'.time().".".$ext;
            $upload = (new Doc($request->passport_photo))->save($filename);

            if(json_decode($upload)->status == 'success'){
                $passportPhoto = $filename;
            }else{
                $passportPhoto = "null";
            }

        }else{
            $passportPhoto = "null";
        }
        //birth cert
        if($request->birth_certificate && $request->birth_certificate !="" ){
            $filename  = "docs/students/birth_certificates/".time().'.pdf';
            $upload = (new Doc($request->birth_certificate))->save($filename);

            if(json_decode($upload)->status == 'success'){
                $birthCertificate = $filename;
            }else{
                $birthCertificate = "null";
            }
        }else{
            $birthCertificate = "null";
        }

        //into students table
        $student = new Students();
        $student->create([
            'adm_number'=>$adm_number,
            'name'=>$request->name,
            'birth_cert_number'=>$request->birth_cert_number,
            'nemis_number'=>$request->nemis_number,
            'medical'=>$request->medical,
            'class_id'=>$class_id,
            'gender'=>$request->gender,
            'dob'=>$request->dob,
            'location'=>$request->location,
            'previous_school'=>$request->previous_school
        ]);
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
                'mother_name'=>collect($request->parents)->get('mother_name'),
                'mother_contact'=>collect($request->parents)->get('mother_contact'),
                'father_name'=>collect($request->parents)->get('father_name'),
                'father_contact'=>collect($request->parents)->get('father_contact'),
                'guardian_name'=>collect($request->parents)->get('guardian_name'),
                'guardian_contact'=>collect($request->parents)->get('guardian_contact')
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
            'name'=>'required|string|max:191',
            'adm_number'=>'integer|unique:students,adm_number,'.$request->id,
            'class.name'=>'required',
            'gender'=>'required',
            'birth_cert_number'=>'required',
            'nemis_number'=>'required',
            'dob'=>'required',
            'location'=>'required',
            'previous_school'=>'required|min:5|max:191',
        ]);
        $class_name = collect($request->class)->get('name');
        $class_id = \App\Classes::where('name','=',$class_name)->get('id')->first()->id;

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

        $student->update([
            'name'=>$request->name,
            'adm_number'=>$request->adm_number,
            'birth_cert_number'=>$request->birth_cert_number,
            'nemis_number'=>$request->nemis_number,
            'medical'=>$request->medical,
            'class_id'=>$class_id,
            'gender'=>$request->gender,
            'dob'=>$request->dob,
            'passport_photo'=>$passportPhoto,
            'birth_certificate'=>$birthCertificate,
            'location'=>$request->location,
            'previous_school'=>$request->previous_school
            ]);

             //update parents table
        $student->parents()->update([
            'mother_name'=>collect($request->parents)->get('mother_name'),
            'mother_contact'=>collect($request->parents)->get('mother_contact'),
            'father_name'=>collect($request->parents)->get('father_name'),
            'father_contact'=>collect($request->parents)->get('father_contact'),
            'guardian_name'=>collect($request->parents)->get('guardian_name'),
            'guardian_contact'=>collect($request->parents)->get('guardian_contact')
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

        $deleted_record = $student->find($id)->delete();
        return response()->json([
            'message'=>'Student deleted successfully.',
            'deleted_record'=>$deleted_record
        ]);
    }
}
