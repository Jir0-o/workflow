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
    <h4 class="py-2 m-4"><span class="text-muted fw-light">Send Mails</span></h4>

    <div class="row mt-5">
        <!-- Modal -->
        <div class="modal fade" id="createNoticeModal" tabindex="-1" aria-labelledby="createNoticeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg"> <!-- Added modal-lg class here -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createNoticeModalLabel">Add Mails</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="createNoticeForm">
                            @csrf
                            <input type="hidden" name="previous_url" value="{{ url()->previous() }}">

                            <div class="mb-3">
                                <label for="name">Name</label>
                                <input id="name" name="name" type="text" class="form-control" placeholder="Enter Name">
                            </div>
                            
                            <div class="mb-3">
                                <label for="email">Email</label>
                                <input id="email" type="email" name="email" class="form-control" placeholder="Enter Email">
                            </div>

                            <div class="mb-3">
                                <label for="received_time">Received Time</label>
                                <input id="received_time" type="time" name="received_time" class="form-control" placeholder="Received Time" onclick="this.showPicker()">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="saveNoticeBtn" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Edit Notice Modal -->
        <div class="modal fade" id="editNoticeModal" tabindex="-1" aria-labelledby="editNoticeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editNoticeModalLabel">Edit Mail</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editNoticeForm" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label for="editName">Name</label>
                                <input id="editName" name="name" type="text" class="form-control" placeholder="Enter Name">
                            </div>
                            
                            <div class="mb-3">
                                <label for="editEmail">Email</label>
                                <input id="editEmail" name="email" type="email" class="form-control" placeholder="Enter Email">
                            </div>    

                            <div class="mb-3">
                                <label for="editReceivedTime">Received Time</label>
                                <input id="editReceivedTime" name="received_time" type="time" class="form-control" placeholder="Received Time" onclick="this.showPicker()">
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
                                    All Mails
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
                                        <h5>All Mails</h5>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createNoticeModal">
                                                <i class="bx bx-edit-alt me-1"></i> Add Mail
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="table-responsive text-nowrap p-3">
                                <table id="datatable1" class="table">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Name</th>
                                            <th>Mail Address</th>
                                            <th>Received Time</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($email as $key => $notice)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $notice->name }}</td>
                                            <td>{{ $notice->email_address}}</td>
                                            <td>{{ \Carbon\Carbon::parse($notice->daily_report_time)->format('h:i A') }}</td>
                                            <td> @if ($notice->status == 0)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
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
                                                        <form id="delete-notice-form-{{ $notice->id }}" action="{{ route('mail_send.destroy', ['mail_send' => $notice->id]) }}" method="POST">
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

        // Handle form submission with AJAX
        $('#saveNoticeBtn').on('click', function(e) {
            e.preventDefault();

            // Create a new FormData object
            let formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}'); 
            formData.append('name', $('#name').val());
            formData.append('email', $('#email').val());
            formData.append('received_time', $('#received_time').val());

            $.ajax({
                url: "{{ route('mail_send.store') }}", 
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#createNoticeModal').modal('hide'); 
                    Swal.fire({
                        title: 'Email Created!',
                        text: 'Your email was created successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload(); // Reload the page
                        }
                    });
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $('#createNoticeForm .text-danger').remove();
                        $('#createNoticeForm .is-invalid').removeClass('is-invalid');

                        $.each(errors, function(key, value) {
                            let field = $('#' + key);
                            field.addClass('is-invalid');
                            field.after('<div class="text-danger">' + value[0] + '</div>');
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'There was an error creating the email.',
                            icon: 'error',
                            confirmButtonText: 'Try Again'
                        });
                    }
                }
            });
        });
        // Event listener for edit button click
        $('.edit-button').on('click', function() {
            const noticeId = $(this).data('id');
            
            const editUrl = `{{ route('mail_send.show', ':id') }}`.replace(':id', noticeId);
            const updateUrl = `{{ route('mail_send.update', ':id') }}`.replace(':id', noticeId);

            $.ajax({
                url: editUrl,
                type: 'GET',
                success: function(response) {
                    if (response.status) {
                    $('#editName').val(response.email.name);
                    $('#editEmail').val(response.email.email_address);
                    $('#editReceivedTime').val(response.email.daily_report_time);

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

            const formData = $(this).serialize();
            
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.status) {
                        $('#editNoticeModal').modal('hide');
                        Swal.fire({
                            title: 'Email Updated!',
                            text: 'Your email was updated successfully.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload(); 
                        });
                    } else {
                        alert('Error updating notice');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;

                        // Clear old errors
                        $('#editNoticeForm .text-danger').remove();
                        $('#editNoticeForm .is-invalid').removeClass('is-invalid');

                        // Show new errors
                        $.each(errors, function(key, value) {
                            let field = $('#editNoticeForm').find(`[name="${key}"]`);
                            field.addClass('is-invalid');
                            field.after('<div class="text-danger">' + value[0] + '</div>');
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'There was an error updating the email.',
                            icon: 'error',
                            confirmButtonText: 'Try Again'
                        });
                    }
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

        document.getElementById('received_time').addEventListener('click', function () {
            try {
                this.showPicker();
            } catch (e) {
                // fallback if showPicker is unsupported (do nothing or custom behavior)
                console.log("showPicker not supported");
            }
        });
</script>
@endsection
