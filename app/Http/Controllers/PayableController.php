<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Payable;

class PayableController extends Controller
{
    public function index(){
        return Payable::all();
    }
    public function show(Payable $payable, $id){
        return $payable->find($id);
    }
    
    public function store(Request $request){
        $payable = new Payable();
        $payable->name = $request->name;
        $payable->amount = $request->amount;
        $payable->upcoming = $request->upcoming;
        $payable->save();
    }
    public function edit(Payable $payable,Request $request,$id){
        $payable->find($id)->update([
            'name'=>$request->name,
            'amount'=>$request->amount,
            'upcoming'=>$request->upcoming
        ]);

    }
    public function delete(Payable $payable, $id){
        $pay = $payable->find($id)->delete();
        return json_encode(array(
            'message'=>'Payable record was successfully deleted'
        ));
    }
}
