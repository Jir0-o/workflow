@extends('layouts.master')
@section('content')

<style>
    .pinned {
    color: red;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<h4 class="py-2 m-4"><span class="text-muted fw-light">User Login Details</span></h4>

<div class="row mt-5">
    <div class="col-12 col-md-12 col-lg-12">
        <div class="card p-lg-4 p-2">

        <!-- Nav tabs -->
        <div class="container">
            <div class="row justify-content-center">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#Pending"
                            type="button" role="tab" aria-controls="home" aria-selected="true">
                            Today's Login
                            <span class="badge bg-primary"> {{$loginCount}}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#Incomplete"
                            type="button" role="tab" aria-controls="profile" aria-selected="false">
                            All Login
                            <span class="badge bg-primary">{{$allLoginCount}}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="current-tab" data-bs-toggle="tab" data-bs-target="#current"
                            type="button" role="tab" aria-controls="current" aria-selected="false">
                            Current User Status
                            <span class="badge bg-primary">{{$loginCount}}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="inactive-tab" data-bs-toggle="tab" data-bs-target="#inactive"
                            type="button" role="tab" aria-controls="inactive" aria-selected="false">
                            Not Login Today
                            <span class="badge bg-primary">{{$missingInDetailsLoginsCount}}</span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>

               <!-- Edit Login Details Modal -->
               <div class="modal fade" id="editLoginModal" tabindex="-1" aria-labelledby="editLoginModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editLoginModalLabel">Edit Login Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editLoginForm" method="POST">
                                <meta name="csrf-token" content="{{ csrf_token() }}">
                                @method('PUT')
                                <input type="hidden" id="logId" name="log_id">
                                <div class="mb-3">
                                    <label for="editDate">Login Date</label>
                                    <input id="editDate" name="login_date" type="date" required class="form-control" placeholder="Date">
                                </div>
                                <div class="mb-3">
                                    <label for="editTime">Login Time</label>
                                    <input id="editTime" name="login_time" type="time"  class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="editLogTime">Logout Time</label>
                                    <input id="editLogTime" name="login_time" type="time"  class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="editStatus">Status</label>
                                    <select id="editStatus" name="status" class="form-control" required>
                                        <option value="0">Logged In</option>
                                        <option value="1">Logged Out</option>
                                    </select>
                                </div>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    

            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane active" id="Pending" role="tabpanel" aria-labelledby="home-tab">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5>Today's Login</h5>
                            <i class="pin-icon" data-tab-id="home-tab" style="cursor: pointer;">📌</i>
                        </div>
                        <div class="table-responsive text-nowrap p-3">
                            <table id="datatable1" class="table">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Image</th>
                                        <th>User Name</th>
                                        <th>Email Address</th>
                                        <th>Logs</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @foreach($loginToday->groupBy('email_address') as $email => $userLogs)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>
                                            @php
                                                $profilePath = $userLogs->first()->user->profile_photo_path;
                                            @endphp
                                            @if ($profilePath)
                                                <img src="{{ asset('public/storage/' . $profilePath) }}" alt="Profile Picture" width="50" height="50" class="rounded-circle">
                                            @else
                                                <img src={{ asset ('public/default-profile.jpg')}} alt="Default Profile" width="50" height="50" class="rounded-circle">
                                            @endif
                                        </td>
                                        <td>{{ $userLogs->first()->name }}</td>
                                        <td>{{ $email }}</td>
                                        <td>
                                            <button class="btn btn-link p-0" data-bs-toggle="collapse" data-bs-target="#userLog{{ $loop->index }}">
                                                View Logs ({{ count($userLogs) }})
                                            </button>
                                            <div id="userLog{{ $loop->index }}" class="collapse">
                                                <table class="table mb-0 mt-2">
                                                    <thead>
                                                        <tr>
                                                            <th>SL</th>
                                                            <th>Image</th>
                                                            <th>Login Date</th>
                                                            <th>Login Time</th>
                                                            <th>Logout Time</th>
                                                            <th>Total login Time</th>
                                                            <th>IP Address</th>
                                                            <th>Current Status</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($userLogs as $key => $log)
                                                        <tr>
                                                            <td>{{ $key + 1 }}</td>
                                                            <td>
                                                                @if ($log->user->profile_photo_path)
                                                                    <img src="{{ asset('public/storage/' . $log->user->profile_photo_path) }}" alt="Profile Picture" width="50" height="50" class="rounded-circle">
                                                                @else
                                                                    <img src={{ asset ('public/default-profile.jpg')}} alt="Default Profile" width="50" height="50" class="rounded-circle">
                                                                @endif
                                                            </td>
                                                            <td>{{ \Carbon\Carbon::parse($log->login_date)->format('d F Y') }}</td>
                                                            <td>
                                                                @if ($log->login_time)
                                                                    {{ \Carbon\Carbon::parse($log->login_time)->format('h:i A')}}
                                                                    <small>({{ \Carbon\Carbon::parse($log->login_time)->diffForHumans() }})</small>
                                                                @else
                                                                    <span class="">Logged In From Another Browser</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($log->logout_time)
                                                                    {{ \Carbon\Carbon::parse($log->logout_time)->format('h:i A') }}
                                                                    <small>({{ \Carbon\Carbon::parse($log->logout_time)->diffForHumans() }})</small>
                                                                @else
                                                                    Not Logged Out yet
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <span class="login-hour" data-id="{{ $log->id }}"></span>
                                                                <span class="countdown-timer" id="countdown-{{ $log->id }}">{{ $log->login_hour }}</span>
                                                            </td>
                                                            <td>{{ $log->ip_address }}</td>
                                                            <td>
                                                                @if ($log->status == 0)
                                                                    <span class="badge bg-success">Logged In</span>
                                                                @elseif ($log->status == 1 && $log->is_active == 1)
                                                                    <span class="badge bg-warning">Logged Out (Browser Changed)</span>
                                                                @elseif ($log->status == 1)
                                                                    <span class="badge bg-danger">Logged Out</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <!-- Action buttons for each log -->
                                                                <div class="dropdown">
                                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                                    </button>
                                                                    <div class="dropdown-menu">
                                                                        @can('Edit Login Details')
                                                                        <a class="dropdown-item edit-button" href="javascript:void(0);" data-id="{{ $log->id }}">
                                                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                                                        </a>
                                                                        @endcan      
                                                                        @can('Delete Login Details')                                                                  
                                                                        <form id="Delete-task-form-{{ $log->id }}" action="{{ route('login_details.destroy', ['login_detail' => $log->id]) }}" method="POST">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="button" class="dropdown-item" onclick="confirmDeleteTask({{ $log->id }})">
                                                                                <i class="bx bx-trash me-1"></i> Delete
                                                                            </button>
                                                                        </form>
                                                                        @endcan
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>                                
                            </table>
                        </div>
                    </div>
                </div>
                
        <!-- All Login Tab -->
        <div class="tab-pane" id="Incomplete" role="tabpanel" aria-labelledby="profile-tab">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>All Login</h5>
                    <i class="pin-icon" data-tab-id="profile-tab" style="cursor: pointer;">📌</i>
                </div>
                <div class="table-responsive text-nowrap p-3">
                    <table id="datatable2" class="table">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Image</th>
                                <th>User Name</th>
                                <th>Email Address</th>
                                <th>Login/logout Date</th>
                                <th>Logs</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach($AllLogin->groupBy('email_address') as $email => $userLogsByEmail)
                                @foreach($userLogsByEmail->groupBy('login_date') as $loginDate => $userLogs)
                                    <tr>
                                        <td>{{ $loop->parent->index + 1 }}</td>
                                        <td>
                                            @php
                                                $profilePath = $userLogs->first()->user->profile_photo_path;
                                            @endphp
                                            @if ($profilePath)
                                                <img src="{{ asset('public/storage/' . $profilePath) }}" alt="Profile Picture" width="50" height="50" class="rounded-circle">
                                            @else
                                                <img src={{ asset ('public/default-profile.jpg')}} alt="Default Profile" width="50" height="50" class="rounded-circle">
                                            @endif
                                        </td>
                                        <td>{{ $userLogs->first()->name }}</td>
                                        <td>{{ $email }}</td>
                                        <td>{{ \Carbon\Carbon::parse($loginDate)->format('d F Y') }}</td>
                                        <td>
                                            <button class="btn btn-link p-0" data-bs-toggle="collapse" data-bs-target="#userLog{{ $loop->parent->index }}{{ $loop->index }}">
                                                View Logs ({{ count($userLogs) }})
                                            </button>
                                            <div id="userLog{{ $loop->parent->index }}{{ $loop->index }}" class="collapse">
                                                <table class="table mb-0 mt-2">
                                                    <thead>
                                                        <tr>
                                                            <th>SL</th>
                                                            <th>Photo</th>
                                                            <th>Login Time</th>
                                                            <th>Logout Time</th>
                                                            <th>IP Address</th>
                                                            <th>Current Status</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($userLogs as $key => $log)
                                                        <tr>
                                                            <td>{{ $key + 1 }}</td>
                                                            <td>
                                                                @if ($userLogs->first() && $userLogs->first()->user && $userLogs->first()->user->profile_photo_path)
                                                                <img src="{{ asset('public/storage/' . $userLogs->first()->user->profile_photo_path) }}" alt="Profile Picture" width="50" height="50" class="rounded-circle">
                                                                @else
                                                                    <img src={{ asset ('public/default-profile.jpg')}} alt="Default Profile" width="50" height="50" class="rounded-circle">
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($log->login_time)
                                                                    {{ \Carbon\Carbon::parse($log->login_time)->format('h:i A') }}
                                                                    @if (\Carbon\Carbon::parse($log->login_date)->diffInDays() < 1)
                                                                        <small>({{ \Carbon\Carbon::parse($log->login_time)->diffForHumans() }})</small>
                                                                        @else
                                                                        <small>({{ round(\Carbon\Carbon::parse($log->login_date)->diffInDays()) }} days ago)</small>
                                                                    @endif
                                                                @else
                                                                    <span>Logged In From Another Browser</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($log->logout_time)
                                                                    {{ \Carbon\Carbon::parse($log->logout_time)->format('h:i A') }}
                                                                    @if (\Carbon\Carbon::parse($log->login_date)->diffInDays() < 1)
                                                                        <small>({{ \Carbon\Carbon::parse($log->logout_time)->diffForHumans() }})</small>
                                                                    @endif
                                                                @else
                                                                    <span>Not Logged Out yet</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $log->ip_address }}</td>
                                                            <td>
                                                                @if ($log->status == 0)
                                                                    <span class="badge bg-success">Logged In</span>
                                                                @elseif ($log->status == 1 && $log->is_active == 1)
                                                                    <span class="badge bg-warning">Logged Out (Browser Changed)</span>
                                                                @elseif ($log->status == 1)
                                                                    <span class="badge bg-danger">Logged Out</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <div class="dropdown">
                                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                                    </button>
                                                                    <div class="dropdown-menu">
                                                                        @can('Edit Login Details')
                                                                        <a class="dropdown-item edit-button" href="javascript:void(0);" data-id="{{ $log->id }}">
                                                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                                                        </a>
                                                                        @endcan
                                                                        @can('Delete Login Details')
                                                                        <form id="Delete-task-form-{{ $log->id }}" action="{{ route('login_details.destroy', ['login_detail' => $log->id]) }}" method="POST">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="button" class="dropdown-item" onclick="confirmDeleteTask({{ $log->id }})">
                                                                                <i class="bx bx-trash me-1"></i> Delete
                                                                            </button>
                                                                        </form>
                                                                        @endcan
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Current Login Tab -->
        <div class="tab-pane" id="current" role="tabpanel" aria-labelledby="current-tab">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Current Login Status</h5>
                    <i class="pin-icon" data-tab-id="current-tab" style="cursor: pointer;">📌</i>
                </div>
                <div class="table-responsive text-nowrap p-3">
                    <table id="datatable3" class="table">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Image</th>
                                <th>User Name</th>
                                <th>Email Address</th>
                                <th>Login Date</th>
                                <th>Login Time</th>
                                <th>Logout Time</th>
                                <th>Current Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach($currentLogin->groupBy('email_address') as $email => $userLogsByEmail)
                                @foreach($userLogsByEmail as $key => $log)
                                    <tr>
                                        <td>{{ $loop->parent->index + $loop->index + 1 }}</td>
                                        <td>
                                            @if ($log->user->profile_photo_path)
                                                <img src="{{ asset('public/storage/' . $log->user->profile_photo_path) }}" alt="Profile Picture" width="50" height="50" class="rounded-circle">
                                            @else
                                                <img src={{ asset ('public/default-profile.jpg')}} alt="Default Profile" width="50" height="50" class="rounded-circle">
                                            @endif
                                        </td>
                                        <td>{{ $log->name }}</td>
                                        <td>{{ $email }}</td>
                                        <td>{{ \Carbon\Carbon::parse($log->login_date)->format('d F Y') }}</td>
                                        <td>
                                            @if ($log->login_time)
                                                {{ \Carbon\Carbon::parse($log->login_time)->format('h:i A')}}
                                                <small>({{ \Carbon\Carbon::parse($log->login_time)->diffForHumans() }})</small>
                                            @else
                                                <span class="">Logged In From Another Browser</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->logout_time)
                                                {{ \Carbon\Carbon::parse($log->logout_time)->format('h:i A') }}
                                                <small>({{ \Carbon\Carbon::parse($log->logout_time)->diffForHumans() }})</small>
                                            @else
                                                Not Logged Out yet
                                            @endif
                                        </td>
                                        <td>
                                            @if ($log->status == 0)
                                                <span class="badge bg-success">Logged In</span>
                                            @elseif ($log->status == 1 && $log->is_active == 1)
                                                <span class="badge bg-warning">Logged Out (Browser Changed)</span>
                                            @elseif ($log->status == 1)
                                                <span class="badge bg-danger">Logged Out</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    @can('Edit Login Details')
                                                    <a class="dropdown-item edit-button" href="javascript:void(0);" data-id="{{ $log->id }}">
                                                        <i class="bx bx-edit-alt me-1"></i> Edit
                                                    </a>
                                                    @endcan
                                                    @can('Delete Login Details')   
                                                    <form id="Delete-task-form-{{ $log->id }}" action="{{ route('login_details.destroy', ['login_detail' => $log->id]) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="dropdown-item" onclick="confirmDeleteTask({{ $log->id }})">
                                                            <i class="bx bx-trash me-1"></i> Delete
                                                        </button>
                                                    </form>
                                                    @endcan
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="tab-pane" id="inactive" role="tabpanel" aria-labelledby="inactive-tab">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Not Logged In Today</h5>
                    <i class="pin-icon" data-tab-id="inactive-tab" style="cursor: pointer;">📌</i>
                </div>
                <div class="table-responsive text-nowrap p-3">
                    <table id="datatable4" class="table">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Image</th>
                                <th>User Name</th>
                                <th>Email Address</th>
                                {{-- <th>Login Date</th>
                                <th>Login Time</th> --}}
                                <th>Current Status</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach($missingInDetailsLoginsDetails->groupBy('email') as $email => $userLogsByEmail)
                                @foreach($userLogsByEmail as $key => $log)
                                    <tr>
                                        <td>{{ $loop->parent->index + $loop->index + 1 }}</td>
                                        <td>
                                            @if ($log->user && $log->user->profile_photo_path)
                                                <img src="{{ asset('public/storage/' . $log->user->profile_photo_path) }}" alt="Profile Picture" width="50" height="50" class="rounded-circle">
                                            @else
                                                <img src={{ asset ('public/default-profile.jpg')}} alt="Default Profile" width="50" height="50" class="rounded-circle">
                                            @endif
                                        </td>
                                        <td>{{ $log->name }}</td>
                                        <td>{{ $email }}</td>
                                        {{-- <td>{{ \Carbon\Carbon::parse($log->login_date)->format('d F Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($log->login_time)->format('h:i A') }}</td> --}}
                                        <td>
                                            <span class="badge bg-warning">Not Logged In</span>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


<script>
$(document).ready(function () {
    $('.edit-button').on('click', function () {
        const logId = $(this).data('id'); // Get the data-id from the button
        const editUrl = `{{ route('login_details.edit', ':id') }}`.replace(':id', logId);

        // Fetch the log data using AJAX
        $.ajax({
            url: editUrl,
            type: 'GET',
            success: function (response) {
                if (response.status) {
                    console.log(response);
                    const log = response.data.log;
                    // Convert login_time to Asia/Dhaka time for display in the edit view
                    const localLoginTime = moment(log.login_time, "HH:mm:ss").format('HH:mm');
                    // Check if logout_time exists and format it if not null
                    const localLogLoginTime = log.logout_time ? moment(log.logout_time, "HH:mm:ss").format('HH:mm') : null;


                    // Populate form fields with log data
                    $('#editDate').val(log.login_date);
                    $('#editTime').val(localLoginTime);
                    $('#editLogTime').val(localLogLoginTime);
                    $('#editStatus').val(log.status);

                    // Check if logout_time is null and conditionally hide or show the input
                    if (localLogLoginTime) {
                        $('#editLogTime').val(localLogLoginTime).closest('.mb-3').show(); 
                    } else {
                        $('#editLogTime').val('').closest('.mb-3').hide(); 
                    }

                    // Set the logId in the form's data-id attribute
                    $('#editLoginForm').attr('data-id', logId);

                    // Show the modal
                    $('#editLoginModal').modal('show');
                } else {
                    alert(response.message);
                }
            },
            error: function (error) {
                alert('Failed to retrieve log data');
            }
        });
    });

    $('#editLoginForm').on('submit', function (e) {
    e.preventDefault(); // Prevent default form submission
    const logId = $(this).attr('data-id');

    const loginTime = $('#editTime').val(); // 'HH:mm' format
    const logoutTime = $('#editLogTime').val(); // 'HH:mm' format

    // Convert login time
    const loginParts = loginTime.match(/(\d+):(\d+)\s*(am|pm)?/i);
    let formattedLoginTime = loginTime;
    if (loginParts) {
        let hours = parseInt(loginParts[1]);
        const minutes = loginParts[2];
        const ampm = loginParts[3];
        if (ampm) {
            if (ampm.toLowerCase() === 'pm' && hours < 12) hours += 12;
            if (ampm.toLowerCase() === 'am' && hours === 12) hours = 0;
        }
        formattedLoginTime = `${hours.toString().padStart(2, '0')}:${minutes}`;
    }

    // Convert logout time
    const logoutParts = logoutTime.match(/(\d+):(\d+)\s*(am|pm)?/i);
    let formattedLogoutTime = logoutTime;
    if (logoutParts) {
        let hours = parseInt(logoutParts[1]);
        const minutes = logoutParts[2];
        const ampm = logoutParts[3];
        if (ampm) {
            if (ampm.toLowerCase() === 'pm' && hours < 12) hours += 12;
            if (ampm.toLowerCase() === 'am' && hours === 12) hours = 0;
        }
        formattedLogoutTime = `${hours.toString().padStart(2, '0')}:${minutes}`;
    }

    // Prepare form data
    const formData = $(this).serializeArray();
    formData.push({ name: 'login_time', value: formattedLoginTime });
    formData.push({ name: 'logout_time', value: formattedLogoutTime });

    $.ajax({
        url: `{{ route('login_details.update', ':id') }}`.replace(':id', logId),
        type: 'PUT',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: $.param(formData),
        success: function (response) {
            $('#editLoginModal').modal('hide');
            if (response.status) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Log updated successfully',
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload(); // Reload to reflect the changes
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
            }
        },
        error: function () {
            $('#editLoginModal').modal('hide');
            Swal.fire({
                icon: 'error',
                title: 'Failed',
                text: 'Failed to update log data'
            });
        }
    });
});
    const pinnedTabId = localStorage.getItem("pinnedTab");

    // Check if a tab is pinned and set it as active
    if (pinnedTabId && document.querySelector(`#${pinnedTabId}`)) {
        // Remove 'active' class from all tabs and tab panes
        $(".nav-link, .tab-pane").removeClass("active");

        // Set the saved tab as active
        $(`#${pinnedTabId}`).addClass("active");

        const targetPaneId = $(document.querySelector(`#${pinnedTabId}`)).data("bs-target");

        // Ensure the target pane exists before adding 'active' class
        if (targetPaneId && document.querySelector(targetPaneId)) {
            $(targetPaneId).addClass("active");
        } else {
            console.warn(`Target pane for pinned tab ID ${pinnedTabId} not found.`);
        }
    } else {
        // If no pinned tab or invalid pinned tab, make the first tab active by default
        $("#home-tab").addClass("active");
        $("#Pending").addClass("active");
    }

    // Handle click on the pin icon
    $(".pin-icon").on("click", function () {
        const tabId1 = $(this).data("tab-id");
        localStorage.setItem("pinnedTab", tabId1); // Save the pinned tab to local storage
        
        // Display a notification for pinning
        Toastify({
            text: "Tab pinned successfully!",
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "#4CAF50", // Green background for success
        }).showToast();
    });

    // Handle tab click (for normal tab switching without pinning)
    $(".nav-link").on("click", function () {
        if (!$(this).hasClass("active")) {
            $(".nav-link").removeClass("active");
            $(this).addClass("active");
            localStorage.removeItem("pinnedTab"); // Clear pinned tab if switching manually
        }
    });

     // Start the timer for each active login row
     $('.login-hour').each(function() {
        const loginInfoId = $(this).data('id');
        const countdownElement = $(`#countdown-${loginInfoId}`);
        const initialLoginHour = countdownElement.text(); // Get the existing login_hour from the database display

        // Use Laravel's route helper to generate the URL for the named route
        const url = `{{ route('get.login.hour', ':id') }}`.replace(':id', loginInfoId);

        // Fetch the initial login_hour from the server
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                if (response.login_hour && !response.error) {
                    // The condition matches, start the countdown
                    let [hours, minutes, seconds] = response.login_hour.split(':').map(Number);
                    let totalSeconds = hours * 3600 + minutes * 60 + seconds;

                    // Start the timer to increment login_hour every second
                    setInterval(() => {
                        totalSeconds++;

                        // Calculate hours, minutes, and seconds
                        const displayHours = Math.floor(totalSeconds / 3600);
                        const displayMinutes = Math.floor((totalSeconds % 3600) / 60);
                        const displaySeconds = totalSeconds % 60;

                        // Format and display updated time
                        const formattedTime = 
                            `${String(displayHours).padStart(2, '0')}:${String(displayMinutes).padStart(2, '0')}:${String(displaySeconds).padStart(2, '0')}`;
                        countdownElement.text(formattedTime);
                    }, 1000); // Update every second
                } else {
                    // Condition does not match, display existing login_hour from database without starting countdown
                    countdownElement.text(initialLoginHour);
                }
            },
            error: function() {
                // Handle actual AJAX error (e.g., network issue)
                countdownElement.text(initialLoginHour); // Default back to initial data instead of showing error
            }
        });
    });
});

</script>

<script>
    function confirmDeleteTask(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('Delete-task-form-' + id).submit();

            }
        });
    }
</script>

<script>
    // Check if there is a success message in the session
    @if(session('success'))
        Toastify({
            text: "{{ session('success') }}",
            duration: 3000, // Duration in milliseconds
            gravity: "top", // `top` or `bottom`
            position: 'right', // `left`, `center` or `right`
            backgroundColor: "green",
        }).showToast();
    @endif

    // Check if there is an error message in the session
    @if(session('error'))
        Toastify({
            text: "{{ session('error') }}",
            duration: 3000,
            gravity: "top",
            position: 'right',
            backgroundColor: "red",
        }).showToast();
    @endif
</script>

@endsection
