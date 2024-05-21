@extends('layouts.master')
@section('content')
<h4 class="py-2 m-4"><span class="text-muted fw-light">Create Task</span></h4>

<div class="row mt-5">
    <div class="col-12">
        {{-- Users --}}
        <div class="card">
            <div class="card-header">
                <h5>Asign Task</h5>
            </div>
            <div class="card-body">
            <form action="{{route('asign_tasks.store')}}" method="POST">
                @csrf
                <div class="mb-3 position-relative">
                    <label for="title">Project Title</label>
                    <div class="d-flex align-items-center">
                        <select id="title" name="title" class="form-control" style="width: calc(100% - 30px);">
                            <option value="">Select title</option>
                            @foreach($title as $tit)
                                <option value="{{ $tit->id }}">{{ $tit->project_title }}</option>
                            @endforeach
                        </select>
                        <a href="{{ route('project_title.create') }}" class="btn btn-link p-0 ml-2" style="text-decoration: none;"data-toggle="tooltip" title="Create new project title">
                            <i class="fas fa-plus-circle" style="font-size: 24px; color: #007bff;"></i>
                        </a>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="user_id">User Name</label>
                    <select id="user_id" name="user_id[]" class="form-control" multiple="multiple" required>
                        <option value="">Select User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach 
                    </select>
                </div>
                <div class="mb-3">
                    <label for="description">Task Description</label>
                    <textarea id="description" name="description" class="form-control" rows="4" required placeholder="Task Details"></textarea>
                </div>
                <div class="mb-3">
                    <label for="last_submit_date">Last Submit Date</label>
                    <input id="last_submit_date" name="last_submit_date" type="date" required class="form-control" value="{{ date('Y-m-d') }}" placeholder="Date">
                </div>
                <a href="/settings" class="btn btn-secondary">Back</a>
                <button type="submit" class="btn btn-primary">Create Task</button>
            </form>
            </div>
        </div>
    </div>
</div>
<!-- Include jQuery first -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
    $(document).ready(function() {
        $('#user_id').select2({
            placeholder: 'Select User',
            allowClear: true
        });
    });
</script>
@endsection
