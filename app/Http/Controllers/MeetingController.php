<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\MeetingResource;
use App\Http\Resources\UserResource;
use App\Models\Project;
use App\Models\Task;
use App\Models\Utility;
use App\Models\WorkspaceType;


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
    public function huddles($slug)
    {
        $objUser = Auth::user();
        $workspace_type = WorkspaceType::get();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        $pendingMeetings = Meeting::where('is_canceled', '!=', 1)
            ->whereHas('members', function ($query) use ($objUser) {
                $query->where('member_id', $objUser->id);
                $query->where('meeting_users.is_accepted', null);
            })
            ->whereNot('meeting_cundocter_id', $objUser->id)->get();

        $allMeetings = Meeting::where('is_canceled', '!=', 1)
            ->where(function ($query) use ($objUser) {
                $query->where('meeting_cundocter_id', auth()->id())
                    ->orWhereHas('members', function ($subquery) use ($objUser) {
                        $subquery->where('member_id', $objUser->id)
                            ->where('meeting_users.is_accepted', 1);
                    });
            })
            ->get();

        $WSUsers = UserResource::collection($currentWorkspace->users);
        $meetingCollection = MeetingResource::collection($allMeetings);
        return view('vue-ui.pages.calender.huddle-calender', compact('currentWorkspace', 'WSUsers', 'meetingCollection', 'workspace_type','pendingMeetings'));
    }
}
