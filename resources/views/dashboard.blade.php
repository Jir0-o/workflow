@extends('layouts.master')
@section('content')
<style>
.notice-board-container {
    padding: 20px;
    background-color: #f8f9fc;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.notice-card {
    border: 2px solid #4e73df;
    border-radius: 10px;
    background: #e6e9ff;
    padding: 20px;
    margin-bottom: 15px;
}

.notice-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.toggle-button {
    font-size: 1.2em;
    background: none;
    border: none;
    cursor: pointer;
    color: #4e73df;
}

.notice-details {
    display: block;
}

.notice-details.collapsed {
    display: none;
}

/* Card hover effect */
.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    cursor: pointer;
}

.card .d-flex i {
    color: #6c757d; /* Default icon color */
    transition: color 0.3s ease;
}

.card:hover .d-flex i {
    color: #007bff; /* Highlight color on hover */
}


</style>
@if (session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif
<!-- Dashboard Container -->
<div class="container-fluid">
    <!-- Top Section: Summary Cards and Notice Board -->
    <div class="row mb-4">
        <!-- Summary Cards -->
        <div class="col-lg-7">
            <div class="row">
                @if (Auth::user()->hasRole('Super Admin'))
                <!-- Total Login Users -->
                <div class="col-6 mb-3">
                    <a href="{{ route('login_details.index') }}" style="text-decoration: none;">
                        <div class="card" style="height: 120px;">
                            <div class="d-flex justify-content-between mb-2">
                                <div class="card-body">
                                    <h6 class="d-block text-500 font-medium mb-2">TODAY LOGIN USERS</h6>
                                    <div class="text-900 fs-5" id="total_login_users">{{$countTotalLoginUser}}</div>
                                </div>
                                <div class="d-flex align-items-center justify-content-center rounded"
                                    style="width: 2.5rem; height: 2.5rem; margin-top: 10px; margin-right: 5px">
                                    <i class='bx bxs-user-check'></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Not Logged In Users -->
                <div class="col-6 mb-3">
                    <a href="{{ route('login_details.index') }}" style="text-decoration: none;">
                        <div class="card" style="height: 120px;">
                            <div class="d-flex justify-content-between mb-2">
                                <div class="card-body">
                                    <h6 class="d-block text-500 font-medium mb-2">TODAY NOT LOGGED IN</h6>
                                    <div class="text-900 fs-5" id="not_logged_in_users">{{$missingInUsersCount}}</div>
                                </div>
                                <div class="d-flex align-items-center justify-content-center rounded"
                                    style="width: 2.5rem; height: 2.5rem; margin-top: 10px; margin-right: 5px">
                                    <i class='bx bxs-user-x'></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                @endif

                <!-- Total Running Projects -->
                <div class="col-6 mb-3">
                    <a @if (Auth::user()->hasRole('Super Admin')) href="{{ route('project_title.index') }}" @else href="{{ route('tasks.index') }}" @endif style="text-decoration: none;">
                        <div class="card" style="height: 120px;">
                            <div class="d-flex justify-content-between mb-2">
                                <div class="card-body">
                                    <h6 class="d-block text-500 font-medium mb-2">@if (Auth::user()->hasRole('Super Admin')) TOTAL RUNNING PROJECTS @else ASSIGNED PROJECTS @endif</h6>
                                    <div class="text-900 fs-5" id="total_running_projects">@if (Auth::user()->hasRole('Super Admin')){{$runningProject}} @else {{$runningAuthProject}} @endif</div>
                                </div>
                                <div class="d-flex align-items-center justify-content-center rounded"
                                    style="width: 2.5rem; height: 2.5rem; margin-top: 10px; margin-right: 5px">
                                    <i class='bx bxs-folder-open'></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Running Tasks -->
                <div class="col-6 mb-3">
                    <a @if (Auth::user()->hasRole('Super Admin')) href="{{ route('asign_tasks.index') }}" @else href="{{ route('tasks.index') }}" @endif style="text-decoration: none;">
                        <div class="card" style="height: 120px;">
                            <div class="d-flex justify-content-between mb-2">
                                <div class="card-body">
                                    <h6 class="d-block text-500 font-medium mb-2">@if (Auth::user()->hasRole('Super Admin'))RUNNING TASKS @else PENDING TASKS @endif</h6>
                                    <div class="text-900 fs-5" id="running_tasks">@if (Auth::user()->hasRole('Super Admin')){{$pendingCount}} @else {{$pendingAuthCount}} @endif</div>
                                </div>
                                <div class="d-flex align-items-center justify-content-center rounded"
                                    style="width: 2.5rem; height: 2.5rem; margin-top: 10px; margin-right: 5px">
                                    <i class='bx bx-task'></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Running Workplans -->
                <div class="col-6 mb-3">
                    <a @if (Auth::user()->hasRole('Super Admin')) href="{{ route('manage_work.index') }}" @else href="{{ route('work_plan.index') }}" @endif style="text-decoration: none;">
                        <div class="card" style="height: 120px;">
                            <div class="d-flex justify-content-between mb-2">
                                <div class="card-body">
                                    <h6 class="d-block text-500 font-medium mb-2">@if (Auth::user()->hasRole('Super Admin'))RUNNING WORKPLANS @else PENDING WORKPLANS @endif</h6>
                                    <div class="text-900 fs-5" id="running_workplans">@if (Auth::user()->hasRole('Super Admin')){{$runningWork}} @else {{$runningAuthWork}} @endif</div>
                                </div>
                                <div class="d-flex align-items-center justify-content-center rounded"
                                    style="width: 2.5rem; height: 2.5rem; margin-top: 10px; margin-right: 5px">
                                    <i class='bx bx-calendar-event'></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                        <!-- New User Activity Card -->
                    <div class="col-6 mb-3">
                    <div class="card" style="height: 120px;">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="card-body">
                                <h6 class="d-block text-500 font-medium mb-3">USER ACTIVITY</h6>
                                <div id="user_activity" class="text-900 fs-6">
                                    <!-- Populate each user's login details and time here -->
                                    @foreach ($activeUsers as $users)
                                        <div>
                                            <strong>{{ $user->name }}:</strong> 
                                            Time: {{ \Carbon\Carbon::parse($users->login_time)->format('d F Y, h:i A') }}
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
         <!-- Dynamic Notice Board or To-Do Table -->
         <div class="col-lg-5">
            @if ($notice->count() > 0)
                <!-- Notice Board -->
                <div class="card notice-board-container" style="height: 100%; overflow-y: auto; padding-bottom: 20px;">
                    <!-- Notice Board Title with Count -->
                    <h3 class="notice-board-title">Notice Board ({{ count($notice) }})</h3>
                    <div class="notice-board" style="max-height: 380px; overflow-y: auto; padding: 10px; border: 1px solid #ccc; border-radius: 8px;">
                        @foreach($notice as $notices)
                            <div class="notice-card mb-3">
                                <div class="notice-header d-flex justify-content-between align-items-center">
                                    <h4 class="notice-title">{{ $notices->title }}</h4>
                                    <button class="toggle-button btn btn-sm btn-outline-secondary" onclick="toggleNotice(this)">🔽</button>
                                </div>
                                <div class="notice-details">
                                    <div class="notice-date">Date: {{ $notices->notice_date->timezone('Asia/Dhaka')->format('d F Y, h:i A') }}</div>
                                    <div class="notice-author">
                                        <span class="author-name">{{ $notices->user->name }}</span>
                                        Posted on {{ $notices->notice_date->format('d F Y') }}
                                    </div>
                                    <div class="notice-message">
                                        <p>{!! $notices->description !!}</p>
                                        <ul class="notice-points">
                                            <li>Start Date: {{ $notices->start_date->format('d F Y') }}</li>
                                            <li>End Date: {{ $notices->end_date->format('d F Y') }}</li>
                                        </ul>
                                    </div>
                                    <div class="notice-offer">🟡 Notice valid till 11:59 PM, {{ $notices->end_date->format('d F Y') }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <!-- Placeholder for To-Do Table in Notice Board's Position -->
                <div id="placeholder-todo" class="card p-lg-4 p-2 mb-4">
                    <h5 class="mb-4">To-Do</h5>
                    <!-- Same To-Do Table Code -->
                    <ul class="nav nav-tabs" id="toDoTab" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="toDoDaily-tab" data-bs-toggle="tab" data-bs-target="#toDoDaily" type="button" role="tab">@if (Auth::user()->hasRole('Super Admin'))Working @else Pending Work @endif</button>
                        </li>
                        @if (Auth::user()->hasRole('Super Admin'))
                        <li class="nav-item">
                            <button class="nav-link" id="toDoWeekly-tab" data-bs-toggle="tab" data-bs-target="#toDoWeekly" type="button" role="tab">Not Working</button>
                        </li>
                        @endif 
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="toDoDaily" role="tabpanel">
                            <div class="table-responsive p-3">
                                @if($WorkingData->isNotEmpty() || $WorkingAuthData->isNotEmpty())
                                    <table id="datatable1" class="table">
                                        <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Photo</th>
                                                <th>Work Plan</th>
                                                <th>Assigned To</th>
                                                <th>Deadline</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (Auth::user()->hasRole('Super Admin'))
                                                @foreach ($WorkingData as $key => $Working)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td><img src="{{ asset('storage/' . $Working->user->profile_photo_path) }}" alt="user" class="rounded-circle" width="40" height="40"></td>
                                                        <td>{!! $Working->description !!}</td>
                                                        <td>{{ $Working->user->name }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($Working->submit_date)->format('d F Y') }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                @foreach ($WorkingAuthData as $key => $Pending)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td><img src="{{ asset('storage/' . $Pending->user->profile_photo_path) }}" alt="user" class="rounded-circle" width="40" height="40"></td>
                                                        <td>{!! $Pending->description !!}</td>
                                                        <td>{{ $Pending->user->name }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($Pending->submit_date)->format('d F Y') }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                @else
                                    @if (Auth::user()->hasRole('Super Admin'))
                                        <div class="text-center py-5">
                                            <h4>Hurray, no users have work for today!<i class="bx bx-happy-beaming bx-sm text-success"></i></h4>
                                            <p>You can assign work plans from <a href="{{ route('manage_work.index') }}">Assign Work Plan Page</a>.</p>
                                        </div>
                                    @else
                                        <div class="text-center py-5">
                                            <h4>Hurray, no work for today! <i class="bx bx-happy-beaming bx-sm text-success"></i></h4>
                                            <p>You can create a work plan from <a href="{{ route('work_plan.index') }}">Create Work Plan</a>.</p>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                        @if (Auth::user()->hasRole('Super Admin'))
                        <div class="tab-pane fade" id="toDoWeekly" role="tabpanel">
                            <div class="table-responsive p-3">
                                <table id="datatable2" class="table">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Photo</th>
                                            <th>User</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($usersWithoutWorkPlan as $key => $notWorking )
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td><img src="{{ asset('storage/' . $notWorking->profile_photo_path) }}" alt="user" class="rounded-circle" width="40" height="40"></td>
                                            <td>{{ $notWorking->name }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

<!-- Middle Section: To-Do Table, Working Hour, and Project Details -->
<div class="row mb-4">
    @if ($notice->count() > 0)
        <!-- To-Do Table -->
        @if (Auth::user()->hasRole('Super Admin'))
            <div class="col-lg-7">
        @else
            <div class="col-lg-12">
        @endif
            <div class="card p-lg-4 p-2 mb-4"> 
                <h5 class="mb-4">To-Do</h5>
                <!-- Nav Tabs -->
                <ul class="nav nav-tabs" id="toDoTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="toDoDaily-tab" data-bs-toggle="tab" data-bs-target="#toDoDaily" type="button" role="tab">@if (Auth::user()->hasRole('Super Admin'))Working @else Pending Work @endif</button>
                    </li>
                    @if (Auth::user()->hasRole('Super Admin'))
                    <li class="nav-item">
                        <button class="nav-link" id="toDoWeekly-tab" data-bs-toggle="tab" data-bs-target="#toDoWeekly" type="button" role="tab">Not Working</button>
                    </li>
                    @endif
                </ul>
                <!-- Tab Panes -->
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="toDoDaily" role="tabpanel">
                        <div class="table-responsive p-3">
                            @if($WorkingData->isNotEmpty() || $WorkingAuthData->isNotEmpty())
                                <table id="datatable1" class="table">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Photo</th>
                                            <th>Work Plan</th>
                                            <th>Assigned To</th>
                                            <th>Deadline</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (Auth::user()->hasRole('Super Admin'))
                                            @foreach ($WorkingData as $key => $Working)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td><img src="{{ asset('storage/' . $Working->user->profile_photo_path) }}" alt="user" class="rounded-circle" width="40" height="40"></td>
                                                    <td>{!! $Working->description !!}</td>
                                                    <td>{{ $Working->user->name }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($Working->submit_date)->format('d F Y') }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            @foreach ($WorkingAuthData as $key => $Pending)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td><img src="{{ asset('storage/' . $Pending->user->profile_photo_path) }}" alt="user" class="rounded-circle" width="40" height="40"></td>
                                                    <td>{!! $Pending->description !!}</td>
                                                    <td>{{ $Pending->user->name }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($Pending->submit_date)->format('d F Y') }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            @else
                                @if (Auth::user()->hasRole('Super Admin'))
                                    <div class="text-center py-5">
                                        <h4>Hurray, no users have work for today!<i class="bx bx-happy-beaming bx-sm text-success"></i></h4>
                                        <p>You can assign work plans from <a href="{{ route('manage_work.index') }}">Assign Work Plan Page</a>.</p>
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <h4>Hurray, no work for today! <i class="bx bx-happy-beaming bx-sm text-success"></i></h4>
                                        <p>You can create a work plan from <a href="{{ route('work_plan.index') }}">Create Work Plan</a>.</p>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                    @if (Auth::user()->hasRole('Super Admin'))
                    <div class="tab-pane fade" id="toDoWeekly" role="tabpanel">
                        <div class="table-responsive p-3">
                            <table id="datatable2" class="table">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Photo</th>
                                        <th>User</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($usersWithoutWorkPlan as $key => $notWorking )
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td><img src="{{ asset('storage/' . $notWorking->profile_photo_path) }}" alt="user" class="rounded-circle" width="40" height="40"></td>
                                        <td>{{ $notWorking->name }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Working Hour Table -->
    @if (Auth::user()->hasRole('Super Admin'))
    <div class="@if($notice->count() > 0) col-lg-5 @else col-lg-7 @endif">
        <div class="card p-lg-4 p-2 mb-4">
            <h5 class="mb-4">Working Hour</h5>
            <!-- Nav Tabs -->
            <ul class="nav nav-tabs" id="workingHourTab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="workingDaily-tab" data-bs-toggle="tab" data-bs-target="#workingDaily" type="button" role="tab">Today</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="workingWeekly-tab" data-bs-toggle="tab" data-bs-target="#workingWeekly" type="button" role="tab">Weekly</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="workingYearly-tab" data-bs-toggle="tab" data-bs-target="#workingYearly" type="button" role="tab">Monthly</button>
                </li>
            </ul>
            <!-- Tab Panes -->
            <div class="tab-content">
                <div class="tab-pane fade show active" id="workingDaily" role="tabpanel">
                    <div class="table-responsive">
                        <table id="DataTable3" class="table">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Hours</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($workingHourToday as $key => $WorkingHour )
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td><img src="{{ asset('storage/' . $WorkingHour->user->profile_photo_path) }}" alt="user" class="rounded-circle" width="40" height="40"></td>
                                    <td>{{ $WorkingHour->user->name }}</td>
                                    <td>{{ $WorkingHour->formatted_hours}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="workingWeekly" role="tabpanel">
                    <div class="table-responsive">
                        <table id="DataTable4" class="table">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Total Hours</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($weeklyWorkingHours as $key => $WorkingHour)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td><img src="{{ asset('storage/' . $WorkingHour->user->profile_photo_path) }}" alt="user" class="rounded-circle" width="40" height="40"></td>
                                    <td>{{ $WorkingHour->user->name }}</td>
                                    <td>{{ $WorkingHour->formatted_hours }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="workingYearly" role="tabpanel">
                    <div class="table-responsive">
                        <table id="DataTable5" class="table">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Total Hours</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($monthlyWorkingHours as $key => $WorkingHour)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td><img src="{{ asset('storage/' . $WorkingHour->user->profile_photo_path) }}" alt="user" class="rounded-circle" width="40" height="40"></td>
                                    <td>{{ $WorkingHour->user->name }}</td>
                                    <td>{{ $WorkingHour->formatted_hours }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

@if (Auth::user()->hasRole('Super Admin'))
    <!-- Project Details Table -->
    @if ($notice->count() === 0)
        <div class="col-lg-5">
        @else
            <div class="col-lg-12">
        @endif
            <div class="card p-lg-4 p-2 mb-4">
                <h5 class="mb-4">Project Hour</h5>
                <!-- Nav Tabs -->
                <ul class="nav nav-tabs" id="projectDetailsTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="projectDaily-tab" data-bs-toggle="tab" data-bs-target="#projectDaily" type="button" role="tab">Today</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="projectWeekly-tab" data-bs-toggle="tab" data-bs-target="#projectWeekly" type="button" role="tab">Pending</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="projectYearly-tab" data-bs-toggle="tab" data-bs-target="#projectYearly" type="button" role="tab">Total</button>
                    </li>
                </ul>
                <!-- Tab Panes -->
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="projectDaily" role="tabpanel">
                        <div class="table-responsive p-3">
                            <table id="ProjectDetailsTableDaily" class="table">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Project Name</th>
                                        <th>Users</th>
                                        <th>Hour</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasksHours as $index => $task)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $task['project_name'] }}</td>
                                        <td>
                                            @foreach($task['users'] as $user)
                                                {{ $user->name }}{{ !$loop->last ? ', ' : '' }}
                                            @endforeach
                                        </td>
                                        <td>
                                            @if($task['today_work_hours'] === '00:00:00' || empty($task['today_work_hours']))
                                                No Data Today
                                            @else
                                                {{ $task['today_work_hours']}} Hrs
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="projectWeekly" role="tabpanel">
                        <div class="table-responsive p-3">
                            <table id="ProjectDetailsTableWeekly" class="table">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Project Name</th>
                                        <th>Users</th>
                                        <th>Hour</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasksHours as $index => $task)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $task['project_name'] }}</td>
                                        <td>
                                            @foreach($task['users'] as $user)
                                                {{ $user->name }}{{ !$loop->last ? ', ' : '' }}
                                            @endforeach
                                        </td>
                                        <td>
                                            @if($task['pending_hours'] === '00:00:00' || empty($task['pending_hours']))
                                                No pending hours
                                            @else
                                                {{ $task['pending_hours'] }} Hrs
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="projectYearly" role="tabpanel">
                        <div class="table-responsive p-3">
                            <table id="ProjectDetailsTableYearly" class="table">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Project Name</th>
                                        <th>Users</th>
                                        <th>Hour</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasksHours as $index => $task)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $task['project_name'] }}</td>
                                        <td>
                                            @foreach($task['users'] as $user)
                                                {{ $user->name }}{{ !$loop->last ? ', ' : '' }}
                                            @endforeach
                                        </td>
                                        <td>
                                            @if($task['completed_hours'] === '00:00:00' || empty($task['completed_hours']))
                                                No hour data
                                            @else
                                                {{ $task['completed_hours'] }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
@endif

    <!-- Calendar Section -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card p-3">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>


<script>
    function toggleNotice(button) {
        const details = button.closest('.notice-card').querySelector('.notice-details');
        const isCollapsed = details.style.display === 'none';
        
        details.style.display = isCollapsed ? 'block' : 'none';
        button.textContent = isCollapsed ? '🔽' : '▶️';
    }

    $(document).ready(function () {
        // Set global defaults for all DataTables
        $.extend($.fn.dataTable.defaults, {
            "pageLength": 4, // Set default number of rows
            "lengthMenu": [4, 10, 25, 50], // Options for dropdown
        });

        // Initialize DataTable for all tables with a specific class or ID
        $('.table').each(function() {
            if ($.fn.DataTable.isDataTable(this)) {
                $(this).DataTable().destroy(); // Reinitialize if already initialized
            }
            $(this).DataTable();
        });

        // Initialize all DataTables on the page
        $('table').DataTable();
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
                const startTime = $(this).data('start'); // Start time from login_time
                const currentTime = Math.floor(Date.now() / 1000); // Current timestamp in seconds
                const activeDuration = currentTime - startTime; // Calculate duration since login_time

                // Update the displayed active time
                $(this).text(formatTime(activeDuration));
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
