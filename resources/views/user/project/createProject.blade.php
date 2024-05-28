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
                <input type="hidden" name="previous_url" value="{{ url()->previous() }}">
                <div class="mb-3 position-relative">
                    <label for="title">Project name</label>
                    <div class="d-flex align-items-center">
                    <input id="title" name="title" type="text" required class="form-control" value="" placeholder="Title Name">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="user_id">Assign User</label>
                    <select id="user_id" name="user_id[]" class="form-control" multiple="multiple" required>
                        <option value="">Select User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach 
                    </select>
                </div>
                <div class="mb-3">
                    <label for="description">Project Description (Not required)</label>
                    <textarea id="description" name="description" class="form-control" rows="4" placeholder="Project Details"></textarea>
                </div>
                <div class="mb-3">
                    <label for="start_date">Project Start Date</label>
                    <input id="start_date" name="start_date" type="date" required class="form-control" value="{{ date('Y-m-d') }}" placeholder="Date">
                </div>
                <div class="mb-3">
                    <label for="end_date">Project End Date</label>
                    <input id="end_date" name="end_date" type="date" required class="form-control" value="{{ date('Y-m-d') }}" placeholder="Date">
                </div>
                <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
                <button type="submit" class="btn btn-primary">Create Project</button>
            </form>
            </div>
        </div>
    </div>
</div>
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
