@extends('layouts.master')
@section('content')

<style>
.cke_notification_message,
.cke_notifications_area,
.cke_button__about_icon,
.cke_button__about {
    display: none !important;
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
}
</style>
    
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<h4 class="py-2 m-4"><span class="text-muted fw-light">Assign Work Plan</span></h4>

<div class="row mt-5">
    <div class="col-12">

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

        <!-- Main Assign work plan Modal -->
        <div class="modal fade" id="assignTaskModal" tabindex="-1" aria-labelledby="assignTaskModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="assignTaskModalLabel">Assign Work</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="assignTaskForm">
                            @csrf
                            <div class="mb-3">
                                <label for="task_title">Task Title<span class="text-danger">*</span></label>
                                <div class="d-flex align-items-center">
                                    <select id="task_title" name="title" class="form-control" style="width: calc(100% - 30px);">
                                        @php
                                        $assignedTasks = $title->filter(function ($titles) {
                                            return !empty($titles->task_title); // Only check if the task title is not empty
                                        });
                                        @endphp
                                
                                        @if($assignedTasks->isNotEmpty())
                                            <option value="">Select your task</option>
                                            @foreach($assignedTasks as $task)
                                                <option value="{{ $task->id }}" data-project-title="{{ $task->title_name_id }}">
                                                    {{ $task->task_title}}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="" disabled selected>
                                                No tasks are available. Create a new task first to create Work Plan
                                            </option>
                                        @endif
                                    </select>
                                    <!-- Button to open Create Task Sub-Modal -->
                                    <button type="button" class="btn btn-link p-0 ml-2" data-bs-toggle="modal" data-bs-target="#newAssignTaskModal">
                                        <i class="fas fa-plus-circle" style="font-size: 24px; color: #007bff;"></i>
                                    </button>
                                    <span class="text-danger error-title"></span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="main_user_id">User Name<span class="text-danger">*</span></label>
                                <select id="task_user_id" name="user_id[]" class="form-control new_model_select2" multiple="multiple" required>
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach 
                                </select>
                                <span class="text-danger error-user_id"></span>
                            </div>
                            <div class="mb-3">
                                <label for="task_description">Work Description<span class="text-danger">*</span></label>
                                <textarea id="task_description" name="description" class="form-control" rows="4" required placeholder="Task Details"></textarea>
                                <span class="text-danger error-description"></span>
                            </div>
                            <div class="mb-3">
                                <label for="work_status">Work Status<span class="text-danger">*</span></label>
                                <select id="work_submit_status" name="work_status" class="form-control">
                                    <option value="Work From Home">Work From Home</option>
                                    <option value="Work From Office">Work From Office</option>
                                </select>
                                <span class="text-danger error-work_status"></span>
                            </div>
                            <div class="mb-3">
                                <label for="last_submit_date">Last Submit Date<span class="text-danger">*</span></label>
                                <input id="last_submit_date" name="last_submit_date" type="date" class="form-control" value="{{ date('Y-m-d') }}">
                                <span class="text-danger error-last_submit_date"></span>
                            </div>
                            <div class="mb-3">
                                <label for="task_attachment">Attachments</label>
                                <input id="task_attachment" name="attachment[]" type="file" class="form-control" multiple>
                                <span class="text-danger error-attachment"></span>
                                <div id="file-names" class="mt-2"></div> 
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="saveTaskBtn" class="btn btn-primary">Create Work Plan</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sub-modal for Create Project -->
        <div class="modal fade" id="createProjectModal" tabindex="-1" aria-labelledby="createProjectModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content"> 
                    <div class="modal-header">
                        <h5 class="modal-title" id="createProjectModalLabel">Create Project</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body custom-select2-modal">
                        <form id="createProjectForm">
                            @csrf
                            <div class="mb-3">
                                <label for="modal_title">Project Name<span class="text-danger">*</span></label>
                                <input id="modal_title" name="title" type="text" required class="form-control" placeholder="Title Name">
                                <span class="text-danger error-title"></span>
                            </div>
                            <div class="mb-3">
                                <label for="modal_user_id">Assign User<span class="text-danger">*</span></label>
                                <select id="modal_user_id" name="user_id[]" class="form-control model_select2" multiple="multiple" required>
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach 
                                </select>
                                <span class="text-danger error-user_id"></span>
                            </div>
                            <div class="mb-3">
                                <label for="modal_description">Project Description</label>
                                <textarea id="modal_description" name="description" class="form-control" rows="4" placeholder="Project Details"></textarea>
                                <span class="text-danger error-description"></span>
                            </div>
                            <div class="mb-3">
                                <label for="modal_start_date">Project Start Date<span class="text-danger">*</span></label>
                                <input id="modal_start_date" name="start_date" type="date" required class="form-control" value="{{ date('Y-m-d') }}">
                                <span class="text-danger error-start_date"></span>
                            </div>
                            <div class="mb-3">
                                <label for="modal_end_date">Project End Date<span class="text-danger">*</span></label>
                                <input id="modal_end_date" name="end_date" type="date" required class="form-control" value="{{ date('Y-m-d') }}">
                                <span class="text-danger error-end_date"></span>
                            </div>
                            <div class="mb-3">
                                <label for="modal_attachments">Attachments</label>
                                <input id="modal_attachments" name="attachment[]" type="file" class="form-control" multiple>
                                <span class="text-danger error-attachment"></span>
                                <div id="modal-file-names" class="mt-2"></div> <!-- Selected file names will appear here -->
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="saveProjectBtn" class="btn btn-primary">Create Project</button>
                    </div>
                </div>
            </div>
        </div>

    <!-- sub modal for Assign Task Modal -->
    <div class="modal fade" id="newAssignTaskModal" tabindex="-1" aria-labelledby="newAssignTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newassignTaskModalLabel">Assign Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="newAssignTaskForm">
                        @csrf
                        <div class="mb-3">
                            <label for="work_title">Task Title<span class="text-danger">*</span></label>
                            <input id="work_title" name="work_title" type="text" required class="form-control" placeholder="Write a task title">
                            <span class="text-danger error-work_title"></span>
                        </div>
                        <div class="mb-3">
                            <label for="work_task_title">Project Title<span class="text-danger">*</span></label>
                            <div class="d-flex align-items-center">
                                <select id="work_task_title" name="work_task_title" class="form-control" style="width: calc(100% - 30px);">
                                    <option value="">Select title</option>
                                    @foreach($projectTitle as $tit)
                                        <option value="{{ $tit->id }}">{{ $tit->project_title }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-work_task_title"></span>
                                <!-- Button to open Create Project Sub-Modal -->
                                <button type="button" class="btn btn-link p-0 ml-2" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                                    <i class="fas fa-plus-circle" style="font-size: 24px; color: #007bff;"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="work_user_id">User Name<span class="text-danger">*</span></label>
                            <select id="work_user_id" name="user_id[]" class="form-control work_model_select2" multiple="multiple" required>
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach 
                            </select>
                            <span class="text-danger error-work_user_id"></span>
                        </div>
                        <div class="mb-3">
                            <label for="work_description">Task Description<span class="text-danger">*</span></label>
                            <textarea id="work_description" name="work_description" class="form-control" rows="4" required placeholder="Task Details"></textarea>
                            <span class="text-danger error-work_description"></span>
                        </div>
                        <div class="mb-3">
                            <label for="work_submit_date">Last Submit Date<span class="text-danger">*</span></label>
                            <input id="work_submit_date" name="work_submit_date" type="date" class="form-control" value="{{ date('Y-m-d') }}">
                            <span class="text-danger error-work_submit_date"></span>
                        </div>
                        <div class="mb-3">
                            <label for="work_attachments">Attachments</label>
                            <input id="work_attachments" name="attachment[]" type="file" class="form-control" multiple>
                            <span class="text-danger error-attachment"></span>
                            <div id="work-file-names" class="mt-2"></div> 
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="saveWorkBtn" class="btn btn-primary">Create Task</button>
                </div>
            </div>
        </div>
    </div>

<!-- Edit assign work Modal -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTaskModalLabel">Edit Assign Work</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editProjectForm" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" id="edit_task_id" name="task_id">

                    <div class="mb-3">
                        <label for="title">Select Title<span class="text-danger">*</span></label>
                        <select id="title" name="title" class="form-control" required>
                            @php
                            $assignedTasks = $title->filter(function ($titles) {
                                return !empty($titles->task_title); // Only check if the task title is not empty
                            });
                            @endphp
                    
                            @if($assignedTasks->isNotEmpty())
                                <option value="">Select your task</option>
                                @foreach($assignedTasks as $task)
                                    <option value="{{ $task->id }}" data-project-title="{{ $task->title_name_id }}">{{ $task->task_title }}</option>
                                @endforeach
                            @else
                                <option value="" disabled selected>
                                    No tasks are available. Create a new task first to assign Work Plan
                                </option>
                            @endif
                        </select>
                        <span class="text-danger error-title"></span>
                    </div>

                    <div class="mb-3">
                        <label for="user_id">User Name<span class="text-danger">*</span></label>
                        <select id="user_id" name="task_user_id" class="form-control" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger error-user_id"></span>
                    </div>

                    <div class="mb-3">
                        <label for="description">Work Description<span class="text-danger">*</span></label>
                        <textarea id="description" name="description" class="form-control" rows="4" placeholder="Task Details"></textarea>
                        <span class="text-danger error-description"></span>
                    </div>

                    <div class="mb-3">
                        <label for="last_submit_date">Due Date<span class="text-danger">*</span></label>
                        <input id="Edit_last_submit_date" name="last_submit_date" type="date" class="form-control">
                        <span class="text-danger error-last_submit_date"></span>
                    </div>
                    <div class="mb-3">
                        <label for="work_status">Work Status<span class="text-danger">*</span></label>
                        <select id="work_status" name="work_status" class="form-control">
                            <option value="Work From Home">Work From Home</option>
                            <option value="Work From Office">Work From Office</option>
                        </select>
                        <span class="text-danger error-work_status"></span>
                    </div>

                    <div class="mb-3">
                        <label for="status">Task Status<span class="text-danger">*</span></label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="pending">Ongoing</option>
                            <option value="incomplete">Overdue</option>
                            <option value="completed">Completed</option>
                            <option value="in_progress">In Progress</option>
                        </select>
                        <span class="text-danger error-status"></span>
                    </div>

                    <div class="form-group">
                        <label for="editAttachment">Attachments</label>
                        <input type="file" id="editAttachment" name="attachment[]" class="form-control" multiple>
                        <span class="text-danger error-attachment"></span>
                        <div id="edit-file-names" class="mt-2"></div> 
                        <small class="text-muted">
                            Current Attachments:
                            <div id="currentAttachments" class="mt-1"></div>
                        </small>
                        <!-- Hidden input to store current attachments as JSON -->
                        <input type="hidden" id="currentAttachmentsData" name="currentAttachments" value="[]">
                    </div>      
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="updateTaskBtn" class="btn btn-primary">Update Work Plan</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Submitted Assign Task Modal -->
<div class="modal fade" id="editSubmittedTaskModal" tabindex="-1" aria-labelledby="editSubmittedTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSubmittedTaskModalLabel">Edit Assign Work Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editSubmitProjectForm" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" id="edit_submit_task_id" name="task_id">

                    <div class="mb-3">
                        <label for="Submit_title">Select Title<span class="text-danger">*</span></label>
                        <select id="Submit_title" name="title" class="form-control" required>
                            @php
                            $assignedTasks = $title->filter(function ($titles) {
                                return !empty($titles->task_title); // Only check if the task title is not empty
                            });
                            @endphp
                    
                            @if($assignedTasks->isNotEmpty())
                                <option value="">Select your task</option>
                                @foreach($assignedTasks as $task)
                                    <option value="{{ $task->id }}" data-project-title="{{ $task->title_name_id }}">{{ $task->task_title }}</option>
                                @endforeach
                            @else
                                <option value="" disabled selected>
                                    No tasks are available. Create a new task first to create Work Plan
                                </option>
                            @endif
                        </select>
                        <span class="text-danger error-title"></span>
                    </div>

                    <div class="mb-3">
                        <label for="submit_user_id">User Name<span class="text-danger">*</span></label>
                        <select id="submit_user_id" name="task_user_id" class="form-control">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger error-user_id"></span>
                    </div>

                    <div class="mb-3">
                        <label for="submit_description">Work Description<span class="text-danger">*</span></label>
                        <textarea id="submit_description" name="description" class="form-control" rows="4" placeholder="Task Details"></textarea>
                        <span class="text-danger error-description"></span>
                    </div>

                    <div class="mb-3">
                        <label for="submit_last_submit_date">Due Date<span class="text-danger">*</span></label>
                        <input id="submit_last_submit_date" name="last_submit_date" type="date" class="form-control">
                        <span class="text-danger error-last_submit_date"></span>
                    </div>

                    <div class="mb-3">
                        <label for="submit_submit_date">Submitted Date<span class="text-danger">*</span></label>
                        <input id="submit_submit_date" name="submit_date" type="datetime-local" class="form-control">
                        <span class="text-danger error-submit_date"></span>
                    </div>

                    <div class="mb-3">
                        <label for="submit_work_status">Work Status<span class="text-danger">*</span></label>
                        <select id="submit_work_status" name="work_status" class="form-control">
                            <option value="Work From Home">Work From Home</option>
                            <option value="Work From Office">Work From Office</option>
                        </select>
                        <span class="text-danger error-work_status"></span>
                    </div>

                    <div class="mb-3">
                        <label for="submit_status">Task Status<span class="text-danger">*</span></label>
                        <select id="submit_status" name="status" class="form-control" required>
                            <option value="pending">Ongoing</option>
                            <option value="incomplete">Overdue</option>
                            <option value="completed">Completed</option>
                            <option value="in_progress">In Progress</option>
                        </select>
                        <span class="text-danger error-status"></span>
                    </div>
                    <div class="form-group">
                        <label for="submit_work_attachments">Attachments</label>
                        <input type="file" id="submit_work_attachments" name="attachment[]" class="form-control" multiple>
                        <span class="text-danger error-attachment"></span>
                        <div id="edit-submit-file-names" class="mt-2"></div>
                        <small class="text-muted">
                            Current Attachments:
                            <div id="currentAttachmentsSubmit" class="mt-1"></div>
                        </small>
                        <!-- Hidden input to store current attachments as JSON -->
                        <input type="hidden" id="currentAttachmentsSubmit" name="currentAttachments" value="[]">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="updateSubmitTaskBtn" class="btn btn-primary">Update Work Plan</button>
            </div>
        </div>
    </div>
</div>
</div>
</div>


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
                                    Ongoing Work Plan
                                    <span class="badge bg-primary"> {{ $pendingCount }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#Incomplete"
                                    type="button" role="tab" aria-controls="profile" aria-selected="false">
                                    Overdue Work Plan
                                    <span class="badge bg-primary"> {{ $incompleteCount }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#Complete"
                                    type="button" role="tab" aria-controls="messages" aria-selected="false">
                                    Completed Work Plan
                                    <span class="badge bg-primary"> {{ $completeCount }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#Request"
                                    type="button" role="tab" aria-controls="messages" aria-selected="false">
                                    Requested Work Plan
                                    <span class="badge bg-primary"> {{ $inprogressCount }}</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane active" id="Pending" role="tabpanel" aria-labelledby="home-tab">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <h5>Ongoing Work Plan</h5>
                                    </div>
                                    @can('Manage Work Create')
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <!-- Button trigger modal -->
                                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignTaskModal">
                                                <i class="bx bx-edit-alt me-1"></i> Assign Work Plan
                                            </a>
                                        </div>
                                    </div>
                                    @endcan
                                </div>
                            </div>
                            
                            <div class="table-responsive text-nowrap p-3">
                                <table id="datatable1" class="table">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>User Name</th>
                                            <th>Task Title</th>
                                            <th>Work Plan</th>
                                            <th>Start Date</th>
                                            <th>Due Date</th>
                                            <th>Work Status</th>
                                            <th>Attachments</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.0/js/bootstrap.bundle.min.js"></script>
                                        @foreach($pendingTasks as $key => $task)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                <a href="{{ route('working_profile.show', $task->user->id) }}"
                                                    class="user-hover"
                                                    data-user-id="{{ $task->user->id }}">
                                                    {{ $task->user->name }}
                                                </a>
                                            </td>
                                            <td>{{ $task->task->task_title ?? 'No task title selected' }}</td>
                                            <td>{!!($task->description)!!}</td>
                                            <td>{{ $task->created_at->format('d F Y, h:i A') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($task->submit_date)->format('d F Y') }}</td>
                                            <td>
                                                {{ $task->work_status }}</td>
                                            <td>
                                                @if ($task->attachment)
                                                    @php
                                                        // Decode JSON-encoded attachments and names
                                                        $attachment = json_decode($task->attachment, true);
                                                        $attachmentName = json_decode($task->attachment_name, true);
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
                                            <td>
                                                @if ($task->status == 'pending')
                                                    <span class="badge bg-label-warning me-1">Ongoing</span>
                                                @elseif ($task->status == 'incomplete')
                                                    <span class="badge bg-label-danger me-1">Overdue</span>
                                                    @else
                                                    {{ $task->status }}
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        @can('Manage Work Edit')
                                                        <a href="javascript:void(0);" class="dropdown-item editTaskModal" data-bs-toggle="modal" data-task-id="{{ $task->id }}">
                                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                                        </a>                                                       
                                                        @endcan      
                                                        @can('Manage Work Delete')                                                
                                                        <form id="delete-task-form-{{ $task->id }}" action="{{ route('manage_work.destroy', $task->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="dropdown-item" onclick="confirmDeleteTask({{ $task->id}})">
                                                                <i class="bx bx-trash me-1"></i> Delete
                                                            </button>
                                                        </form>
                                                        @endcan
                                                        @can('Manage Work Change Status')
                                                        <div class="dropdown-divider"></div>
                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item test" href="#" id="dropdownStatusLink">
                                                                <i class="bx bx-refresh me-1"></i> Change Status
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="dropdownStatusLink">
                                                                <li>
                                                                    <form id="complete-task-form-{{ $task->id }}" action="{{ route('manage_work.complete', $task->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmCompleteTask({{ $task->id}})">
                                                                            <i class='bx bx-check'></i> Make Completed
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                <li>
                                                                    <form id="requested-task-form-{{ $task->id }}" action="{{ route('manage_work.requested', $task->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmRequestedTask({{ $task->id}})">
                                                                            <i class="bx bx-task"></i> Move to Requested
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        @endcan
                                                    </div>
                                                </div>                                                           
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-pane" id="Incomplete" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <h5>Overdue Work Plan</h5>
                                    </div>
                                    @can('Manage Work Create')
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <!-- Button trigger modal -->
                                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignTaskModal">
                                                <i class="bx bx-edit-alt me-1"></i> Assign Work Plan
                                            </a>
                                        </div>
                                    </div>
                                    @endcan
                                </div>
                            </div>
                            
                            <div class="table-responsive text-nowrap p-3">
                                <table id="datatable2" class="table">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>User Name</th>
                                            <th>Task Title</th>
                                            <th>Work Plan</th>
                                            <th>Start Date</th>
                                            <th>Due Date</th>
                                            <th>Work Status</th>
                                            <th>Attachments</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($incompleteTasks as $key => $task)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                <a href="{{ route('working_profile.show', $task->user->id) }}"
                                                    class="user-hover"
                                                    data-user-id="{{ $task->user->id }}">
                                                    {{ $task->user->name }}
                                                </a>
                                            </td>
                                            <td>{{ $task->task->task_title ?? 'No task title selected' }}</td>
                                            <td>{!!($task->description)!!}</td>
                                            <td>{{ $task->created_at->format('d F Y, h:i A') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($task->submit_date)->format('d F Y') }}</td>
                                            <td>{{ $task->work_status }}</td>
                                            <td>
                                                @if ($task->attachment)
                                                    @php
                                                        // Decode JSON-encoded attachments and names
                                                        $attachment = json_decode($task->attachment, true);
                                                        $attachmentName = json_decode($task->attachment_name, true);
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
                                            <td>
                                                @if ($task->status == 'pending')
                                                    <span class="badge bg-label-warning me-1">Ongoing</span>
                                                @elseif ($task->status == 'incomplete')
                                                    <span class="badge bg-label-danger me-1">Overdue</span>
                                                    @else
                                                    {{ $task->status }}
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        @can('Manage Work Edit')
                                                        <a href="javascript:void(0);" class="dropdown-item editTaskModal" data-bs-toggle="modal" data-task-id="{{ $task->id }}">
                                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                                        </a>    
                                                        @endcan
                                                        @can('Manage Work Delete')
                                                        <form id="delete-task-form-{{ $task->id }}" action="{{ route('manage_work.destroy', $task->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="dropdown-item" onclick="confirmDeleteTask({{ $task->id}})">
                                                                <i class="bx bx-trash me-1"></i> Delete
                                                            </button>
                                                        </form>
                                                        @endcan
                                                        @can('Manage Work Change Status')
                                                        <div class="dropdown-divider"></div>
                                                        <!-- Right-aligned dropdown for Change Status -->
                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item test" href="#" id="dropdownStatusLink">
                                                                <i class="bx bx-refresh me-1"></i> Change Status
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="dropdownStatusLink">
                                                                <li>
                                                                    <form id="pending-task-form-{{ $task->id }}" action="{{ route('manage_work.pendingdate', $task->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmPendingTask({{ $task->id}})">
                                                                            <i class='bx bx-repost' ></i> Make Ongoing
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                <li>
                                                                    <form id="complete-task-form-{{ $task->id }}" action="{{ route('manage_work.complete', $task->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmCompleteTask({{ $task->id}})">
                                                                            <i class="bx bx-check"></i> Make Completed
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                <li>
                                                                    <form id="requested-task-form-{{ $task->id }}" action="{{ route('manage_work.requested', $task->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmRequestedTask({{ $task->id}})">
                                                                            <i class="bx bx-task"></i> Move to Requested
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        @endcan
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-pane" id="Complete" role="tabpanel" aria-labelledby="messages-tab">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <h5>Completed Work Plan</h5>
                                    </div>
                                    @can('Manage Work Create')
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <!-- Button trigger modal -->
                                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignTaskModal">
                                                <i class="bx bx-edit-alt me-1"></i> Assign Work Plan
                                            </a>
                                        </div>
                                    </div>
                                    @endcan
                                </div>
                            </div>
                            
                            <div class="table-responsive text-nowrap p-3">
                                <table id="datatable3" class="table">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>User Name</th>
                                            <th>Task Title</th>
                                            <th>Work Plan</th>
                                            <th>Start Date</th>
                                            <th>Due Date</th>
                                            <th>Submitted Date</th>
                                            <th>Work Status</th>
                                            <th>Attachments</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($completeTasks as $key => $task)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                <a href="{{ route('working_profile.show', $task->user->id) }}"
                                                    class="user-hover"
                                                    data-user-id="{{ $task->user->id }}">
                                                    {{ $task->user->name }}
                                                </a>
                                            </td>
                                            <td>{{ $task->task->task_title ?? 'No task title selected' }}</td>
                                            <td>{!!($task->description)!!}</td>
                                            <td>{{ $task->created_at->format('d F Y, h:i A') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($task->submit_date)->format('d F Y') }}</td>
                                            <td>{{ $task->submit_by_date ? \Carbon\Carbon::parse($task->submit_by_date)->format('d F Y, h:i A') : 'Still Pending' }}</td>
                                            <td>{{ $task->work_status }}</td>
                                            <td>
                                                @if ($task->attachment)
                                                    @php
                                                        // Decode JSON-encoded attachments and names
                                                        $attachment = json_decode($task->attachment, true);
                                                        $attachmentName = json_decode($task->attachment_name, true);
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
                                            <td>{{ $task->status }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        @can('Manage Work Edit')
                                                        <a href="javascript:void(0);" class="dropdown-item editSubmitTaskModal" data-bs-toggle="modal" data-task-id="{{ $task->id }}">
                                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                                        </a>   
                                                        @endcan
                                                        @can('Manage Work Delete')
                                                        <form id="delete-task-form-{{ $task->id }}" action="{{ route('manage_work.destroy', $task->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="dropdown-item" onclick="confirmDeleteTask({{ $task->id}})">
                                                                <i class="bx bx-trash me-1"></i> Delete
                                                            </button>
                                                        </form>
                                                        @endcan
                                                        @can('Manage Work Change Status')
                                                        <div class="dropdown-divider"></div>
                                                        <!-- Right-aligned dropdown for Change Status -->
                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item test" href="#" id="dropdownStatusLink">
                                                                <i class="bx bx-refresh me-1"></i> Change Status
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="dropdownStatusLink">
                                                                <li>
                                                                    <form id="pending-task-form-{{ $task->id }}" action="{{ route('manage_work.pendingdate', $task->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmPendingTask({{ $task->id}})">
                                                                            <i class="bx bx-repost"></i> Make Ongoing
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                <li>
                                                                    <form id="requested-task-form-{{ $task->id }}" action="{{ route('manage_work.requested', $task->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmRequestedTask({{ $task->id}})">
                                                                            <i class="bx bx-task"></i> Move to Requested
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        @endcan
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                   <div class="tab-pane" id="Request" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <h5>Requested Work Plan</h5>
                                    </div>
                                    @can('Manage Work Create')
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <!-- Button trigger modal -->
                                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignTaskModal">
                                                <i class="bx bx-edit-alt me-1"></i> Assign Work Plan
                                            </a>
                                        </div>
                                    </div>
                                    @endcan
                                </div>
                            </div>
                            
                            <div class="table-responsive text-nowrap p-3">
                                <table id="datatable4" class="table">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>User Name</th>
                                            <th>Task Title</th>
                                            <th>Work Plan</th>
                                            <th>Suggestion</th>
                                            <th>Start Date</th>
                                            <th>Due Date</th>
                                            <th>Work Status</th>
                                            <th>Attachments</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($inprogressTasks as $key => $task)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                <a href="{{ route('working_profile.show', $task->user->id) }}"
                                                    class="user-hover"
                                                    data-user-id="{{ $task->user->id }}">
                                                    {{ $task->user->name }}
                                                </a>
                                            </td>
                                            <td>{{ $task->task->task_title ?? 'No task title selected' }}</td>
                                            <td>{!!($task->description)!!}</td>
                                            <td>{!!($task->message) !!}</td>
                                            <td>{{ $task->created_at->format('d F Y, h:i A') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($task->submit_date)->format('d F Y') }}</td>
                                            <td>{{ $task->work_status }}</td>
                                            <td>
                                                @if ($task->attachment)
                                                    @php
                                                        // Decode JSON-encoded attachments and names
                                                        $attachment = json_decode($task->attachment, true);
                                                        $attachmentName = json_decode($task->attachment_name, true);
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
                                            <td>{{ $task->status }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        @can('Manage Work Accept/Reject')
                                                        <a href="javascript:void(0);" class="dropdown-item editTaskModal" data-bs-toggle="modal" data-task-id="{{ $task->id }}">
                                                            <i class="bx bx-edit-alt me-1"></i> Accept
                                                        </a>
                                                        <form id="incomplete-task-form-{{ $task->id }}" action="{{ route('manage_work.incomplete', $task->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="button" class="dropdown-item" onclick="confirmIncompleteTask({{ $task->id}})">
                                                                <i class="bx bx-trash me-1"></i> Reject
                                                            </button>
                                                        </form>
                                                        @endcan
                                                        @can('Manage Work Change Status')
                                                        <div class="dropdown-divider"></div>
                                                        <!-- Right-aligned dropdown for Change Status -->
                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item test" href="#" id="dropdownStatusLink">
                                                                <i class="bx bx-refresh me-1"></i> Change Status
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="dropdownStatusLink">
                                                                <li>
                                                                    <form id="pending-task-form-{{ $task->id }}" action="{{ route('manage_work.pendingdate', $task->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmPendingTask({{ $task->id}})">
                                                                            <i class="bx bx-repost"></i> Make Ongoing
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                <li>
                                                                    <form id="complete-task-form-{{ $task->id }}" action="{{ route('manage_work.complete', $task->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmCompleteTask({{ $task->id}})">
                                                                            <i class="bx bx-check"></i> Make Completed
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        @endcan
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <script>
                    function confirmDeleteTask(DeleteTaskId) {
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "Do you want to delete this task?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, Delete it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById(`delete-task-form-${DeleteTaskId}`).submit();
                            }
                        });
                    } 
                </script> 
                {{-- Sweet alart for completed task change  --}}
                <script>
                    function confirmCompleteTask(StatusTaskId) {
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "Do you want to make this task completed?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById(`complete-task-form-${StatusTaskId}`).submit();
                            }
                        });
                    } 
                </script> 
                {{-- Sweet alart for requested task change  --}}
                <script>
                    function confirmRequestedTask(requestTaskId) {
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "Do you want to make this task requested?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById(`requested-task-form-${requestTaskId}`).submit();
                            }
                        });
                    } 
                </script> 
                {{-- Sweet alart for pending task change  --}}
                <script>
                    function confirmPendingTask(pendingTaskId) {
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "Do you want to make this task pending? Task last date of submit will be today.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById(`pending-task-form-${pendingTaskId}`).submit();
                            }
                        });
                    } 
                </script>
                {{-- Sweet alart for incomplete task change  --}}
                <script>
                    function confirmIncompleteTask(incompleteTaskId) {
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "Do you want to make this task incomplete or pending? If task last submit date is not expired then pending other wise incomplete.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById(`incomplete-task-form-${incompleteTaskId}`).submit();
                            }
                        });
                    } 
                </script>
            </div>
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.0/js/bootstrap.bundle.min.js"></script>
<style>
    /* Limit z-index to modal-specific select2 */
    .select2-container {
    z-index: 9999 !important;
    }
</style>

<script>
$(document).ready(function(){
    CKEDITOR.replace('modal_description');
    CKEDITOR.replace('task_description');
    CKEDITOR.replace('description');
    CKEDITOR.replace('submit_description');
    CKEDITOR.replace('work_description');

            // Initialize select2 when modal is shown
            $('#createProjectModal').on('shown.bs.modal', function() {
            $('.model_select2').select2({
                placeholder: 'Select User',
                allowClear: true,
                width: '100%'
                });
            });

            // Initialize select2 when modal is shown
            $('#assignTaskModal').on('shown.bs.modal', function() {
            $('.new_model_select2').select2({
                placeholder: 'Select User',
                allowClear: true,
                width: '100%'
                });
            });

            // Initialize select2 for assign task is shown
            $('#newAssignTaskModal').on('shown.bs.modal', function() {
            $('.work_model_select2').select2({
                placeholder: 'Select User',
                allowClear: true,
                width: '100%'
                });
            });

        $('.dropdown-submenu a.test').on("click", function(e){
            $(this).next('ul').toggle();
            e.stopPropagation();
            e.preventDefault();
        });

        // Handle Project creation form submission
        $('#saveProjectBtn').on('click', function(e) {
        e.preventDefault();

            // Create a new FormData object
            let formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('title', $('#modal_title').val());
            formData.append('description', CKEDITOR.instances['modal_description'].getData());
            formData.append('start_date', $('#modal_start_date').val());
            formData.append('end_date', $('#modal_end_date').val());
            // Append all selected files
            let files = $('#modal_attachments')[0].files;
            for (let i = 0; i < files.length; i++) {
                formData.append('attachment[]', files[i]);
            }

            // Append selected user IDs from modal
            $('#modal_user_id').val().forEach(user => {
                formData.append('user_id[]', user);
            });

            $.ajax({
                url: "{{ route('project_title.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#createProjectModal').modal('hide');
                    Swal.fire({
                        title: 'Project Created!',
                        text: 'Your project was created successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                },
                error: function (xhr) { 
                    $('.text-danger').text('');

                    if (xhr.status === 422) {
                        $.each(xhr.responseJSON.errors, function (key, value) {
                            $('.error-' + key).text(value[0]); 
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Something went wrong. Please try again later.',
                            icon: 'error',
                        });
                    }
                }
            });
        });

        // Handle Task creation form submission
        $('#saveTaskBtn').on('click', function(e) {
            e.preventDefault();

            // Create a new FormData object
            let formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('title', $('#task_title').val());
            formData.append('description', CKEDITOR.instances['task_description'].getData());
            formData.append('last_submit_date', $('#last_submit_date').val());
            formData.append('work_status', $('#work_submit_status').val());
            // Append all selected files
            let files = $('#task_attachment')[0].files;
            for (let i = 0; i < files.length; i++) {
                formData.append('attachment[]', files[i]);
            }
            // Fetch data-project-title from selected <option>
            let selectedTask = $('#task_title').find(':selected');
            let projectId = selectedTask.data('project-title') || null; 
            console.log('Selected Project ID:', projectId); 
            formData.append('projectId', projectId);

            // Append selected user IDs from modal
            $('#task_user_id').val().forEach(userId => {
            formData.append('user_id[]', userId);
            });

            $.ajax({
                url: "{{ route('manage_work.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#assignTaskModal').modal('hide');
                    Swal.fire({
                        title: 'Task Created!',
                        text: 'Your Task was created successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                },
                error: function (xhr) { 
                    $('.text-danger').text('');

                    if (xhr.status === 422) {
                        $.each(xhr.responseJSON.errors, function (key, value) {
                            $('.error-' + key).text(value[0]); 
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Something went wrong. Please try again later.',
                            icon: 'error',
                        });
                    }
                }
            });
        });

    // Handle Task creation form submission
    $('#saveWorkBtn').on('click', function(e) {
        e.preventDefault();

        // Create a new FormData object
        let formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('task_title', $('#work_title').val());
        formData.append('title', $('#work_task_title').val());
        formData.append('description', CKEDITOR.instances['work_description'].getData());  // Same ID for description
        formData.append('last_submit_date', $('#work_submit_date').val());  // Updated to match the new ID
        formData.append('work_status', $('#work_submit_status').val());  // Same ID for work status
        // Append all selected files
        let files = $('#work_attachments')[0].files;
        for (let i = 0; i < files.length; i++) {
            formData.append('attachment[]', files[i]);
        }

        // Append selected user IDs from modal
        $('#work_user_id').val().forEach(userId => {  // Updated to match the new ID
            formData.append('user_id[]', userId);
        });

        $.ajax({
            url: "{{ route('asign_tasks.store') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#newAssignTaskModal').modal('hide');  // Updated to match the new modal ID
                Swal.fire({
                    title: 'Task Created!',
                    text: 'Your Task was created successfully.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                }); 
            },
            error: function (xhr) { 
                $('.text-danger').text('');

                if (xhr.status === 422) {
                    $.each(xhr.responseJSON.errors, function (key, value) {
                        $('.error-' + key).text(value[0]); 
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Something went wrong. Please try again later.',
                        icon: 'error',
                    });
                }
            }
        });
    });


    $('#updateSubmitTaskBtn').on('click', function () {
    const updateUrl = $(this).data('url'); 


    const form = $('#editSubmitProjectForm')[0]; 
    const formData = new FormData(form);

    // Validate required fields
    const title = $('#Submit_title').val();
    const description = CKEDITOR.instances.submit_description.getData(); 

    formData.append('_token', $('input[name="_token"]').val());
    formData.append('task_user_id', $('#submit_user_id').val());
    formData.append('title', title);
    formData.append('description', description);
    formData.append('last_submit_date', $('#submit_last_submit_date').val());
    formData.append('submit_by_date', $('#submit_submit_date').val());
    formData.append('work_status', $('#submit_work_status').val());
    formData.append('status', $('#submit_status').val());
    formData.append('currentAttachments', $('#currentAttachmentsSubmit').val());
    formData.append('attachment', $('#currentAttachmentsSubmit').val());
    formData.append('projectId', $('#Submit_title').find(':selected').data('project-title'));

    $.ajax({
        url: updateUrl,
        type: 'POST', 
        data: formData,
        processData: false, 
        contentType: false,
        success: function (response) {
            if (response.status) {
                $('#editSubmittedTaskModal').modal('hide'); 
                Swal.fire({
                    title: 'Project Updated!',
                    text: 'Your project was updated successfully.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload(); // Reload the page after confirmation
                });
            } else {
                Swal.fire({
                    title: 'Update Failed',
                    text: 'Failed to update the task.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        },
        error: function (xhr) {
            $('.text-danger').text('');

            if (xhr.status === 422) {
                $.each(xhr.responseJSON.errors, function (key, value) {
                    $('.error-' + key).text(value[0]); 
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: 'Something went wrong. Please try again later.',
                    icon: 'error',
                });
            }
        }
    });
});


    $('#updateTaskBtn').on('click', function () {
    const updateNewUrl = $(this).data('url'); // Get the URL from data-url attribute

        const form = $('#editProjectForm')[0]; 
        const formData = new FormData(form);

        // Validate required fields
        const title = $('#title').val();
        const description = CKEDITOR.instances.description.getData(); // Get CKEditor content

        formData.append('_token', $('input[name="_token"]').val());
        formData.append('task_user_id', $('#user_id').val());
        formData.append('title', title);
        formData.append('description', description);
        formData.append('last_submit_date', $('#Edit_last_submit_date').val());
        formData.append('work_status', $('#work_status').val());
        formData.append('status', $('#status').val());
        formData.append('currentAttachments', $('#currentAttachmentsData').val());
        formData.append('projectId', $('#title').find(':selected').data('project-title'));

        $.ajax({
            url: updateNewUrl,
            type: 'POST', // Use POST for file uploads
            data: formData,
            processData: false, 
            contentType: false, 
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF protection
            },
            success: function (response) {
                if (response.status) {
                    $('#editTaskModal').modal('hide'); // Close modal

                    Swal.fire({
                        title: 'Project Updated!',
                        text: 'Your project was updated successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload(); // Reload the page after confirmation
                    });
                } else {
                    Swal.fire({
                        title: 'Update Failed',
                        text: 'Failed to update the task.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function (xhr) {
                $('.text-danger').text('');

                if (xhr.status === 422) {
                    $.each(xhr.responseJSON.errors, function (key, value) {
                        $('.error-' + key).text(value[0]);
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Something went wrong. Please try again later.',
                        icon: 'error',
                    });
                }
            }
        });
    });


  // Edit button click handler
  $('.editTaskModal').on('click', function() {
        const taskId = $(this).data('task-id');
        const editUrl = `{{ route('manage_work.edit', ':id') }}`.replace(':id', taskId);
        const updateNewUrl = `{{ route('manage_work.update', ':id') }}`.replace(':id', taskId);

        $.ajax({
            url: editUrl,
            type: 'GET',
            success: function(response) {
                if (response.status) {
                    console.log(response);
                    $('#edit_task_id').val(taskId);
                    $('#title').val(response.data.tasks.task_id);
                    $('#user_id').val(response.data.tasks.user_id);
                    CKEDITOR.instances.description.setData(response.data.tasks.description);
                    $('#Edit_last_submit_date').val(response.data.tasks.submit_date);
                    $('#work_status').val(response.data.tasks.work_status);

                    const attachmentName = response.data.tasks.attachment_name || ''; 
                    const attachment = response.data.tasks.attachment || '';
                    let attachments = [];
                    if (attachment) {
                        try {
                            attachments = JSON.parse(attachment); 
                        } catch (e) {
                            attachments = [attachment]; 
                        }
                    }

                    // Update hidden input field (for storing the attachments data in JSON format if needed)
                    $('#currentAttachmentsData').val(JSON.stringify(attachments));
                    loadExistingAttachments(attachments);

                    // Clear and reset new file selection
                    $('#editAttachment').val('');
                    $('#editAttachmentList').html('<p>No files selected.</p>');

                    if (response.data.tasks.status == "in_progress") {
                        $('#status').val('').closest('.mb-3').hide();
                    } else {
                        $('#status').val(response.data.tasks.status).closest('.mb-3').show();
                    }

                    // Show modal
                    $('#editTaskModal').modal('show');

                    // Set updateNewUrl as a data-url attribute on the update button
                    $('#updateTaskBtn').data('url', updateNewUrl);
                } else {
                    alert('Error loading task data');
                }
            },
            error: function() {
                alert('Failed to load task data');
            }
        });
    });

        // Function to load existing attachments into the modal
        function loadExistingAttachments(attachments) {
        const currentAttachmentsContainer = $('#currentAttachments');
        currentAttachmentsContainer.empty(); // Clear existing content

        if (attachments.length > 0) {
            attachments.forEach((attachment, index) => {
                // Extract the file name from the attachment URL or path
                const fileName = attachment.split('/').pop();

                currentAttachmentsContainer.append(`
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <a href="${attachment}" target="_blank">${fileName}</a>
                        <button type="button" class="btn btn-sm btn-danger remove-existing-attachment" data-index="${index}">&times;</button>
                    </div>
                `);
            });
        } else {
            // Show a placeholder message if no attachments exist
            currentAttachmentsContainer.html('<p>No attachments available.</p>');
        }
    }

        $(document).on('click', '.remove-existing-attachment', function () {
        const attachmentIndex = $(this).data('index');
        const attachmentsData = $('#currentAttachmentsData').val();

        console.log('Attachment Index:', attachmentIndex);
        console.log('Attachments Data:', attachmentsData);

        // Parse attachments from hidden input
        let attachments = [];
        if (attachmentsData) {
            try {
                attachments = JSON.parse(attachmentsData);
            } catch (error) {
                console.error('Error parsing attachments data:', error);
                return; // Stop execution if JSON parsing fails
            }
        }

        // Validate the index before attempting to remove the attachment
        if (attachments.length > 0 && attachmentIndex >= 0 && attachmentIndex < attachments.length) {
            // Remove the attachment at the specified index
            attachments.splice(attachmentIndex, 1);
            
            // Update the hidden input with the new attachments array
            $('#currentAttachmentsData').val(JSON.stringify(attachments));

            // Refresh the attachment display
            loadExistingAttachments(attachments);
        } else {
            console.error('Invalid attachment index or no attachments to remove');
        }
    });

    $('.editSubmitTaskModal').on('click', function() {
    const taskId = $(this).data('task-id');
    const editUrl = `{{ route('manage_work.edit', ':id') }}`.replace(':id', taskId);
    const updateSubmitUrl = `{{ route('manage_work.update', ':id') }}`.replace(':id', taskId);

    $.ajax({
        url: editUrl,
        type: 'GET',
        success: function(response) {
            if (response.status) {
                $('#edit_submit_task_id').val(taskId);
                $('#Submit_title').val(response.data.tasks.task_id);
                $('#submit_user_id').val(response.data.tasks.user_id);
                CKEDITOR.instances.submit_description.setData(response.data.tasks.description);
                $('#submit_last_submit_date').val(response.data.tasks.submit_date); 
                $('#submit_submit_date').val(response.data.tasks.submit_by_date); 
                $('#submit_work_status').val(response.data.tasks.work_status);
                $('#submit_status').val(response.data.tasks.status);

                const attachmentName = response.data.tasks.attachment_name || ''; 
                const attachment = response.data.tasks.attachment || '';
                let attachments = [];
                if (attachment) {
                    try {
                        attachments = JSON.parse(attachment); 
                    } catch (e) {
                        attachments = [attachment]; 
                    }
                }

                // Update hidden input field (for storing the attachments data in JSON format if needed)
                $('#currentAttachmentsSubmit').val(JSON.stringify(attachments));
                loadExistingAttachmentsSubmit(attachments);

                // Clear and reset new file selection
                $('#submit_work_attachments').val('');
                $('#editAttachmentSubmit').html('<p>No files selected.</p>');

                $('#editSubmittedTaskModal').modal('show');

                // Store update URL in a data attribute for retrieval during update
                $('#updateSubmitTaskBtn').data('url', updateSubmitUrl);
            } else {
                alert('Error loading task data');
            }
        },
        error: function() {
            alert('Failed to load task data');
        }
    });
});

    // Function to load existing attachments into the modal
    function loadExistingAttachmentsSubmit(attachments) {
        const currentAttachmentsContainer = $('#currentAttachmentsSubmit');
        currentAttachmentsContainer.empty(); // Clear existing content

        if (attachments.length > 0) {
            attachments.forEach((attachment, index) => {
                // Extract the file name from the attachment URL or path
                const fileName = attachment.split('/').pop();

                currentAttachmentsContainer.append(`
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <a href="${attachment}" target="_blank">${fileName}</a>
                        <button type="button" class="btn btn-sm btn-danger remove-existing-attachment-submit" data-index="${index}">&times;</button>
                    </div>
                `);
            });
        } else {
            // Show a placeholder message if no attachments exist
            currentAttachmentsContainer.html('<p>No attachments available.</p>');
        }
    }

        $(document).on('click', '.remove-existing-attachment-submit', function () {
        const attachmentIndex = $(this).data('index');
        const attachmentsData = $('#currentAttachmentsSubmit').val();

        // Parse attachments from hidden input
        let attachments = [];
        if (attachmentsData) {
            try {
                attachments = JSON.parse(attachmentsData);
            } catch (error) {
                console.error('Error parsing attachments data:', error);
                return; // Stop execution if JSON parsing fails
            }
        }

        // Validate the index before attempting to remove the attachment
        if (attachments.length > 0 && attachmentIndex >= 0 && attachmentIndex < attachments.length) {
            // Remove the attachment at the specified index
            attachments.splice(attachmentIndex, 1);
            
            // Update the hidden input with the new attachments array
            $('#currentAttachmentsSubmit').val(JSON.stringify(attachments));

            // Refresh the attachment display
            loadExistingAttachmentsSubmit(attachments);
        } else {
            console.error('Invalid attachment index or no attachments to remove');
        }
    });

    // Display selected file names dynamically with remove option
    $('#task_attachment').on('change', function() {
        let fileList = $('#file-names'); // Target the div for file names
        fileList.empty(); // Clear previous file names

        let files = $(this)[0].files; // Get selected files
        if (files.length > 0) {
            let ul = $('<ul class="list-group"></ul>'); // Create a list group
            $.each(files, function(index, file) {
                ul.append(
                    `<li class="list-group-item d-flex justify-content-between align-items-center">
                        ${file.name}
                        <button class="btn btn-sm btn-danger remove-file" data-file-index="${index}">&times;</button>
                    </li>`
                );
            });
            fileList.append(ul); // Append the list to the display div
        } else {
            fileList.html('<p>No files selected.</p>'); // Show message if no files are selected
        }
    });

    // Handle remove attachment functionality
    $(document).on('click', '.remove-file', function() {
        let fileIndex = $(this).data('file-index'); 
        let inputElement = $('#task_attachment')[0]; 
        let dataTransfer = new DataTransfer(); 

        let files = inputElement.files;
        for (let i = 0; i < files.length; i++) {
            if (i !== fileIndex) {
                dataTransfer.items.add(files[i]);
            }
        }
        // Update the input element's file list
        inputElement.files = dataTransfer.files;

        $('#task_attachment').trigger('change');
    });

    // Display selected file names dynamically with remove option
    $('#modal_attachments').on('change', function() {
        let fileList = $('#modal-file-names'); // Target the div for file names
        fileList.empty(); // Clear previous file names

        let files = $(this)[0].files; // Get selected files
        if (files.length > 0) {
            let ul = $('<ul class="list-group"></ul>'); // Create a list group
            $.each(files, function(index, file) {
                ul.append(
                    `<li class="list-group-item d-flex justify-content-between align-items-center">
                        ${file.name}
                        <button class="btn btn-sm btn-danger modal-remove-file" data-file-index="${index}">&times;</button>
                    </li>`
                );
            });
            fileList.append(ul); // Append the list to the display div
        } else {
            fileList.html('<p>No files selected.</p>'); // Show message if no files are selected
        }
    });

    // Handle remove attachment functionality
    $(document).on('click', '.modal-remove-file', function() {
        let fileIndex = $(this).data('file-index'); 
        let inputElement = $('#modal_attachments')[0]; 
        let dataTransfer = new DataTransfer(); 

        let files = inputElement.files;
        for (let i = 0; i < files.length; i++) {
            if (i !== fileIndex) {
                dataTransfer.items.add(files[i]);
            }
        }
        // Update the input element's file list
        inputElement.files = dataTransfer.files;

        $('#modal_attachments').trigger('change');
    });

    // Display selected file names dynamically with remove option
    $('#work_attachments').on('change', function() {
        let fileList = $('#work-file-names'); // Target the div for file names
        fileList.empty(); // Clear previous file names

        let files = $(this)[0].files; // Get selected files
        if (files.length > 0) {
            let ul = $('<ul class="list-group"></ul>'); // Create a list group
            $.each(files, function(index, file) {
                ul.append(
                    `<li class="list-group-item d-flex justify-content-between align-items-center">
                        ${file.name}
                        <button class="btn btn-sm btn-danger work-remove-file" data-file-index="${index}">&times;</button>
                    </li>`
                );
            });
            fileList.append(ul); // Append the list to the display div
        } else {
            fileList.html('<p>No files selected.</p>'); // Show message if no files are selected
        }
    });

    // Handle remove attachment functionality
    $(document).on('click', '.work-remove-file', function() {
        let fileIndex = $(this).data('file-index'); 
        let inputElement = $('#work_attachments')[0]; 
        let dataTransfer = new DataTransfer(); 

        let files = inputElement.files;
        for (let i = 0; i < files.length; i++) {
            if (i !== fileIndex) {
                dataTransfer.items.add(files[i]);
            }
        }
        // Update the input element's file list
        inputElement.files = dataTransfer.files;

        $('#work_attachments').trigger('change');
    });

    // Display selected file names dynamically with remove option
    $('#editAttachment').on('change', function() {
        let fileList = $('#edit-file-names'); // Target the div for file names
        fileList.empty(); // Clear previous file names

        let files = $(this)[0].files; // Get selected files
        if (files.length > 0) {
            let ul = $('<ul class="list-group"></ul>'); // Create a list group
            $.each(files, function(index, file) {
                ul.append(
                    `<li class="list-group-item d-flex justify-content-between align-items-center">
                        ${file.name}
                        <button class="btn btn-sm btn-danger edit-remove-file" data-file-index="${index}">&times;</button>
                    </li>`
                );
            });
            fileList.append(ul); // Append the list to the display div
        } else {
            fileList.html('<p>No files selected.</p>'); // Show message if no files are selected
        }
    });

    // Handle remove attachment functionality
    $(document).on('click', '.edit-remove-file', function() {
        let fileIndex = $(this).data('file-index'); 
        let inputElement = $('#editAttachment')[0]; 
        let dataTransfer = new DataTransfer(); 

        let files = inputElement.files;
        for (let i = 0; i < files.length; i++) {
            if (i !== fileIndex) {
                dataTransfer.items.add(files[i]);
            }
        }
        // Update the input element's file list
        inputElement.files = dataTransfer.files;

        $('#editAttachment').trigger('change');
    });

    // Display selected file names dynamically with remove option
    $('#submit_work_attachments').on('change', function() {
        let fileList = $('#edit-submit-file-names'); // Target the div for file names
        fileList.empty(); // Clear previous file names

        let files = $(this)[0].files; // Get selected files
        if (files.length > 0) {
            let ul = $('<ul class="list-group"></ul>'); // Create a list group
            $.each(files, function(index, file) {
                ul.append(
                    `<li class="list-group-item d-flex justify-content-between align-items-center">
                        ${file.name}
                        <button class="btn btn-sm btn-danger edit-submit-remove-file" data-file-index="${index}">&times;</button>
                    </li>`
                );
            });
            fileList.append(ul); // Append the list to the display div
        } else {
            fileList.html('<p>No files selected.</p>'); // Show message if no files are selected
        }
    });

    // Handle remove attachment functionality
    $(document).on('click', '.edit-submit-remove-file', function() {
        let fileIndex = $(this).data('file-index'); 
        let inputElement = $('#submit_work_attachments')[0]; 
        let dataTransfer = new DataTransfer(); 

        let files = inputElement.files;
        for (let i = 0; i < files.length; i++) {
            if (i !== fileIndex) {
                dataTransfer.items.add(files[i]);
            }
        }
        // Update the input element's file list
        inputElement.files = dataTransfer.files;

        $('#submit_work_attachments').trigger('change');
    });

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
                let userDetails = data.data.user_details;
                let loginTime = data.data.loginTime;

                // Update user details
                $("#username").text(user.name);
                $("#userRank").text(userDetails?.user_title || "Info Not Updated");
                $("#profilePhoto").attr("src", user.profile_photo_path ? "{{ asset('storage') }}/" + user.profile_photo_path : "{{ asset('storage/default-profile.png') }}");
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
@if (session('success'))
<script>
    Swal.fire({
        toast: true,
        icon: 'success',
        title: '{{ session('success') }}',
        showConfirmButton: false,
        timer: 3000

        }
    );
</script>
@endif 

@endsection
