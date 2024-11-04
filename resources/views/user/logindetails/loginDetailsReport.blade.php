@extends('layouts.master')
@section('content')
<h4 class="py-2 m-4"><span class="text-muted fw-light">Create Report</span></h4>

<div class="row mt-5">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Create Report</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('loginReport.report') }}">
                    @csrf
                    <div class="row mb-3">
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
                                <option value="0" {{ old('status', $oldInput['status'] ?? '') == '0' ? 'selected' : '' }}>Login</option>
                                <option value="1" {{ old('status', $oldInput['status'] ?? '') == '1' ? 'selected' : '' }}>Log Out</option>
                            </select>
                        </div>
                    </div>
                    @can('Generate Login Report')
                    <button type="submit" class="btn btn-primary float-end">Generate Report</button>
                    @endcan
                </form>

                @if(isset($tasks) && $tasks->isNotEmpty())
                    @can('Print Login Report')
                    <div class="row mt-4">
                        <div class="col-md-12 text-end">
                            <button onclick="printReport()" class="btn btn-secondary">Print Report</button>
                        </div>
                    </div>
                    @endcan
                    <div class="table-responsive text-nowrap p-3" id="reportSection">
                        <div id="reportHeader" class="d-none container">
                            <div class="text-center">
                                <h2>Login Report</h2>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Start Date:</strong> {{ $formattedStartDate }}</p>
                                    <p><strong>End Date:</strong> {{ $formattedEndDate }}</p>
                                </div>
                                <div class="col-md-6 text-right">
                                    <p><strong>Status:</strong> {{ $oldInput['status'] ?? 'Not Selected' }}</p>
                                    <p><strong>User:</strong> {{ $selectedUser->name ?? 'Not Selected' }}</p>
                                </div>
                            </div>
                        </div>

                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>User</th>
                                    <th>Login Time</th>
                                    <th>Logout Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- {{dd ($tasks)}} --}}
                                @foreach($tasks as $key => $task)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ \Carbon\Carbon::parse($task->created_at)->format('d F Y') }}</td>
                                        <td> @if ($task->status == 0)
                                            <span class="badge bg-success">Logged In</span>
                                            @else
                                                <span class="badge bg-danger">Logged Out</span>
                                            @endif
                                        </td>
                                        <td>{{ $task->name ?? 'N/A' }}</td>
                                        <td>{{ $task->login_time ? \Carbon\Carbon::parse($task->login_time)->format(' h:i A') : 'Logged In From Another Browser' }}</td>
                                        <td>{{ $task->logout_time ? \Carbon\Carbon::parse($task->logout_time)->format(' h:i A') : 'Not Logged Out' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="mt-4">No records found for the selected criteria.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function printReport() {
        document.getElementById('reportHeader').classList.remove('d-none');
        const printContents = document.getElementById('reportSection').innerHTML;
        const originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        window.location.reload();
    }
</script>

<style>
    @media print {
        .d-none, .btn, form { display: none !important; }
        .page { page-break-after: always; }
        th, td { word-wrap: break-word; overflow-wrap: break-word; }
    }
</style>
@endsection
