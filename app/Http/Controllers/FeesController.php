<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Fees;
use App\Students;
use App\Payable;

class FeesController extends Controller
{
    //
    public function index(){
        $fees = Fees::with(['student','student.class'])->with('payable:id,name,amount')->latest()->paginate(Fees::count());
        return $fees;
    }

    public function show(Fees $fees, $id){
        $res = $fees->find($id);
        return $res;
    }
    public function edit(Fees $fees,Request $request, $id){
        $fees->find($id)->update([
            'amount'=>$request->amount,
            'adm_number'=>$request->adm_number,
            'receipt'=>$request->receipt
        ]);
        return json_encode(array(
            'message'=>'Fees record updated successfully'
        ));
    }

    //get payment information about all classes
    public function paymentInfo(){
        $students = Students::with(['class','fees'])->paginate(Students::count());
        return $students;
    }
    
    public function showFees(Fees $fees, $id){
        $res =  $fees->where('adm_number',$id)->with('payable:id,name')->latest()->get();
        return $res;
    }

    public function showPaymentSummary(Fees $fees, $id, $payable_id){
        $res = $fees->where('adm_number',$id,'and')->where('payable_id',$payable_id)->latest()->get();
        return $res;
    }

    public function getTotal(Fees $fees,$id, $payable_id){
       $response = $fees->where('adm_number', $id,'AND')->where('payable_id',$payable_id)->sum('amount');
       return $response;

    }


    public function destroy(Fees $fees, $id){
        $fees->find($id)->delete();
        return json_encode(array(
            'message'=>'Fees record deleted successfully'
        ));
    }
}
