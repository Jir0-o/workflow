<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NoticeController extends Controller
{
    /**
     * Display a listing of the resource.
     */ 
    public function index()
    {
        //today's date
        $todayDate = Carbon::today();
        $todayNotice = Notice::where('status', 0)
            ->whereDate('end_date', '>=', $todayDate)
            ->orderBy('notice_date', 'desc')
            ->get();

        $allNotice = Notice::orderBy('notice_date', 'desc')->get();
        $expried = Notice::where('status', 1)->orderBy('notice_date', 'desc')->get();


        $noticeDate = Notice::all();
        //auto Notic status update
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



        //count all notice
        $countToday = Notice::where('status', 0)
        ->whereDate('end_date', '>=', $todayDate)
        ->orderBy('notice_date', 'desc')
        ->count();
        $countAll = Notice::count();
        $countExpried = Notice::where('status', 1)->count();

        return view('user.other_tab.notice_board', compact('todayNotice','allNotice','expried','countToday','countAll','countExpried'));
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
        try {
            // Validate the request data
            $validated = $request->validate([ 
                'title' => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'description' => 'nullable|string'
            ]);

            // Parse and convert start_date and end_date to UTC for database storage
            $startDateTime = Carbon::createFromFormat('Y-m-d\TH:i', $validated['start_date'], 'Asia/Dhaka')->setTimezone('Asia/Dhaka');
            $endDateTime = Carbon::createFromFormat('Y-m-d\TH:i', $validated['end_date'], 'Asia/Dhaka')->setTimezone('Asia/Dhaka');   
                
            $notice = new Notice();
            $notice->title = $validated['title'];
            $notice->user_id = Auth::user()->id;
            $notice->description = $request->description;
            $notice->notice_date = Carbon::now('Asia/Dhaka')->setTimezone('Asia/Dhaka');
            $notice->start_date = $startDateTime->setTimezone('Asia/Dhaka');
            $notice->end_date = $endDateTime->setTimezone('Asia/Dhaka');;
            $notice->save();
    
            return response()->json([
                'status' => true,
                'message' => 'Notice created successfully',
                'data' => [
                    'notice_id' => $notice->id,
                    'title' => $notice->title,
                    'description' => $notice->description,
                    'start_date' => $notice->start_date,
                    'end_date' => $notice->end_date,
                ]
            ], 201);
    
        } catch (\Exception $e) {
            Log::error('Error creating notice: '.$e->getMessage());
    
            // Return a JSON error response
            return response()->json([
                'status' => false,
                'message' => 'Failed to create notice',
                'error' => $e->getMessage()
            ], 500);
        }
    }    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $notice = Notice::findOrFail($id);
    
            // Format dates for datetime-local input fields
            $formattedNotice = [
                'id' => $notice->id,
                'title' => $notice->title,
                'description' => $notice->description,
                'start_date' => $notice->start_date ? $notice->start_date->timezone('Asia/Dhaka')->format('Y-m-d\TH:i') : null,
                'end_date' => $notice->end_date ? $notice->end_date->timezone('Asia/Dhaka')->format('Y-m-d\TH:i') : null,
                'status' => $notice->status,
            ];
    
            return response()->json([
                'status' => true,
                'notice' => $formattedNotice,
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading notice data: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to load notice data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    
    public function update(Request $request, string $id)
    {
        try {
            // Validate the request data
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'description' => 'nullable|string'
            ]);
    
            $notice = Notice::findOrFail($id);
    
            $notice->title = $validated['title'];
            $notice->description = $request->description;
            $notice->start_date = $validated['start_date'];
            $notice->end_date = $validated['end_date'];
            $notice->save();
    
            return response()->json([
                'status' => true,
                'message' => 'Notice updated successfully',
                'data' => [
                    'notice_id' => $notice->id,
                    'title' => $notice->title,
                    'description' => $notice->description,
                    'start_date' => $notice->start_date,
                    'end_date' => $notice->end_date,
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error updating notice: '.$e->getMessage());
    
            // Return a JSON error response
            return response()->json([
                'status' => false,
                'message' => 'Failed to update notice',
                'error' => $e->getMessage()
            ], 500);
        }
    }    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $notice = Notice::findOrFail($id);
        $notice->delete();
 
        return back()->with('success', 'Notice deleted successfully.');
    }
    public function noticeEnd(string $id)
    {
        $notice = Notice::findOrFail($id);
        //update notice status
        $notice->status = 1;
        $notice->end_date = null;
        $notice->save();
 
        return back()->with('success', 'Notice ended successfully.');
    }
    public function noticeStart(string $id)
    {
        $notice = Notice::findOrFail($id);
        //update notice status
        $notice->status = 0;
        $notice->notice_date = Carbon::now('Asia/Dhaka')->setTimezone('Asia/Dhaka');
        $notice->start_date = Carbon::now('Asia/Dhaka')->setTimezone('Asia/Dhaka');
        $notice->end_date = Carbon::now('Asia/Dhaka')->addDay()->setTimezone('Asia/Dhaka');
        $notice->save();
 
        return back()->with('success', 'Notice started successfully.');
    }

    public function getNotices()
    {

    }

}
