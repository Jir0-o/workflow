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
    <h4 class="py-2 m-4"><span class="text-muted fw-light">All Work Pan</span></h4>
    {{-- @include('sweetalert::alert') --}}
    <div class="row mt-5">
        <div class="col-12 col-md-12 col-lg-12">
            <div class="card p-lg-4 p-2"> 
                {{-- Reason to Extend Task --}}    
                <div class="modal fade" id="reasonTaskModal" tabindex="-1" aria-labelledby="reasonTaskModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content"> 
                            <div class="modal-header">
                                <h5 class="modal-title" id="reasonTaskModalLabel">Extend Work Plan <span class="text-danger">*</span></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="reasonTaskForm" method="POST">
                                    @csrf
                                    @method('PUT')
                                <input type="hidden" id="taskId" name="taskId"> <!-- Hidden input for task ID -->
                                <textarea id="extension_reason"></textarea>
                                <span class="text-danger error-extension_reason"></span>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" id="submitExtensionBtn">Submit</button>
                            </form> 
                            </div>
                        </div>
                    </div>
                </div>

                {{-- feedback model --}}
                <div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form action="{{ route('feedback.submit.work_plan') }}" method="POST">
                        @csrf
                        <input type="hidden" name="task_id" id="feedback-task-id">
                
                        <div class="modal-header">
                            <h5 class="modal-title" id="feedbackModalLabel">Task Feedback</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                
                        <div class="modal-body">
                            <textarea name="feedback" id="feedback-editor"></textarea>
                        </div>
                
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Submit Feedback</button>
                        </div>
                        </form>
                    </div>
                    </div>
                </div>

            <!-- Task Create Modal -->
            <div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createTaskModalLabel">Create Work Plan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="createTaskForm" action="{{ route('work_plan.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="title">Select Title<span class="text-danger">*</span></label>
                                    <select id="title" name="title" class="form-control" required>
                                        @php
                                            $assignedTasks = $titles->filter(function ($title) use ($userId) {
                                                return in_array($userId, explode(',', $title->user_id)) && !empty($title->task_title);
                                            });
                                        @endphp
                                
                                        @if($assignedTasks->isNotEmpty())
                                            <option value="">Select your task</option>
                                            @foreach($assignedTasks as $task)
                                            <option value="{{ $task->id }}" data-project-title="{{ $task->title_name_id }}">
                                                {{ $task->task_title }}
                                            </option>
                                            @endforeach
                                        @else
                                            <option value="" disabled selected>
                                                You have not created any task or no task has been assigned to you.
                                            </option>
                                        @endif
                                    </select>
                                    <span class="text-danger error-title"></span>
                                </div>
                                <div class="mb-3">
                                    <label for="status">Work Status<span class="text-danger">*</span></label>
                                    <select id="status" name="status" class="form-control" required>
                                        <option value="Work From Home">Work From Home</option>
                                        <option value="Work From Office">Work From Office</option>
                                    </select>
                                    <span class="text-danger error-status"></span>
                                </div>
                                <div class="mb-3">
                                    <label for="description">Work Description<span class="text-danger">*</span></label>
                                    <textarea id="description" name="description" class="form-control" rows="4" required placeholder="Task Details"></textarea>
                                    <span class="text-danger error-description"></span>
                                </div>
                                <div class="mb-3">
                                    <label for="last_submit_date">Last Submit Date<span class="text-danger">*</span></label>
                                    <input id="last_submit_date" name="last_submit_date" type="date" required class="form-control" value="{{ date('Y-m-d') }}" placeholder="Date">
                                    <span class="text-danger error-last_submit_date"></span>
                                </div>
                                <div class="mb-3">
                                    <label for="attachment">Attachments</label>
                                    <input id="attachment" name="attachment[]" type="file" class="form-control" multiple>
                                    <span class="text-danger error-attachment"></span>
                                    <div id="file-names" class="mt-2"></div> 
                                </div>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Create Work Plan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Task Modal -->
            <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editTaskModalLabel">Message to Edit</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editTaskForm" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label for="message">Write a message to edit</label>
                                    <textarea id="message" name="message" class="form-control" rows="4" required placeholder="Your message"></textarea>
                                </div>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
                                <button type="submit" class="btn btn-primary" id="updateTaskBtn" data-url="">Send Request</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
                <!-- Nav tabs -->
                <div class="container">
                    <div class="row justify-content-center">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#Pending"
                                    type="button" role="tab" aria-controls="home" aria-selected="true">
                                    Ongoing Plan
                                    <span class="badge bg-primary"> {{ $pendingCount }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#Incomplete"
                                    type="button" role="tab" aria-controls="profile" aria-selected="false">
                                    Overdue Plan
                                    <span class="badge bg-primary"> {{ $incompleteCount }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#Complete"
                                    type="button" role="tab" aria-controls="messages" aria-selected="false">
                                    Completed Plan
                                    <span class="badge bg-primary"> {{ $completeCount }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#Requested"
                                    type="button" role="tab" aria-controls="messages" aria-selected="false">
                                    Requested Plan
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
                                     @can('Create Work Plan')
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <!-- Button trigger modal -->
                                            <button type="button" class="btn btn-primary my-3" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                                                Create Work Plan
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
                                            <th>Task Title</th>
                                            <th>Work Plan</th>
                                            <th>Start Date</th>
                                            <th>Due Date</th>
                                            <th>Submitted Date</th>
                                            <th>Work Status</th>
                                            <th>Extend Reason</th>
                                            <th>Attachments</th>
                                            <th>Status</th>
                                            @can('Work Plan Allow Action')
                                            <th>Actions</th>
                                            @endcan
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($pendingTasks as $key => $pendingTask)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $pendingTask->task->task_title ?? 'No project title selected'  }}</td>
                                            <td>{!!($pendingTask->description) !!}</td>
                                            <td>{{ $pendingTask->created_at->format('d F Y, h:i A') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($pendingTask->submit_date)->format('d F Y') }}</td>
                                            <td>{{ $pendingTask->submit_by_date ? \Carbon\Carbon::parse($pendingTask->submit_by_date)->format('d F Y, h:i A') : 'Still Pending' }}</td>
                                            <td>{{ $pendingTask->work_status }}</td>
                                            <td>
                                                @php
                                                    // Explode the reason_message field
                                                    $reasons = explode('|', $pendingTask->reason_message);
                                                    $reason_count = count($reasons);
                                                @endphp
                                                @if ($pendingTask->reason_message !== null)
                                                <ul>
                                                    @foreach($reasons as $key => $reason)
                                                        <li>{!! $reason !!}</li>
                                                    @endforeach
                                                </ul>
                                                <p>Total: {{ $reason_count }}</p>
                                                @else
                                                <p>No reasons available</p>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($pendingTask->attachment)
                                                    @php
                                                        // Decode JSON-encoded attachments and names
                                                        $attachment = json_decode($pendingTask->attachment, true);
                                                        $attachmentName = json_decode($pendingTask->attachment_name, true);
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
                                                @if ($pendingTask->status == 'pending')
                                                    <span class="badge bg-label-warning me-1">Ongoing</span>
                                                @elseif ($pendingTask->status == 'incomplete')
                                                    <span class="badge bg-label-danger me-1">Overdue</span>
                                                 @else
                                                    {{ $pendingTask->status }}
                                                @endif
                                            </td>
                                            @can('Work Plan Allow Action')
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item suggestEdit" href="javascript:void(0);" data-task-id="{{ $pendingTask->id }}">
                                                            <i class="bx bx-edit-alt me-1"></i> Suggest Edit
                                                        </a>
                                                        <form id="complete-task-form-{{ $pendingTask->id }}" action="{{ route('work_plan.complete', $pendingTask->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="button" class="dropdown-item" onclick="confirmCompleteTask({{ $pendingTask->id }})">
                                                                <i class="bx bx-check me-1"></i> Complete Work Plan
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                            @endcan
                                        </tr>
                                        @endforeach
                                        <script>
                                            function confirmCompleteTask(taskId) {
                                                Swal.fire({
                                                    title: 'Are you sure?',
                                                    text: "Do you want to complete this work plan?",
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#3085d6',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: 'Yes, complete it!'
                                                }).then(function (result) {
                                                    if (result.isConfirmed) {
                                                        Swal.fire({
                                                            title: 'Would you like to leave feedback?',
                                                            icon: 'question',
                                                            showCancelButton: true,
                                                            confirmButtonText: 'Yes',
                                                            cancelButtonText: 'No'
                                                        }).then(function (feedbackResult) {
                                                            if (feedbackResult.isConfirmed) {
                                                                $('#feedback-task-id').val(taskId);
                                                                $('#feedbackModal').modal('show');
                                                            } else {
                                                                $(`#complete-task-form-${taskId}`).submit();
                                                            }
                                                        });
                                                    }
                                                });
                                            }
                                        </script> 
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
                                    @can('Create Work Plan')
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <!-- Button trigger modal -->
                                            <button type="button" class="btn btn-primary my-3" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                                                Create Work Plan
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
                                            <th>Task Title</th>
                                            <th>Work Plan</th>
                                            <th>Start Date</th>
                                            <th>Due Date</th>
                                            <th>Submitted Date</th>
                                            <th>Work Status</th>
                                            <th>Extend Reason</th>
                                            <th>Attachments</th>
                                            <th>Status</th>
                                            @can('Work Plan Allow Action')
                                            <th>Actions</th>
                                            @endcan
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($incompletedTasks as $key => $incompletedtask)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $incompletedtask->task->task_title ?? 'No project title selected' }}</td>
                                            <td>{!! ($incompletedtask->description) !!}</td>
                                            <td>{{ $incompletedtask->created_at->format('d F Y, h:i A') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($incompletedtask->submit_date)->format('d F Y') }}</td>
                                            <td>{{ $incompletedtask->submit_by_date ? \Carbon\Carbon::parse($incompletedtask->submit_by_date)->format('d F Y, h:i A') : 'Task Overdue' }}</td>
                                            <td>{{ $incompletedtask->work_status }}</td>
                                            <td>
                                                @php
                                                    // Explode the reason_message field
                                                    $reasons = explode('|', $incompletedtask->reason_message);
                                                    $reason_count = count($reasons);
                                                @endphp
                                                @if ($incompletedtask->reason_message !== null)
                                                <ul>
                                                    @foreach($reasons as $key => $reason)
                                                        <li>{!! $reason !!}</li>
                                                    @endforeach
                                                </ul>
                                                <p>Total: {{ $reason_count }}</p>
                                                @else
                                                <p>No reasons available</p>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($incompletedtask->attachment)
                                                    @php
                                                        // Decode JSON-encoded attachments and names
                                                        $attachment = json_decode($incompletedtask->attachment, true);
                                                        $attachmentName = json_decode($incompletedtask->attachment_name, true);
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
                                                @if ($incompletedtask->status == 'pending')
                                                    <span class="badge bg-label-warning me-1">Ongoing</span>
                                                @elseif ($incompletedtask->status == 'incomplete')
                                                    <span class="badge bg-label-danger me-1">Overdue</span>
                                                 @else
                                                    {{ $incompletedtask->status }}
                                                @endif
                                            </td>
                                            @can('Work Plan Allow Action')
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        {{-- <a class="dropdown-item" href="{{ route('tasks.edit', ['task' => $incompletedtask->id]) }}">
                                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                                        </a>
                                                        <form action="{{ route('tasks.destroy', $incompletedtask->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="bx bx-trash me-1"></i> Delete
                                                            </button>
                                                        </form> --}}
                                                        <a class="dropdown-item reasonTask" href="javascript:void(0);" data-task-id="{{ $incompletedtask->id }}">
                                                            <i class="bx bx-timer"></i> Extend Work Plan
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                            @endcan
                                        </tr>
                                        @endforeach
                                        <script>
                                            function confirmInCompleteTask(intaskId) {
                                                Swal.fire({
                                                    title: 'Are you sure?',
                                                    text: "Do you want to extend this task? Request will be send to admin.",
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#3085d6',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: 'Yes'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        document.getElementById(`incomplete-task-form-${intaskId}`).submit();
                                                    }
                                                });
                                            }
                                        </script>

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
                                    @can('Create Work Plan')
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <!-- Button trigger modal -->
                                            <button type="button" class="btn btn-primary my-3" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                                                Create Work Plan
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
                                            <th>Task Title</th>
                                            <th>Work Plan</th>
                                            <th>Start Date</th>
                                            <th>Due Date</th>
                                            <th>Submitted Date</th>
                                            <th>Work Status</th>
                                            <th>Feedback</th>
                                            <th>Extend Reason</th>
                                            <th>Attachments</th>
                                            <th>Status</th>
                                            @can('Work Plan Allow Action')
                                            <th>Actions</th>
                                            @endcan
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($completedTasks as $key => $completedtask)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $completedtask->task->task_title  ?? 'No project title selected' }}</td>
                                            <td>{!!($completedtask->description) !!}</td>
                                            <td>{{ $completedtask->created_at->format('d F Y, h:i A') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($completedtask->submit_date)->format('d F Y') }}</td>
                                            <td>{{ $completedtask->submit_by_date ? \Carbon\Carbon::parse($completedtask->submit_by_date)->format('d F Y, h:i A') : 'Task completed' }}</td>
                                            <td>{{ $completedtask->work_status }}</td>
                                            <td>
                                                @if ($completedtask->message != "Time Expired" && $completedtask->message != null)
    
                                                    {!!($completedtask->message) !!}</td>
                                                @else
                                                    No Feedback
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    // Explode the reason_message field
                                                    $reasons = explode('|', $completedtask->reason_message);
                                                    $reason_count = count($reasons);
                                                @endphp
                                                @if ($completedtask->reason_message !== null)
                                                <ul>
                                                    @foreach($reasons as $key => $reason)
                                                        <li>{!! $reason !!}</li>
                                                    @endforeach
                                                </ul>
                                                <p>Total: {{ $reason_count }}</p>
                                                @else
                                                <p>No reasons available</p>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($completedtask->attachment)
                                                    @php
                                                        // Decode JSON-encoded attachments and names
                                                        $attachment = json_decode($completedtask->attachment, true);
                                                        $attachmentName = json_decode($completedtask->attachment_name, true);
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
                                            <td>{{ $completedtask->status }}</td>
                                            @can('Work Plan Allow Action')
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <form id="complete-task-form-{{ $completedtask->id }}" action="{{ route('work_plan.redo', $completedtask->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="button" class="dropdown-item" onclick="confirmCompletedTask({{ $completedtask->id }})">
                                                                <i class="bx bx-redo"></i> Re-Do Work Plan
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                            @endcan
                                        </tr>
                                        @endforeach
                                        <script>
                                            function confirmCompletedTask(completetaskId) {
                                                Swal.fire({
                                                    title: 'Are you sure?',
                                                    text: "Do you want to Re-do this task? Last submit date of this task will be today.",
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#3085d6',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: 'Yes'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        document.getElementById(`complete-task-form-${completetaskId}`).submit();
                                                    }
                                                });
                                            }
                                        </script>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="Requested" role="tabpanel" aria-labelledby="messages-tab">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <h5>Requested Work Plan</h5>
                                    </div>
                                    @can('Create Work Plan')
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <!-- Button trigger modal -->
                                            <button type="button" class="btn btn-primary my-3" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                                                Create Work Plan
                                            </button>
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
                                            <th>Task Title</th>
                                            <th>Work Plan</th>
                                            <th>Start Date</th>
                                            <th>Due Date</th>
                                            <th>Your Message</th>
                                            <th>Work Status</th>
                                            <th>Extend Reason</th>
                                            <th>Attachments</th>
                                            <th>Status</th>
                                            @can('Work Plan Allow Action')
                                            <th>Actions</th>
                                            @endcan
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($requestedTasks as $key => $requestedTask)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $requestedTask->task->task_title  ?? 'No project title selected' }}</td>
                                            <td>{!!($requestedTask->description) !!}</td>
                                            <td>{{ $requestedTask->created_at->format('d F Y, h:i A') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($requestedTask->submit_date)->format('d F Y') }}</td>
                                            <td>{!!($requestedTask->message) !!}</td>
                                            <td>{{ $requestedTask->work_status }}</td>
                                            <td>
                                                @php
                                                    // Explode the reason_message field
                                                    $reasons = explode('|', $requestedTask->reason_message);
                                                    $reason_count = count($reasons);
                                                @endphp
                                                @if ($requestedTask->reason_message !== null)
                                                <ul>
                                                    @foreach($reasons as $key => $reason)
                                                        <li>{!! $reason !!}</li>
                                                    @endforeach
                                                </ul>
                                                <p>Total: {{ $reason_count }}</p>
                                                @else
                                                <p>No reasons available</p>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($requestedTask->attachment)
                                                    @php
                                                        // Decode JSON-encoded attachments and names
                                                        $attachment = json_decode($requestedTask->attachment, true);
                                                        $attachmentName = json_decode($requestedTask->attachment_name, true);
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
                                            <td>{{ $requestedTask->status }}</td>
                                            @can('Work Plan Allow Action')
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <form id="request-task-form-{{ $requestedTask->id }}" action="{{ route('work_plan.cancel', $requestedTask->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="button" class="dropdown-item" onclick="confirmRequestedTask({{ $requestedTask->id }})">
                                                                <i class="bx bx-task-x"></i> Request Cancel
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                            @endcan
                                        </tr>
                                        @endforeach
                                        <script>
                                            function confirmRequestedTask(requestTaskId) {
                                                Swal.fire({
                                                    title: 'Are you sure?',
                                                    text: "Do you want to cancel this request?",
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#3085d6',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: 'Yes'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        document.getElementById(`request-task-form-${requestTaskId}`).submit();
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
    $(document).ready(function () {
        CKEDITOR.replace('message');
        CKEDITOR.replace('description');
        CKEDITOR.replace('extension_reason');
        CKEDITOR.replace('feedback-editor');

        $('#createTaskForm').on('submit', function (e) {
            e.preventDefault(); // Prevent the form from submitting the usual way

            // Create a FormData object
            let formData = new FormData(this);

            // Append CKEditor data to FormData
            const messageData = CKEDITOR.instances['description'].getData();
            formData.append('description', messageData);

            // Get the selected task's `data-project-title` attribute
            let selectedTask = $('#title').find(':selected');
            let projectTitleId = selectedTask.data('project-title'); // Extract the `data-project-title`

            // Append projectTitleId to the FormData
            if (projectTitleId) {
                formData.append('projectTitle', projectTitleId);
            }

            $.ajax({
                url: "{{ route('work_plan.store') }}",
                type: "POST",
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processData: false,
                contentType: false,
                success: function (response) {
                    $('#createTaskModal').modal('hide');
                    Swal.fire({
                        title: 'Task Created!',
                        text: 'Your task was created successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
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

    // Display selected file names dynamically with remove option
    $('#attachment').on('change', function() {
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
            let inputElement = $('#attachment')[0]; 
            let dataTransfer = new DataTransfer(); 

            let files = inputElement.files;
            for (let i = 0; i < files.length; i++) {
                if (i !== fileIndex) {
                    dataTransfer.items.add(files[i]);
                }
            }
            // Update the input element's file list
            inputElement.files = dataTransfer.files;

            $('#attachment').trigger('change');
        });



        // Handle form submission via AJAX
        $('#editTaskForm').on('submit', function(event) {
            event.preventDefault();
            const updateUrl = $('#updateTaskBtn').data('url');
            const messageData = {
                _token: $('input[name="_token"]').val(),
                message: CKEDITOR.instances['message'].getData(),
            };

            $.ajax({
                url: updateUrl,
                type: 'PUT',
                data: messageData,
                success: function(response) {
                    $('#editTaskModal').modal('hide');
                    if (response.status) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Edit message sent to admin successfully.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload(); // Reload the page after confirmation
                        });
                    } else {
                        $('#editTaskModal').modal('hide');
                        Swal.fire({
                            title: 'Update Failed',
                            text: response.message,
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


    // Handle form submission via AJAX
    $('#submitExtensionBtn').on('click', function() {
        const taskId = $('#taskId').val();
        const updateUrl = `{{ route('work_plan.extend', ':id') }}`.replace(':id', taskId); // Set the correct URL
        const formData = {
            _token: $('input[name="_token"]').val(),
            extension_reason: CKEDITOR.instances['extension_reason'].getData(), // Update with CKEditor data
            _method: 'PUT',
        };

        $.ajax({
            url: updateUrl,
            type: 'POST',
            data: formData,
            success: function(response) {
                console.log(response.status);
                $('#reasonTaskModal').modal('hide');
                if (response.status) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Extend request sent to admin successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload(); // Reload the page after confirmation
                    });
                } else {
                    Swal.fire({
                        title: 'Update Failed',
                        text: response.message,
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

    });

    $('.suggestEdit').on('click', function() {
            const taskId = $(this).data('task-id');
            // Set the update URL with the task ID
            $('#updateTaskBtn').data('url', `{{ route('work_plan.update', '') }}/${taskId}`); // Set the update URL
            $('#message').val(''); // Clear the textarea for a new message
            $('#editTaskModal').modal('show'); // Show the modal
        });

        // Show the modal and set task ID in hidden field and URL for submission
        $('.reasonTask').on('click', function() {
            const taskId = $(this).data('task-id');
            $('#taskId').val(taskId); // Set taskId in hidden input
            $('#reasonTaskModal').modal('show'); // Show the modal
            $('#extension_reason').val(''); // Clear the textarea for a new message
        });

        $('#feedbackModal').on('shown.bs.modal', function () {
            if (!$('#feedback-editor').data('ckeditorInstance')) {
                ClassicEditor
                    .create(document.querySelector('#feedback-editor'))
                    .then(editor => {
                        $('#feedback-editor').data('ckeditorInstance', editor);
                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
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
