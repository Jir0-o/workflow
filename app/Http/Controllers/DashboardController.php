<?php

namespace App\Http\Controllers;

use App\Models\Task;
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
        $userId = auth()->id();
        $Today = Carbon::today();

        $pendingCount = Task::where('status', 'pending')->count();
        $completeCount = Task::where('status', 'completed')->count();

        $pendingUserTasks = Task::where('status', 'pending')->where('user_id', $userId)->whereDate('created_at', $Today)->with('user')->latest()->get();
        $completeUserTasks = Task::where('status', 'completed')->where('user_id', $userId)->whereDate('created_at', $Today)->with('user')->latest()->get();

        $pendingAdminTasks = Task::where('status', 'pending')->whereDate('created_at', $Today)->with('user')->latest()->get();
        $completeAdminTasks = Task::where('status', 'completed')->whereDate('created_at', $Today)->with('user')->latest()->get();

        return view('dashboard',compact('tasks','pendingCount','completeCount','pendingUserTasks','completeUserTasks','pendingAdminTasks','completeAdminTasks'));
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
