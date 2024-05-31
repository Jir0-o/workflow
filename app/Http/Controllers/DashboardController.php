<?php

namespace App\Http\Controllers;

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
        $tasks = Task::all();
        $user = auth()->user();
        $userId = auth()->id();
        $Today = Carbon::today();

        $pendingCount = Task::where('status', 'pending')->count();
        $completeCount = Task::where('status', 'completed')->count();

        $pendingUserTasks = Task::where('status', 'pending')->where('user_id', $userId)->whereDate('created_at', $Today)->with('user')->orderBy('created_at', 'desc')->get();
        $completeUserTasks = Task::where('status', 'completed')->where('user_id', $userId)->with('user')->orderBy('submit_by_date', 'desc')->get();

        $pendingAdminTasks = Task::where('status', 'pending')->whereDate('created_at', $Today)->with('user')->orderBy('created_at', 'desc')->get();
        $completeAdminTasks = Task::where('status', 'completed')->with('user')->orderBy('submit_by_date', 'desc')->get();

        return view('dashboard',compact('tasks','pendingCount','completeCount','pendingUserTasks','completeUserTasks','pendingAdminTasks','completeAdminTasks','user'));
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
