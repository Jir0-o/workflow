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
    <h4 class="py-2 m-4"><span class="text-muted fw-light">Notice Board</span></h4>

    <div class="row mt-5">
        <!-- Modal -->
        <div class="modal fade" id="createNoticeModal" tabindex="-1" aria-labelledby="createNoticeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg"> <!-- Added modal-lg class here -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createNoticeModalLabel">Create Notice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="createNoticeForm">
                            @csrf
                            <input type="hidden" name="previous_url" value="{{ url()->previous() }}">

                            <div class="mb-3">
                                <label for="title">Title</label>
                                <input id="title" name="title" type="text" required class="form-control" placeholder="Enter Notice Title">
                            </div>

                            <div class="mb-3">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" class="form-control" rows="4" placeholder="Enter Notice Details"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="start_date">Start Date & Time</label>
                                <input id="start_date" name="start_date" type="datetime-local" required class="form-control" value="{{ date('Y-m-d\TH:i') }}">
                            </div>
                            
                            <div class="mb-3">
                                <label for="end_date">End Date & Time</label>
                                <input id="end_date" name="end_date" type="datetime-local" required class="form-control" value="{{ date('Y-m-d\TH:i') }}">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="saveNoticeBtn" class="btn btn-primary">Create Notice</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Edit Notice Modal -->
        <div class="modal fade" id="editNoticeModal" tabindex="-1" aria-labelledby="editNoticeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editNoticeModalLabel">Edit Notice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editNoticeForm" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label for="editTitle">Title</label>
                                <input id="editTitle" name="title" type="text" required class="form-control" placeholder="Enter Notice Title">
                            </div>
                            
                            <div class="mb-3">
                                <label for="editDescription">Description</label>
                                <textarea id="editDescription" name="description" class="form-control" rows="4" placeholder="Enter Notice Details"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="editStartDate">Start Date</label>
                                <input id="editStartDate" name="start_date" type="datetime-local" required class="form-control">
                            </div>
                            
                            <div class="mb-3">
                                <label for="editEndDate">End Date</label>
                                <input id="editEndDate" name="end_date" type="datetime-local" required class="form-control">
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
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
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#Pending"
                                    type="button" role="tab" aria-controls="home" aria-selected="true">
                                    Running Notice
                                    <span class="badge bg-primary"> {{$countToday}}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#Incomplete"
                                    type="button" role="tab" aria-controls="profile" aria-selected="false">
                                    Expired Notice
                                    <span class="badge bg-primary">{{$countExpried}}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#Complete"
                                    type="button" role="tab" aria-controls="messages" aria-selected="false">
                                    All Notice
                                    <span class="badge bg-primary">{{$countAll}}</span>
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
                                        <h5>Running Notice</h5>
                                    </div>
                                    @can('Create Project')
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createNoticeModal">
                                                <i class="bx bx-edit-alt me-1"></i> Create Notice
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
                                            <th>Title</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($todayNotice as $key => $notice)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $notice->title }}</td>
                                            <td>{{ $notice->start_date->timezone('Asia/Dhaka')->format('d F Y , h:i A') }}</td>
                                            <td>{{ $notice->end_date ? $notice->end_date->timezone('Asia/Dhaka')->format('d F Y, h:i A') : 'Notice Ended' }}</td>
                                            <td>{!! $notice->description !!}</td>
                                            <td> @if ($notice->status == 0)
                                                <span class="badge bg-success">Running</span>
                                            @else
                                                <span class="badge bg-danger">Expired</span>
                                            @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <!-- Edit Notice -->
                                                        <a class="dropdown-item edit-button" href="javascript:void(0);" data-id="{{ $notice->id }}">
                                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                                        </a>
                                                        <!-- Delete Notice -->
                                                        <form id="delete-notice-form-{{ $notice->id }}" action="{{ route('notice.destroy', ['notice' => $notice->id]) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="dropdown-item" onclick="confirmDeleteNotice({{ $notice->id }})">
                                                                <i class="bx bx-trash me-1"></i> Delete
                                                            </button>
                                                        </form>
                                                        <div class="dropdown-divider"></div>
                                                        <!-- Change Status -->
                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item test" href="#" id="dropdownStatusLink">
                                                                <i class="bx bx-refresh me-1"></i> Change Status
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="dropdownStatusLink">
                                                                <li>
                                                                    <form id="End-notice-form-{{ $notice->id }}" action="{{ route('notice.end', ['notice' => $notice->id]) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmEndNotice({{ $notice->id }})">
                                                                            <i class="bx bx-check me-1"></i> End Notice
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
                    
                    <div class="tab-pane" id="Incomplete" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <h5>Expired Notice</h5>
                                    </div>
                                    @can('Create Project')
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <!-- Button trigger modal -->
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createNoticeModal">
                                                <i class="bx bx-edit-alt me-1"></i> Create Notice
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
                                            <th>Title</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($expried as $key => $notice)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $notice->title }}</td>
                                            <td>{{ $notice->start_date->timezone('Asia/Dhaka')->format('d F Y , h:i A') }}</td>
                                            <td>{{ $notice->end_date ? $notice->end_date->timezone('Asia/Dhaka')->format('d F Y, h:i A') : 'Notice Ended' }}</td>
                                            <td>{!! $notice->description !!}</td>
                                            <td> 
                                            @if ($notice->status == 0)
                                                <span class="badge bg-success">Running</span>
                                            @else
                                                <span class="badge bg-danger">Expired</span>
                                            @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <!-- Edit Notice -->
                                                        <a class="dropdown-item edit-button" href="javascript:void(0);" data-id="{{ $notice->id }}">
                                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                                        </a>
                                                        <!-- Delete Notice -->
                                                        <form id="delete-notice-form-{{ $notice->id }}" action="{{ route('notice.destroy', ['notice' => $notice->id]) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="dropdown-item" onclick="confirmDeleteNotice({{ $notice->id }})">
                                                                <i class="bx bx-trash me-1"></i> Delete
                                                            </button>
                                                        </form>
                                                        <div class="dropdown-divider"></div>
                                                        <!-- Change Status -->
                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item test" href="#" id="dropdownStatusLink">
                                                                <i class="bx bx-refresh me-1"></i> Change Status
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="dropdownStatusLink">
                                                                <li>
                                                                    <form id="start-notice-form-{{ $notice->id }}" action="{{ route('notice.start', ['notice' => $notice->id]) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmStartNotice({{ $notice->id }})">
                                                                            <i class="bx bx-stop me-1"></i> Start Notice
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
                                        <h5>All Notice</h5>
                                    </div>
                                    @can('Create Project')
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <!-- Button trigger modal -->
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createNoticeModal">
                                                <i class="bx bx-edit-alt me-1"></i> Create Notice
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
                                            <th>Title</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($allNotice as $key => $notice)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $notice->title }}</td>
                                            <td>{{ $notice->start_date->timezone('Asia/Dhaka')->format('d F Y , h:i A') }}</td>
                                            <td>{{ $notice->end_date ? $notice->end_date->timezone('Asia/Dhaka')->format('d F Y, h:i A') : 'Notice Ended' }}</td>
                                            <td>{!! $notice->description !!}</td>
                                            <td> @if ($notice->status == 0)
                                                <span class="badge bg-success">Running</span>
                                            @else
                                                <span class="badge bg-danger">Expired</span>
                                            @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <!-- Edit Notice -->
                                                        <a class="dropdown-item edit-button" href="javascript:void(0);" data-id="{{ $notice->id }}">
                                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                                        </a>
                                                        <!-- Delete Notice -->
                                                        <form id="delete-notice-form-{{ $notice->id }}" action="{{ route('notice.destroy', ['notice' => $notice->id]) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="dropdown-item" onclick="confirmDeleteNotice({{ $notice->id }})">
                                                                <i class="bx bx-trash me-1"></i> Delete
                                                            </button>
                                                        </form>
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

        // Handle form submission with AJAX
        $('#saveNoticeBtn').on('click', function(e) {
            e.preventDefault();

            // Create a new FormData object
            let formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}'); 
            formData.append('title', $('#title').val());
            formData.append('description', CKEDITOR.instances['description'].getData());
            formData.append('start_date', $('#start_date').val());
            formData.append('end_date', $('#end_date').val());

            $.ajax({
                url: "{{ route('notice.store') }}", 
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#createNoticeModal').modal('hide'); 
                    Swal.fire({
                        title: 'Notice Created!',
                        text: 'Your notice was created successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload(); // Reload the page
                        }
                    });
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                    Swal.fire({
                        title: 'Error!',
                        text: 'There was an error creating the notice.',
                        icon: 'error',
                        confirmButtonText: 'Try Again'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload(); // Reload the page
                        }
                    });
                }
            });
        });
        // Event listener for edit button click
        $('.edit-button').on('click', function() {
            const noticeId = $(this).data('id');
            
            const editUrl = `{{ route('notice.show', ':id') }}`.replace(':id', noticeId);
            const updateUrl = `{{ route('notice.update', ':id') }}`.replace(':id', noticeId);

            $.ajax({
                url: editUrl,
                type: 'GET',
                success: function(response) {
                    if (response.status) {
                    $('#editTitle').val(response.notice.title);
                    CKEDITOR.instances.editDescription.setData(response.notice.description);

                    // Populate datetime-local inputs with formatted dates
                    $('#editStartDate').val(response.notice.start_date);
                    $('#editEndDate').val(response.notice.end_date);

                    $('#editNoticeModal').modal('show');

                        $('#editNoticeForm').attr('action', updateUrl);
                    } else {
                        alert('Error loading notice data');
                    }
                },
                error: function() {
                    alert('Failed to load notice data');
                }
            });
        });

        $('#editNoticeForm').on('submit', function(e) {
            e.preventDefault();

            const descriptionData = CKEDITOR.instances['editDescription'].getData();
            const formData = $(this).serialize() + '&description=' + encodeURIComponent(descriptionData);
            
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.status) {
                        $('#editNoticeModal').modal('hide');
                        Swal.fire({
                            title: 'Notice Updated!',
                            text: 'Your notice was updated successfully.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload(); 
                        });
                    } else {
                        alert('Error updating notice');
                    }
                },
                error: function() {
                    alert('Failed to update notice');
                }
            });
        });
    });
    function confirmDeleteNotice(DeleteTaskId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to Delete this project?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-notice-form-${DeleteTaskId}`).submit();
                }
            });
        }

        function confirmEndNotice(DeleteNotice) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to End this notice?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`End-notice-form-${DeleteNotice}`).submit();
                }
            });
        }
        function confirmStartNotice(StartNotice) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to start this notice again? Notice Start and End date will be Today.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`start-notice-form-${StartNotice}`).submit();
                }
            });
        }
</script>
@endsection
