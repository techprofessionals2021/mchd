<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;

class MeetingController extends Controller
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
       $meeting = Meeting::create([
           'title' => $request->title,
           'description' => $request->description,
           'time_in' =>date('H:i', strtotime($request->time_in)),
           'time_out' => date('H:i', strtotime($request->time_out)),
           'meeting_cundocter_id' => auth()->id(),
           'color' => $request->color,
           'meeting_date' => $request->date,
        ]);
        $meeting->members()->attach($request->assignee);
        return response()->json([
            'meeting' => $meeting,
            'success' => true
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Meeting  $meeting
     * @return \Illuminate\Http\Response
     */
    public function show(Meeting $meeting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Meeting  $meeting
     * @return \Illuminate\Http\Response
     */
    public function edit(Meeting $meeting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Meeting  $meeting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Meeting $meeting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Meeting  $meeting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Meeting $meeting)
    {
        //
    }

    public function cancelMeeting(Request $req)
    {
        $meeting = Meeting::find($req->id);
        $meeting->is_canceled = 1;
        $meeting->canceled_by = auth()->id();
        $meeting->save();
        return redirect()->back()->with('success',__('Meeting has been canceled successfully'));
    }

    public function acceptOrReject($meeting_id,$decision)
    {
        $meeting = Meeting::find($meeting_id);
        $meeting->members()->updateExistingPivot(auth()->id(),['is_accepted' => $decision]);
        $message = $decision == 0 ? 'rejected' : 'accepted';
        return redirect()->back()->with('success',__('Meeting has been '. $message .' successfully'));
    }
}
