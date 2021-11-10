<?php

namespace App\Http\Controllers;

use App\Logs;
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
        $userType  = auth()->user()->type;
        $userId = auth()->user()->id;

        $log = new Logs();
        $log->user_id = $userId;
        $log->user_type = $userType;
        $log->activity = $request->activity;
        $log->save();

        return response()->json(['message'=>'Log has been created successfully. ']);
    }

    //creating logs for activites
    public function createlog($activity){
        $userType  = auth()->user()->type;
        $userId = auth()->user()->id;

        $log = new Logs();
        $log->user_id = $userId;
        $log->user_type = $userType;
        $log->activity = $activity;
        $log->save();

        return response()->json(['message'=>'Log has been created successfully. ']);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Logs  $logs
     * @return \Illuminate\Http\Response
     */
    public function show(Logs $logs)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Logs  $logs
     * @return \Illuminate\Http\Response
     */
    public function edit(Logs $logs)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Logs  $logs
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Logs $logs)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Logs  $logs
     * @return \Illuminate\Http\Response
     */
    public function destroy(Logs $logs)
    {
        //
    }
}
