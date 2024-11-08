@extends('layouts.master')
@section('content')

<style>
.cke_notification_message,
.cke_notifications_area,
.cke_button__about_icon,
.cke_button__about {
    display: none !important;
}
</style>
    
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<h4 class="py-2 m-4"><span class="text-muted fw-light">Assign Task</span></h4>

<div class="row mt-5">
    <div class="col-12">
        <!-- Main Assign Task Modal -->
        <div class="modal fade" id="assignTaskModal" tabindex="-1" aria-labelledby="assignTaskModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="assignTaskModalLabel">Assign Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="assignTaskForm">
                            @csrf
                            <div class="mb-3">
                                <label for="task_title">Project Title</label>
                                <div class="d-flex align-items-center">
                                    <select id="task_title" name="title" class="form-control" style="width: calc(100% - 30px);">
                                        <option value="">Select title</option>
                                        @foreach($title as $tit)
                                            <option value="{{ $tit->id }}">{{ $tit->project_title }}</option>
                                        @endforeach
                                    </select>
                                    <!-- Button to open Create Project Sub-Modal -->
                                    <button type="button" class="btn btn-link p-0 ml-2" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                                        <i class="fas fa-plus-circle" style="font-size: 24px; color: #007bff;"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="main_user_id">User Name</label>
                                <select id="task_user_id" name="user_id[]" class="form-control new_model_select2" multiple="multiple" required>
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach 
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="task_description">Task Description</label>
                                <textarea id="task_description" name="description" class="form-control" rows="4" required placeholder="Task Details"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="work_status">Work Status</label>
                                <select id="work_submit_status" name="work_status" class="form-control">
                                    <option value="Work From Home">Work From Home</option>
                                    <option value="Work From Office">Work From Office</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="last_submit_date">Last Submit Date</label>
                                <input id="last_submit_date" name="last_submit_date" type="date" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="saveTaskBtn" class="btn btn-primary">Create Task</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sub-modal for Create Project -->
        <div class="modal fade" id="createProjectModal" tabindex="-1" aria-labelledby="createProjectModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content"> 
                    <div class="modal-header">
                        <h5 class="modal-title" id="createProjectModalLabel">Create Project</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body custom-select2-modal">
                        <form id="createProjectForm">
                            @csrf
                            <div class="mb-3">
                                <label for="modal_title">Project Name</label>
                                <input id="modal_title" name="title" type="text" required class="form-control" placeholder="Title Name">
                            </div>
                            <div class="mb-3">
                                <label for="modal_user_id">Assign User</label>
                                <select id="modal_user_id" name="user_id[]" class="form-control model_select2" multiple="multiple" required>
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach 
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="modal_description">Project Description (Optional)</label>
                                <textarea id="modal_description" name="description" class="form-control" rows="4" placeholder="Project Details"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="modal_start_date">Project Start Date</label>
                                <input id="modal_start_date" name="start_date" type="date" required class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="mb-3">
                                <label for="modal_end_date">Project End Date</label>
                                <input id="modal_end_date" name="end_date" type="date" required class="form-control" value="{{ date('Y-m-d') }}">
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
    </div>
</div>

<!-- Edit assign task Modal -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTaskModalLabel">Edit Assign Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editProjectForm" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" id="edit_task_id" name="task_id">

                    <div class="mb-3">
                        <label for="title">Select Title</label>
                        <select id="title" name="title" class="form-control" required>
                            @foreach($title as $tit)
                                <option value="{{ $tit->id }}">{{ $tit->project_title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="user_id">User Name</label>
                        <select id="user_id" name="task_user_id" class="form-control" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="description">Task Description</label>
                        <textarea id="description" name="description" class="form-control" rows="4" placeholder="Task Details"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="last_submit_date">Due Date</label>
                        <input id="Edit_last_submit_date" name="last_submit_date" type="date" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="work_status">Work Status</label>
                        <select id="work_status" name="work_status" class="form-control">
                            <option value="Work From Home">Work From Home</option>
                            <option value="Work From Office">Work From Office</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="status">Task Status</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="pending">Pending</option>
                            <option value="incomplete">Incomplete</option>
                            <option value="completed">Completed</option>
                            <option value="in_progress">In Progress</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="updateTaskBtn" class="btn btn-primary">Update Task</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Submitted Assign Task Modal -->
<div class="modal fade" id="editSubmittedTaskModal" tabindex="-1" aria-labelledby="editSubmittedTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSubmittedTaskModalLabel">Edit Assign Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editSubmitProjectForm" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" id="edit_submit_task_id" name="task_id">

                    <div class="mb-3">
                        <label for="Submit_title">Select Title</label>
                        <select id="Submit_title" name="title" class="form-control" required>
                            @foreach($title as $tit)
                                <option value="{{ $tit->id }}">{{ $tit->project_title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="submit_user_id">User Name</label>
                        <select id="submit_user_id" name="task_user_id" class="form-control">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="submit_description">Task Description</label>
                        <textarea id="submit_description" name="description" class="form-control" rows="4" placeholder="Task Details"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="submit_last_submit_date">Due Date</label>
                        <input id="submit_last_submit_date" name="last_submit_date" type="date" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="submit_submit_date">Submitted Date</label>
                        <input id="submit_submit_date" name="submit_date" type="datetime-local" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="submit_work_status">Work Status</label>
                        <select id="submit_work_status" name="work_status" class="form-control">
                            <option value="Work From Home">Work From Home</option>
                            <option value="Work From Office">Work From Office</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="submit_status">Task Status</label>
                        <select id="submit_status" name="status" class="form-control" required>
                            <option value="pending">Pending</option>
                            <option value="incomplete">Incomplete</option>
                            <option value="completed">Completed</option>
                            <option value="in_progress">In Progress</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="updateSubmitTaskBtn" class="btn btn-primary">Update Task</button>
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
                                    Pending Task 
                                    <span class="badge bg-primary"> {{ $pendingCount }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#Incomplete"
                                    type="button" role="tab" aria-controls="profile" aria-selected="false">
                                    Incomplete Task
                                    <span class="badge bg-primary"> {{ $incompleteCount }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#Complete"
                                    type="button" role="tab" aria-controls="messages" aria-selected="false">
                                    Completed Task
                                    <span class="badge bg-primary"> {{ $completeCount }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#Request"
                                    type="button" role="tab" aria-controls="messages" aria-selected="false">
                                    Requested Task
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
                                        <h5>Pending Task</h5>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <!-- Button trigger modal -->
                                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignTaskModal">
                                                <i class="bx bx-edit-alt me-1"></i> Assign Task
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="table-responsive text-nowrap p-3">
                                <table id="datatable1" class="table">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Start Date</th>
                                            <th>Due Date</th>
                                            <th>Submitted Date</th>
                                            <th>User Name</th>
                                            <th>Project Title</th>
                                            <th>Task</th>
                                            <th>Work Status</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.0/js/bootstrap.bundle.min.js"></script>
                                        @foreach($pendingTasks as $key => $task)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $task->created_at->format('d F Y, h:i A') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($task->submit_date)->format('d F Y') }}</td>
                                            <td>{{ $task->submit_by_date ? \Carbon\Carbon::parse($task->submit_by_date)->format('d F Y, h:i A') : 'Still Pending' }}</td>
                                            <td>{{ $task->user->name }}</td>
                                            <td>{{ $task->title_name->project_title ?? 'No project title selected' }}</td>
                                            <td>{!!($task->description)!!}</td>
                                            <td>{{ $task->work_status }}</td>
                                            <td>{{ $task->status }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a href="javascript:void(0);" class="dropdown-item editTaskModal" data-bs-toggle="modal" data-task-id="{{ $task->id }}">
                                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                                        </a>                                                        
                                                        <form id="delete-task-form-{{ $task->id }}" action="{{ route('asign_tasks.destroy', $task->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="dropdown-item" onclick="confirmDeleteTask({{ $task->id}})">
                                                                <i class="bx bx-trash me-1"></i> Delete
                                                            </button>
                                                        </form>
                                                        <div class="dropdown-divider"></div>
                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item test" href="#" id="dropdownStatusLink">
                                                                <i class="bx bx-refresh me-1"></i> Change Status
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="dropdownStatusLink">
                                                                <li>
                                                                    <form id="complete-task-form-{{ $task->id }}" action="{{ route('asign_tasks.complete', $task->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmCompleteTask({{ $task->id}})">
                                                                            <i class='bx bx-check'></i> Make Completed
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                <li>
                                                                    <form id="requested-task-form-{{ $task->id }}" action="{{ route('asign_tasks.requested', $task->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmRequestedTask({{ $task->id}})">
                                                                            <i class="bx bx-task"></i> Move to Requested
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
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
                                        <h5>Incomplete Task</h5>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <!-- Button trigger modal -->
                                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignTaskModal">
                                                <i class="bx bx-edit-alt me-1"></i> Assign Task
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="table-responsive text-nowrap p-3">
                                <table id="datatable2" class="table">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Start Date</th>
                                            <th>Due Date</th>
                                            <th>Submitted Date</th>
                                            <th>User Name</th>
                                            <th>Project Title</th>
                                            <th>Task</th>
                                            <th>Work Status</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($incompleteTasks as $key => $task)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $task->created_at->format('d F Y, h:i A') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($task->submit_date)->format('d F Y') }}</td>
                                            <td>{{ $task->submit_by_date ? \Carbon\Carbon::parse($task->submit_by_date)->format('d F Y') : 'Task Incomplete' }}</td>
                                            <td>{{ $task->user->name }}</td>
                                            <td>{{ $task->title_name->project_title ?? 'No project title selected' }}</td>
                                            <td>{!!($task->description)!!}</td>
                                            <td>{{ $task->work_status }}</td>
                                            <td>{{ $task->status }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a href="javascript:void(0);" class="dropdown-item editTaskModal" data-bs-toggle="modal" data-task-id="{{ $task->id }}">
                                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                                        </a>    
                                                        <form id="delete-task-form-{{ $task->id }}" action="{{ route('asign_tasks.destroy', $task->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="dropdown-item" onclick="confirmDeleteTask({{ $task->id}})">
                                                                <i class="bx bx-trash me-1"></i> Delete
                                                            </button>
                                                        </form>
                                                        <div class="dropdown-divider"></div>
                                                        <!-- Right-aligned dropdown for Change Status -->
                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item test" href="#" id="dropdownStatusLink">
                                                                <i class="bx bx-refresh me-1"></i> Change Status
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="dropdownStatusLink">
                                                                <li>
                                                                    <form id="pending-task-form-{{ $task->id }}" action="{{ route('asign_tasks.pendingdate', $task->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmPendingTask({{ $task->id}})">
                                                                            <i class='bx bx-repost' ></i> Make Pending
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                <li>
                                                                    <form id="complete-task-form-{{ $task->id }}" action="{{ route('asign_tasks.complete', $task->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmCompleteTask({{ $task->id}})">
                                                                            <i class="bx bx-check"></i> Make Completed
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                <li>
                                                                    <form id="requested-task-form-{{ $task->id }}" action="{{ route('asign_tasks.requested', $task->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmRequestedTask({{ $task->id}})">
                                                                            <i class="bx bx-task"></i> Move to Requested
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
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
                                        <h5>Completed Task</h5>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <!-- Button trigger modal -->
                                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignTaskModal">
                                                <i class="bx bx-edit-alt me-1"></i> Assign Task
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="table-responsive text-nowrap p-3">
                                <table id="datatable3" class="table">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Start Date</th>
                                            <th>Due Date</th>
                                            <th>Submitted Date</th>
                                            <th>User Name</th>
                                            <th>Project Title</th>
                                            <th>Task</th>
                                            <th>Work Status</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($completeTasks as $key => $task)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $task->created_at->format('d F Y, h:i A') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($task->submit_date)->format('d F Y') }}</td>
                                            <td>{{ $task->submit_by_date ? \Carbon\Carbon::parse($task->submit_by_date)->format('d F Y, h:i A') : 'Still Pending' }}</td>
                                            <td>{{ $task->user->name }}</td>
                                            <td>{{ $task->title_name->project_title ?? 'No project title selected' }}</td>
                                            <td>{!!($task->description)!!}</td>
                                            <td>{{ $task->work_status }}</td>
                                            <td>{{ $task->status }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a href="javascript:void(0);" class="dropdown-item editSubmitTaskModal" data-bs-toggle="modal" data-task-id="{{ $task->id }}">
                                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                                        </a>   
                                                        <form id="delete-task-form-{{ $task->id }}" action="{{ route('asign_tasks.destroy', $task->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="dropdown-item" onclick="confirmDeleteTask({{ $task->id}})">
                                                                <i class="bx bx-trash me-1"></i> Delete
                                                            </button>
                                                        </form>
                                                        <div class="dropdown-divider"></div>
                                                        <!-- Right-aligned dropdown for Change Status -->
                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item test" href="#" id="dropdownStatusLink">
                                                                <i class="bx bx-refresh me-1"></i> Change Status
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="dropdownStatusLink">
                                                                <li>
                                                                    <form id="pending-task-form-{{ $task->id }}" action="{{ route('asign_tasks.pendingdate', $task->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmPendingTask({{ $task->id}})">
                                                                            <i class="bx bx-repost"></i> Make Pending
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                <li>
                                                                    <form id="requested-task-form-{{ $task->id }}" action="{{ route('asign_tasks.requested', $task->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmRequestedTask({{ $task->id}})">
                                                                            <i class="bx bx-task"></i> Move to Requested
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
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
                                        <h5>Requested Task</h5>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <!-- Button trigger modal -->
                                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignTaskModal">
                                                <i class="bx bx-edit-alt me-1"></i> Assign Task
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="table-responsive text-nowrap p-3">
                                <table id="datatable4" class="table">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Start Date</th>
                                            <th>Due Date</th>
                                            <th>User Name</th>
                                            <th>Project Title</th>
                                            <th>Task</th>
                                            <th>Suggestion</th>
                                            <th>Work Status</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($inprogressTasks as $key => $task)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $task->created_at->format('d F Y, h:i A') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($task->submit_date)->format('d F Y') }}</td>
                                            <td>{{ $task->user->name }}</td>
                                            <td>{{ $task->title_name->project_title ?? 'No project title selected' }}</td>
                                            <td>{!!($task->description)!!}</td>
                                            <td>{!!($task->message) !!}</td>
                                            <td>{{ $task->work_status }}</td>
                                            <td>{{ $task->status }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a href="javascript:void(0);" class="dropdown-item editTaskModal" data-bs-toggle="modal" data-task-id="{{ $task->id }}">
                                                            <i class="bx bx-edit-alt me-1"></i> Accept
                                                        </a>
                                                        <form id="incomplete-task-form-{{ $task->id }}" action="{{ route('asign_tasks.incomplete', $task->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="button" class="dropdown-item" onclick="confirmIncompleteTask({{ $task->id}})">
                                                                <i class="bx bx-trash me-1"></i> Reject
                                                            </button>
                                                        </form>
                                                        <div class="dropdown-divider"></div>
                                                        <!-- Right-aligned dropdown for Change Status -->
                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item test" href="#" id="dropdownStatusLink">
                                                                <i class="bx bx-refresh me-1"></i> Change Status
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="dropdownStatusLink">
                                                                <li>
                                                                    <form id="pending-task-form-{{ $task->id }}" action="{{ route('asign_tasks.pendingdate', $task->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmPendingTask({{ $task->id}})">
                                                                            <i class="bx bx-repost"></i> Make Pending
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                <li>
                                                                    <form id="complete-task-form-{{ $task->id }}" action="{{ route('asign_tasks.complete', $task->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmCompleteTask({{ $task->id}})">
                                                                            <i class="bx bx-check"></i> Make Completed
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
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
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                    Swal.fire({
                        title: 'Error!',
                        text: 'There was an error creating the project.',
                        icon: 'error',
                        confirmButtonText: 'Try Again'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
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

            // Append selected user IDs from modal
            $('#task_user_id').val().forEach(userId => {
            formData.append('user_id[]', userId);
            });

            $.ajax({
                url: "{{ route('asign_tasks.store') }}",
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
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                    Swal.fire({
                        title: 'Error!',
                        text: 'There was an error creating the task.',
                        icon: 'error',
                        confirmButtonText: 'Try Again'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                }
            });
        });

    // Edit button click handler
    $('.editTaskModal').on('click', function() {
        const taskId = $(this).data('task-id');
        const editUrl = `{{ route('asign_tasks.edit', ':id') }}`.replace(':id', taskId);
        const updateNewUrl = `{{ route('asign_tasks.update', ':id') }}`.replace(':id', taskId);

        $.ajax({
            url: editUrl,
            type: 'GET',
            success: function(response) {
                if (response.status) {
                    console.log(response);
                    $('#edit_task_id').val(taskId);
                    $('#title').val(response.data.tasks.title_name_id);
                    $('#user_id').val(response.data.tasks.user_id);
                    CKEDITOR.instances.description.setData(response.data.tasks.description);
                    $('#Edit_last_submit_date').val(response.data.tasks.submit_date);
                    $('#work_status').val(response.data.tasks.work_status);

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

    $('.editSubmitTaskModal').on('click', function() {
    const taskId = $(this).data('task-id');
    const editUrl = `{{ route('asign_tasks.edit', ':id') }}`.replace(':id', taskId);
    const updateSubmitUrl = `{{ route('asign_tasks.update', ':id') }}`.replace(':id', taskId);

    $.ajax({
        url: editUrl,
        type: 'GET',
        success: function(response) {
            if (response.status) {
                $('#edit_submit_task_id').val(taskId);
                $('#Submit_title').val(response.data.tasks.title_name_id);
                $('#submit_user_id').val(response.data.tasks.user_id);
                CKEDITOR.instances.submit_description.setData(response.data.tasks.description);
                $('#submit_last_submit_date').val(response.data.tasks.submit_date); 
                $('#submit_submit_date').val(response.data.tasks.submit_by_date); 
                $('#submit_work_status').val(response.data.tasks.work_status);
                $('#submit_status').val(response.data.tasks.status);

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

    $('#updateSubmitTaskBtn').on('click', function() {
        const updateUrl = $(this).data('url');
        console.log("Update URL:", updateUrl); // Check if the URL is correct

        $.ajax({
            url: updateUrl,
            type: 'PUT',
            data: {
                _token: $('input[name="_token"]').val(),
                task_user_id: $('#submit_user_id').val(),
                title: $('#Submit_title').val(),
                description: CKEDITOR.instances.submit_description.getData(),
                last_submit_date: $('#submit_last_submit_date').val(),
                submit_by_date: $('#submit_submit_date').val(),
                work_status: $('#submit_work_status').val(),
                status: $('#submit_status').val(),
            },
            success: function(response) {
                if (response.status) {
                    $('#editSubmittedTaskModal').modal('hide'); // Close modal
                    console.log("Task updated successfully"); // For debugging

                    // Show SweetAlert success notification
                    Swal.fire({
                        title: 'Project Updated!',
                        text: 'Your project was updated successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload(); // Reload the page after confirmation
                    });
                } else {
                    console.log("Failed to update task"); // Debugging
                    // Show failure SweetAlert
                    Swal.fire({
                        title: 'Update Failed',
                        text: 'Failed to update the task.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr) {
                console.error("Error:", xhr.responseJSON.message); // Debugging
                // Show error SweetAlert with the specific error message
                Swal.fire({
                    title: 'Error',
                    text: 'Error updating task: ' + xhr.responseJSON.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    // Update button click handler
    $('#updateTaskBtn').on('click', function() {
        const updateNewUrl = $(this).data('url'); // Get the URL from data-url attribute

        $.ajax({
            url: updateNewUrl,
            type: 'PUT',
            data: {
                _token: $('input[name="_token"]').val(),
                task_user_id: $('#user_id').val(),
                title: $('#title').val(),
                description: CKEDITOR.instances.description.getData(),
                last_submit_date: $('#Edit_last_submit_date').val(),
                work_status: $('#work_status').val(),
                status: $('#status').val(),
            },
            success: function(response) {
                if (response.status) {
                    $('#editTaskModal').modal('hide'); // Close modal

                    // Show SweetAlert success notification
                    Swal.fire({
                        title: 'Project Updated!',
                        text: 'Your project was updated successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload(); // Reload the page after confirmation
                    });
                } else {
                    // Show failure SweetAlert
                    Swal.fire({
                        title: 'Update Failed',
                        text: 'Failed to update the task.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr) {
                // Show error SweetAlert with the specific error message
                Swal.fire({
                    title: 'Error',
                    text: 'Error updating task: ' + xhr.responseJSON.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
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
