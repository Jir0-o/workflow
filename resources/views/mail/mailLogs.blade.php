@extends('layouts.master')
@section('content')

<style>
.cke_notification_message,
.cke_notifications_area,
.cke_button__about_icon,
.cke_button__about {
    display: none !important;
}
</style>
    

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <h4 class="py-2 m-4"><span class="text-muted fw-light">Mail Logs</span></h4>

    <div class="row mt-5">
        <div class="col-12 col-md-12 col-lg-12">
            <div class="card p-lg-4 p-2">
                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane active" id="Pending" role="tabpanel" aria-labelledby="home-tab">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <h5>All Logs</h5>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="float-end d-flex gap-2">
                                            <button type="button" class="btn btn-success" id="sendDailyReport">
                                                <i class="bx bx-calendar-check me-1"></i> Daily Report
                                            </button>
                                    
                                            <button type="button" class="btn btn-info" id="sendMonthlyReport">
                                                <i class="bx bx-calendar-event me-1"></i> Monthly Report
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="table-responsive text-nowrap p-3">
                                <table id="datatable1" class="table">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Name</th>
                                            <th>Mail Address</th>
                                            <th>Mail Type</th>
                                            <th>Send Time</th>
                                            <th>Status</th>
                                            <th>Send By</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($logs as $key => $log)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $log->name }}</td>
                                            <td>{{ $log->mailAddress->email_address ?? '' }}</td>
                                            <td>{{ $log->mail_type }}</td>
                                            <td>{{ \Carbon\Carbon::parse($log->mail_date)->format('d F Y, h:i A') }}</td>
                                            <td> @if ($log->status == 1)
                                                <span class="badge bg-success">Send</span>
                                            @else
                                                <span class="badge bg-danger">Not Send</span>
                                            @endif
                                            </td>
                                            <td>@if($log->is_active == 1)
                                                <span class="badge bg-gray">Menually</span>
                                            @else
                                                <span class="badge bg-warning">System Automatic</span>
                                            @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>                            
                        </div>
                    </div>  
                </div>
            </div>
        </div>
    </div>


<script>
    $(document).ready(function () {
        $('#sendDailyReport').click(function () {
            Swal.fire({
                title: 'Send Daily Report?',
                text: "Do you want to send the daily report to all user emails?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, send it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading alert
                    Swal.fire({
                        title: 'Sending...',
                        text: 'Please wait while the report is being sent.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '{{ route("send.daily.report") }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            Swal.fire('Sent!', 'Daily report email has been sent.', 'success');
                            location.reload();
                        },
                        error: function () {
                            Swal.fire('Error!', 'Something went wrong.', 'error');
                        }
                    });
                }
            });
        });


        $('#sendMonthlyReport').click(function () {
            Swal.fire({
                title: 'Send Monthly Report?',
                text: "Do you want to send the monthly report to all user emails?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, send it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {

                    Swal.fire({
                        title: 'Sending...',
                        text: 'Please wait while the report is being sent.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '{{ route("send.monthly.report") }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            Swal.fire('Sent!', 'Monthly report email has been sent.', 'success');
                            location.reload();
                        },
                        error: function () {
                            Swal.fire('Error!', 'Something went wrong.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
    
@endsection
