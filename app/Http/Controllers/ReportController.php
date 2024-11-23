<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TitleName;
use App\Models\User;
use App\Models\WorkPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = WorkPlan::orderBy('created_at', 'desc')->get();
        $users = User::all();
        $titles = Task::all();
        return view('user.project.report', compact('projects', 'users','titles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'title_name_id' => 'nullable',
        ]);
    
        $projects = WorkPlan::orderBy('created_at', 'desc')->get();
        $users = User::all();
        $titles = Task::all();
    

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $dateField = $request->date_criteria;
        $query = WorkPlan::whereBetween($dateField, [$startDate, $endDate]);
    
        if ($request->filled('title_name_id')) {
            $query->where('title_name_id', $request->title_name_id);
        }
    
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
    
        if ($request->filled('user')) {
            $query->where('user_id', $request->user);
        }
    
        $tasks = $query->get();
        $selectedUser = $request->filled('user') ? User::find($request->user) : null;
        $selectedTitle = $request->filled('title_name_id') ? Task::find($request->title_name_id) : null;

        $oldInput = $request->all(); 
        $startDate = Carbon::parse($oldInput['start_date']);
        $endDate = Carbon::parse($oldInput['end_date']);
        $formattedStartDate = $startDate->format('d-F, Y');
        $formattedEndDate = $endDate->format('d-F, Y');
    
        return view('user.project.report', [
            'tasks' => $tasks,
            'projects' => $projects,
            'users' => $users,
            'titles' => $titles,
            'selectedUser' => $selectedUser,
            'selectedTitle' => $selectedTitle,
            'formattedStartDate' => $formattedStartDate,
            'formattedEndDate' => $formattedEndDate,
            'oldInput' => $oldInput,
        ]);
    
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

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

    public function generateReport(Request $request)
    {

    }
}