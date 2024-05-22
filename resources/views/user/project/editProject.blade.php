@extends('layouts.master')
@section('content')
<h4 class="py-2 m-4"><span class="text-muted fw-light">Edit Project</span></h4>

<div class="row mt-5">
    <div class="col-12">
        {{-- Users --}}
        <div class="card">
            <div class="card-header">
                <h5>Edit Details</h5>
            </div>
            <div class="card-body">
            <form action="{{route('project_title.update', $project->id)}}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3 position-relative">
                    <label for="title">Project name</label>
                    <div class="d-flex align-items-center">
                    <input id="title" name="title" type="text" required class="form-control" value="{{ $project->project_title }}" placeholder="Title Name">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="description">Project Description (Not required)</label>
                    <textarea id="description" name="description" class="form-control" rows="4" placeholder="Project Details">{{ $project->description}}</textarea>
                </div>
                <div class="mb-3">
                    <label for="start_date">Project Start Date</label>
                    <input id="start_date" name="start_date" type="date" required class="form-control" value="{{ $project->start_date }}" placeholder="Date">
                </div>
                <div class="mb-3">
                    <label for="end_date">Project End Date</label>
                    <input id="end_date" name="end_date" type="date" required class="form-control" value="{{ $project->end_date }}" placeholder="Date">
                </div>
                <a href="#" class="btn btn-secondary">Back</a>
                <button type="submit" class="btn btn-primary">Edit Project</button>
            </form>
            </div>
        </div>
    </div>
</div>
@endsection
