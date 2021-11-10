<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Students;
use App\Staffs;
use App\Custom\Doc;

class UploadsController extends Controller
{
    public function index(Request $request){
        $user_type = $request->user_type;
        $user_id = $request->user_id;
        $document = $request->document;
        $document_name = $request->document_name;

        if(preg_match("/student*/i", $user_type)){
            //save as student
            $student = Students::findOrFail($request->user_id);
            $ext = explode('/',explode(':',substr($request->document, 0, strpos($request->document, ';')))[1])[1];
            $filename = 'docs/students/uploads/'.time().".".$ext;

            $upload = (new Doc($request->document))->save($filename);

            if(json_decode($upload)->status == 'success'){
                $student->documents()->create([
                    'name'=>$request->document_name,
                    'url'=>$filename
                ]);
                return response()->json([
                    'status'=>'success',
                    'msg'=>'document uploaded successfully'
                ]);

            }else{
               //no document to upload
               return response()->json([
                   'status'=>'fail',
                   'msg'=>'Unable to get document to upload'
               ]);
            }

        }else{
            //save as staff
            $staff = Staffs::findOrFail($request->user_id);

            $ext = exploT5HRde('/',explode(':',substr($request->document, 0, strpos($request->document, ';')))[1])[1];
            $filename = 'docs/staffs//uploads/'.time().".".$ext;
            $upload = (new Doc($request->document))->save($filename);

            if(json_decode($upload)->status == 'success'){
                $staff->documents()->create([
                    'name'=>$request->document_name,
                    'url'=>$filename
                ]);

                return response()->json([
                    'status'=>'success',
                    'msg'=>'Document uploaded successfully'
                ]);
            }else{
               //no document to upload
               return response()->json([
                'status'=>'fail',
                'msg'=>'Unable to get document to upload'
            ]);
            }
        }

    }
}
