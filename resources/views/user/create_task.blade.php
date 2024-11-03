@extends('layouts.master')
@section('content')
<h4 class="py-2 m-4"><span class="text-muted fw-light">Create Task</span></h4>

<div class="row mt-5">
    <div class="col-12">
        <!-- Modal -->
        <div class="modal fade" id="createProjectModal" tabindex="-1" aria-labelledby="createProjectModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content"> 
                    <div class="modal-header">
                        <h5 class="modal-title" id="createProjectModalLabel">Create Project</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body custom-select2-modal">
                        <form id="createProjectForm">
                            @csrf
                            <input type="hidden" name="previous_url" value="{{ url()->previous() }}">

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

        <!-- Main Form for Assigning Task -->
        <div class="card">
            <div class="card-header">
                <h5>Assign Task</h5>
            </div>
            <div class="card-body">
                <form action="{{route('asign_tasks.store')}}" method="POST">
                    @csrf
                    <div class="mb-3 position-relative">
                        <label for="task_title">Project Title</label>
                        <div class="d-flex align-items-center">
                            <select id="task_title" name="title" class="form-control" style="width: calc(100% - 30px);">
                                <option value="">Select title</option>
                                @foreach($title as $tit)
                                    <option value="{{ $tit->id }}">{{ $tit->project_title }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-link p-0 ml-2" data-toggle="tooltip" title="Create new project title" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                                <i class="fas fa-plus-circle" style="font-size: 24px; color: #007bff;"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="main_user_id">User Name</label>
                        <select id="main_user_id" name="user_id[]" class="form-control" multiple="multiple" required>
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
                        <label for="last_submit_date">Last Submit Date</label>
                        <input id="last_submit_date" name="last_submit_date" type="date" required class="form-control" value="{{ date('Y-m-d') }}" placeholder="Date">
                    </div>
                    <a href="{{route('asign_tasks.index')}}" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn btn-primary">Create Task</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery and Select2 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<style>
    /* Limit z-index to modal-specific select2 */
    #createProjectModal .select2-container {
        z-index: 1050 !important;
    }
</style>

<script>
    $(document).ready(function() {
        // Initialize CKEditor for the Project Description field in the modal
        CKEDITOR.replace('modal_description');
        CKEDITOR.replace('task_description');

        // Initialize Select2 with distinct IDs for each section
        $('#main_user_id').select2({
            placeholder: 'Select User',
            allowClear: true,
            width: '100%'
        });

        $('#modal_user_id').select2({
            placeholder: 'Select User',
            allowClear: true,
            width: '100%'
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
    });
</script>
@endsection
