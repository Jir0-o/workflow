<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\DetailLogin;

class ActivityController extends Controller
{
    public function store(Request $request)
    {
        // Validate incoming data
        $data = $request->validate([
            'user_id' => 'required|integer',
            'app_name' => 'required|string',
            'duration' => 'required|integer',  // Duration in seconds
            'timestamp' => 'required|date'
        ]);

        // Create a new activity record in the database
        Activity::create($data);

        return response()->json(['message' => 'Activity data stored successfully'], 201);
    }

    public function updateLoginTime(Request $request)
{
    $login = DetailLogin::where('id', $request->login_id)->first();

    if ($login) {
        // Format active time to HH:MM:SS based on `active_seconds` received
        $activeDuration = gmdate('H:i:s', $request->active_seconds);

        DetailLogin::where('id', $request->login_id)
            ->update(['login_hour' => $activeDuration, 'updated_at' => now()]);

        return response()->json(['login_hour' => $activeDuration]);
    }
    
    return response()->json(['error' => 'Login session not found'], 404);
}

}
