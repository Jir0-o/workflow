@extends('layouts.master')
@section('content')
<h4 class="py-2 m-4"><span class="text-muted fw-light">Edit Task</span></h4>

<div class="row mt-5">
    <div class="col-12">
        {{-- Users --}}
        <div class="card">
            <div class="card-header">
                <h5>Message to edit</h5>
            </div>
            <div class="card-body">
            <form action="{{ route('tasks.update', $task->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="message">Write a message to edit</label>
                    <textarea id="message" name="message" class="form-control" rows="4" required placeholder="Your message"></textarea>
                </div>
                <a href="{{route('tasks.index')}}" class="btn btn-secondary">Back</a>
                <button type="submit" class="btn btn-primary">Send Request</button>
            </form>
            </div>
        </div>
    </div>
</div>
@endsection
