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
                        <div class="col-md-4">
                            <label for="date_criteria" class="form-label">Date Criteria</label>
                            <select id="date_criteria" name="date_criteria" class="form-control" required>
                                <option value="created_at" {{ old('date_criteria', $oldInput['date_criteria'] ?? '') == 'created_at' ? 'selected' : '' }}>Created At</option>
                                <option value="submit_date" {{ old('date_criteria', $oldInput['date_criteria'] ?? '') == 'submit_date' ? 'selected' : '' }}>Last Submit Date</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" id="start_date" name="start_date" class="form-control" value="{{ old('start_date', $oldInput['start_date'] ?? date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-4">
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
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="columns" class="form-label">Select Columns to Display</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="col_create_date" data-column="2" checked>
                                <label class="form-check-label" for="col_create_date">Start Date</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="col_submit_date" data-column="3" checked>
                                <label class="form-check-label" for="col_submit_date">Due Date</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="col_submit_date" data-column="4" checked>
                                <label class="form-check-label" for="col_submitted_date">Submitted Date</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="col_project_title" data-column="5" checked>
                                <label class="form-check-label" for="col_project_title">Project Title</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="col_description" data-column="6" checked>
                                <label class="form-check-label" for="col_description">Description</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="col_assigned_user" data-column="7" checked>
                                <label class="form-check-label" for="col_assigned_user">Assigned User</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="col_task_message" data-column="8" checked>
                                <label class="form-check-label" for="col_task_message">Task Message</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="col_task_message" data-column="9" checked>
                                <label class="form-check-label" for="col_admin_message">Admin Message</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="col_status" data-column="10" checked>
                                <label class="form-check-label" for="col_status">Status</label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary float-end">Generate Report</button>
                </form>

            @if(isset($tasks))
                <div class="row mb-3">
                    <div class="col-md-12 text-end">
                        <button onclick="printReport()" class="btn btn-secondary">Print Report</button>
                    </div>
                </div>
                <div class="table-responsive text-nowrap p-3" id="reportSection">
                    <div id="reportHeader" class="d-none container">
                        <div class="text-center">
                            <h2>Report</h2>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-6 text-left">
                                        <p><strong>Start Date:</strong> {{ $oldInput['start_date'] }}</p>
                                    </div>
                                    <div class="col-6 text-right">
                                        <p><strong>End Date:</strong> {{ $oldInput['end_date'] }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <p><strong>Status:</strong> {{ $oldInput['status'] }}</p>
                                <p><strong>User:</strong> {{ $oldInput['user'] }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Project Title:</strong> {{ $oldInput['title_name_id'] }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <table id="datatable1" class="table">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th class="column-create-date">Start Date</th>
                                <th class="column-submit-date">Due Date</th>
                                <th class="column-submitted-date">Submitted Date</th>
                                <th class="column-project-title">Project Title</th>
                                <th class="column-description">Description</th>
                                <th class="column-assigned-user">Assigned User</th>
                                <th class="column-task-message">Task Message</th>
                                <th class="column-admin-message">Admin Message</th>
                                <th class="column-status">Status</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach($tasks as $key => $task)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td class="column-create-date">{{ \Carbon\Carbon::parse($task->created_at)->format('d F Y, h:i A') }}</td>
                                    <td class="column-submit-date">{{ \Carbon\Carbon::parse($task->submit_date)->format('d F Y') }}</td>
                                    <td class="column-submitted-date">{{ $task->submit_by_date ? \Carbon\Carbon::parse($task->submit_by_date)->format('d F Y, h:i A') : 'Not Submitted' }}</td></td>
                                    <td class="column-project-title">{{ $task->title_name->project_title ?? 'N/A' }}</td>
                                    <td class="column-description">{{ $task->description }}</td>
                                    <td class="column-assigned-user">{{ $task->user->name }}</td>
                                    <td class="column-task-message">{{ $task->message ?? 'N/A'}}</td>
                                    <td class="column-admin-message">{{ $task->admin_message ?? 'N/A'}}</td>
                                    <td class="column-status">{{ $task->status }}</td>
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
            // Show the report header
            document.getElementById('reportHeader').classList.remove('d-none');
            
            var printContents = document.getElementById('reportSection').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload();
        }

        // JavaScript to handle column visibility
        document.querySelectorAll('.form-check-input').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                var column = this.dataset.column;
                var cells = document.querySelectorAll('td:nth-child(' + column + '), th:nth-child(' + column + ')');
                cells.forEach(function(cell) {
                    cell.style.display = checkbox.checked ? '' : 'none';
                });
            });
        });

        // Initially hide unchecked columns
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.form-check-input').forEach(function(checkbox) {
                if (!checkbox.checked) {
                    var column = checkbox.dataset.column;
                    var cells = document.querySelectorAll('td:nth-child(' + column + '), th:nth-child(' + column + ')');
                    cells.forEach(function(cell) {
                        cell.style.display = 'none';
                    });
                }
            });
        });
    </script>

    <style>
        @media print {
            .d-none {
                display: block !important;
            }
            .btn, .form-label, .form-control, .form-check, .form-check-inline, .form-select, .card-header, form {
                display: none !important;
            }
        }
    </style>
@endsection
