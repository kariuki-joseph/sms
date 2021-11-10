<?php

namespace App\Http\Controllers;
use App\Students;
class DocumentsController extends Controller
{
public function index(){
    $stud = Students::find(10);
     $insert = $stud->documents()->create([
        'name'=>'Name of document one',
        'user_type'=>'students',
        'url'=>'path_to_document'
    ]);
    echo json_encode(array(
        'status'=>$insert ? 'success' : 'fail',
        'message'=>$insert ? 'Record created successfully' : 'Unable to add your document record. Please try again later.'
    ));
}

}
