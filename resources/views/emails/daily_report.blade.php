<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Daily Task Report</title>
</head>
<body>
    <h2>Hello, here is your Daily Task Log</h2>

    {{-- 🗓 YESTERDAY --}}
    <h3>🗓 Yesterday ({{ \Carbon\Carbon::yesterday()->format('d F, Y') }})</h3>
    <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse: collapse;">
        <tr>
            {{-- Yesterday Tasks --}}
            <td valign="top" width="50%" style="border: 1px solid #ccc;">
                <h4>Tasks</h4>
                @if($yesterdayTasks->count())
                    <table border="1" cellpadding="6" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>S.L</th>
                                <th>User Name</th>
                                <th>Title</th>
                                <th>Task</th>
                                <th>Status</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($yesterdayTasks as $task)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $task->user->name ?? 'N/A' }}</td>
                                    <td>{{ $task->task_title ?? 'N/A' }}</td>
                                    <td>{!! $task->description ?? 'N/A' !!}</td>
                                    <td>{{ ucfirst($task->status) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($task->submit_date)->format('d F Y, h:i A') }}</td>
                                    <td>
                                        @if($task->submit_by_date)
                                            {{ \Carbon\Carbon::parse($task->submit_by_date)->format('d F Y, h:i A') }}
                                        @else
                                            Not Submitted
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No tasks found for yesterday.</p>
                @endif
            </td>

            {{-- Yesterday Work Plans --}}
            <td valign="top" width="50%" style="border: 1px solid #ccc;">
                <h4>Work Plans Under Tasks</h4>
                @if($yesterdayWorkPlans->count())
                <table border="1" cellpadding="6" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>S.L</th>
                                <th>User Name</th>
                                <th>Title</th>
                                <th>Work Plan</th>
                                <th>Status</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($yesterdayWorkPlans as $task)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $task->user->name ?? 'N/A' }}</td>
                                    <td>{{ $task->task->task_title ?? 'N/A' }}</td>
                                    <td>{!! $task->description ?? 'N/A' !!}</td>
                                    <td>{{ ucfirst($task->status) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($task->submit_date)->format('d F Y, h:i A') }}</td>
                                    <td>
                                        @if($task->submit_by_date)
                                            {{ \Carbon\Carbon::parse($task->submit_by_date)->format('d F Y, h:i A') }}
                                        @else
                                            Not Submitted
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No work plans found for yesterday.</p>
                @endif
            </td>
        </tr>
    </table>

    <br>

    {{-- 🗓 TODAY --}}
    <h3>🗓 Today ({{ \Carbon\Carbon::today()->format('d F, Y') }})</h3>
    <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse: collapse;">
        <tr>
            {{-- Today Tasks --}}
            <td valign="top" width="50%" style="border: 1px solid #ccc;">
                <h4>Tasks</h4>
                @if($todayTasks->count())
                    <table border="1" cellpadding="6" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>S.L</th>
                                <th>User Name</th>
                                <th>Title</th>
                                <th>Task</th>
                                <th>Status</th>
                                <th>Start</th>
                                <th>End</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($todayTasks as $task)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $task->user->name ?? 'N/A' }}</td>
                                    <td>{{ $task->task_title ?? 'N/A' }}</td>
                                    <td>{!! $task->description ?? 'N/A' !!}</td>
                                    <td>{{ ucfirst($task->status) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($task->submit_date)->format('d F Y, h:i A') }}</td>
                                    <td>
                                        @if($task->submit_by_date)
                                            {{ \Carbon\Carbon::parse($task->submit_by_date)->format('d F Y, h:i A') }}
                                        @else
                                            Not Submitted
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No tasks found for today.</p>
                @endif
            </td>

            {{-- Today Work Plans --}}
            <td valign="top" width="50%" style="border: 1px solid #ccc;">
                <h4>Work Plans Under Tasks</h4>
                @if($todayWorkPlans->count())
                    <table border="1" cellpadding="6" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>S.L</th>
                                <th>User Name</th>
                                <th>Title</th>
                                <th>Work Plan</th>
                                <th>Status</th>
                                <th>Start</th>
                                <th>End</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($todayWorkPlans as $task)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $task->user->name ?? 'N/A' }}</td>
                                    <td>{{ $task->task->task_title ?? 'N/A' }}</td>
                                    <td>{!! $task->description ?? 'N/A' !!}</td>
                                    <td>{{ ucfirst($task->status) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($task->submit_date)->format('d F Y, h:i A') }}</td>
                                    <td>
                                        @if($task->submit_by_date)
                                            {{ \Carbon\Carbon::parse($task->submit_by_date)->format('d F Y, h:i A') }}
                                        @else
                                            Not Submitted
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No work plans found for today.</p>
                @endif
            </td>
        </tr>
    </table>

    <h3>🗓 Today's Working Data ({{ \Carbon\Carbon::today()->format('d F, Y') }})</h3>
    <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse: collapse;">
        <tr>
            {{-- Today Tasks --}}
            <td valign="top" width="50%" style="border: 1px solid #ccc;">
                <h4>Working Users</h4>
                @if($workingUsers->count())
                    <table border="1" cellpadding="6" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>S.L</th>
                                <th>User Name</th>
                                <th>Login Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($workingUsers as $working)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $working->name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($working->login_time)->format('d F Y, h:i A') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No user logged in today.</p>
                @endif
            </td>

            {{-- Today Work Plans --}}
            <td valign="top" width="50%" style="border: 1px solid #ccc;">
                <h4>Not Working Users</h4>
                @if($notWorkingUsers->count())
                    <table border="1" cellpadding="6" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>S.L</th>
                                <th>User Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notWorkingUsers as $task)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $task->name ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No user working today.</p>
                @endif
            </td>
        </tr>
    </table>

    <br>
    <p>Regards,<br>Task Manager System</p>
</body>
</html>
