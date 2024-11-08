
@extends('layouts.master')
@section('content')
<div class="row row-cols-1 row-cols-md-4 mb-4">
    <div class="col mb-4">
        <div class="card total-user-hover" style="height: 135px;">
            <div class="d-flex justify-content-between mb-3">
                <div class="card-body">
                    <h6 class="d-block text-500 font-medium mb-3">TOTAL USERS</h6>
                    <div class="text-900 fs-4" id="total_users"></div>
                </div>
                <div class="d-flex align-items-center justify-content-center rounded"
                     style="width: 2.5rem; height: 2.5rem; margin-top: 10px; margin-right: 5px">
                    <i class='bx bxs-user-account'></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col mb-4">
        <div class="card total-poes-hover" style="height: 135px;">
            <div class="d-flex justify-content-between mb-3">
                <div class="card-body">
                    <h6 class="d-block text-500 font-medium mb-3">TOTAL PENDING</h6>
                    <div class="text-900 fs-4" id="total_poes"></div>
                </div>
                <div class="d-flex align-items-center justify-content-center rounded"
                     style="width: 2.5rem; height: 2.5rem; margin-top: 10px; margin-right: 5px">
                    <i class='bx bxs-id-card'></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col mb-4">
        <div class="card total-standards-hover" style="height: 135px;">
            <div class="d-flex justify-content-between mb-3">
                <div class="card-body">
                    <h6 class="d-block text-500 font-medium mb-3">TOTAL COMPLETED</h6>
                    <div class="text-900 fs-4" id="total_Standard"></div>
                </div>
                <div class="d-flex align-items-center justify-content-center rounded"
                     style="width: 2.5rem; height: 2.5rem; margin-top: 10px; margin-right: 5px">
                    <i class='bx bxs-layout'></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col mb-4">
        <div class="card total-criterias-hover" style="height: 135px;">
            <div class="d-flex justify-content-between mb-3">
                <div class="card-body">
                    <h6 class="d-block text-500 font-medium mb-3">TOTAL SCHOOL</h6>
                    <div class="text-900 fs-4" id="total_Criteria"></div>
                </div>
                <div class="d-flex align-items-center justify-content-center rounded"
                     style="width: 2.5rem; height: 2.5rem; margin-top: 10px; margin-right: 5px">
                    <i class='bx bx-check-double'></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col mb-4">
        <div class="card total-section-hover" style="height: 135px;">
            <div class="d-flex justify-content-between mb-3">
                <div class="card-body">
                    <h6 class="d-block text-500 font-medium mb-3">TOTAL DISCIPLINE</h6>
                    <div class="text-900 fs-4" id="total_Section"></div>
                </div>
                <div class="d-flex align-items-center justify-content-center rounded"
                     style="width: 2.5rem; height: 2.5rem; margin-top: 10px; margin-right: 5px">
                    <i class='bx bxs-folder-open'></i>
                </div>
            </div>
        </div>
    </div>

       <!-- New User Activity Card -->
    <div class="col mb-4">
        <div class="card total-activity-hover" style="height: 135px;">
            <div class="d-flex justify-content-between mb-3">
                <div class="card-body">
                    <h6 class="d-block text-500 font-medium mb-3">USER ACTIVITY</h6>
                    <div id="user_activity" class="text-900 fs-6">
                        <!-- Populate each user's login details and time here -->
                        @foreach ($activeUsers as $users)
                            <div>
                                <strong>{{ $user->name }}:</strong> 
                                Logged in at {{ \Carbon\Carbon::parse($users->login_time)->format('d F Y, h:i A') }}
                                <span id="duration-{{ $user->id }}" class="text-muted">
                                    (Active: <span class="active-time" data-id="{{ $users->id }}" data-start="{{ \Carbon\Carbon::parse($users->login_time)->timestamp }}">00:00:00</span>)
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-center rounded"
                     style="width: 2.5rem; height: 2.5rem; margin-top: 10px; margin-right: 5px">
                    <i class='bx bx-time-five'></i>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@can('Dashboard Task View')
<div class="row mb-4">
    <div class="col-12 col-md-6 col-lg-6">
        <!-- Roles -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="row">
                    <div class="col-12 col-md-6">
                        @if ($user->hasRole('Super Admin'))
                        <h5>Today's All Pending</h5>
                        @else
                        <h5>Today's Pending Tasks</h5>
                        @endif
                    </div>
                    <div class="col-12 col-md-6">
                        @if ($user->hasRole('Super Admin'))
                        <div class="float-end">
                            <a href="{{ route('asign_tasks.index') }}" class="btn btn-primary">
                                <i class="bx bx-edit-alt me-1"></i> View Assign Details
                            </a>
                        </div>
                        @else
                        <div class="float-end">
                            <a href="{{ route('tasks.index') }}" class="btn btn-primary">
                                <i class="bx bx-edit-alt me-1"></i> View Details
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="table-responsive text-nowrap p-3">
                <table id="datatable1" class="table">
                    <thead>
                        @if ($user->hasRole('Super Admin'))
                        <tr>
                            <th>SL</th>
                            <th>Start Date</th>
                            <th>Due Date</th>
                            <th>User Name</th>
                            <th>Task</th>
                            <th>Work Task</th>
                            </tr>
                        @else
                        <tr>
                            <th>SL</th>
                            <th>Start Date</th>
                            <th>Due Date</th>
                            <th>Task</th>
                        </tr>
                        @endif
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @if ($user->hasRole('Super Admin'))
                        @foreach ($pendingAdminTasks as $Task)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $Task->created_at->format('d F Y, h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($Task->submit_date)->format('d F Y') }}</td>
                            <td>{{ $Task->user->name }}</td>
                            <td>{!! nl2br(e($Task->description)) !!}</td>
                            <td>{{ $Task->work_status }}</td>
                        </tr>
                        @endforeach
                        @else
                        @foreach ($pendingUserTasks as $Task)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $Task->created_at->format('d F Y, h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($Task->submit_date)->format('d F Y') }}</td>
                            <td>{!! nl2br(e($Task->description)) !!}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <!--/ Roles -->
    </div>
    <div class="col-12 col-md-6 col-lg-6">
        <!-- Permissions -->
        <div class="card mb-4"> 
            <div class="card-header">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <h5>Recently Completed</h5>
                    </div>
                    <div class="col-12 col-md-6">
                        @if ($user->hasRole('Super Admin'))
                        <div class="float-end">
                            <a href="{{ route('asign_tasks.index') }}" class="btn btn-primary">
                                <i class="bx bx-edit-alt me-1"></i> View Assign Details
                            </a>
                        </div>
                        @else
                        <div class="float-end">
                            <a href="{{ route('tasks.index') }}" class="btn btn-primary">
                                <i class="bx bx-edit-alt me-1"></i> View Details
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="table-responsive text-nowrap p-3">
                <table id="datatable2" class="table">
                    <thead>
                        @if ($user->hasRole('Super Admin'))
                        <tr>
                            <th>SL</th>
                            <th>Start Date</th>
                            <th>Completed Date</th>
                            <th>User Name</th>
                            <th>Task</th>
                            <th>Work Task</th>
                        </tr>
                        @else
                        <tr>
                            <th>SL</th>
                            <th>Start Date</th>
                            <th>Completed Date</th>
                            <th>Task</th>  
                        </tr>
                        @endif
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @if ($user->hasRole('Super Admin'))
                        @foreach ($completeAdminTasks as $Task)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $Task->created_at->format('d F Y, h:i A') }}</td>
                            <td>{{ $Task->submit_by_date ? \Carbon\Carbon::parse($Task->submit_by_date)->format('d F Y, h:i A') : 'Task completed' }}</td>
                            <td>{{ $Task->user->name }}</td>
                            <td>{!! nl2br(e($Task->description)) !!}</td>
                            <td>{{ $Task->work_status }}</td>
                        </tr>
                        @endforeach
                        @else
                        @foreach ($completeUserTasks as $Task)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $Task->created_at->format('d F Y, h:i A') }}</td>
                            <td>{{ $Task->submit_by_date ? \Carbon\Carbon::parse($Task->submit_by_date)->format('d F Y, h:i A') : 'Task completed' }}</td>
                            <td>{!! nl2br(e($Task->description)) !!}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endcan


<div class="card p-3">
    <div id="calendar"></div>
</div>

<script>
$(document).ready(function() {
    // Function to format seconds into HH:MM:SS
    function formatTime(seconds) {
        let hrs = Math.floor(seconds / 3600);
        let mins = Math.floor((seconds % 3600) / 60);
        let secs = seconds % 60;
        return `${hrs.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }

    // Update each user's active time every second
    setInterval(function () {
        $('.active-time').each(function () {
            const userId = $(this).data('id');
            const startTime = $(this).data('start');
            const currentTime = Math.floor(Date.now() / 1000);
            const activeDuration = currentTime - startTime;

            // Update the displayed active time
            $(this).text(formatTime(activeDuration));

            // Send an Ajax request to update `login_hour` in the database
            $.ajax({
                url: "{{ route('updateLoginTime') }}",
                type: 'POST',
                data: {
                    login_id: userId,
                    active_seconds: activeDuration,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    // Optionally, handle success feedback
                },
                error: function () {
                    console.log('Error updating login time');
                }
            });
        });
    }, 1000); // Update every second

    calendarEl = document.getElementById('calendar');
    calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: ['timeline', 'dayGrid', 'timeGrid', 'interaction'],
        editable: true,
        header: {
            left: 'today prev,next',
            center: 'title',
            right: 'timelineDay,timeGridWeek,dayGridMonth'
        },
        defaultView: 'dayGridMonth',
        displayEventEnd: true,
        selectable: true,
    });
    calendar.render();
    
});

</script>
@endsection
