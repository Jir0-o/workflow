@extends('layouts.master')
@section('content')

<h4 class="py-2 m-4"><span class="text-muted fw-light">Applications</span></h4>
<div class="row mt-5">
    <!--application Modal Structure -->
    <div class="modal fade" id="createApplication" tabindex="-1" aria-labelledby="createApplicationLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createApplicationLabel">Create New Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="applicationForm"> 
                        @csrf
                        
                        <p>
                            Name: <b id="auth_user_name">{{ Auth::user()->name }}</b>
                        </p>
                        
                        <p>
                            Job Role: 
                            <select class="form-select d-inline" style="width: auto;" name="role" id="role" required>
                                <option value="Junior Software Developer">Junior Software Developer</option>
                                <option value="Senior Software Develope">Senior Software Developer</option>
                            </select>
                        </p>
                        
                        <p>
                            UNICORN SOFTWARE & SOLUTIONS LTD
                        </p>
                        
                        <p>
                            Date: <b id="currentDate">{{ \Carbon\Carbon::today()->format('d F Y') }}</b>
                        </p>
                        
                        <p>
                            <strong>Subject:</strong> Application for 
                            <select class="form-select d-inline" style="width: auto;" name="leave_type" id="leave_type" required>
                                <option value="Paid" selected>Paid</option>
                                <option value="Unpaid">Unpaid</option>
                            </select>
                            Leave
                        </p>
                        
                        <p>
                            Dear [Manager's Name/HR Manager],
                        </p>
                        
                        <p>
                            I hope this message finds you well. I am writing to formally request 
                            <input type="number" class="form-control d-inline" style="width: 80px;" name="days_requested" id="days_requested" placeholder="No. of days" required>
                            days of 
                            <span id="leaveTypeDisplay" class="fw-bold">Paid</span> leave from 
                            <input type="date" class="form-control d-inline" style="width: auto;" name="leave_start_date" id="leave_start_date" value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required> 
                            to 
                            <input type="date" class="form-control d-inline" style="width: auto;" name="leave_end_date" id="leave_end_date" value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required>, 
                            due to 
                            <textarea class="form-control d-inline" style="width: 60%;" name="reason" id="reason" rows="1" placeholder="Enter your reason" required></textarea>.
                        </p>
                        
                        <p>
                            I kindly request your approval for this leave. Please let me know if you require any additional information or documentation to process this request.
                        </p>
                        
                        <p>
                            Thank you for your understanding and support.
                        </p>
                        
                        <p>
                            Sincerely,<br>
                            <b id="auth_user_name">{{ Auth::user()->name }}</b><br>
                            <b id="auth_user_email">{{ Auth::user()->email }}</b>
                        </p>
                        
                        <div class="d-flex justify-content-end">
                            <button type="button" id="submitApplicationBtn" class="btn btn-primary">Submit Application</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- View Application Modal -->
    <div class="modal fade" id="viewApplication" tabindex="-1" aria-labelledby="viewApplicationLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewApplicationLabel">View Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="applicationId">
                    <input type="hidden" id="applicationStatus">
                    <p>
                        <span id="viewAuthUserName"></span>
                    </p>
                    
                    <p>
                        <span id="viewRole"></span>
                    </p>
                    
                    <p>
                        UNICORN SOFTWARE & SOLUTIONS LTD
                    </p>
                    
                    <p>
                        Date: <span class="fw-bold" id="viewDate"></span>
                    </p>
                    
                    <p>
                        <strong>Subject:</strong> Application for 
                        <span id="viewLeaveType"></span>
                        Leave
                    </p>
                    
                    <p>
                        Dear [Manager's Name/HR Manager],
                    </p>
                    
                    <p>
                        I hope this message finds you well. I am writing to formally request 
                        <span id="viewDaysRequested"></span>
                        days of 
                        <span id="view_leave_type_display"></span></span> leave from 
                        <span id="viewLeaveStartDate"></span>
                        to 
                        <span id="viewLeaveEndDate"></span>,
                        due to 
                        <span id="viewReason"></span>
                    </p>
                    
                    <p>
                        I kindly request your approval for this leave. Please let me know if you require any additional information or documentation to process this request.
                    </p>
                    
                    <p>
                        Thank you for your understanding and support.
                    </p>
                    
                    <p>
                        Sincerely,<br>
                        <b id="view_auth_user_name">{{ Auth::user()->name }}</b><br>
                        <b id="view_auth_user_email">{{ Auth::user()->email }}</b>
                    </p>
                </div>
                <div class="modal-footer">
                    @can('Accept/Reject Application')
                    <button type="button" class="btn btn-success" id="acceptApplicationBtn" data-id="">Accept</button>
                    <button type="button" class="btn btn-danger" id="rejectApplicationBtn" data-id="">Reject</button>
                    @endcan
                    @can('Send Application')
                    <button type="button" class="btn btn-success" id="sendApplicationBtn" data-id="">Send</button>
                    @endcan
                    @can('Cancel Application')
                    <button type="button" class="btn btn-secondary" id="cancelApplicationBtn" data-id="">Cancel</button>
                    @endcan
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Application Modal Structure -->
    <div class="modal fade" id="editApplication" tabindex="-1" aria-labelledby="editApplicationLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editApplicationLabel">Edit Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="applicationEditId" name="id">
                    <form id="editApplicationForm">
                        @csrf
                        
                        <p>
                            Name: <b id="auth_user_name1"></b>
                        </p>
                        
                        <p>
                            Job Role: 
                            <select class="form-select d-inline" style="width: auto;" name="edit_role" id="edit_role" required>
                                <option value="Junior Software Developer">Junior Software Developer</option>
                                <option value="Senior Software Developer">Senior Software Developer</option>
                            </select>
                        </p>
                        
                        <p>
                            UNICORN SOFTWARE & SOLUTIONS LTD
                        </p>
                        
                        <p>
                            Date: <b id="editDate"></b>
                        </p>
                        
                        <p>
                            <strong>Subject:</strong> Application for 
                            <select class="form-select d-inline" style="width: auto;" name="edit_leave_type" id="edit_leave_type" required>
                                <option value="Paid">Paid</option>
                                <option value="Unpaid">Unpaid</option>
                            </select>
                            Leave
                        </p>
                        
                        <p>
                            Dear [Manager's Name/HR Manager],
                        </p>
                        
                        <p>
                            I hope this message finds you well. I am writing to formally request 
                            <input type="number" class="form-control d-inline" style="width: 80px;" name="edit_days_requested" id="edit_days_requested" placeholder="No. of days" required>
                            days of 
                            <span id="editLeaveTypeDisplay" class="fw-bold">Paid</span> leave from 
                            <input type="date" class="form-control d-inline" style="width: auto;" name="edit_leave_start_date" id="edit_leave_start_date" required> 
                            to 
                            <input type="date" class="form-control d-inline" style="width: auto;" name="edit_leave_end_date" id="edit_leave_end_date" required>, 
                            due to 
                            <textarea class="form-control d-inline" style="width: 60%;" name="edit_reason" id="edit_reason" rows="1" placeholder="Enter your reason" required></textarea>.
                        </p>
                        
                        <p>
                            I kindly request your approval for this leave. Please let me know if you require any additional information or documentation to process this request.
                        </p>
                        
                        <p>
                            Thank you for your understanding and support.
                        </p>
                        
                        <p>
                            Sincerely,<br>
                            <b id="auth_user_name2"></b><br>
                            <b id="auth_email"></b>
                        </p>
                        
                        <div class="d-flex justify-content-end">
                            <button type="button" id="updateApplicationBtn" class="btn btn-primary">Update Application</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Return Application Modal Structure -->
    <div class="modal fade" id="returnApplication" tabindex="-1" aria-labelledby="returnApplicationLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="returnApplicationLabel">Return Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="returnApplicationId">
                    <form id="returnApplicationForm">
                        @csrf
                        <div class="mb-3">
                            <label for="returnReason" class="form-label">Reason for Return:</label>
                            <textarea id="returnReason" class="form-control" name="reason" rows="6" required></textarea>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" id="submitReturnBtn" class="btn btn-primary">Submit Return</button>
                        </div>
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
                    <ul class="nav nav-tabs" id="applicationTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#Pending"
                                type="button" role="tab" aria-controls="Pending" aria-selected="true">
                                Pending
                                <span class="badge bg-primary">@if(auth()->user()->hasRole('Super Admin')){{$pendingApplicationsCount}} @else {{$pendingAuthCount}} @endif</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#Approved"
                                type="button" role="tab" aria-controls="Approved" aria-selected="false">
                                Approved
                                <span class="badge bg-success">@if(auth()->user()->hasRole('Super Admin')){{$approvedApplicationsCount}} @else {{$approvedAuthCount}} @endif</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#Rejected"
                                type="button" role="tab" aria-controls="Rejected" aria-selected="false">
                                Rejected
                                <span class="badge bg-danger">@if(auth()->user()->hasRole('Super Admin')){{$rejectedApplicationsCount}} @else {{$rejectedAuthCount}} @endif</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="return-tab" data-bs-toggle="tab" data-bs-target="#ReturnApplications"
                                type="button" role="tab" aria-controls="ReturnApplications" aria-selected="false">
                                Return Applications
                                <span class="badge bg-info">@if(auth()->user()->hasRole('Super Admin')){{$returnApplicationsCount}} @else {{$returnAuthCount}} @endif</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="all-tab" data-bs-toggle="tab" data-bs-target="#AllApplications"
                                type="button" role="tab" aria-controls="AllApplications" aria-selected="false">
                                All Applications
                                <span class="badge bg-secondary">@if(auth()->user()->hasRole('Super Admin')){{$applicationCount}} @else {{$authApplicationCount}} @endif</span>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Tab panes -->
            <div class="tab-content">
                <!-- Pending Applications Tab -->
                <div class="tab-pane active" id="Pending" role="tabpanel" aria-labelledby="pending-tab">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h5>Pending Applications</h5>
                                </div>
                                @can('Create Application')
                                <div class="col-12 col-md-6">
                                    <div class="float-end">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createApplication"> 
                                            <i class="bx bx-edit-alt me-1"></i> Create Application
                                        </button>
                                    </div>
                                </div>
                                @endcan
                            </div>
                        </div>
                        <div class="table-responsive text-nowrap p-3">
                            <table id="datatable" class="table">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>Subject</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (auth()-> user()->hasRole('Super Admin'))
                                    @foreach ($pendingApplications as $application)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $application->name }}</td>
                                        <td>{{ $application->date ? \Carbon\Carbon::parse($application->date)->format('d F Y') : $application->date }}</td>
                                        <td>Application for {{ $application->leave_type }}</td>
                                        <td>{{ $application->reason }}</td>
                                        <td>
                                            @if ($application->status == 0)
                                                <span class="badge bg-primary">Pending</span>
                                            @endif    
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    @can('Show Application')
                                                    <a class="dropdown-item show-button" href="javascript:void(0);" data-id="{{ $application->id }}" data-status="{{ $application->status }}">
                                                        <i class="bx bx-show-alt me-1"></i> Show
                                                    </a>
                                                    @endcan
                                                    @can('Return Application')
                                                    <a class="dropdown-item return-button" href="javascript:void(0);" data-id="{{ $application->id }}">
                                                        <i class="bx bx-undo me-1"></i> Return
                                                    </a>
                                                    @endcan
                                                    @can('Edit Application')
                                                    @if (Auth::user()->hasRole('Super Admin') || $application->status == 2)
                                                    <a class="dropdown-item edit-button" href="javascript:void(0);" data-id="{{ $application->id }}">
                                                        <i class="bx bx-edit-alt me-1"></i> Edit
                                                    </a>
                                                    @endif
                                                    @endcan
                                                    @can('Delete Application')
                                                    <form id="Delete-task-form-{{ $application->id }}" action="{{ route('application.destroy', ['application' => $application->id]) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="dropdown-item" onclick="confirmDeleteTask({{ $application->id }})">
                                                            <i class="bx bx-trash me-1"></i> Delete
                                                        </button>
                                                    </form>
                                                    @endcan
                                                </div>
                                            </div>                                                           
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    @foreach ($pendingAuth as $application)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $application->name }}</td>
                                        <td>{{ $application->date ? \Carbon\Carbon::parse($application->date)->format('d F Y') : $application->date }}</td>
                                        <td>Application for {{ $application->leave_type }}</td>
                                        <td>{{ $application->reason }}</td>
                                        <td>
                                            @if ($application->status == 0)
                                                <span class="badge bg-primary">Pending</span>
                                            @endif    
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    @can('Show Application')
                                                    <a class="dropdown-item show-button" href="javascript:void(0);" data-id="{{ $application->id }}" data-status="{{ $application->status }}">
                                                        <i class="bx bx-show-alt me-1"></i> Show
                                                    </a>
                                                    @endcan
                                                    @can('Cancel Application')
                                                    <form id="Delete-cancel-form-{{ $application->id }}" 
                                                        action="{{ route('application.cancel', ['id' => $application->id]) }}" 
                                                        method="POST">
                                                      @csrf
                                                      @method('DELETE')
                                                  
                                                      <button type="button" class="dropdown-item" onclick="confirmCancelTask({{ $application->id }})">
                                                          <i class="bx bx-undo me-1"></i> Cancel
                                                      </button>
                                                  </form>
                                                  @endcan
                                                </div>
                                            </div>                                                           
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                    <!-- Add more dummy rows as needed -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Approved Applications Tab -->
                <div class="tab-pane" id="Approved" role="tabpanel" aria-labelledby="approved-tab">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h5>Approved Applications</h5>
                                </div>
                                @can('Create Application')
                                <div class="col-12 col-md-6">
                                    <div class="float-end">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createApplication"> 
                                            <i class="bx bx-edit-alt me-1"></i> Create Application
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
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>Subject</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (auth()-> user()->hasRole('Super Admin'))
                                    @foreach ($approvedApplications as $application)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $application->name }}</td>
                                        <td>{{ $application->date ? \Carbon\Carbon::parse($application->date)->format('d F Y') : $application->date }}</td>
                                        <td>Application for {{ $application->leave_type }}</td>
                                        <td>{{ $application->reason }}</td>
                                        <td>
                                            @if ($application->status == 1)
                                                <span class="badge bg-success">Approved</span>
                                            @endif    
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    @can('Show Application')
                                                    <a class="dropdown-item show-button" href="javascript:void(0);" data-id="{{ $application->id }}" data-status="{{ $application->status }}">
                                                        <i class="bx bx-show-alt me-1"></i> Show
                                                    </a>
                                                    @endcan
                                                    @can('Edit Application')
                                                    @if (Auth::user()->hasRole('Super Admin') || $application->status == 2)
                                                    <a class="dropdown-item edit-button" href="javascript:void(0);" data-id="{{ $application->id }}">
                                                        <i class="bx bx-edit-alt me-1"></i> Edit
                                                    </a>
                                                    @endif
                                                    @endcan
                                                    @can('Delete Application')
                                                    <form id="Delete-task-form-{{ $application->id }}" action="{{ route('application.destroy', ['application' => $application->id]) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="dropdown-item" onclick="confirmDeleteTask({{ $application->id }})">
                                                            <i class="bx bx-trash me-1"></i> Delete
                                                        </button>
                                                    </form>
                                                    @endcan
                                                </div>
                                            </div>                                                           
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    @foreach ($approvedAuth as $application)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $application->name }}</td>
                                        <td>{{ $application->date ? \Carbon\Carbon::parse($application->date)->format('d F Y') : $application->date }}</td>
                                        <td>Application for {{ $application->leave_type }}</td>
                                        <td>{{ $application->reason }}</td>
                                        <td>
                                            @if ($application->status == 1)
                                                <span class="badge bg-success">Approved</span>
                                            @endif    
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    @can('Show Application')
                                                    <a class="dropdown-item show-button" href="javascript:void(0);" data-id="{{ $application->id }}" data-status="{{ $application->status }}">
                                                        <i class="bx bx-show-alt me-1"></i> Show
                                                    </a>
                                                    @endcan
                                                </div>
                                            </div>                                                           
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                    <!-- Add more dummy rows as needed -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Rejected Applications Tab -->
                <div class="tab-pane" id="Rejected" role="tabpanel" aria-labelledby="rejected-tab">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h5>Rejected Applications</h5>
                                </div>
                                @can('Create Application')
                                <div class="col-12 col-md-6">
                                    <div class="float-end">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createApplication"> 
                                            <i class="bx bx-edit-alt me-1"></i> Create Application
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
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>Subject</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (auth()-> user()->hasRole('Super Admin'))
                                    @foreach ($rejectedApplications as $application)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $application->name }}</td>
                                        <td>{{ $application->date ? \Carbon\Carbon::parse($application->date)->format('d F Y') : $application->date }}</td>
                                        <td>Application for {{ $application->leave_type }}</td>
                                        <td>{{ $application->reason }}</td>
                                        <td>
                                            @if ($application->status == 2)
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif    
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    @can('Show Application')
                                                    <a class="dropdown-item show-button" href="javascript:void(0);" data-id="{{ $application->id }}" data-status="{{ $application->status }}">
                                                        <i class="bx bx-show-alt me-1"></i> Show
                                                    </a>
                                                    @endcan
                                                    @can('Edit Application')
                                                    @if (Auth::user()->hasRole('Super Admin') || $application->status == 2)
                                                    <a class="dropdown-item edit-button" href="javascript:void(0);" data-id="{{ $application->id }}">
                                                        <i class="bx bx-edit-alt me-1"></i> Edit
                                                    </a>
                                                    @endif
                                                    @endcan
                                                    @can('Delete Application')
                                                    <form id="Delete-task-form-{{ $application->id }}" action="{{ route('application.destroy', ['application' => $application->id]) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="dropdown-item" onclick="confirmDeleteTask({{ $application->id }})">
                                                            <i class="bx bx-trash me-1"></i> Delete
                                                        </button>
                                                    </form>
                                                    @endcan
                                                </div>
                                            </div>                                                           
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    @foreach ($rejectedAuth as $application)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $application->name }}</td>
                                        <td>{{ $application->date ? \Carbon\Carbon::parse($application->date)->format('d F Y') : $application->date }}</td>
                                        <td>Application for {{ $application->leave_type }}</td>
                                        <td>{{ $application->reason }}</td>
                                        <td>
                                            @if ($application->status == 2)
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif    
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    @can('Show Application')
                                                    <a class="dropdown-item show-button" href="javascript:void(0);" data-id="{{ $application->id }}" data-status="{{ $application->status }}">
                                                        <i class="bx bx-show-alt me-1"></i> Show
                                                    </a>
                                                    @endcan
                                                    @can('Edit User Application')
                                                    @if (Auth::user()->hasRole('Super Admin') || $application->status == 2)
                                                    <a class="dropdown-item edit-button" href="javascript:void(0);" data-id="{{ $application->id }}">
                                                        <i class="bx bx-edit-alt me-1"></i> Edit
                                                    </a>
                                                    @endif
                                                    @endcan
                                                </div>
                                            </div>                                                           
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                    <!-- Add more dummy rows as needed -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- return Applications Tab -->
                <div class="tab-pane" id="ReturnApplications" role="tabpanel" aria-labelledby="return-tab">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h5>Return Applications</h5>
                                </div>
                                @can('Create Application')
                                <div class="col-12 col-md-6">
                                    <div class="float-end">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createApplication">
                                            <i class="bx bx-edit-alt me-1"></i> Create Application
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
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>Subject</th>
                                        <th>Reason</th>
                                        <th>Return Reason</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (auth()-> user()->hasRole('Super Admin'))
                                    @foreach ($returnApplications as $application)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $application->name }}</td>
                                        <td>{{ $application->date ? \Carbon\Carbon::parse($application->date)->format('d F Y') : $application->date }}</td>
                                        <td>Application for {{ $application->leave_type }}</td>
                                        <td>{{ $application->reason }}</td>
                                        <td>{!! $application->return_reason !!}</td>
                                        <td>
                                            @if ($application->status == 4)
                                                <span class="badge bg-danger">Returned</span>
                                            @endif    
                                        </td>
                                        <td>    
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    @can('Show Application')
                                                    <a class="dropdown-item show-button" href="javascript:void(0);" data-id="{{ $application->id }}" data-status="{{ $application->status }}">
                                                        <i class="bx bx-show-alt me-1"></i> Show
                                                    </a>
                                                    @endcan
                                                    @can('Edit Application')
                                                    @if (Auth::user()->hasRole('Super Admin') || $application->status == 2)
                                                    <a class="dropdown-item edit-button" href="javascript:void(0);" data-id="{{ $application->id }}">
                                                        <i class="bx bx-edit-alt me-1"></i> Edit
                                                    </a>
                                                    @endif
                                                    @endcan
                                                    @can('Delete Application')
                                                    <form id="Delete-task-form-{{ $application->id }}" action="{{ route('application.destroy', ['application' => $application->id]) }}" method="POST">
                                                        @csrf                                                       
                                                        @method('DELETE')
                                                        <button type="button" class="dropdown-item" onclick="confirmDeleteTask({{ $application->id }})">
                                                            <i class="bx bx-trash me-1"></i> Delete
                                                        </button>
                                                    </form>
                                                    @endcan
                                                </div>
                                            </div>                                                           
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    @foreach ($returnAuth as $application)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $application->name }}</td>
                                        <td>{{ $application->date ? \Carbon\Carbon::parse($application->date)->format('d F Y') : $application->date }}</td>
                                        <td>Application for {{ $application->leave_type }}</td>
                                        <td>{{ $application->reason }}</td>
                                        <td>{!! $application->return_reason !!}</td>
                                        <td>
                                            @if ($application->status == 4)
                                                <span class="badge bg-warning">Returned</span>
                                            @endif    
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    @can('Show Application')
                                                    <a class="dropdown-item show-button" href="javascript:void(0);" data-id="{{ $application->id }}" data-status="{{ $application->status }}">
                                                        <i class="bx bx-show-alt me-1"></i> Show
                                                    </a>
                                                    @endcan
                                                    @can('Edit User Application')
                                                    @if ( $application->status == 4)
                                                    <a class="dropdown-item edit-button" href="javascript:void(0);" data-id="{{ $application->id }}">
                                                        <i class="bx bx-edit-alt me-1"></i> Edit
                                                    </a>
                                                    @endif
                                                    @endcan
                                                </div>
                                            </div>                                                           
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- All Applications Tab -->
                <div class="tab-pane" id="AllApplications" role="tabpanel" aria-labelledby="all-tab">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h5>All Applications</h5>
                                </div>
                                @can('Create Application')
                                <div class="col-12 col-md-6">
                                    <div class="float-end">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createApplication"> 
                                            <i class="bx bx-edit-alt me-1"></i> Create Application
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
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>Subject</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (Auth::user()->hasRole('Super Admin'))
                                    @foreach ($applications as $application)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $application->name }}</td>
                                        <td>{{ $application->date ? \Carbon\Carbon::parse($application->date)->format('d F Y') : $application->date }}</td>
                                        <td>Application for {{ $application->leave_type }}</td>
                                        <td>{{ $application->reason }}</td>
                                        <td>
                                            @if ($application->status == 0)
                                                <span class="badge bg-primary">Pending</span>
                                            @elseif ($application->status == 1)
                                                <span class="badge bg-success">Approved</span>
                                            @elseif ($application->status == 2)
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif    
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    @can('Show Application')
                                                    <a class="dropdown-item show-button" href="javascript:void(0);" data-id="{{ $application->id }}" data-status="{{ $application->status }}">
                                                        <i class="bx bx-show-alt me-1"></i> Show
                                                    </a>
                                                    @endcan
                                                    @can('Edit Application')
                                                    @if (Auth::user()->hasRole('Super Admin') || $application->status == 2)
                                                    <a class="dropdown-item edit-button" href="javascript:void(0);" data-id="{{ $application->id }}">
                                                        <i class="bx bx-edit-alt me-1"></i> Edit
                                                    </a>
                                                    @endif
                                                    @endcan
                                                    @can('Delete Application')
                                                    <form id="Delete-task-form-{{ $application->id }}" action="{{ route('application.destroy', ['application' => $application->id]) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="dropdown-item" onclick="confirmDeleteTask({{ $application->id }})">
                                                            <i class="bx bx-trash me-1"></i> Delete
                                                        </button>
                                                    </form>
                                                    @endcan
                                                </div>
                                            </div>                                                           
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    @foreach ($authApplications as $application)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $application->name }}</td>
                                        <td>{{ $application->date ? \Carbon\Carbon::parse($application->date)->format('d F Y') : $application->date }}</td>
                                        <td>Application for {{ $application->leave_type }}</td>
                                        <td>{{ $application->reason }}</td>
                                        <td>
                                            @if ($application->status == 0)
                                                <span class="badge bg-primary">Pending</span>
                                            @elseif ($application->status == 1)
                                                <span class="badge bg-success">Approved</span>
                                            @elseif ($application->status == 2)
                                                <span class="badge bg-danger">Rejected</span>
                                            @elseif ($application->status == 4)
                                                <span class="badge bg-warning">Returned</span>
                                            @endif    
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    @can('Show Application')
                                                    <a class="dropdown-item show-button" href="javascript:void(0);" data-id="{{ $application->id }}" data-status="{{ $application->status }}">
                                                        <i class="bx bx-show-alt me-1"></i> Show
                                                    </a>
                                                    @endcan
                                                    @can('Edit User Application')
                                                    @if ($application->status == 4 || $application->status == 2)
                                                    <a class="dropdown-item edit-button" href="javascript:void(0);" data-id="{{ $application->id }}">
                                                        <i class="bx bx-edit-alt me-1"></i> Edit
                                                    </a>
                                                    @endif
                                                    @endcan
                                                </div>
                                            </div>                                                           
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
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
    // Automatically update leave type based on the subject dropdown
    document.querySelector('[name="leave_type"]').addEventListener('change', function () {
        document.getElementById('leaveTypeDisplay').textContent = this.value;
    });

    function formatDate(dateString) {
    const options = { day: '2-digit', month: 'long', year: 'numeric' };
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', options); 
    }

    function statusModal(applicationId, status) {
    // Set the application ID and status in the modal
    $('#applicationId').val(applicationId);
    $('#applicationStatus').val(status);

    // Toggle button visibility based on status
    if (status == 1) {
        $('#acceptApplicationBtn').hide();
        $('#rejectApplicationBtn').hide();
        $('#cancelApplicationBtn').hide();
        $('#sendApplicationBtn').hide();
    }else if (status == 4 || status == 2) {
        $('#acceptApplicationBtn').hide();
        $('#rejectApplicationBtn').hide();
        $('#cancelApplicationBtn').hide();
        $('#sendApplicationBtn').show();
    } else if (status == 0) {
        $('#cancelApplicationBtn').show();
        $('#acceptApplicationBtn').show();
        $('#rejectApplicationBtn').show();
        $('#sendApplicationBtn').hide();
    } else {
        $('#acceptApplicationBtn').hide();
        $('#rejectApplicationBtn').hide();
        $('#cancelApplicationBtn').hide();
        $('#sendApplicationBtn').show();
    }
}
    function confirmCancelTask(applicationId) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to cancel this application? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Cancel it!',
            cancelButtonText: 'No, Keep it'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the form
                document.getElementById(`Delete-cancel-form-${applicationId}`).submit();
            }
        });
    }
    function confirmDeleteTask(DeleteTaskId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to cancel this application? This application will be delete permanently.",
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
    // Create Application
    $(document).ready(function () {
        CKEDITOR.replace('returnReason');

        $('#submitApplicationBtn').on('click', function (e) {
        e.preventDefault();

        let role = $('#role').val();
        let leaveType = $('#leave_type').val();
        let daysRequested = $('#days_requested').val();
        let leaveStartDate = $('#leave_start_date').val();
        let leaveEndDate = $('#leave_end_date').val();
        let reason = $('#reason').val();

        $.ajax({
            url: "{{ route('application.store') }}",  // Make sure this route is correct
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                role: role,
                leave_type: leaveType,
                days_requested: daysRequested,
                leave_start_date: leaveStartDate,
                leave_end_date: leaveEndDate,
                reason: reason,
            },
            success: function (response) {
                $('#createApplication').modal('hide');

                Swal.fire({
                    title: 'Application Submitted!',
                    text: 'Your application has been submitted successfully. Please wait for confirmation.',
                    icon: 'success',
                    confirmButtonText: 'OK',
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            },
            error: function (xhr) {
                $('#createApplication').modal('hide');
                console.error('Error:', xhr.responseText);
                Swal.fire({
                    title: 'Error!',
                    text: 'There was an error submitting your application.',
                    icon: 'error',
                    confirmButtonText: 'Try Again',
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            },
        });
    });

    $('.show-button').on('click', function () {
        let applicationId = $(this).data('id');
        const status = $(this).data('status'); 
        statusModal(applicationId, status);
        // Set the application ID in the hidden input
        $('#applicationId').val(applicationId);


        // Fetch application details via AJAX
        $.ajax({
            url: "{{ route('application.show', ':id') }}".replace(':id', applicationId),
            type: 'GET',
            success: function (response) {
                $('#applicationId').val(response.id);
                $('#viewAuthUserName').text(response.name);
                $('#viewRole').text(response.role);
                $('#viewDate').text(formatDate(response.date)); 
                $('#viewLeaveType').text(response.leave_type);
                $('#view_leave_type_display').text(response.leave_type);
                $('#viewDaysRequested').text(response.days_number);
                $('#viewLeaveStartDate').text(formatDate(response.from_date)); 
                $('#viewLeaveEndDate').text(formatDate(response.end_date)); 
                $('#viewReason').text(response.reason);

                // Update button data-id
                $('#acceptApplicationBtn').data('id', applicationId);
                $('#rejectApplicationBtn').data('id', applicationId);
                $('#cancelApplicationBtn').data('id', applicationId);
                $('#sendApplicationBtn').data('id', applicationId);

                // Show the modal
                $('#viewApplication').modal('show');
            },
            error: function (xhr) {
                console.error('Error fetching application details:', xhr.responseText);
                Swal.fire({
                    title: 'Error!',
                    text: 'Unable to fetch application details.',
                    icon: 'error',
                });
            },
        });
    });

        // Handle Accept button click
        $('#acceptApplicationBtn').on('click', function () {
        let applicationId = $(this).data('id');

        // Perform AJAX request
        $.ajax({
            url: "{{ route('application.accept', ':id') }}".replace(':id', applicationId),
            type: 'POST',
            data: { application_id: applicationId }, 
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (response) {
                $('#viewApplication').modal('hide');
                Swal.fire({
                    title: 'Success!',
                    text: 'Application has been accepted.',
                    icon: 'success',
                });
                // Optionally reload the table or update the UI
                location.reload();
            },
            error: function (xhr) {
                console.error('Error accepting application:', xhr.responseText);
                Swal.fire({
                    title: 'Error!',
                    text: 'Unable to accept the application.',
                    icon: 'error',
                });
            },
        });
    });

    // Handle Reject button click
    $('#rejectApplicationBtn').on('click', function () {
        let applicationId = $(this).data('id');

        // Perform AJAX request
        $.ajax({
            url: "{{ route('application.reject', ':id') }}".replace(':id', applicationId),
            type: 'POST',
            data: { application_id: applicationId }, 
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (response) {
                $('#viewApplication').modal('hide');
                Swal.fire({
                    title: 'Success!',
                    text: 'Application has been rejected.',
                    icon: 'success',
                });
                // Optionally reload the table or update the UI
                location.reload();
            },
            error: function (xhr) {
                console.error('Error rejecting application:', xhr.responseText);
                Swal.fire({
                    title: 'Error!',
                    text: 'Unable to reject the application.',
                    icon: 'error',
                });
            },
        })
    });

    $('#cancelApplicationBtn').on('click', function () {
        $('#viewApplication').modal('hide');
        let applicationId = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to cancel this application? This application will be deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Cancel it!',
            cancelButtonText: 'No, Keep it'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('application.cancel', ':id') }}".replace(':id', applicationId),
                    type: 'DELETE',
                    data: { id: applicationId },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function (response) {
                        $('#viewApplication').modal('hide');
                        Swal.fire({
                            title: 'Cancelled!',
                            text: 'The application has been successfully cancelled.',
                            icon: 'success',
                        });
                        location.reload();
                    },
                    error: function (xhr) {
                        console.error('Error cancelling application:', xhr.responseText);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Unable to cancel the application. Please try again.',
                            icon: 'error',
                        });
                    },
                });
            }
        });
    });

    $('#sendApplicationBtn').on('click', function () {
        $('#viewApplication').modal('hide');
        let applicationId = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to send this application? This application will be pending.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Send it!',
            cancelButtonText: 'No' 
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('application.send', ':id') }}".replace(':id', applicationId),
                    type: 'POST',
                    data: { id: applicationId },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function (response) {
                        $('#viewApplication').modal('hide');
                        Swal.fire({
                            title: 'Sent!',
                            text: 'The application has been successfully sent.',
                            icon: 'success',
                        });
                        location.reload();
                    },
                    error: function (xhr) {
                        console.error('Error sending application:', xhr.responseText);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Unable to send the application. Please try again.',
                            icon: 'error',
                        });
                    },
                });
            }
        });
    });

    // Update leave type display when dropdown changes
    $('#edit_leave_type').on('change', function () {
        $('#editLeaveTypeDisplay').text($(this).val());
    });

        $('.edit-button').on('click', function () {
        const applicationEditId = $(this).data('id');

        // Fetch application data via AJAX
        $.ajax({
            url: "{{ route('application.edit', ':id') }}".replace(':id', applicationEditId),
            method: 'GET',
            success: function (response) {
                if (response.status) {
                    const application = response.data;
                    // Populate modal fields with the fetched data
                    $('#applicationEditId').val(application.id);
                    $('#auth_user_name1').html(application.name);
                    $('#auth_user_name2').html(application.name);
                    $('#edit_role').val(application.role);
                    $('#editDate').text(formatDate(application.date)); // If the date format needs to be adjusted
                    $('#edit_leave_type').val(application.leave_type);
                    $('#edit_days_requested').val(application.days_number);
                    $('#edit_leave_start_date').val(application.from_date);
                    $('#edit_leave_end_date').val(application.end_date);
                    $('#edit_reason').val(application.reason);
                    $('#auth_email').text(application.email);

                    // Update leave type display
                    $('#editLeaveTypeDisplay').text(application.leave_type);

                    // Open the modal
                    $('#editApplication').modal('show');
                } else {
                    alert('Failed to fetch application details.');
                }
            },
            error: function (xhr) {
                alert('Failed to fetch application details.');
            }
        });
    });
    // Handle the update button click
    $('#updateApplicationBtn').on('click', function () {
        const applicationEditId = $('#applicationEditId').val();

            $.ajax({
                url: "{{ route('application.update', ':id') }}".replace(':id', applicationEditId),
                method: 'PUT',
                data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                role: $('#edit_role').val(),
                leave_type: $('#edit_leave_type').val(),
                days_number: $('#edit_days_requested').val(),
                from_date: $('#edit_leave_start_date').val(),
                end_date: $('#edit_leave_end_date').val(),
                reason: $('#edit_reason').val(),
            },
            success: function (response) {
                $('#editApplication').modal('hide');
                Swal.fire({
                    title: 'Application Edited!',
                    text: 'Your application has been edited successfully.',
                    icon: 'success',
                    confirmButtonText: 'OK',
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            },
            error: function (xhr) {
                $('#editApplication').modal('hide');
                console.error('Error:', xhr.responseText);
                Swal.fire({
                    title: 'Error!',
                    text: 'There was an error editing your application.',
                    icon: 'error',
                    confirmButtonText: 'Try Again',
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            },
        });
    });

    // Handle Return button click
    $('.return-button').on('click', function () {
    const applicationId = $(this).data('id');
    $('#returnApplicationId').val(applicationId);

    $('#returnApplication').modal('show');
    });

    // Handle Return button submit
        $('#submitReturnBtn').on('click', function () {
        const applicationId = $('#returnApplicationId').val();
        const returnReason = CKEDITOR.instances.returnReason.getData();

        $.ajax({
            url: "{{ route('application.return', ':id') }}".replace(':id', applicationId),
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                reason: returnReason
            },
            success: function (response) {
                $('#returnApplication').modal('hide');
                Swal.fire({
                    title: 'Application Returned!',
                    text: 'The application has been successfully returned.',
                    icon: 'success',
                    confirmButtonText: 'OK',
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            },
            error: function (xhr) {
                console.error('Error:', xhr.responseText);
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to return the application. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'Try Again',
                });
            },
        });
    });
});

</script>
@endsection