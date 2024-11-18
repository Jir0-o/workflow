<?php

namespace App\Http\Controllers;

use App\Models\DetailLogin;
use App\Models\Notice;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;


class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $todayDate = Carbon::today();
        $tasks = Task::all();
        $user = auth()->user();
        $userId = auth()->id();
        $Today = Carbon::today();
        $activeUsers = DetailLogin::where('user_id', $userId)->get();

        $pendingCount = Task::where('status', 'pending')->count();
        $completeCount = Task::where('status', 'completed')->count();

        $pendingUserTasks = Task::where('status', 'pending')->where('user_id', $userId)->whereDate('created_at', $Today)->with('user')->orderBy('created_at', 'desc')->get();
        $completeUserTasks = Task::where('status', 'completed')->where('user_id', $userId)->with('user')->orderBy('submit_by_date', 'desc')->get();

        $pendingAdminTasks = Task::where('status', 'pending')->whereDate('created_at', $Today)->with('user')->orderBy('created_at', 'desc')->get();
        $completeAdminTasks = Task::where('status', 'completed')->with('user')->orderBy('submit_by_date', 'desc')->get();

        $notice = Notice::where('status', 0)->get();
        $noticeUser = Notice::where('status', 0)->where('user_id', $userId)->get();

        $noticeDate = Notice::all();

        foreach ($noticeDate as $noticeDates) {
            $endDate = $noticeDates->end_date ? Carbon::parse($noticeDates->end_date) : null;
        
            if (($endDate && Carbon::now()->startOfDay()->isAfter($endDate->endOfDay())) || is_null($endDate)) {
                // If end_date is null or today is strictly after the end date, set status to 1
                $noticeDates->status = 1;
            } else {
                // Otherwise, keep status as 0
                $noticeDates->status = 0;
            }
        
            $noticeDates->save();
        }


        return view('dashboard',compact('tasks','pendingCount','completeCount','pendingUserTasks','completeUserTasks','pendingAdminTasks','completeAdminTasks','user','activeUsers','notice','noticeUser'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
