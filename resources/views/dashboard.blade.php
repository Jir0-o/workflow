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

/* .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    cursor: pointer;
} */

.card .d-flex i {
    color: #6c757d; /* Default icon color */
    transition: color 0.3s ease;
}

.card:hover .d-flex i {
    color: #007bff; /* Highlight color on hover */
}

.user-profile-popup {
    position: absolute;
    background: white;
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 10px;
    width: 220px;
    display: none;
    z-index: 1000;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
}


.user-hover {
    color: inherit !important; /* Keeps the text color the same as surrounding text */
    text-decoration: none !important; /* Removes underline */
    font-weight: bold; /* Makes the text bold */
}

/* Login status box */

.user-box {
    width: 100%;
    max-width: auto;
    margin: 20px auto;
    border: 1px solid #ccc;
    border-radius: 5px;
    overflow: hidden;
}
.header {
    background: #6d757e;
    color: white;
    padding: 10px;
    cursor: pointer;
    font-weight: bold;
    display: flex;
    justify-content: space-between;
}
.content {
    display: none;
    padding: 10px;
    color: black;
    background: #f8f9fa;
}
.loading {
    text-align: center;
    padding: 10px;
    font-style: italic;
    display: none;
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
            <!-- Profile Pop-up -->
            <div id="userProfilePopup" class="user-profile-popup shadow-lg rounded p-3 bg-white" style="width: 350px;">
                <input type="hidden" id="profile_user_id" name="profile_user_id" value="">

                <!-- Profile Header -->
                <div class="text-center">
                    <img id="profilePhoto" alt="User Badge" class="rounded-circle border my-2" width="80" height="80">
                    <h5 id="username" class="mb-1 fw-bold" style="color: #30D158; display: inline;"></h5> 
                    <span id="roleName" class="badge bg-danger ms-1"></span>
                    <h6 id="userRank" class="text-muted" style="color: #B0B3B8;"></h6>
                </div>

                <!-- User Details -->
                <div class="px-3 text-start">
                    <p class="mb-2"><strong>📧 Email:</strong> <span id="contractEmail"></span></p>
                    <p class="mb-2"><strong>🏠 Address:</strong> <span id="userAddress"></span></p>
                </div>

                <!-- Work Stats -->
                <div class="text-center mt-3">
                    <div class="d-flex justify-content-between px-3">
                        <p class="mb-1"><strong>✅ Work Plans:</strong> <span class="badge bg-primary" id="completedWorkPlan"></span></p>
                        <p class="mb-1"><strong>📌 Tasks:</strong> <span class="badge bg-info" id="completedTask"></span></p>
                        <p class="mb-1"><strong>⏳ Pending Work:</strong> <span class="badge bg-warning" id="pendingTasks"></span></p>
                    </div>
                </div>

                <!-- Login Details -->
                <div class="text-center mt-3">
                    <p class="mb-1"><small>🕒 <strong>Last Login:</strong> <span id="lastSeen"></span></small></p>
                    <p class="mb-1"><small>⏳ <strong>Session Duration:</strong> <span id="loginDuration"></span></small></p>
                </div>
            </div>

            
                            
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
                                                        <td>
                                                            <a href="{{ route('working_profile.show', $Working->user->id) }}"
                                                            class="user-hover"
                                                            data-user-id="{{ $Working->user->id }}">
                                                                <img src="{{ $Working->user->profile_photo_path ? asset('storage/' . $Working->user->profile_photo_path) : asset('default-profile.jpg') }}" 
                                                                    alt="user" class="rounded-circle" width="40" height="40">
                                                            </a>
                                                        </td>
                                                        <td>{!! $Working->description !!}</td>
                                                        <td>
                                                            <a href="{{ route('working_profile.show', $Working->user->id) }}"
                                                            class="user-hover"
                                                            data-user-id="{{ $Working->user->id }}">
                                                                {{ $Working->user->name }}
                                                            </a>
                                                        </td>
                                                        <td>{{ \Carbon\Carbon::parse($Working->submit_date)->format('d F Y') }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                @foreach ($WorkingAuthData as $key => $Pending)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>
                                                            <a href="{{ route('working_profile.show', $Pending->user->id) }}"
                                                            class="user-hover"
                                                            data-user-id="{{ $Pending->user->id }}">
                                                                <img src="{{ $Pending->user->profile_photo_path ? asset('storage/' . $Pending->user->profile_photo_path) : asset('default-profile.jpg') }}" 
                                                                    alt="user" class="rounded-circle" width="40" height="40">
                                                            </a>
                                                        </td>
                                                        <td>{!! $Pending->description !!}</td>
                                                        <td>
                                                            <a href="{{ route('working_profile.show', $Pending->user->id) }}"
                                                            class="user-hover"
                                                            data-user-id="{{ $Pending->user->id }}">
                                                                {{ $Pending->user->name }}
                                                            </a>
                                                        </td>
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
                                        @foreach ($usersWithoutWorkPlan as $key => $notWorking)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                <a href="{{ route('working_profile.show', $notWorking->id) }}"
                                                   class="user-hover"
                                                   data-user-id="{{ $notWorking->id }}">
                                                    @if($notWorking->profile_photo_path)
                                                        <img src="{{ asset('storage/' . $notWorking->profile_photo_path) }}" alt="user" class="rounded-circle" width="40" height="40">
                                                    @else
                                                        <img src={{ asset('default-profile.jpg') }} alt="Default Profile" width="50" height="50" class="rounded-circle">
                                                    @endif
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('working_profile.show', $notWorking->id) }}"
                                                   class="user-hover"
                                                   data-user-id="{{ $notWorking->id }}">
                                                    {{ $notWorking->name }}
                                                </a>
                                            </td>
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
                                                    <td>
                                                        <a href="{{ route('working_profile.show', $Working->user->id) }}"
                                                        class="user-hover"
                                                        data-user-id="{{ $Working->user->id }}">
                                                            <img src="{{ $Working->user->profile_photo_path ? asset('storage/' . $Working->user->profile_photo_path) : asset('default-profile.jpg') }}" 
                                                                alt="user" class="rounded-circle" width="40" height="40">
                                                        </a>
                                                    </td>
                                                    <td>{!! $Working->description !!}</td>
                                                    <td>
                                                        <a href="{{ route('working_profile.show', $Working->user->id) }}"
                                                        class="user-hover"
                                                        data-user-id="{{ $Working->user->id }}">
                                                            {{ $Working->user->name }}
                                                        </a>
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($Working->submit_date)->format('d F Y') }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            @foreach ($WorkingAuthData as $key => $Pending)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>
                                                        <a href="{{ route('working_profile.show', $Pending->user->id) }}"
                                                        class="user-hover"
                                                        data-user-id="{{ $Pending->user->id }}">
                                                            <img src="{{ $Pending->user->profile_photo_path ? asset('storage/' . $Pending->user->profile_photo_path) : asset('default-profile.jpg') }}" 
                                                                alt="user" class="rounded-circle" width="40" height="40">
                                                        </a>
                                                    </td>
                                                    <td>{!! $Pending->description !!}</td>
                                                    <td>
                                                        <a href="{{ route('working_profile.show', $Pending->user->id) }}"
                                                        class="user-hover"
                                                        data-user-id="{{ $Pending->user->id }}">
                                                            {{ $Pending->user->name }}
                                                        </a>
                                                    </td>
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
                                    @foreach ($usersWithoutWorkPlan as $key => $notWorking)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <a href="{{ route('working_profile.show', $notWorking->id) }}"
                                               class="user-hover"
                                               data-user-id="{{ $notWorking->id }}">
                                                @if($notWorking->profile_photo_path)
                                                    <img src="{{ asset('storage/' . $notWorking->profile_photo_path) }}" alt="user" class="rounded-circle" width="40" height="40">
                                                @else
                                                    <img src={{ asset('default-profile.jpg') }} alt="Default Profile" width="50" height="50" class="rounded-circle">
                                                @endif
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('working_profile.show', $notWorking->id) }}"
                                               class="user-hover"
                                               data-user-id="{{ $notWorking->id }}">
                                                {{ $notWorking->name }}
                                            </a>
                                        </td>
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
                                    <td>
                                        <a href="{{ route('working_profile.show', $WorkingHour->user->id) }}"
                                            class="user-hover"
                                            data-user-id="{{ $WorkingHour->user->id }}">
                                            <img src="{{ $WorkingHour->user->profile_photo_path ? asset('storage/' . $WorkingHour->user->profile_photo_path) : asset('default-profile.jpg') }}" 
                                                alt="user" class="rounded-circle" width="40" height="40">
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('working_profile.show', $WorkingHour->user->id) }}"
                                            class="user-hover"
                                            data-user-id="{{ $WorkingHour->user->id }}">
                                            {{ $WorkingHour->user->name }}
                                        </a>
                                    </td>
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
                                    <td>
                                        <a href="{{ route('working_profile.show', $WorkingHour->user->id) }}"
                                            class="user-hover"
                                            data-user-id="{{ $WorkingHour->user->id }}">
                                            <img src="{{ $WorkingHour->user->profile_photo_path ? asset('storage/' . $WorkingHour->user->profile_photo_path) : asset('default-profile.jpg') }}" 
                                                alt="user" class="rounded-circle" width="40" height="40">
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('working_profile.show', $WorkingHour->user->id) }}"
                                            class="user-hover"
                                            data-user-id="{{ $WorkingHour->user->id }}">
                                            {{ $WorkingHour->user->name }}
                                        </a>
                                    </td>
                                    <td>{{ $WorkingHour->formatted_hours}}</td>
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
                                    <td>
                                        <a href="{{ route('working_profile.show', $WorkingHour->user->id) }}"
                                            class="user-hover"
                                            data-user-id="{{ $WorkingHour->user->id }}">
                                            <img src="{{ $WorkingHour->user->profile_photo_path ? asset('storage/' . $WorkingHour->user->profile_photo_path) : asset('default-profile.jpg') }}" 
                                                alt="user" class="rounded-circle" width="40" height="40">
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('working_profile.show', $WorkingHour->user->id) }}"
                                            class="user-hover"
                                            data-user-id="{{ $WorkingHour->user->id }}">
                                            {{ $WorkingHour->user->name }}
                                        </a>
                                    </td>
                                    <td>{{ $WorkingHour->formatted_hours}}</td>
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
                                                <a href="{{ route('working_profile.show', $user->id) }}"
                                                   class="user-hover"
                                                   data-user-id="{{ $user->id }}">
                                                    {{ $user->name }}
                                                </a>{{ !$loop->last ? ', ' : '' }}
                                            @endforeach
                                        </td>
                                        <td>
                                            @if($task['today_work_hours'] === '00:00:00' || empty($task['today_work_hours']))
                                                No Data Today
                                            @else
                                                {{ $task['today_work_hours'] }}
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
                                                <a href="{{ route('working_profile.show', $user->id) }}"
                                                   class="user-hover"
                                                   data-user-id="{{ $user->id }}">
                                                    {{ $user->name }}
                                                </a>{{ !$loop->last ? ', ' : '' }}
                                            @endforeach
                                        </td>
                                        <td>
                                            @if($task['pending_hours'] === '00:00:00' || empty($task['pending_hours']))
                                                No pending hours
                                            @else
                                                {{ $task['pending_hours'] }}
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
                                                <a href="{{ route('working_profile.show', $user->id) }}"
                                                   class="user-hover"
                                                   data-user-id="{{ $user->id }}">
                                                    {{ $user->name }}
                                                </a>{{ !$loop->last ? ', ' : '' }}
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

<div class="user-box">
    <div class="header" onclick="toggleSection('loggedInUsers')">
        Online Users <span>+</span>
    </div>
    <div class="loading" id="loadingLoggedIn">Loading...</div>
    <div class="content" id="loggedInUsers"></div>
</div>

<div class="user-box">
    <div class="header" onclick="toggleSection('notLoggedInUsers')">
        Not Logged In Users <span>+</span>
    </div>
    <div class="loading" id="loadingNotLoggedIn">Loading...</div>
    <div class="content" id="notLoggedInUsers"></div>
</div>




<script>
    function toggleNotice(button) {
        const details = button.closest('.notice-card').querySelector('.notice-details');
        const isCollapsed = details.style.display === 'none';
        
        details.style.display = isCollapsed ? 'block' : 'none';
        button.textContent = isCollapsed ? '🔽' : '▶️';
    }

    function toggleSection(sectionId) {
    let contentDiv = $("#" + sectionId);
    let loadingDiv = $("#loading" + sectionId.charAt(0).toUpperCase() + sectionId.slice(1));

    if (contentDiv.is(":visible")) {
        contentDiv.slideUp();
        contentDiv.prev().find("span").text("+");
    } else {
        if (contentDiv.is(":empty")) {
            loadingDiv.show();
            $.ajax({
                url: "{{ route('get.users') }}",
                method: "GET",
                success: function(data) {
                    let usersList = data[sectionId === "loggedInUsers" ? "loggedInUsers" : "notLoggedInUsers"];
                    let html = usersList.length > 0 
                        ? usersList.map(user => {
                            let userId = sectionId === "loggedInUsers" ? user.user_id : user.id;
                            return `<a href="{{ route('working_profile.show', '') }}/${userId}" class="user-hover" data-user-id="${userId}">${user.name}</a>`;
                        }).join(", ") 
                        : "No users found.";

                    contentDiv.html(html);
                    loadingDiv.hide();
                    contentDiv.slideDown();
                },
                error: function() {
                    contentDiv.html("Error loading users.");
                    loadingDiv.hide();
                }
            });
        } else {
            contentDiv.slideDown();
        }
        contentDiv.prev().find("span").text("-");
    }
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
            initialView: 'timeGridWeek', // Weekly view
            height: 300,  // Reduced height
            aspectRatio: 1.5, // Keeps it compact
            editable: true,
            selectable: true,
            slotDuration: '00:15:00', // More detailed time slots
            slotLabelInterval: '01:00', // Hourly labels for clarity
            displayEventEnd: true,
            eventTimeFormat: { // Show detailed event time
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            },
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
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

    // Function to convert login time to Bangladesh Time (GMT+6) with AM/PM
    function convertToBangladeshTime(loginDate, loginTime) {
        if (!loginDate || !loginTime) return "Not Logged In";

        // Combine date & time into full datetime string
        let fullDateTime = `${loginDate} ${loginTime}`;

        // Convert to Date object
        let date = new Date(fullDateTime);

        // If the date is invalid, return error message
        if (isNaN(date.getTime())) return "Invalid Date";

        // Convert to Bangladesh Time (GMT+6)
        let options = { 
            timeZone: "Asia/Dhaka",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
            hour12: true 
        };
        
        return new Intl.DateTimeFormat("en-US", options).format(date);
    }

    // Function to format login duration (HH:MM:SS) into "X hours Y minutes"
    function formatDuration(timeString) {
        if (!timeString) return "Not Logged In";

        let parts = timeString.split(":");
        if (parts.length !== 3) return "Invalid Duration";

        let hours = parseInt(parts[0], 10);
        let minutes = parseInt(parts[1], 10);

        return `${hours}h ${minutes}m`;
    }

    $(document).on("mouseenter", ".user-hover", function (e) {
        clearTimeout($("#userProfilePopup").data("timeout"));

        let userId = $(this).data("user-id");
        if (!userId) return; // Ensure userId exists

        // Show "Loading..." before fetching data
        $("#username").text("Loading...");
        $("#userRank").text("Loading...");
        $("#profilePhoto").attr("src", "{{ asset('storage/default-profile.png') }}");
        $("#roleName").text("Loading...");
        $("#contractEmail").text("Loading...");
        $("#userAddress").text("Loading...");
        $("#loginTime").text("Loading...");
        $("#loginDuration").text("Loading...");
        $("#lastSeen").text("Loading...");
        $("#pendingTasks").text("Loading...");
        $("#completedTask").text("Loading...");
        $("#completedWorkPlan").text("Loading...");

        // **AJAX request to fetch user data**
        $.ajax({
            url: "{{ route('get.user.profile', '') }}/" + userId, // Fix route formatting
            type: "GET",
            dataType: "json",
            success: function (data) {
                let user = data.data.user;
                let userDetails = data.data.user.user_detail[0];
                let loginTime = data.data.loginTime;

                // Update user details
                $("#username").text(user.name);
                $("#userRank").text(userDetails?.user_title|| "Info Not Updated");
                $("#profilePhoto").attr("src", user.profile_photo_path ? "{{ asset('storage') }}/" + user.profile_photo_path : "{{ asset('default-profile.png') }}");
                $("#roleName").text(userDetails?.role_name || "Info Not Updated");
                $("#contractEmail").text(userDetails?.email || "Info Not Updated");
                $("#userAddress").text(userDetails?.address || "Info Not Updated");

                $("#pendingTasks").text(data.data.pendingWork);
                $("#completedTask").text(data.data.completedTask);
                $("#completedWorkPlan").text(data.data.completedWork);

                // Convert last login time
                $("#lastSeen").text(loginTime?.login_time && loginTime?.login_date ? convertToBangladeshTime(loginTime.login_date, loginTime.login_time) : "Not Logged In");

                // Convert login duration
                $("#loginDuration").text(loginTime?.login_hour ? formatDuration(loginTime.login_hour) : "Not Logged In");
            },
            error: function () {
                $("#username").text("Error loading data");
            }
        });

    // **Position popup next to the cursor or above it based on available space**
    let popupHeight = $("#userProfilePopup").outerHeight();
    let popupWidth = $("#userProfilePopup").outerWidth();
    let windowHeight = $(window).height();
    let windowWidth = $(window).width();
    let cursorY = e.pageY;
    let cursorX = e.pageX;

    // Check if there is enough space below the cursor
    if (cursorY + popupHeight + 10 > windowHeight) {
        // Not enough space below, show above the cursor
        $("#userProfilePopup")
            .css({
                top: cursorY - popupHeight - 10 + "px", 
                left: cursorX + 15 + "px"
            })
            .fadeIn(200);
    } else {
        // Enough space below, show below the cursor
        $("#userProfilePopup")
                .css({
                    top: cursorY + 10 + "px", 
                    left: cursorX + 15 + "px"
                })
                .fadeIn(200);
        }
    });

    // **Hide popup on mouseleave**
    $(document).on("mouseleave", ".user-hover", function () {
        let hidePopup = setTimeout(function () {
            $("#userProfilePopup").fadeOut(200);
        }, 500);
        $("#userProfilePopup").data("timeout", hidePopup);
    });

    // Keep popup open when hovering over it
    $("#userProfilePopup").hover(
        function () {
            clearTimeout($(this).data("timeout"));
        },
        function () {
            let hidePopup = setTimeout(function () {
                $("#userProfilePopup").fadeOut(200);
            }, 500);
            $(this).data("timeout", hidePopup);
        }
    );
});

</script>
@endsection
