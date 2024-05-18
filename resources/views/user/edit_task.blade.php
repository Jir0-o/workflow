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
            <form action="{{ route('tasks.update', $task->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="description">Task Description</label>
                    <textarea id="description" name="description" class="form-control" rows="4" required placeholder="Task Details">{{ $task->description }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="last_submit_date">Last Submit Date</label>
                    <input id="last_submit_date" name="last_submit_date" type="date" required class="form-control" value="{{ $task->submit_date }}">
                </div>
                <a href="/settings" class="btn btn-secondary">Back</a>
                <button type="submit" class="btn btn-primary">Update Task</button>
            </form>
            </div>
        </div>
    </div>
</div>
@endsection
