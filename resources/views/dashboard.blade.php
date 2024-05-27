@extends('layouts.master')
@section('content')
<div class="row row-cols-1 row-cols-md-4">
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

    <!-- <div class="col mb-4">
        <div class="card total-rubrics-hover" style="height: 135px;">
            <div class="d-flex justify-content-between mb-3">
                <div class="card-body">
                    <h6 class="d-block text-500 font-medium mb-3">TOTAL RUBRICS</h6>
                    <div class="text-900 fs-4" id="total_Rubric"></div>
                </div>
                <div class="d-flex align-items-center justify-content-center rounded"
                    style="width: 2.5rem; height: 2.5rem; margin-top: 10px; margin-right: 5px">
                    <i class='bx bxs-bar-chart-alt-2'></i>
                </div>
            </div>
        </div>
    </div> -->
</div>

<div class="card p-3">
    <div id="calendar">

    </div>
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