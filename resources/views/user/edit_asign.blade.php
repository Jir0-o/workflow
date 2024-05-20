@extends('layouts.master')
@section('content')
<h4 class="py-2 m-4"><span class="text-muted fw-light">Create Task</span></h4>

<div class="row mt-5">
    <div class="col-12">
        {{-- Users --}}
        <div class="card">
            <div class="card-header">
                <h5>Assign Task</h5>
            </div>
            <div class="card-body">
            <form action="{{ route('asign_tasks.update', $tasks->id) }}" method="POST">
                {{-- {{dd ($titles)}} --}}
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="title">Select Title</label>
                    <select id="title" name="title" class="form-control"  required>
                        {{-- <option value="">{{ $tasks->title->project_title ?? 'No project title selected' }}</option> --}}
                        @foreach($title as $tit)
                        <option value="{{ $tit->id }}" {{ $tit->id == $tasks->title_id ? 'selected' : '' }}>{{$tit->project_title}}</option>
                        @endforeach 
                    </select>
                </div>
                <div class="mb-3">
                    <label for="user_id">User Name</label>
                    <select id="user_id" name="task_user_id" class="form-control" required>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $user->id == $tasks->user_id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach 
                    </select>
                </div> 
                <div class="mb-3">
                    <label for="description">Task Description</label>
                    <textarea id="description" name="description" class="form-control" rows="4" required placeholder="Task Details">{{ $tasks->description }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="last_submit_date">Last Submit Date</label>
                    <input id="last_submit_date" name="last_submit_date" type="date" required class="form-control" value="{{ $tasks->submit_date }}">
                </div>
                <div class="mb-3">
                    <label for="status">Task Status</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="pending" {{ $tasks->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="incomplete" {{ $tasks->status == 'incomplete' ? 'selected' : '' }}>Incomplete</option>
                        <option value="completed" {{ $tasks->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="in_progress" {{ $tasks->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    </select>
                </div> 
                <a href="/settings" class="btn btn-secondary">Back</a>
                <button type="submit" class="btn btn-primary">Update Task</button>
            </form>
            </div>
        </div>
    </div>
</div>
@endsection
