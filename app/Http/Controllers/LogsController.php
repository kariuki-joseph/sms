<?php

namespace App\Http\Controllers;

use App\Log;
use Illuminate\Http\Request;

class LogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $userType  = auth()->user()->type ?auth()->user()->type : 'user';
        $userId = auth()->user()->id;

        $log =  new Log();
        $log->user_id = $userId;
        $log->user_type = $userType;
        $log->activity = $request->activity;
        $log->save();

        return response()->json(['message'=>'Log has been created successfully. ','data'=>$log]);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Log  $logs
     * @return \Illuminate\Http\Response
     */
    public function show(Log $logs)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Log  $logs
     * @return \Illuminate\Http\Response
     */
    public function edit(Log $logs)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Log  $logs
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Log $logs)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Log  $logs
     * @return \Illuminate\Http\Response
     */
    public function destroy(Log $logs)
    {
        //
    }
}
