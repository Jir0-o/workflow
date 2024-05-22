@extends('layouts.master')
@section('content')
    <h4 class="py-2 m-4"><span class="text-muted fw-light">Create Report</span></h4>

    <div class="row mt-5">
        <div class="col-12">
            {{-- Users --}}
            <div class="card">
                <div class="card-header">
                    <h5>Create Report</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('report.create') }}">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" id="start_date" name="start_date" class="form-control" value="{{ old('start_date', $oldInput['start_date'] ?? date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" id="end_date" name="end_date" class="form-control" value="{{ old('end_date', $oldInput['end_date'] ?? date('Y-m-d')) }}" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="title_name_id" class="form-label">Project Title</label>
                                <select id="title_name_id" name="title_name_id" class="form-control">
                                    <option value="">Select Project Title</option>
                                    @foreach($titles as $title)
                                        <option value="{{ $title->id }}" {{ old('title_name_id', $oldInput['title_name_id'] ?? '') == $title->id ? 'selected' : '' }}>{{ $title->project_title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="user" class="form-label">User</label>
                                <select id="user" name="user" class="form-control">
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user', $oldInput['user'] ?? '') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="status" class="form-label">Status</label>
                                <select id="status" name="status" class="form-select">
                                    <option value="">Select Status</option>
                                    <option value="pending" {{ old('status', $oldInput['status'] ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="completed" {{ old('status', $oldInput['status'] ?? '') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="in_progress" {{ old('status', $oldInput['status'] ?? '') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="incomplete" {{ old('status', $oldInput['status'] ?? '') == 'incomplete' ? 'selected' : '' }}>Incomplete</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Generate Report</button>
                    </form>
            @if(isset($tasks))
                <div class="row mb-3">
                    <div class="card-header">
                        <h5>Report Status</h5>
                    </div>
                    <div class="col-md-4">
                        <button onclick="printReport()" class="btn btn-secondary">Print Report</button>
                    </div>
                </div>
                <div class="table-responsive text-nowrap p-3" id="reportSection">
                    <table id="datatable1" class="table">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Project Title</th>
                                <th>Description</th>
                                <th>Assigned User</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach($tasks as $key => $task)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $task->title_name->project_title ?? 'N/A' }}</td>
                                    <td>{{ $task->description }}</td>
                                    <td>{{ $task->user->name }}</td>
                                    <td>{{ $task->status }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                </div>
            @endif
        </div>
        </div>
    </div>

    <script>
        function printReport() {
            var printContents = document.getElementById('reportSection').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload();
        }
    </script>
@endsection
