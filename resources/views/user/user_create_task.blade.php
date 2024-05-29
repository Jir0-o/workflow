@extends('layouts.master')
@section('content')
<h4 class="py-2 m-4"><span class="text-muted fw-light">Create Task</span></h4>

<div class="row mt-5">
    <div class="col-12">
        {{-- Users --}}
        <div class="card">
            <div class="card-header">
                <h5>Todays Task</h5>
            </div>
            <div class="card-body">
            <form action="{{route('tasks.store')}}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="title">Select Title</label>
                    <select id="title" name="title" class="form-control" required>
                        <option value="">Select project Title</option>
                        @foreach($title as $tit)
                        <option value="{{ $tit->id }}">{{ $tit->project_title }}</option>
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
                <a href="{{route('tasks.index')}}" class="btn btn-secondary">Back</a>
                <button type="submit" class="btn btn-primary">Create Task</button>
            </form>
            </div>
        </div>
    </div>
</div>
@endsection
