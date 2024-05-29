@extends('layouts.master')
@section('content')
<div class="row row-cols-1 row-cols-md-4 mb-4">
    <div class="col mb-4">
        <div class="card total-user-hover" style="height: 135px;">
            <div class="d-flex justify-content-between mb-3">
                <div class="card-body">
                    <h6 class="d-block text-500 font-medium mb-3">TOTAL USERS</h6>
                    <div class="text-900 fs-4" id="total_users"></div>
                </div>
                <div class="d-flex align-items-center justify-content-center rounded"
                     style="width: 2.5rem; height: 2.5rem; margin-top: 10px; margin-right: 5px">
                    <i class='bx bxs-user-account'></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col mb-4">
        <div class="card total-poes-hover" style="height: 135px;">
            <div class="d-flex justify-content-between mb-3">
                <div class="card-body">
                    <h6 class="d-block text-500 font-medium mb-3">TOTAL PROGRAMS</h6>
                    <div class="text-900 fs-4" id="total_poes"></div>
                </div>
                <div class="d-flex align-items-center justify-content-center rounded"
                     style="width: 2.5rem; height: 2.5rem; margin-top: 10px; margin-right: 5px">
                    <i class='bx bxs-id-card'></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col mb-4">
        <div class="card total-standards-hover" style="height: 135px;">
            <div class="d-flex justify-content-between mb-3">
                <div class="card-body">
                    <h6 class="d-block text-500 font-medium mb-3">TOTAL COURSES</h6>
                    <div class="text-900 fs-4" id="total_Standard"></div>
                </div>
                <div class="d-flex align-items-center justify-content-center rounded"
                     style="width: 2.5rem; height: 2.5rem; margin-top: 10px; margin-right: 5px">
                    <i class='bx bxs-layout'></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col mb-4">
        <div class="card total-criterias-hover" style="height: 135px;">
            <div class="d-flex justify-content-between mb-3">
                <div class="card-body">
                    <h6 class="d-block text-500 font-medium mb-3">TOTAL SCHOOL</h6>
                    <div class="text-900 fs-4" id="total_Criteria"></div>
                </div>
                <div class="d-flex align-items-center justify-content-center rounded"
                     style="width: 2.5rem; height: 2.5rem; margin-top: 10px; margin-right: 5px">
                    <i class='bx bx-check-double'></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col mb-4">
        <div class="card total-section-hover" style="height: 135px;">
            <div class="d-flex justify-content-between mb-3">
                <div class="card-body">
                    <h6 class="d-block text-500 font-medium mb-3">TOTAL DISCIPLINE</h6>
                    <div class="text-900 fs-4" id="total_Section"></div>
                </div>
                <div class="d-flex align-items-center justify-content-center rounded"
                     style="width: 2.5rem; height: 2.5rem; margin-top: 10px; margin-right: 5px">
                    <i class='bx bxs-folder-open'></i>
                </div>
            </div>
        </div>
    </div>
</div>

@can('View Today Task')
<div class="row mb-4">
    <div class="col-12 col-md-6 col-lg-6">
        <!-- Roles -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <h5>Today's Pending</h5>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="float-end">
                            <a href="{{ route('tasks.index') }}" class="btn btn-primary">
                                <i class="bx bx-edit-alt me-1"></i> View Work Plan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive text-nowrap p-3">
                <table id="datatable1" class="table">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Due Date</th>
                            <th>Task</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach ($pendingUserTasks as $Task)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($Task->submit_date)->format('d F Y') }}</td>
                            <td>{{$Task->description}}</td>
                            <td>{{$Task->status}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!--/ Roles -->
    </div>
    <div class="col-12 col-md-6 col-lg-6">
        <!-- Permissions -->
        <div class="card mb-4"> 
            <div class="card-header">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <h5>Today's Completed</h5>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="float-end">
                            <a href="{{ route('tasks.index') }}" class="btn btn-primary">
                                <i class="bx bx-edit-alt me-1"></i> View Work Plan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive text-nowrap p-3">
                <table id="datatable2" class="table">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Due Date</th>
                            <th>Completed Date</th>
                            <th>Task</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach ($completeUserTasks as $Task)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($Task->submit_date)->format('d F Y') }}</td>
                            <td>{{ $Task->submit_by_date ? \Carbon\Carbon::parse($Task->submit_by_date)->format('d F Y, h:i A') : 'Task completed' }}</td>
                            <td>{{$Task->description}}</td>
                            <td>{{$Task->status}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endcan

{{-- Admin Part --}}
@can('View All Today Task')
<div class="row mb-4"> 
<div class="col-12 col-md-6 col-lg-6">
    <!-- Roles -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="row">
                <div class="col-12 col-md-6">
                    <h5>Today's All Pending</h5>
                </div>
                <div class="col-12 col-md-6">
                    <div class="float-end">
                        <a href="{{ route('asign_tasks.index') }}" class="btn btn-primary">
                            <i class="bx bx-edit-alt me-1"></i> View All Task
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap p-3">
            <table id="datatable3" class="table">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Start Date</th>
                        <th>Due Date</th>
                        <th>User Name</th>
                        <th>Task</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($pendingAdminTasks as $Task)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $Task->created_at->format('d F Y, h:i A') }}</td>
                        <td>{{ \Carbon\Carbon::parse($Task->submit_date)->format('d F Y') }}</td>
                        <td>{{ $Task->user->name }}</td>
                        <td>{{$Task->description}}</td>
                        <td>{{$Task->status}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!--/ Roles -->
</div>
<div class="col-12 col-md-6 col-lg-6">
    <!-- Permissions -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="row">
                <div class="col-12 col-md-6">
                    <h5>Today's All Completed</h5>
                </div>
                <div class="col-12 col-md-6">
                    <div class="float-end">
                        <a href="{{ route('asign_tasks.index') }}" class="btn btn-primary">
                            <i class="bx bx-edit-alt me-1"></i> View All Task
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap p-3">
            <table id="datatable4" class="table">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Due Date</th>
                        <th>Completed Date</th>
                        <th>User Name</th>
                        <th>Task</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($completeAdminTasks as $Task)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($Task->submit_date)->format('d F Y') }}</td>
                        <td>{{ $Task->submit_by_date ? \Carbon\Carbon::parse($Task->submit_by_date)->format('d F Y, h:i A') : 'Task completed' }}</td>
                        <td>{{ $Task->user->name }}</td>
                        <td>{{$Task->description}}</td>
                        <td>{{$Task->status}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!--/ Permissions -->
</div>
</div>
@endcan

<div class="card p-3">
    <div id="calendar"></div>
</div>

<script>
$(document).ready(function() {
    calendarEl = document.getElementById('calendar');
    calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: ['timeline', 'dayGrid', 'timeGrid', 'interaction'],
        editable: true,
        header: {
            left: 'today prev,next',
            center: 'title',
            right: 'timelineDay,timeGridWeek,dayGridMonth'
        },
        defaultView: 'dayGridMonth',
        displayEventEnd: true,
        selectable: true,
    });
    calendar.render();
});
</script>
@endsection
