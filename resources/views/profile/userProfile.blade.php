@extends('layouts.master')
@section('content')

<style>
        /* Normal Mode Styles */
        body {
            background-color: #ffffff;
            color: #000000;
            font-family: Arial, sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }

        .profile-container {
            max-width: 1100px;
            margin: auto;
            background: #f0f0f0;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s;
        }

        .profile-header {
            display: flex;
            align-items: center;
            background: #e0e0e0;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            transition: background-color 0.3s;
        }

        .username {
            font-size: 24px;
            color: #000000;
            margin: 0;
        }

        .admin {
            color: #a71837;
            font-size: 18px;
        }

        .user {
            color: #4cbd20;
            font-size: 18px;
        }

        .stats, .time {
            font-size: 14px;
            color: #666;
            margin: 5px 0;
        }

        .buttons button {
            background: #444;
            color: #fff;
            border: none;
            font-size: 13px;
            padding: 10px 10px;
            margin-right: 10px;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .buttons button:hover {
            background: #555;
        }

        .tabs {
            margin-top: 20px;
        }

        .tablink {
            background: #444;
            color: #ddd;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            margin-right: 10px;
            border-radius: 4px;
            transition: background 0.3s;
        }

        .tablink.active {
            background: #555;
        }

        .tabcontent {
            background: #e0e0e0;
            padding: 20px;
            border-radius: 8px;
            margin-top: 10px;
            transition: background-color 0.3s;
        }

        .profile-details {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .section {
            background: #dad8d8;
            padding: 15px;
            border-radius: 8px;
            width: 32%;
            margin-bottom: 15px;
            transition: background-color 0.3s;
        }

        h3 {
            color: #1f4cca;
            border-bottom: 2px solid #cf4f14;
            padding-bottom: 10px;
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ccc;
        }

        th {
            background: #e0e0e0;
            color: #ffcc00;
        }

        tbody tr:hover {
            background: #d0d0d0;
        }

        .signature {
            text-align: center;
            margin-top: 20px;
            font-style: italic;
            color: #666;
        }

        .signature img {
            max-width: 100%;
            border-radius: 5px;
            margin-top: 10px;
        }

        /* user info css */

        .user-info {
            position: relative;
            padding-bottom: 50px; /* Ensure space at the bottom */
        }

        .button-group {
            position: absolute;
            bottom: 10px;
            right: 10px;
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .add-details {
            background-color: #28a745;
            color: white;
        }

        .edit-details {
            background-color: #007bff;
            color: white;
        }

        .add-details:hover {
            background-color: #218838;
        }

        .edit-details:hover {
            background-color: #0056b3;
        }

        /* change image CSS */
        .image-container {
        position: relative;
        display: inline-block;
        cursor: pointer;
        overflow: hidden;
    }

    .image {
        display: block;
        width: 150px; /* Adjust as needed */
        height: 150px;
        border-radius: 50%;
        margin-right: 20px;
        object-fit: cover;
        border: 3px solid #ffcc00;
    }

    .overlay {
        position: absolute;
        bottom: -50px; /* Initially hidden */
        left: 0;
        width: 100%;
        background: rgba(0, 0, 0, 0.6);
        color: white;
        text-align: center;
        padding: 5px 0;
        transition: bottom 0.3s ease-in-out;
        border-radius: 0 0 50% 50%;
    }

    .image-container:hover .overlay {
        bottom: 0; /* Slide up effect */
    }

    .overlay i {
        font-size: 18px;
        margin-right: 5px;
    }

    /* action Button */

    .action-buttons {
    position: absolute;
    top: 20%;
    right: -50px;
    transform: translateY(-50%);
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 10px;
    }

    .action-buttons button {
        padding: 5px 5px;
        background-color: #5d6163;
        color: white;
        font-size: 13px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .action-buttons button:hover {
        background-color: #7fafe4;
    }
</style>

<div class="profile-container">
    <!-- Add Details Modal -->
    <div class="modal fade" id="addDetailsModal" tabindex="-1" aria-labelledby="addDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDetailsModalLabel">Add User Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addDetailsForm" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="user_title">Job Title<span class="text-danger">*</span></label>
                            <input type="text" id="user_title" name="user_title" class="form-control" required>
                            <span class="text-danger error-user_title"></span>
                        </div>

                        <div class="mb-3">
                            <label for="age">Age<span class="text-danger">*</span></label>
                            <input type="date" id="age" name="age" class="form-control" required>
                            <span class="text-danger error-age"></span>
                        </div>

                        <div class="mb-3">
                            <label for="gender">Gender<span class="text-danger">*</span></label>
                            <select id="gender" name="gender" class="form-control" required>
                                <option disabled selected value="">Select</option>
                                <option value="1">Male</option>
                                <option value="2">Female</option>
                                <option value="3">Other</option>
                            </select>
                            <span class="text-danger error-gender"></span>
                        </div>

                        <div class="mb-3">
                            <label for="address">Address<span class="text-danger">*</span></label>
                            <textarea id="address" name="address" class="form-control" rows="2" required></textarea>
                            <span class="text-danger error-address"></span>
                        </div>

                        <div class="mb-3">
                            <label for="phone">Phone</label>
                            <input type="text" id="phone" name="phone" class="form-control" >
                            <span class="text-danger error-phone"></span>
                        </div>

                        <div class="mb-3">
                            <label for="email">Contract Email</label>
                            <input type="email" id="email" name="email" class="form-control">
                            <span class="text-danger error-email"></span>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Details</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Details Modal -->
    <div class="modal fade" id="editDetailsModal" tabindex="-1" aria-labelledby="editDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDetailsModalLabel">Edit User Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editDetailsForm" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="user_id" name="user_id"> 

                        <div class="mb-3">
                            <label for="user_title">Job Title<span class="text-danger">*</span></label>
                            <input type="text" id="edit_user_title" name="edit_user_title" class="form-control" required>
                            <span class="text-danger error-edit_user_title"></span>
                        </div>

                        <div class="mb-3">
                            <label for="age">Age<span class="text-danger">*</span></label>
                            <input type="date" id="edit_age" name="edit_age" class="form-control" required>
                            <span class="text-danger error-edit_age"></span>
                        </div>

                        <div class="mb-3">
                            <label for="gender">Gender<span class="text-danger">*</span></label>
                            <select id="edit_gender" name="edit_gender" class="form-control" required>
                                <option disabled selected value="">Select</option>
                                <option value="1">Male</option>
                                <option value="2">Female</option>
                                <option value="3">Other</option>
                            </select>
                            <span class="text-danger error-edit_gender"></span>
                        </div>

                        <div class="mb-3">
                            <label for="address">Address<span class="text-danger">*</span></label>
                            <textarea id="edit_address" name="edit_address" class="form-control" rows="2" required></textarea>
                            <span class="text-danger error-edit_address"></span>
                        </div>

                        <div class="mb-3">
                            <label for="phone">Phone</label>
                            <input type="text" id="edit_phone" name="edit_phone" class="form-control">
                            <span class="text-danger error-edit_phone"></span>
                        </div>

                        <div class="mb-3">
                            <label for="email">Contract Email</label>
                            <input type="email" id="edit_con_email" name="edit_con_email" class="form-control">
                            <span class="text-danger error-email"></span>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Details</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- Edit user Model --}}
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <form id="editUserForm" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" id="editUserId" name="userId">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">User Name</label>
                        <input type="text" id="edit_name" name="name" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" id="edit_email" name="email" class="form-control" required>
                    </div>
    
                    <div class="mb-3">
                        <label for="edit_profile_picture" class="form-label">Profile Picture</label>
                        <input type="file" id="edit_profile_picture" name="profile_picture" class="form-control" accept="image/*">
                        <img id="editprofilePreview" src="" alt="Profile Picture" class="img-fluid mt-2" style="max-height: 150px;">
                    </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button id="saveUserBtn" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Change Password Modal --}}
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Error messages will be prepended here -->
                    <form id="changePasswordForm" method="POST">
                        @csrf
                        @method('PUT')
    
                        <input type="hidden" id="editPasswordId" name="passwordId">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" id="current_password" name="current_password" class="form-control" required>
                        </div>
    
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" id="new_password" name="new_password" class="form-control" required>
                        </div>
    
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button id="savePasswordBtn" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    
    <div class="profile-header">
        <div class="image-container">
            <img src="{{ Auth::user()->profile_photo_path ? asset('storage/' . Auth::user()->profile_photo_path) : asset('default-profile.jpg') }}" 
                 alt="Profile Picture" 
                 class="image" 
                 id="profileImage">
            <div class="overlay">
                <i class="fas fa-camera"></i> Change Image
            </div>
        </div>
        
        <input type="file" id="imageInput" style="display: none;" accept="image/*">
        <div class="user-info">
            <h2 class="username">
                <span id="usernameDisplay">
                    {{ Auth::user()->name }}
                </span>
                
                <i id="editUsername" title="Edit Username" class="fas fa-edit" style="cursor: pointer; font-size: 14px;"></i>
            
                @if (Auth::user()->hasRole('Super Admin'))
                    <span class="admin"> {{ Auth::user()->getRoleNames()->join(', ') }}</span>
                @else
                    <span class="user"> {{ Auth::user()->getRoleNames()->join(', ') }}</span>
                @endif
            </h2>
            
            <!-- Hidden input field for username -->
            <input type="text" id="usernameInput" value="{{ Auth::user()->name }}" style="display:none;">
            
            <p class="stats">
                @if ($userInfo)
                    <!-- Use an envelope icon for Job Title -->
                    <i class="fas fa-briefcase"></i> Job Title: {{$userInfo->user_title}}  
                @else
                    <!-- Use an envelope icon for Job Title -->
                    <i class="fas fa-briefcase"></i> Job Title: Details Not added
                @endif
                &nbsp;
                <i class="fas fa-envelope"></i> Email: 
                <span id="emailDisplay">{{ Auth::user()->email }}</span>
                <i id="editEmail" title="Change email address" class="fas fa-edit" style="cursor: pointer; font-size: 16px;"></i>
            </p>
            
            <!-- Hidden input field for email -->
            <input type="text" id="emailInput" value="{{ Auth::user()->email }}" style="display:none;">

            <p class="time">📅 {{ \Carbon\Carbon::now()->format('l, d F Y - h:i A') }}</p>
            <div class="buttons">
                <button class="upload">Pending Work Plan: {{$pendingWorkPlanCount}}</button>
                <button class="upload">Completed Work Plan: {{$totalWorkPLanCount}}</button>
                <button class="rep">Completed Task: {{$totalTaskCount}}</button>
                <button class="forum-rep">Completed Project: {{$totalProjectCount}}</button>
                <button class="upload">Incomplete Work Plan: {{$incompleteWorkPlanCount}}</button>
            </div>
            <div class="action-buttons">
                <button  class="dropdown-item" 
                href="#" 
                data-bs-toggle="modal" 
                data-bs-target="#editUserModal" 
                data-id="{{ Auth::user()->id }}">
                 <i class="bx bx-edit-alt me-1"></i> Edit Profile
                </button>
                <button class="dropdown-item change-password"
                data-id="{{ Auth::user()->id }}">
                    <i class="bx bx-lock me-1"></i> Change Password
                </button>
            </div>
        </div>
    </div>
    
    <div class="tabs">
        <button class="tablink active" data-tab="profileDetails">Profile Details</button>
        <button class="tablink" data-tab="torrent">Login Log</button>
        <button class="tablink" data-tab="dailyComplete">Completed Task</button>
        <button class="tablink" data-tab="dailyWorkComplete">Completed Work Plan</button>
    </div>
    
    <div id="profileDetails" class="tabcontent">
        <div class="profile-details">
            <!-- Work Info Section -->
            <div class="section general">
                <h3>Work Info</h3>
    
                <!-- Bootstrap Tabs for Work Info -->
                <ul class="nav nav-tabs" id="workInfoTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="daily-tab" data-toggle="tab" href="#dailyWork" role="tab" aria-controls="dailyWork" aria-selected="true">Daily</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="weekly-tab" data-toggle="tab" href="#weeklyWork" role="tab" aria-controls="weeklyWork" aria-selected="false">Weekly</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="monthly-tab" data-toggle="tab" href="#monthlyWork" role="tab" aria-controls="monthlyWork" aria-selected="false">Monthly</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="yearly-tab" data-toggle="tab" href="#yearlyWork" role="tab" aria-controls="yearlyWork" aria-selected="false">Yearly</a>
                    </li>
                </ul>
    
                <!-- Tab Content for Work Info -->
                <div class="tab-content" id="workInfoContent">
                    <!-- Daily Work Info -->
                    <div class="tab-pane fade show active" id="dailyWork" role="tabpanel" aria-labelledby="daily-tab">
                        <table id="dailyWorkTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Metric</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Completed Work Plan</td>
                                    <td><a href="{{ route('work_plan.index') }}">{{$DailyWorkPLanCount}}</a></td>
                                </tr>
                                <tr>
                                    <td>Completed Task</td>
                                    <td><a href="{{ route('tasks.index') }}">{{$DailyTaskCount}}</a></td>
                                </tr>
                                <tr>
                                    <td>Completed Project</td>
                                    <td>@if(auth()->user()->hasRole('Super Admin')) <a href="{{ route('project_title.index') }}">{{$DailyProjectCount}}</a>@else {{$DailyProjectCount}} @endif</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
    
                    <!-- Weekly Work Info -->
                    <div class="tab-pane fade" id="weeklyWork" role="tabpanel" aria-labelledby="weekly-tab">
                        <table id="weeklyWorkTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Metric</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Completed Work Plan</td>
                                    <td><a href="{{ route('work_plan.index') }}">{{$WeeklyWorkPLanCount}}</a></td>
                                </tr>
                                <tr>
                                    <td>Completed Task</td>
                                    <td><a href="{{ route('tasks.index') }}">{{$WeeklyTaskCount}}</a></td>
                                </tr>
                                <tr>
                                    <td>Completed Project</td>
                                    <td>@if(auth()->user()->hasRole('Super Admin')) <a href="{{ route('project_title.index') }}">{{$WeeklyProjectCount}}</a>@else {{$WeeklyProjectCount}} @endif</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
    
                    <!-- Monthly Work Info -->
                    <div class="tab-pane fade" id="monthlyWork" role="tabpanel" aria-labelledby="monthly-tab">
                        <table id="monthlyWorkTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Metric</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Completed Work Plan</td>
                                    <td><a href="{{ route('work_plan.index') }}">{{$MonthlyWorkPLanCount}}</a></td>
                                </tr>
                                <tr>
                                    <td>Completed Task</td>
                                    <td><a href="{{ route('tasks.index') }}">{{$MonthlyTaskCount}}</a></td>
                                </tr>
                                <tr>
                                    <td>Completed Project</td>
                                    <td>@if(auth()->user()->hasRole('Super Admin')) <a href="{{ route('project_title.index') }}">{{$MonthlyProjectCount}}</a>@else {{$MonthlyProjectCount}} @endif</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
    
                    <!-- Yearly Work Info -->
                    <div class="tab-pane fade" id="yearlyWork" role="tabpanel" aria-labelledby="yearly-tab">
                        <table id="yearlyWorkTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Metric</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Completed Work Plan</td>
                                    <td><a href="{{ route('work_plan.index') }}">{{$YearlyWorkPLanCount}}</a></td>
                                </tr>
                                <tr>
                                    <td>Completed Task</td>
                                    <td><a href="{{ route('tasks.index') }}">{{$YearlyTaskCount}}</a></td>
                                <tr>
                                    <td>Completed Project</td>
                                    <td>@if(auth()->user()->hasRole('Super Admin')) <a href="{{ route('project_title.index') }}">{{$YearlyProjectCount}}</a>@else {{$YearlyProjectCount}} @endif</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    
            <!-- Login Info Section -->
            <div class="section seed-info">
                <h3>Login Info</h3>
    
                <!-- Bootstrap Tabs for Login Info -->
                <ul class="nav nav-tabs" id="loginInfoTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="dailyLogin-tab" data-toggle="tab" href="#dailyLogin" role="tab" aria-controls="dailyLogin" aria-selected="true">Today</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="weeklyLogin-tab" data-toggle="tab" href="#weeklyLogin" role="tab" aria-controls="weeklyLogin" aria-selected="false">Weekly</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="monthlyLogin-tab" data-toggle="tab" href="#monthlyLogin" role="tab" aria-controls="monthlyLogin" aria-selected="false">Monthly</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="yearlyLogin-tab" data-toggle="tab" href="#yearlyLogin" role="tab" aria-controls="yearlyLogin" aria-selected="false">Yearly</a>
                    </li>
                </ul>
    
                <!-- Tab Content for Login Info -->
                <div class="tab-content" id="loginInfoContent">
                    <!-- Daily Login Info -->
                    <div class="tab-pane fade show active" id="dailyLogin" role="tabpanel" aria-labelledby="dailyLogin-tab">
                        <table id="dailyLoginTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Metric</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- <tr>
                                    <td>Login Date</td>
                                    <td>
                                        {{ $todayLogins->isNotEmpty() ? \Carbon\Carbon::parse($todayLogins->first()->login_date)->format('d F Y') : 'Not Logged In' }}
                                    </td>
                                </tr> --}}
                                <tr>
                                    <td>Login Time</td>
                                    <td>
                                        {{ $todayLogins->isNotEmpty() ? \Carbon\Carbon::parse($todayLogins->first()->login_time)->format('h:i A') : 'Not Logged In' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Login Hour</td>
                                    <td>
                                        @if($todayLogins->isNotEmpty())
                                            @php
                                                $loginTime = \Carbon\Carbon::parse($todayLogins->first()->login_hour);
                                                $hours = $loginTime->format('H');
                                                $minutes = $loginTime->format('i');
                                            @endphp
                                            {{ $hours }} hours {{ $minutes }} minutes
                                        @else
                                            Not Logged In
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Previous Login</td>
                                    <td>
                                        {{ $previousLoginTime ? \Carbon\Carbon::parse($previousLoginTime->login_time)->format('h:i A') : 'Not Logged In' }}
                                    </td>                                    
                                </tr>
                            </tbody>
                        </table>
                    </div>
    
                    <!-- Weekly Login Info -->
                    <div class="tab-pane fade" id="weeklyLogin" role="tabpanel" aria-labelledby="weeklyLogin-tab">
                        <table id="weeklyLoginTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Metric</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>On Time Login</td>
                                    <td>
                                        {{ $onTimeLoginCountWeekly }} Day's
                                    </td>
                                </tr>
                                <tr>
                                    <td>Late Login</td>
                                    <td>{{$lateLoginCountWeekly}} Day's</td>
                                </tr>
                                <tr>
                                    <td>Avg Login Time</td>
                                    <td>
                                        @if($averageLoginHourWeekly)
                                            @php
                                                $loginTime = \Carbon\Carbon::parse($averageLoginHourWeekly);
                                                $hours = $loginTime->format('H');
                                                $minutes = $loginTime->format('i');
                                            @endphp
                                            {{ $hours }} hours {{ $minutes }} minutes
                                        @else
                                            Not Logged In
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Login Count</td>
                                    <td>
                                        {{ $weeklyLoginCount ?? 0 }} Times ({{$weeklyLoginDays ?? 0}} Day's)
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
    
                    <!-- Monthly Login Info -->
                    <div class="tab-pane fade" id="monthlyLogin" role="tabpanel" aria-labelledby="monthlyLogin-tab">
                        <table id="monthlyLoginTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Metric</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>On Time Login</td>
                                    <td>
                                        {{ $onTimeLoginCountMonthly }} Day's
                                    </td>
                                </tr>
                                <tr>
                                    <td>Late Login</td>
                                    <td>{{$lateLoginCountMonthly}} Day's</td>
                                </tr>
                                <tr>
                                    <td>Avg Login Time</td>
                                    <td>
                                        @if($averageLoginHourMonthly)
                                            @php
                                                $loginTime = \Carbon\Carbon::parse($averageLoginHourMonthly);
                                                $hours = $loginTime->format('H');
                                                $minutes = $loginTime->format('i');
                                            @endphp
                                            {{ $hours }} hours {{ $minutes }} minutes
                                        @else
                                            Not Logged In
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Login Count</td>
                                    <td>
                                        {{ $monthlyLoginCount ?? 0 }} Times ({{$monthlyLoginDays ?? 0}} Day's)
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
    
                    <!-- Yearly Login Info -->
                    <div class="tab-pane fade" id="yearlyLogin" role="tabpanel" aria-labelledby="yearlyLogin-tab">
                        <table id="yearlyLoginTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Metric</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>On Time Login</td>
                                    <td>
                                        {{ $onTimeLoginCountYearly }} Day's
                                    </td>
                                </tr>
                                <tr>
                                    <td>Late Login</td>
                                    <td>{{$lateLoginCountYearly}} Day's</td>
                                </tr>
                                <tr>
                                    <td>Avg Login Time</td>
                                    <td>
                                        @if($averageLoginHourYearly)
                                            @php
                                                $loginTime = \Carbon\Carbon::parse($averageLoginHourYearly);
                                                $hours = $loginTime->format('H');
                                                $minutes = $loginTime->format('i');
                                            @endphp
                                            {{ $hours }} hours {{ $minutes }} minutes
                                        @else
                                            Not Logged In
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Login Count</td>
                                    <td>
                                        {{ $yearlyLoginCount ?? 0 }} Times ({{$yearlyLoginDays ?? 0}} Day's)
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    
            <!-- User Info Section -->
            <div class="section user-info">
                <h3>User Info</h3>
                @if ($userInfo)
                    <p>Joined: {{ \Carbon\Carbon::parse(Auth::user()->created_at)->format('d M Y') }}</p>
                    <p>Job Title: {{$userInfo->user_title}}</p>
                    <p>Country: {{$userInfo->country}}</p>
                    <p>Age: {{ \Carbon\Carbon::parse($userInfo->age)->age }}</p>
                    <p>Gender: 
                        @if ($userInfo->gender == 1)
                            Male
                        @elseif ($userInfo->gender == 2)
                            Female
                        @else
                            Other
                        @endif
                    </p>
                    <p>Address: {{$userInfo->address ?? 'N/A'}}</p>
                    <p>Phone: {{$userInfo->phone ?? 'N/A'}}</p>
                    <p>Email: {{$userInfo->email ?? 'N/A'}}</p>
                @else
                    <h4><p>No user info found. Click "Add Details" button to add user info.</p></h4>
                @endif
                <!-- Buttons Container -->
                <div class="button-group">
                    @if (!$userInfo)
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDetailsModal">Add Details</button>
                    @else
                    <button class="btn btn-primary edit-user-btn" data-bs-toggle="modal" data-id="{{ Auth::user()->id }}" data-bs-target="#editDetailsModal">
                        Edit Details
                    </button>                    
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div id="torrent" class="tabcontent" style="display: none;">
        <h3 class="mb-4">Login Log</h3>
    
        <!-- Bootstrap Tabs -->
        <ul class="nav nav-tabs" id="logTabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#today">Today</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#weekly">Weekly</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#monthly">Monthly</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#yearly">Yearly</a>
            </li>
        </ul>
    
        <!-- Tab Content -->
        <div class="tab-content mt-3">
            <!-- Today Tab -->
            <div class="tab-pane fade show active" id="today">
                <table id="todayTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Login Date</th>
                            <th>Login Time</th>
                            <th>Log out time</th>
                            <th>Total login time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dailyUserLogin as $dailyUser )
                        <tr>  
                            <td>{{$dailyUser->name}}</td>
                            <td>{{$dailyUser->login_date }}</td>
                            <td>{{$dailyUser->login_time }}</td>
                            <td>{{$dailyUser->logout_time ?? 'Not logged out yet' }}</td>
                            <td>{{$dailyUser->login_hour ?? 'N/A'}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
    
            <!-- Weekly Tab -->
            <div class="tab-pane fade" id="weekly">
                <table id="weeklyTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Login Date</th>
                            <th>Login Time</th>
                            <th>Log out time</th>
                            <th>Total login time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($weeklyUserLogin as $weeklyUser)
                        <tr>
                            <td>{{$weeklyUser->name}}</td>
                            <td>{{$weeklyUser->login_date }}</td>
                            <td>{{$weeklyUser->login_time }}</td>
                            <td>{{$weeklyUser->logout_time ?? 'Not logged out yet' }}</td>
                            <td>{{$weeklyUser->login_hour ?? 'N/A'}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
    
            <!-- Monthly Tab -->
            <div class="tab-pane fade" id="monthly">
                <table id="monthlyTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Login Date</th>
                            <th>Login Time</th>
                            <th>Log out time</th>
                            <th>Total login time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($monthlyUserLogin as $monthlyUser)
                        <tr>
                            <td>{{$monthlyUser->name}}</td>
                            <td>{{$monthlyUser->login_date }}</td>
                            <td>{{$monthlyUser->login_time }}</td>
                            <td>{{$monthlyUser->logout_time ?? 'Not logged out yet' }}</td>
                            <td>{{$monthlyUser->login_hour ?? 'N/A'}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
    
            <!-- Yearly Tab -->
            <div class="tab-pane fade" id="yearly">
                <table id="yearlyTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Login Date</th>
                            <th>Login Time</th>
                            <th>Log out time</th>
                            <th>Total login time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($yearlyUserLogin as $yearlyUser)
                        <tr>
                            <td>{{$yearlyUser->name}}</td>
                            <td>{{$yearlyUser->login_date }}</td>
                            <td>{{$yearlyUser->login_time }}</td>
                            <td>{{$yearlyUser->logout_time ?? 'Not logged out yet' }}</td>
                            <td>{{$yearlyUser->login_hour ?? 'N/A'}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="dailyComplete" class="tabcontent" style="display: none;">
        <h3 class="mb-4">Completed Task</h3>
    
        <!-- Bootstrap Tabs -->
        <ul class="nav nav-tabs" id="TaskTabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#todayTask">Today</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#weeklyTask">Weekly</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#monthlyTask">Monthly</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#yearlyTask">Yearly</a>
            </li>
        </ul>
    
        <!-- Tab Content -->
        <div class="tab-content mt-3">
            <!-- Today Tab -->
            <div class="tab-pane fade show active" id="todayTask">
                <table id="todayTaskTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Task</th>
                            <th>Start Date</th>
                            <th>Due Date</th>
                            <th>Submitted Date</th>
                            <th>Attachment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($TodayCompletedTask as $TodayCompleted )
                        <tr>  
                            <td>{{$TodayCompleted->title_name->project_title ?? 'No project title selected' }}</td>
                            <td>{!!($TodayCompleted->description) !!}</td>
                            <td>{{ $TodayCompleted->created_at->format('d F Y, h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($TodayCompleted->submit_date)->format('d F Y, h:i A') }}</td>
                            <td>{{ $TodayCompleted->submit_by_date ? \Carbon\Carbon::parse($TodayCompleted->submit_by_date)->format('d F Y, h:i A') : 'Task completed' }}</td>
                            <td>
                                @if ($TodayCompleted->attachment)
                                    @php
                                        // Decode JSON-encoded attachments and names
                                        $attachment = json_decode($TodayCompleted->attachment, true);
                                        $attachmentName = json_decode($TodayCompleted->attachment_name, true);
                                    @endphp
                            
                                    @foreach ($attachment as $index => $attachments)
                                        <a href="{{ asset($attachments) }}" target="_blank" title="Download {{ $attachmentName[$index] ?? 'Attachment' }}">
                                            {{ $attachmentName[$index] ?? 'Attachment' }}
                                        </a><br> 
                                    @endforeach
                                @else
                                    No Attachments
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
    
            <!-- Weekly Tab -->
            <div class="tab-pane fade" id="weeklyTask">
                <table id="weeklyTaskTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Task</th>
                            <th>Start Date</th>
                            <th>Due Date</th>
                            <th>Submitted Date</th>
                            <th>Attachment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($WeeklyCompletedTask as $TodayCompleted )
                        <tr>  
                            <td>{{$TodayCompleted->title_name->project_title ?? 'No project title selected' }}</td>
                            <td>{!!($TodayCompleted->description) !!}</td>
                            <td>{{ $TodayCompleted->created_at->format('d F Y, h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($TodayCompleted->submit_date)->format('d F Y, h:i A') }}</td>
                            <td>{{ $TodayCompleted->submit_by_date ? \Carbon\Carbon::parse($TodayCompleted->submit_by_date)->format('d F Y, h:i A') : 'Task completed' }}</td>
                            <td>
                                @if ($TodayCompleted->attachment)
                                    @php
                                        // Decode JSON-encoded attachments and names
                                        $attachment = json_decode($TodayCompleted->attachment, true);
                                        $attachmentName = json_decode($TodayCompleted->attachment_name, true);
                                    @endphp
                            
                                    @foreach ($attachment as $index => $attachments)
                                        <a href="{{ asset($attachments) }}" target="_blank" title="Download {{ $attachmentName[$index] ?? 'Attachment' }}">
                                            {{ $attachmentName[$index] ?? 'Attachment' }}
                                        </a><br> 
                                    @endforeach
                                @else
                                    No Attachments
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
    
            <!-- Monthly Tab -->
            <div class="tab-pane fade" id="monthlyTask">
                <table id="monthlyTaskTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Task</th>
                            <th>Start Date</th>
                            <th>Due Date</th>
                            <th>Submitted Date</th>
                            <th>Attachment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($MonthlyCompletedTask as $TodayCompleted )
                        <tr>  
                            <td>{{$TodayCompleted->title_name->project_title ?? 'No project title selected' }}</td>
                            <td>{!!($TodayCompleted->description) !!}</td>
                            <td>{{ $TodayCompleted->created_at->format('d F Y, h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($TodayCompleted->submit_date)->format('d F Y, h:i A') }}</td>
                            <td>{{ $TodayCompleted->submit_by_date ? \Carbon\Carbon::parse($TodayCompleted->submit_by_date)->format('d F Y, h:i A') : 'Task completed' }}</td>
                            <td>
                                @if ($TodayCompleted->attachment)
                                    @php
                                        // Decode JSON-encoded attachments and names
                                        $attachment = json_decode($TodayCompleted->attachment, true);
                                        $attachmentName = json_decode($TodayCompleted->attachment_name, true);
                                    @endphp
                            
                                    @foreach ($attachment as $index => $attachments)
                                        <a href="{{ asset($attachments) }}" target="_blank" title="Download {{ $attachmentName[$index] ?? 'Attachment' }}">
                                            {{ $attachmentName[$index] ?? 'Attachment' }}
                                        </a><br> 
                                    @endforeach
                                @else
                                    No Attachments
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
    
            <!-- Yearly Tab -->
            <div class="tab-pane fade" id="yearlyTask">
                <table id="yearlyTaskTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Task</th>
                            <th>Start Date</th>
                            <th>Due Date</th>
                            <th>Submitted Date</th>
                            <th>Attachment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($YearlyCompletedTask as $TodayCompleted )
                        <tr>  
                            <td>{{$TodayCompleted->title_name->project_title ?? 'No project title selected' }}</td>
                            <td>{!!($TodayCompleted->description) !!}</td>
                            <td>{{ $TodayCompleted->created_at->format('d F Y, h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($TodayCompleted->submit_date)->format('d F Y, h:i A') }}</td>
                            <td>{{ $TodayCompleted->submit_by_date ? \Carbon\Carbon::parse($TodayCompleted->submit_by_date)->format('d F Y, h:i A') : 'Task completed' }}</td>
                            <td>
                                @if ($TodayCompleted->attachment)
                                    @php
                                        // Decode JSON-encoded attachments and names
                                        $attachment = json_decode($TodayCompleted->attachment, true);
                                        $attachmentName = json_decode($TodayCompleted->attachment_name, true);
                                    @endphp
                            
                                    @foreach ($attachment as $index => $attachments)
                                        <a href="{{ asset($attachments) }}" target="_blank" title="Download {{ $attachmentName[$index] ?? 'Attachment' }}">
                                            {{ $attachmentName[$index] ?? 'Attachment' }}
                                        </a><br> 
                                    @endforeach
                                @else
                                    No Attachments
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    {{-- <div id="dailyComplete" class="tabcontent" style="display: none;">
        <h3 class="mb-4">Completed Task</h3>
    
        <!-- Bootstrap Tabs -->
        <ul class="nav nav-tabs" id="logTabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#today">Today</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#weekly">Weekly</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#monthly">Monthly</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#yearly">Yearly</a>
            </li>
        </ul>
    
        <!-- Tab Content -->
        <div class="tab-content mt-3">
            <!-- Today Tab -->
            <div class="tab-pane fade show active" id="today">
                <table id="todayTask" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Task</th>
                            <th>Start Date</th>
                            <th>Due Date</th>
                            <th>Submitted Date</th>
                            <th>Attachment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($TodayCompletedTask as $TodayCompleted )
                        <tr>  
                            <td>{{$TodayCompleted->title_name->project_title ?? 'No project title selected' }}</td>
                            <td>{!!($TodayCompleted->description) !!}</td>
                            <td>{{ $TodayCompleted->created_at->format('d F Y, h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($TodayCompleted->submit_date)->format('d F Y, h:i A') }}</td>
                            <td>{{ $TodayCompleted->submit_by_date ? \Carbon\Carbon::parse($TodayCompleted->submit_by_date)->format('d F Y, h:i A') : 'Task completed' }}</td>
                            <td>
                                @if ($TodayCompleted->attachment)
                                    @php
                                        // Decode JSON-encoded attachments and names
                                        $attachment = json_decode($TodayCompleted->attachment, true);
                                        $attachmentName = json_decode($TodayCompleted->attachment_name, true);
                                    @endphp
                            
                                    @foreach ($attachment as $index => $attachments)
                                        <a href="{{ asset($attachments) }}" target="_blank" title="Download {{ $attachmentName[$index] ?? 'Attachment' }}">
                                            {{ $attachmentName[$index] ?? 'Attachment' }}
                                        </a><br> 
                                    @endforeach
                                @else
                                    No Attachments
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
    
            <!-- Weekly Tab -->
            <div class="tab-pane fade" id="weekly">
                <table id="weeklyTask" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Task</th>
                            <th>Start Date</th>
                            <th>Due Date</th>
                            <th>Submitted Date</th>
                            <th>Attachment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($WeeklyCompletedTask as $TodayCompleted )
                        <tr>  
                            <td>{{$TodayCompleted->title_name->project_title ?? 'No project title selected' }}</td>
                            <td>{!!($TodayCompleted->description) !!}</td>
                            <td>{{ $TodayCompleted->created_at->format('d F Y, h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($TodayCompleted->submit_date)->format('d F Y, h:i A') }}</td>
                            <td>{{ $TodayCompleted->submit_by_date ? \Carbon\Carbon::parse($TodayCompleted->submit_by_date)->format('d F Y, h:i A') : 'Task completed' }}</td>
                            <td>
                                @if ($TodayCompleted->attachment)
                                    @php
                                        // Decode JSON-encoded attachments and names
                                        $attachment = json_decode($TodayCompleted->attachment, true);
                                        $attachmentName = json_decode($TodayCompleted->attachment_name, true);
                                    @endphp
                            
                                    @foreach ($attachment as $index => $attachments)
                                        <a href="{{ asset($attachments) }}" target="_blank" title="Download {{ $attachmentName[$index] ?? 'Attachment' }}">
                                            {{ $attachmentName[$index] ?? 'Attachment' }}
                                        </a><br> 
                                    @endforeach
                                @else
                                    No Attachments
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
    
            <!-- Monthly Tab -->
            <div class="tab-pane fade" id="monthly">
                <table id="monthlyTask" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Task</th>
                            <th>Start Date</th>
                            <th>Due Date</th>
                            <th>Submitted Date</th>
                            <th>Attachment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($MonthlyCompletedTask as $TodayCompleted )
                        <tr>  
                            <td>{{$TodayCompleted->title_name->project_title ?? 'No project title selected' }}</td>
                            <td>{!!($TodayCompleted->description) !!}</td>
                            <td>{{ $TodayCompleted->created_at->format('d F Y, h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($TodayCompleted->submit_date)->format('d F Y, h:i A') }}</td>
                            <td>{{ $TodayCompleted->submit_by_date ? \Carbon\Carbon::parse($TodayCompleted->submit_by_date)->format('d F Y, h:i A') : 'Task completed' }}</td>
                            <td>
                                @if ($TodayCompleted->attachment)
                                    @php
                                        // Decode JSON-encoded attachments and names
                                        $attachment = json_decode($TodayCompleted->attachment, true);
                                        $attachmentName = json_decode($TodayCompleted->attachment_name, true);
                                    @endphp
                            
                                    @foreach ($attachment as $index => $attachments)
                                        <a href="{{ asset($attachments) }}" target="_blank" title="Download {{ $attachmentName[$index] ?? 'Attachment' }}">
                                            {{ $attachmentName[$index] ?? 'Attachment' }}
                                        </a><br> 
                                    @endforeach
                                @else
                                    No Attachments
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
    
            <!-- Yearly Tab -->
            <div class="tab-pane fade" id="yearly">
                <table id="yearlyTask" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Task</th>
                            <th>Start Date</th>
                            <th>Due Date</th>
                            <th>Submitted Date</th>
                            <th>Attachment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($YearlyCompletedTask as $TodayCompleted )
                        <tr>  
                            <td>{{$TodayCompleted->title_name->project_title ?? 'No project title selected' }}</td>
                            <td>{!!($TodayCompleted->description) !!}</td>
                            <td>{{ $TodayCompleted->created_at->format('d F Y, h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($TodayCompleted->submit_date)->format('d F Y, h:i A') }}</td>
                            <td>{{ $TodayCompleted->submit_by_date ? \Carbon\Carbon::parse($TodayCompleted->submit_by_date)->format('d F Y, h:i A') : 'Task completed' }}</td>
                            <td>
                                @if ($TodayCompleted->attachment)
                                    @php
                                        // Decode JSON-encoded attachments and names
                                        $attachment = json_decode($TodayCompleted->attachment, true);
                                        $attachmentName = json_decode($TodayCompleted->attachment_name, true);
                                    @endphp
                            
                                    @foreach ($attachment as $index => $attachments)
                                        <a href="{{ asset($attachments) }}" target="_blank" title="Download {{ $attachmentName[$index] ?? 'Attachment' }}">
                                            {{ $attachmentName[$index] ?? 'Attachment' }}
                                        </a><br> 
                                    @endforeach
                                @else
                                    No Attachments
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div> --}}

    <div id="dailyWorkComplete" class="tabcontent" style="display: none;">
        <h3 class="mb-4">Completed Work Plan</h3>
    
        <!-- Bootstrap Tabs -->
        <ul class="nav nav-tabs" id="WorkTabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#todayWorkTab">Today</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#weeklyWorkTab">Weekly</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#monthlyWorkTab">Monthly</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#yearlyWorkTab">Yearly</a>
            </li>
        </ul>
    
        <!-- Tab Content -->
        <div class="tab-content mt-3">
            <!-- Today Tab -->
            <div class="tab-pane fade show active" id="todayWorkTab">
                <table id="todayWorkComplete" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Task Title</th>
                            <th>Work Plan</th>
                            <th>Start Date</th>
                            <th>Due Date</th>
                            <th>Submitted Date</th>
                            <th>Attachment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($TodayCompletedWork as $TodayCompleted)
                        <tr>  
                            <td>{{ $TodayCompleted->task->task_title ?? 'No project title selected'  }}</td>
                            <td>{!! $TodayCompleted->description !!}</td>
                            <td>{{ $TodayCompleted->created_at->format('d F Y, h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($TodayCompleted->submit_date)->format('d F Y, h:i A') }}</td>
                            <td>{{ $TodayCompleted->submit_by_date ? \Carbon\Carbon::parse($TodayCompleted->submit_by_date)->format('d F Y, h:i A') : 'Task completed' }}</td>
                            <td>
                                @if ($TodayCompleted->attachment)
                                    @php
                                        $attachment = json_decode($TodayCompleted->attachment, true);
                                        $attachmentName = json_decode($TodayCompleted->attachment_name, true);
                                    @endphp
                                    @foreach ($attachment as $index => $attachments)
                                        <a href="{{ asset($attachments) }}" target="_blank" title="Download {{ $attachmentName[$index] ?? 'Attachment' }}">
                                            {{ $attachmentName[$index] ?? 'Attachment' }}
                                        </a><br> 
                                    @endforeach
                                @else
                                    No Attachments
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
    
            <!-- Weekly Tab -->
            <div class="tab-pane fade" id="weeklyWorkTab">
                <table id="weeklyWorkComplete" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Task Title</th>
                            <th>Work Plan</th>
                            <th>Start Date</th>
                            <th>Due Date</th>
                            <th>Submitted Date</th>
                            <th>Attachment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($WeeklyCompletedWork as $TodayCompleted)
                        <tr>  
                            <td>{{ $TodayCompleted->task->task_title ?? 'No project title selected'  }}</td>
                            <td>{!! $TodayCompleted->description !!}</td>
                            <td>{{ $TodayCompleted->created_at->format('d F Y, h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($TodayCompleted->submit_date)->format('d F Y, h:i A') }}</td>
                            <td>{{ $TodayCompleted->submit_by_date ? \Carbon\Carbon::parse($TodayCompleted->submit_by_date)->format('d F Y, h:i A') : 'Task completed' }}</td>
                            <td>
                                @if ($TodayCompleted->attachment)
                                    @php
                                        $attachment = json_decode($TodayCompleted->attachment, true);
                                        $attachmentName = json_decode($TodayCompleted->attachment_name, true);
                                    @endphp
                                    @foreach ($attachment as $index => $attachments)
                                        <a href="{{ asset($attachments) }}" target="_blank" title="Download {{ $attachmentName[$index] ?? 'Attachment' }}">
                                            {{ $attachmentName[$index] ?? 'Attachment' }}
                                        </a><br> 
                                    @endforeach
                                @else
                                    No Attachments
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
    
            <!-- Monthly Tab -->
            <div class="tab-pane fade" id="monthlyWorkTab">
                <table id="monthlyWorkComplete" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Task Title</th>
                            <th>Work Plan</th>
                            <th>Start Date</th>
                            <th>Due Date</th>
                            <th>Submitted Date</th>
                            <th>Attachment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($MonthlyCompletedWork as $TodayCompleted)
                        <tr>  
                            <td>{{ $TodayCompleted->task->task_title ?? 'No project title selected'  }}</td>
                            <td>{!! $TodayCompleted->description !!}</td>
                            <td>{{ $TodayCompleted->created_at->format('d F Y, h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($TodayCompleted->submit_date)->format('d F Y, h:i A') }}</td>
                            <td>{{ $TodayCompleted->submit_by_date ? \Carbon\Carbon::parse($TodayCompleted->submit_by_date)->format('d F Y, h:i A') : 'Task completed' }}</td>
                            <td>
                                @if ($TodayCompleted->attachment)
                                    @php
                                        $attachment = json_decode($TodayCompleted->attachment, true);
                                        $attachmentName = json_decode($TodayCompleted->attachment_name, true);
                                    @endphp
                                    @foreach ($attachment as $index => $attachments)
                                        <a href="{{ asset($attachments) }}" target="_blank" title="Download {{ $attachmentName[$index] ?? 'Attachment' }}">
                                            {{ $attachmentName[$index] ?? 'Attachment' }}
                                        </a><br> 
                                    @endforeach
                                @else
                                    No Attachments
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
    
            <!-- Yearly Tab -->
            <div class="tab-pane fade" id="yearlyWorkTab">
                <table id="yearlyWorkComplete" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Task Title</th>
                            <th>Work Plan</th>
                            <th>Start Date</th>
                            <th>Due Date</th>
                            <th>Submitted Date</th>
                            <th>Attachment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($YearlyCompletedWork as $TodayCompleted)
                        <tr>  
                            <td>{{ $TodayCompleted->task->task_title ?? 'No project title selected'  }}</td>
                            <td>{!! $TodayCompleted->description !!}</td>
                            <td>{{ $TodayCompleted->created_at->format('d F Y, h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($TodayCompleted->submit_date)->format('d F Y, h:i A') }}</td>
                            <td>{{ $TodayCompleted->submit_by_date ? \Carbon\Carbon::parse($TodayCompleted->submit_by_date)->format('d F Y, h:i A') : 'Task completed' }}</td>
                            <td>
                                @if ($TodayCompleted->attachment)
                                    @php
                                        $attachment = json_decode($TodayCompleted->attachment, true);
                                        $attachmentName = json_decode($TodayCompleted->attachment_name, true);
                                    @endphp
                                    @foreach ($attachment as $index => $attachments)
                                        <a href="{{ asset($attachments) }}" target="_blank" title="Download {{ $attachmentName[$index] ?? 'Attachment' }}">
                                            {{ $attachmentName[$index] ?? 'Attachment' }}
                                        </a><br> 
                                    @endforeach
                                @else
                                    No Attachments
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    {{-- <div class="signature">
        <p>"If you don't like your destiny, don’t accept it. Instead, have the courage to change it the way you want it to be." – Naruto Uzumaki</p>
        <img src="signature-image.png" alt="Signature Image">
    </div> --}}
</div>

<script>
    $(document).ready(function() {
        $('.tablink').click(function() {
            var tabName = $(this).data('tab');
            $('.tabcontent').hide();
            $('#' + tabName).show();
            $('.tablink').removeClass('active');
            $(this).addClass('active');
        });
        

        $('#dailyWorkTable, #weeklyWorkTable, #monthlyWorkTable, #yearlyWorkTable, #dailyLoginTable, #weeklyLoginTable, #monthlyLoginTable, #yearlyLoginTable').DataTable({
            paging: false,
            searching: false,
            info: false
        });

        $('#todayTable, #weeklyTable, #monthlyTable, #yearlyTable').DataTable({
        });

        $('#todayTaskTable, #weeklyTaskTable, #monthlyTaskTable, #yearlyTaskTable').DataTable({
        });

        $('#todayWorkComplete, #weeklyWorkComplete, #monthlyWorkComplete, #yearlyWorkComplete').DataTable({
        });

        $('.image-container').click(function() {
            $('#imageInput').click(); // Open file input
        });

        $('#imageInput').change(function() {
            let formData = new FormData();
            formData.append('profile_photo', $('#imageInput')[0].files[0]);
            formData.append('_token', '{{ csrf_token() }}'); // Laravel CSRF token

            $.ajax({
                url: "{{ route('profile.update.photo') }}", 
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.success) {
                        // Update the image source without refreshing the page
                        $('#profileImage').attr('src', response.image_url + '?' + new Date().getTime());

                        // Show success alert
                        Swal.fire({
                            title: "Success!",
                            text: "Your profile picture has been updated.",
                            icon: "success",
                            confirmButtonText: "OK"
                        });
                    } else {
                        Swal.fire("Error!", response.error, "error");
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        });
        $('#addDetailsForm').submit(function(e) {
            e.preventDefault(); 
            var formData = $(this).serialize(); 

            $.ajax({
                url: "{{ route('working_profile.store') }}", 
                type: 'POST', 
                data: formData, 
                success: function(response) {
                    $('#addDetailsModal').modal('hide'); 
                    Swal.fire({
                        icon: 'success', 
                        title: 'Success!', 
                        text: 'Your details has been saved successfully.', 
                        confirmButtonText: 'OK' 
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Hide the modal
                            $('#addDetailsModal').modal('hide'); 
                            location.reload();
                        }
                    });
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error', 
                        title: 'Error!', 
                        text: 'An error occurred while saving your data.', 
                        confirmButtonText: 'OK' 
                    });
                    console.error('Error:', error); 
                }
            });
        });

        //edit user name
        // Show input field when edit icon is clicked
        $('#editUsername').click(function() {
            var currentUsername = $('#usernameDisplay').text();
            
            // Hide the display username and show the input field with current username
            $('#usernameDisplay').hide();
            $('#usernameInput').val(currentUsername).show().focus();
        });

        // When user stops editing (on blur or enter key)
        $('#usernameInput').blur(function() {
            var newUsername = $(this).val();

            if (newUsername !== '') {
                $.ajax({
                    url: "{{ route('profile.update.username') }}", // Define this route in your web.php
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        name: newUsername
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update the display with the new username
                            $('#usernameDisplay').text(newUsername).show();
                            $('#usernameInput').hide();
                        } else {
                            Swal.fire("Error!", response.error, "error");
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            } else {
                $('#usernameDisplay').show();
                $('#usernameInput').hide();
            }
        });

        $('#usernameInput').keypress(function(e) {
            if (e.which == 13) { // Enter key
                $(this).blur();
            }
        });

        //change email
        // Show input field when edit icon is clicked
        $('#editEmail').click(function() {
            var currentEmail = $('#emailDisplay').text();
            
            // Hide the display email and show the input field with current email
            $('#emailDisplay').hide();
            $('#emailInput').val(currentEmail).show().focus();
        });

        // When user stops editing (on blur or enter key)
        $('#emailInput').blur(function() {
            var newEmail = $(this).val();

            if (newEmail !== '') {
                $.ajax({
                    url: "{{ route('profile.update.email') }}", // Define this route in your web.php
                    type: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        email: newEmail
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update the display with the new email
                            $('#emailDisplay').text(newEmail).show();
                            $('#emailInput').hide();
                        } else {
                            Swal.fire("Error!", response.error, "error");
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            } else {
                $('#emailDisplay').show();
                $('#emailInput').hide();
            }
        });

        // Optionally handle pressing "Enter" to save changes
        $('#emailInput').keypress(function(e) {
            if (e.which == 13) { // Enter key
                $(this).blur();
            }
        });

        // Trigger modal and fetch user data via AJAX
        $('#editUserModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget); 
            const userId = button.data('id'); 

            // Clear previous values in the modal
            $('#editUserModal').find('input, select').val('');
            $('#editprofilePreview').attr('src', '/default-profile.png'); 

            $.ajax({
                url: "{{ route('user.edit', ':id') }}".replace(':id', userId),
                type: 'GET',
                success: function (response) {
                    console.log(response);
                    // Populate the modal with user data
                    $('#editUserId').val(response.user.id);
                    $('#edit_name').val(response.user.name);
                    $('#edit_email').val(response.user.email);
                    $('#editprofilePreview').attr('src', response.user.profile_picture_url);
                },
                error: function (xhr) {
                    alert('Failed to fetch user data. Please try again.');
                    console.error(xhr.responseText);
                },
            });
        });

            // Preview profile picture before upload
            $('#edit_profile_picture').on('change', function (e) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    $('#editprofilePreview').attr('src', e.target.result);
                };
                reader.readAsDataURL(this.files[0]);
            });

                // Preview profile picture before upload
                $('#profile_picture').on('change', function (e) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    $('#profilePreview').attr('src', e.target.result);
                };
                reader.readAsDataURL(this.files[0]);
            });

            $('#saveUserBtn').on('click', function () {
            const userId = $('#editUserId').val();
            const formData = new FormData();
            formData.append('_method', 'PUT');
            formData.append('name', $('#edit_name').val());
            formData.append('email', $('#edit_email').val());

            if ($('#edit_profile_picture')[0].files[0]) {
                formData.append('profile_picture', $('#edit_profile_picture')[0].files[0]);
            }

            $.ajax({
                url: "{{ route('profile.update.profile', ':id') }}".replace(':id', userId),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                success: function (response) {
                    $('#editUserModal').modal('hide');
                    Swal.fire({
                        title: 'Success!',
                        text: 'User updated successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK',
                    }).then(() => {
                        $('#editUserModal').modal('hide');
                        location.reload();
                    });
                },
                error: function (xhr) {
                    $('#editUserModal').modal('hide');
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to update user. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                    });
                    console.error(xhr.responseText);
                },
            });
        });

        $('.change-password').click(function() {
            const userId = $(this).data('id');  
            $('#editPasswordId').val(userId);   
            $('#changePasswordModal').modal('show');
            console.log(userId);
        });

        // Handle Change Password Form submission
            $('#savePasswordBtn').click(function() {
                const userId = $('#editPasswordId').val();  // Getting the user ID
                var currentPassword = $('#current_password').val();
                var newPassword = $('#new_password').val();
                var confirmPassword = $('#confirm_password').val();

                // Clear any previous error messages
                $('#changePasswordModal .alert-danger').remove();

                // Validation: Check if new passwords match
                if (newPassword !== confirmPassword) {
                    $('#changePasswordModal .modal-body').prepend('<div class="alert alert-danger">New Password and Confirm Password do not match.</div>');
                    return;
                }

                // Send the data via AJAX
                $.ajax({
                    url: "{{ route('profile.change-password', ':id') }}".replace(':id', userId),
                    type: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        current_password: currentPassword,
                        new_password: newPassword,
                        new_password_confirmation: confirmPassword  // Add this field for confirmation
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#changePasswordModal').modal('hide');
                            Swal.fire({
                                title: 'Password Updated!',
                                text: 'Your password has been successfully changed. Please log in again.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                $('#changePasswordModal').modal('hide');
                                location.reload();
                                $('#changePasswordForm')[0].reset();
                            });
                        } else {
                            $('#changePasswordModal .modal-body').prepend('<div class="alert alert-danger">' + response.error + '</div>');
                        }
                    },
                    error: function(xhr) {
                        const errorMessage = xhr.responseJSON?.error || 'An error occurred. Please try again.';
                        
                        // Display the error message in the modal
                        $('#changePasswordModal .modal-body').prepend('<div class="alert alert-danger">' + errorMessage + '</div>');

                        console.error(xhr.responseText);  // Optionally log the error for debugging
                    }
                });
            });

            $('.edit-user-btn').click(function () {
            let userId = $(this).data('id');

            // Set the hidden input value for user ID
            $('#editDetailsForm #user_id').val(userId);

            // Perform AJAX request to get user details
            $.ajax({
                url: "{{ route('get.user.details', ':id') }}".replace(':id', userId),
                type: 'GET',
                success: function (response) {
                    if (response.success) {
                        console.log(response);
                        // Populate the form fields with the retrieved data
                        $('#editDetailsForm #edit_user_title').val(response.data.user_title);
                        $('#editDetailsForm #edit_age').val(response.data.age);
                        $('#editDetailsForm #edit_gender').val(response.data.gender);
                        $('#editDetailsForm #edit_address').val(response.data.address);
                        $('#editDetailsForm #edit_phone').val(response.data.phone);
                        $('#editDetailsForm #edit_con_email').val(response.data.email);
                    } else {
                        alert('Failed to fetch user details');
                    }
                },
                error: function () {
                    alert('Error fetching user details');
                }
            });
        });

        $('#editDetailsForm').submit(function (e) {
            e.preventDefault(); // Prevent default form submission

            let userID = $('#user_id').val();  // Get user ID from hidden field
            let formData = $(this).serialize(); // Serialize form data

            $.ajax({
                url: "{{ route('working_profile.update', ':id') }}".replace(':id', userID),
                type: 'POST',  // Use POST because Laravel handles PUT with _method
                data: formData,
                success: function (response) {
                    if (response.success) {
                        $('#editDetailsModal').modal('hide');
                        Swal.fire({
                            title: "Success!",
                            text: "User details updated successfully!",
                            icon: "success",
                            confirmButtonText: "OK"
                        }).then(() => {
                            location.reload(); // Reload the page after success
                        });
                    } else {
                        Swal.fire("Error!", "Failed to update user details.", "error");
                    }
                },
                error: function (xhr) {
                    let errors = xhr.responseJSON.errors;
                    let errorMsg = '';

                    $.each(errors, function (key, value) {
                        errorMsg += value[0] + '\n';
                        $('.error-' + key).text(value[0]); // Display validation error messages
                    });

                    Swal.fire("Validation Error!", errorMsg, "warning");
                }
            });
        });

    });
</script>
@endsection