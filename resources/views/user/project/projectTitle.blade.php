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
    <h4 class="py-2 m-4"><span class="text-muted fw-light">Project Details</span></h4>

    <div class="row mt-5">
        <!-- Modal -->
            <div class="modal fade" id="createProjectModal" tabindex="-1" aria-labelledby="createProjectModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createProjectModalLabel">Create Project</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="createProjectForm">
                                @csrf
                                <input type="hidden" name="previous_url" value="{{ url()->previous() }}">

                                <div class="mb-3">
                                    <label for="title">Project Name</label>
                                    <input id="title" name="title" type="text" required class="form-control" placeholder="Title Name">
                                    <span class="text-danger error-title"></span>
                                </div>

                                <div class="mb-3">
                                    <label for="user_id">Assign User</label>
                                    <select id="user_id" name="user_id[]" class="form-control" multiple="multiple" required>
                                        <option value="">Select User</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach 
                                    </select>
                                    <span class="text-danger error-user_id"></span>
                                </div>

                                <div class="mb-3">
                                    <label for="description">Project Description (Optional)</label>
                                    <textarea id="description" name="description" class="form-control" rows="4" placeholder="Project Details"></textarea>
                                    <span class="text-danger error-description"></span>
                                </div>

                                <div class="mb-3">
                                    <label for="start_date">Project Start Date</label>
                                    <input id="start_date" name="start_date" type="date" required class="form-control" value="{{ date('Y-m-d') }}">
                                    <span class="text-danger error-start_date"></span>
                                </div>

                                <div class="mb-3">
                                    <label for="end_date">Project End Date</label>
                                    <input id="end_date" name="end_date" type="date" required class="form-control" value="{{ date('Y-m-d') }}">
                                    <span class="text-danger error-end_date"></span>
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
            <!-- Edit Project Modal -->
            <div class="modal fade" id="editProjectModal" tabindex="-1" aria-labelledby="editProjectModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editProjectModalLabel">Edit Project</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editProjectForm" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label for="editTitle">Project Name</label>
                                    <input id="editTitle" name="title" type="text" required class="form-control" placeholder="Title Name">
                                    <span class="text-danger error-title"></span>
                                </div>
                                <div class="mb-3">
                                    <label for="editUser">User Name</label>
                                    <select id="editUser" name="user_id[]" class="form-control" multiple="multiple" required></select>
                                    <span class="text-danger error-user_id"></span>
                                </div>
                                <div class="mb-3">
                                    <label for="editDescription">Project Description (Not required)</label>
                                    <textarea id="editDescription" name="description" class="form-control" rows="4" placeholder="Project Details"></textarea>
                                    <span class="text-danger error-description"></span>
                                </div>
                                <div class="mb-3">
                                    <label for="editStartDate">Project Start Date</label>
                                    <input id="editStartDate" name="start_date" type="date" required class="form-control" placeholder="Date">
                                    <span class="text-danger error-start_date"></span>
                                </div>
                                <div class="mb-3">
                                    <label for="editEndDate">Project End Date</label>
                                    <input id="editEndDate" name="end_date" type="date" required class="form-control" placeholder="Date">
                                    <span class="text-danger error-end_date"></span>
                                </div>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        <div class="col-12 col-md-12 col-lg-12">
            <div class="card p-lg-4 p-2">
                <!-- Nav tabs -->
                <div class="container">
                    <div class="row justify-content-center">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#Pending"
                                    type="button" role="tab" aria-controls="home" aria-selected="true">
                                    Running Project 
                                    <span class="badge bg-primary"> {{$runningCount}}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#Incomplete"
                                    type="button" role="tab" aria-controls="profile" aria-selected="false">
                                    Completed Project
                                    <span class="badge bg-primary">{{$completedCount}}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#Complete"
                                    type="button" role="tab" aria-controls="messages" aria-selected="false">
                                    Dropped Project
                                    <span class="badge bg-primary">{{$droppedCount}}</span>
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
                                        <h5>Running Project</h5>
                                    </div>
                                    @can('Create Project')
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                                                <i class="bx bx-edit-alt me-1"></i> Create New Project
                                            </button>
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
                                            <th>Start Date</th>
                                            <th>Expected End Date</th>
                                            <th>Completed Date</th>
                                            <th>assigned User</th>
                                            <th>Project Title</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            @can('View Project Action')
                                            <th>Actions</th>
                                            @endcan
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($runningProject as $key => $project)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ \Carbon\Carbon::parse($project->start_date)->format('d F Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($project->end_date)->format('d F Y') }}</td>
                                            <td>{{ $project->end_by_date ? \Carbon\Carbon::parse($project->end_by_date)->format('d F Y, h:i A') : 'Project Running' }}</td>
                                            <td>
                                                @foreach(explode(',', $project->user_id) as $userId)
                                                @php
                                                    $user = App\Models\User::find($userId);
                                                @endphp
                                                 {{ $user->name ?? 'No user assigned' }}<br>
                                                @endforeach
                                            </td>
                                            <td>{{ $project->project_title ?? 'No project title selected' }}</td>
                                            <td>{!! ($project->description) !!}</td>
                                            <td>{{ $project->status }}</td>
                                            @can('View Project Action')
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item edit-button" href="javascript:void(0);" data-id="{{ $project->id }}">
                                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                                        </a>
                                                        <form id="Delete-task-form-{{ $project->id }}" action="{{ route('project_title.destroy', ['project_title' => $project->id]) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="dropdown-item" onclick="confirmDeleteTask({{ $project->id }})">
                                                                <i class="bx bx-trash me-1"></i> Delete
                                                            </button>
                                                        </form>
                                                        @can('Project Change Status')
                                                        <div class="dropdown-divider"></div>
                                                        <!-- Right-aligned dropdown for Change Status -->
                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item test" href="#" id="dropdownStatusLink">
                                                                <i class="bx bx-refresh me-1"></i> Change Status
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="dropdownStatusLink">
                                                                <li>
                                                                    <form id="Complete-task-form-{{ $project->id }}" action="{{ route('project.complete', ['project_title' => $project->id]) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmCompleteTask({{ $project->id }})">
                                                                            <i class="bx bx-check me-1"></i> Complete Project
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                <li>
                                                                    <form id="Drop-task-form-{{ $project->id }}" action="{{ route('project.drop', ['project_title' => $project->id]) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmDropTask({{ $project->id }})">
                                                                            <i class="bx bx-check me-1"></i> Drop Project
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        @endcan
                                                    </div>
                                                </div>                                                           
                                            </td>
                                            @endcan
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
                                        <h5>Completed Project</h5>
                                    </div>
                                    @can('Create Project')
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <!-- Button trigger modal -->
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                                                <i class="bx bx-edit-alt me-1"></i> Create New Project
                                            </button>
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
                                            <th>Start Date</th>
                                            <th>Expected End Date</th>
                                            <th>Completed Date</th>
                                            <th>assigned User</th>
                                            <th>Project Title</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            @can('View Project Action')
                                            <th>Actions</th>
                                            @endcan
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($completedProject as $key => $project)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ \Carbon\Carbon::parse($project->start_date)->format('d F Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($project->end_date)->format('d F Y') }}</td>
                                            <td>{{ $project->end_by_date ? \Carbon\Carbon::parse($project->end_by_date)->format('d F Y, h:i A') : 'Project completed' }}</td>
                                            <td> @foreach(explode(',', $project->user_id) as $userId)
                                                @php
                                                    $user = App\Models\User::find($userId);
                                                @endphp
                                                 {{ $user->name ?? 'No user assigned' }}<br>
                                                @endforeach
                                            </td>
                                            <td>{{ $project->project_title ?? 'No project title selected' }}</td>
                                            <td>{!! ($project->description) !!}</td>
                                            <td>{{ $project->status }}</td>
                                            @can('View Project Action')
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item edit-button" href="javascript:void(0);" data-id="{{ $project->id }}">
                                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                                        </a>                                                 
                                                        <form id="Delete-task-form-{{ $project->id }}" action="{{ route('project_title.destroy', ['project_title' => $project->id]) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="dropdown-item" onclick="confirmDeleteTask({{ $project->id }})">
                                                                <i class="bx bx-trash me-1"></i> Delete
                                                            </button>
                                                        </form>
                                                        @can('Project Change Status')
                                                        <div class="dropdown-divider"></div>
                                                        <!-- Right-aligned dropdown for Change Status -->
                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item test" href="#" id="dropdownStatusLink">
                                                                <i class="bx bx-refresh me-1"></i> Change Status
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="dropdownStatusLink">
                                                                <li>
                                                                    <form id="Running-task-form-{{ $project->id }}" action="{{ route('project.running', ['project_title' => $project->id]) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmRunningTask({{ $project->id }})">
                                                                            <i class="bx bx-check me-1"></i> Running Project
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                <li>
                                                                    <form id="Drop-task-form-{{ $project->id }}" action="{{ route('project.drop', ['project_title' => $project->id]) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmDropTask({{ $project->id }})">
                                                                            <i class="bx bx-check me-1"></i> Drop Project
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        @endcan
                                                    </div>
                                                </div>                                                           
                                            </td>
                                            @endcan
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
                                        <h5>Dropped Project</h5>
                                    </div>
                                    @can('Create Project')
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <!-- Button trigger modal -->
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                                                <i class="bx bx-edit-alt me-1"></i> Create New Project
                                            </button>
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
                                            <th>Start Date</th>
                                            <th>Expected End Date</th>
                                            <th>assigned User</th>
                                            <th>Project Title</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            @can('View Project Action')
                                            <th>Actions</th>
                                            @endcan
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($droppedProject as $key => $project)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ \Carbon\Carbon::parse($project->start_date)->format('d F Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($project->end_date)->format('d F Y') }}</td>
                                            <td> @foreach(explode(',', $project->user_id) as $userId)
                                                @php
                                                    $user = App\Models\User::find($userId);
                                                @endphp
                                                 {{ $user->name ?? 'No user assigned' }}<br>
                                                @endforeach
                                            </td>
                                            <td>{{ $project->project_title ?? 'No project title selected' }}</td>
                                            <td>{!! ($project->description) !!}</td>
                                            <td>{{ $project->status }}</td>
                                            @can('View Project Action')
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item edit-button" href="javascript:void(0);" data-id="{{ $project->id }}">
                                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                                        </a>
                                                        <form id="Delete-task-form-{{ $project->id }}" action="{{ route('project_title.destroy', ['project_title' => $project->id]) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="dropdown-item" onclick="confirmDeleteTask({{ $project->id }})">
                                                                <i class="bx bx-trash me-1"></i> Delete
                                                            </button>
                                                        </form>
                                                        @can('Project Change Status')
                                                        <div class="dropdown-divider"></div>
                                                        <!-- Right-aligned dropdown for Change Status -->
                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item test" href="#" id="dropdownStatusLink">
                                                                <i class="bx bx-refresh me-1"></i> Change Status
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="dropdownStatusLink">
                                                                <form id="Running-task-form-{{ $project->id }}" action="{{ route('project.running', ['project_title' => $project->id]) }}" method="POST">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="button" class="dropdown-item" onclick="confirmRunningTask({{ $project->id }})">
                                                                        <i class="bx bx-check me-1"></i> Running Project
                                                                    </button>
                                                                </form>
                                                                <li>
                                                                    <form id="Complete-task-form-{{ $project->id }}" action="{{ route('project.complete', ['project_title' => $project->id]) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmCompleteTask({{ $project->id }})">
                                                                            <i class="bx bx-check me-1"></i> Complete Project
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        @endcan
                                                    </div>
                                                </div>                                                           
                                            </td>
                                            @endcan
                                        </tr>
                                        @endforeach
                                        <script>
                                            function confirmRunningTask(runningTaskId) {
                                                Swal.fire({
                                                    title: 'Are you sure?',
                                                    text: "Do you want to make this project running? Your project submitted date will be reset",
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#3085d6',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: 'Yes'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        document.getElementById(`Running-task-form-${runningTaskId}`).submit();
                                                    }
                                                });
                                            }
                                        </script> 
                                        <script>
                                            function confirmCompleteTask(CompleteTaskId) {
                                                Swal.fire({
                                                    title: 'Are you sure?',
                                                    text: "Do you want to Complete this project? Your project completed will be today.",
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#3085d6',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: 'Yes'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        document.getElementById(`Complete-task-form-${CompleteTaskId}`).submit();
                                                    }
                                                });
                                            }
                                        </script> 
                                        <script>
                                            function confirmDeleteTask(DeleteTaskId) {
                                                Swal.fire({
                                                    title: 'Are you sure?',
                                                    text: "Do you want to Delete this project? All related tasks and work plan will be deleted.",
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#3085d6',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: 'Yes'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        document.getElementById(`Delete-task-form-${DeleteTaskId}`).submit();
                                                    }
                                                });
                                            }
                                        </script> 
                                        <script>
                                            function confirmDropTask(DropTaskId) {
                                                Swal.fire({
                                                    title: 'Are you sure?',
                                                    text: "Do you want to Drop this project?",
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#3085d6',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: 'Yes'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        document.getElementById(`Drop-task-form-${DropTaskId}`).submit();
                                                    }
                                                });
                                            }
                                        </script> 
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Initialize CKEditor for the Project Description field
            CKEDITOR.replace('description');
            CKEDITOR.replace('editDescription');
        });
    </script>

    <!-- CSS for Select2 z-index in Modal -->
    <style>
        .select2-container {
            z-index: 9999 !important;
        }
    </style>

    <script>

        $(document).ready(function() {
            $('.dropdown-submenu a.test').on("click", function(e){
            $(this).next('ul').toggle();
            e.stopPropagation();
            e.preventDefault();
        });
            // Initialize select2 when modal is shown
            $('#createProjectModal').on('shown.bs.modal', function() {
            $('#user_id').select2({
                placeholder: 'Select User',
                allowClear: true,
                width: '100%'
                });
            });

            // Initialize select2 when modal is shown
            $('#editProjectModal').on('shown.bs.modal', function() {
            $('#editUser').select2({
                placeholder: 'Select User',
                allowClear: true,
                width: '100%'
                });
            });
        // Handle form submission with AJAX
        $('#saveProjectBtn').on('click', function(e) {
            e.preventDefault();

            // Create a new FormData object
            let formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}'); // Add CSRF token
            formData.append('title', $('#title').val());
            formData.append('description', CKEDITOR.instances['description'].getData()); // Get data from CKEditor
            formData.append('start_date', $('#start_date').val());
            formData.append('end_date', $('#end_date').val());
            
            // Append user_id values individually
            $('#user_id').val().forEach(user => {
                formData.append('user_id[]', user);
            });

            $.ajax({
                url: "{{ route('project_title.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#createProjectModal').modal('hide'); // Hide the modal
                    
                    // Display success message with SweetAlert
                    Swal.fire({
                        title: 'Project Created!',
                        text: 'Your project was created successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload(); // Reload the page
                        }
                    });
                    // Optionally, refresh the page or update the project list dynamically
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
            // Event listener for edit button click
            $('.edit-button').on('click', function() {
                const projectId = $(this).data('id');
                
                // Construct the URL with the project ID dynamically using JavaScript string template
                const editUrl = `{{ route('edit.project_title', ':id') }}`.replace(':id', projectId);
                const updateUrl = `{{ route('project_title.update', ':id') }}`.replace(':id', projectId);

                // Fetch project data
                $.ajax({
                    url: editUrl, // Use the dynamically constructed URL
                    type: 'GET',
                    success: function(response) {
                        if (response.status) {
                            // Populate modal fields with fetched data
                            $('#editTitle').val(response.project.project_title);
                            // Set CKEditor data for the description field
                            if (CKEDITOR.instances['editDescription']) {
                                CKEDITOR.instances['editDescription'].setData(response.project.description);
                            }
                            $('#editStartDate').val(response.project.start_date);
                            $('#editEndDate').val(response.project.end_date);
                            
                            // Populate users select field
                            $('#editUser').empty();
                            response.users.forEach(user => {
                                const isSelected = response.assignedUsers.includes(user.id.toString()) ? 'selected' : '';
                                $('#editUser').append(`<option value="${user.id}" ${isSelected}>${user.name}</option>`);
                            });

                            // Show the modal
                            $('#editProjectModal').modal('show');
                            
                            // Set form action for update
                            $('#editProjectForm').attr('action', updateUrl);
                        } else {
                            alert('Error loading project data');
                        }
                    },
                    error: function() {
                        alert('Failed to load project data');
                    }
                });
            });

            // Handle form submission for update
            $('#editProjectForm').on('submit', function(e) {
                e.preventDefault();
                    // Get the data from CKEditor for the description
                const descriptionData = CKEDITOR.instances['editDescription'].getData();
                const formData = $(this).serialize() + '&description=' + encodeURIComponent(descriptionData);
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.status) {
                            $('#editProjectModal').modal('hide');
                            Swal.fire({
                                title: 'Project Updated!',
                                text: 'Your project was updated successfully.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload(); // Reload the page
                            });
                        } else {
                            alert('Error updating project');
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
    });
</script>
@endsection
