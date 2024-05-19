@extends('layouts.master')
@section('content')
<h4 class="py-2 m-4"><span class="text-muted fw-light">Create Project</span></h4>

<div class="row mt-5">
    <div class="col-12">
        {{-- Users --}}
        <div class="card">
            <div class="card-header">
                <h5>Project Details</h5>
            </div>
            <div class="card-body">
            <form action="{{route('project_title.store')}}" method="POST">
                @csrf
                <div class="mb-3 position-relative">
                    <label for="title">Project name</label>
                    <div class="d-flex align-items-center">
                    <input id="title" name="title" type="text" required class="form-control" value="" placeholder="Title Name">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="description">Project Description (Not required)</label>
                    <textarea id="description" name="description" class="form-control" rows="4" placeholder="Project Details"></textarea>
                </div>
                <div class="mb-3">
                    <label for="start_date">Project Start Date</label>
                    <input id="start_date" name="start_date" type="date" required class="form-control" value="{{ date('Y-m-d') }}" placeholder="Date">
                </div>
                <a href="/settings" class="btn btn-secondary">Back</a>
                <button type="submit" class="btn btn-primary">Create Project</button>
            </form>
            </div>
        </div>
    </div>
</div>
@endsection
