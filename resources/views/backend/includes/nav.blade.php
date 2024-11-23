<style>/* Align notification icon more to the left */
    #notification-icon {
        margin-right: 10px;
    }
    
    .dropdown-menu {
        border: 1px solid #ddd;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        border-radius: 8px;
        padding-top: 1rem;
        padding-bottom: 1rem;
    }
    
    .dropdown-header {
        font-weight: bold;
        color: #333;
    }
    
    #notification-list .btn-light {
        background-color: transparent;
        border: none;
        padding: 0;
        font-size: 1.2em;
    }
    
    #notification-list hr {
        margin: 0.5rem 0;
        border-top: 1px solid #e0e0e0;
    }
    
    #notification-count {
        font-size: 0.8em;
        padding: 3px 6px;
    }

    .notification-read {
    opacity: 0.6; /* Adjust this value for the desired fade effect */
    }
    /* Dark mode styling */
    body.dark-mode {
        background-color: #121212;
        color: #ffffff;
    }

    /* Additional styles for specific elements in dark mode */
    body.dark-mode .navbar {
        background-color: #333333;
    }
    body.dark-mode .dropdown-menu {
        background-color: #444444;
        color: #ffffff;
    }

    .layout-navbar a {
        text-decoration: none !important;
    }

    </style>

<div class="modal fade" id="logoutReasonModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Logout Reason</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <textarea id="logout-reason" class="form-control" placeholder="Enter your reason for logging out"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="submitLogout()">Submit</button>
            </div>
        </div>
    </div>
</div>

<!-- Navbar -->
<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <!-- Search -->
        <div class="navbar-nav align-items-center">
            <div class="nav-item d-flex align-items-center">
                <i class="bx bx-search fs-4 lh-0"></i>
                <input type="text" class="form-control border-0 shadow-none ps-1 ps-sm-2" placeholder="Search..." aria-label="Search..." />
            </div>
        </div>
        <!-- /Search -->

        <ul class="navbar-nav flex-row align-items-center ms-auto">
            <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Notification Icon with Count and Dropdown -->
        <li class="nav-item dropdown lh-1 me-4 position-relative">
            <!-- Dark Mode Toggle Icon -->
            <a href="javascript:void(0);" id="dark-mode-toggle" class="nav-item lh-1 me-3 text-decoration-none">
                <i class="fas fa-moon"></i>
            </a>

            <!-- Notification Icon -->
            <a href="#" id="notification-icon" class="text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-bell"></i>
                <span id="notification-count" class="badge bg-danger position-absolute top-0 start-100 translate-middle">0</span>
            </a>

            <!-- Notification Dropdown Menu -->
            <div class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="notification-icon" style="width: 350px; max-height: 400px; overflow-y: auto;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="dropdown-header">Notifications</h6>
                    <div>
                        <button class="btn btn-sm btn-link text-primary" id="mark-all-read">Mark All as Read</button>
                        <button class="btn btn-sm btn-link text-danger" id="clear-notifications">Clear All</button>
                    </div>
                </div>

                <div id="notification-list">
                    <p class="text-center text-muted">No new notifications</p>
                </div>
            </div>
        </li>
        
            <!-- User Name -->
            <li class="nav-item lh-1 me-3">
                <span class="fw-medium d-block">{{ Auth::user()->name }}</span>
            </li>

            <!-- User Dropdown -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                    @if (Auth::user()->profile_photo_path)
                        <img src="{{ Storage::url(Auth::user()->profile_photo_path) }}" alt="Profile Picture" width="50" height="50" class="rounded-circle">
                    @else
                        <img src="https://via.placeholder.com/50" alt="Default Profile" width="50" height="50" class="rounded-circle">
                    @endif
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.show') }}">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                    @if (Auth::user()->profile_photo_path)
                                        <img src="{{ Storage::url(Auth::user()->profile_photo_path) }}" alt="Profile Picture" width="50" height="50" class="rounded-circle">
                                    @else
                                        <img src="https://via.placeholder.com/50" alt="Default Profile" width="50" height="50" class="rounded-circle">
                                    @endif
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-medium d-block">{{ Auth::user()->name }}</span>
                                    <small class="text-muted">{{ Auth::user()->getRoleNames()->join(', ') }}</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.show') }}">
                            <i class="bx bx-user me-2"></i>
                            <span class="align-middle">My Profile</span>
                        </a>
                    </li>
                    @can('View Role Permission Menu')
                    <li>
                        <a class="dropdown-item" href="{{ route('settings') }}">
                            <i class="bx bx-cog me-2"></i>
                            <span class="align-middle">Role & Permission</span>
                        </a>
                    </li>
                    @endcan
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a id="logout-button" class="dropdown-item" href="javascript:void(0);">
                            <i class="bx bx-power-off me-2"></i>
                            <span class="align-middle">Log Out</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!--/ User Dropdown -->
        </ul>
    </div>
    {{-- <div id="nev_user_activity" class="d-none">
        @foreach ($activeUsers as $users)
            <div>
                <strong>{{ $users->name }}:</strong> 
                Logged in at {{ \Carbon\Carbon::parse($users->login_time)->format('d F Y, h:i A') }}
                <span id="nev-duration-{{ $users->id }}" class="text-muted">
                    (Active: <span class="active-nev-time" data-nev_id="{{ $users->id }}" data-nev_start="{{ \Carbon\Carbon::parse($users->login_time)->timestamp }}">00:00:00</span>)
                </span>
            </div>
        @endforeach
    </div> --}}
</nav>

<script>
function submitLogout() {
    const reason = $('#logout-reason').val();

    $.ajax({
        url: "{{ route('updateLogoutTime') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            logout_reason: reason
        },
        success: function() {
            $.ajax({
                url: "{{ route('logout') }}", 
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}" 
                },
                success: function() {
                    window.location.href = "{{ route('login') }}";
                },
                error: function() {
                    alert("Error logging out. Please try again.");
                }
            });
        },
        error: function() {
            alert("Error updating logout reason. Please try again.");
        }
    });
}

</script>

<script>
$(document).ready(function () {


const markAsReadUrlTemplate = "{{ route('notifications.markAsRead', ':id') }}";

// Ensure markAsRead is available globally
$(document).ready(function() {

    const $toggleIcon = $('#dark-mode-toggle');
    const darkModeClass = 'dark-mode';
    
    // Apply dark mode if user has previously enabled it
    if (localStorage.getItem('theme') === darkModeClass) {
        $('body').addClass(darkModeClass);
        $toggleIcon.html('<i class="fas fa-sun"></i>');
    } else {
        $toggleIcon.html('<i class="fas fa-moon"></i>');
    }
    
    // Toggle dark mode on icon click
    $toggleIcon.on('click', function () {
        $('body').toggleClass(darkModeClass);
        
        // Update the icon based on the current mode
        if ($('body').hasClass(darkModeClass)) {
            $toggleIcon.html('<i class="fas fa-sun"></i>');
            localStorage.setItem('theme', darkModeClass);
        } else {
            $toggleIcon.html('<i class="fas fa-moon"></i>');
            localStorage.removeItem('theme');
        }
    });

    // Stop the dropdown from closing when clicking inside it
    $('.dropdown-menu').on('click', function (e) {
        e.stopPropagation();
    });
    
    window.markAsRead = function(notificationId, element) {

        // Replace ':id' with the actual notification ID
        const url = markAsReadUrlTemplate.replace(':id', notificationId);

        $.ajax({
            url: url,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function() {
                $(element).addClass('notification-read');
            },
            error: function(xhr, status, error) {
                console.error('Error marking notification as read:', error);
            }
        });
    };
});
    // Fetch notifications when the dropdown is clicked
    $('#notification-icon').on('click', function () {
        fetchNotifications();
    });


    function fetchNotifications() {
    $.ajax({
        url: "{{ route('notifications.get') }}",
        type: 'GET',
        success: function (response) {
            let notificationList = $('#notification-list');
            notificationList.empty();

            if (response.data.notifications.length > 0) {
                response.data.notifications.forEach(function (notification) {
                    // Convert created_at to Dhaka time and format it
                    let createdAt = new Date(notification.created_at).toLocaleString('en-US', {
                        timeZone: 'Asia/Dhaka',
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric',
                        hour: 'numeric',
                        minute: 'numeric',
                        hour12: true
                    });

                    // Determine if the notification is read
                    let readClass = notification.is_read ? 'notification-read' : '';

                    // Each notification item with a separate delete button
                    let notificationItem = `
                        <div class="d-flex justify-content-between align-items-center mb-2 ${readClass}">
                            <a href="${notification.link}" class="text-decoration-none text-dark" 
                               onclick="markAsRead(${notification.id}, this)" style="flex-grow: 1;">
                                <div class="d-flex align-items-center">
                                    <!-- User Image -->
                                   <img src="${notification.user.profile_photo_path ? '{{ Storage::url('') }}' + notification.user.profile_photo_path : 'https://via.placeholder.com/50'}" 
                                    alt="User Image" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                    <div>
                                        <strong>${notification.title}</strong>
                                        <p class="mb-1" style="font-size: 0.9em; color: #555;">${notification.text}</p>
                                        <small class="text-muted">${createdAt}</small>
                                    </div>
                                </div>
                            </a>
                            <button class="btn btn-sm btn-light text-danger" onclick="deleteNotification(${notification.id})">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                        <hr>`;
                    notificationList.append(notificationItem);
                });
            } else {
                notificationList.html('<p class="text-center text-muted">No new notifications</p>');
            }
        },
        error: function (xhr, status, error) {
            console.error('Error fetching notifications:', error);
        }
    });
}

    window.deleteNotification = function (notificationId) {
        let url = "{{ route('notifications.delete', ':id') }}";
        url = url.replace(':id', notificationId);
        
        $.ajax({
            url: url,
            type: 'DELETE',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function () {
                fetchNotifications();  // Refresh notifications
                updateNotificationCount();  // Update notification count
            },
            error: function (xhr, status, error) {
                console.error('Error deleting notification:', error);
            }
        });
    };


    // Mark all notifications as read
    $('#mark-all-read').on('click', function () {

        $.ajax({
            url: "{{ route('notifications.markAllRead') }}",
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function () {
                fetchNotifications();
                updateNotificationCount();
            },
            error: function (xhr, status, error) {
                console.error('Error marking notifications as read:', error);
            }
        });
    });

// URL for fetching notification count
const notificationCountUrl = "{{ route('notifications.count') }}";

// Update notification count
function updateNotificationCount() {
    $.ajax({
        url: notificationCountUrl,
        type: 'GET',
        success: function (data) {
            const count = data.data.count; // Get the notification count
            const notificationCountElement = $('#notification-count');
            
            // Update the badge with the count
            if (count > 0) {
                notificationCountElement.text(count).show();  // Show count if greater than 0
            } else {
                notificationCountElement.hide();  // Hide badge if count is 0
            }
        },
        error: function (xhr, status, error) {
            console.error('Error updating notification count:', error);
        }
    });
}

// Periodically update notification count every 10 seconds
setInterval(updateNotificationCount, 10000);  // 10 seconds
setInterval(fetchNotifications, 10000);

// Call initially to load the count on page load
updateNotificationCount();

$('#clear-notifications').on('click', function() {
    $.ajax({
        url: "{{ route('notifications.clear') }}",  // Ensure this route is correct
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')  // Include CSRF token
        },
        success: function(response) {
            if (response.status) {
                // Optionally update the UI or refresh the notification list
                fetchNotifications(); // Refresh the notifications
                updateNotificationCount();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error clearing notifications:', error);
        }
    });
});

$('#logout-button').on('click', function (e) {
    e.preventDefault();
    
    const currentTime = new Date().toLocaleString("en-US", { timeZone: "Asia/Dhaka" });
    const hours = new Date(currentTime).getHours();

    if (hours < 18) {  // Before 6 pm
        $('#logoutReasonModal').modal('show');  // Show reason modal
    } else {
        // Show instant feedback to user
        $('#logout-button').text('Logging Out...'); 
        
        // Perform logout in the background
        $.post("{{ route('logout') }}", { _token: "{{ csrf_token() }}" }, function() {
            window.location.href = "{{ route('login') }}";
        });
    }
});
});

</script>

{{-- <script>
    $(document).ready(function() {
    // Function to format seconds into HH:MM:SS
    function formatTime(seconds) {
        let hrs = Math.floor(seconds / 3600);
        let mins = Math.floor((seconds % 3600) / 60);
        let secs = seconds % 60;
        return `${hrs.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }

    // Counter to track intervals
    let ajaxCounter = 0;

    // Update each user's active time every second
    setInterval(function () {
        $('.active-nev-time').each(function () {
            const userId = $(this).data('nev-id');
            const startTime = $(this).data('nev-start');
            const currentTime = Math.floor(Date.now() / 1000);
            const activeDuration = currentTime - startTime;

            // Update the displayed active time
            $(this).text(formatTime(activeDuration));

            // Increment the counter
            ajaxCounter++;

            // Send an Ajax request every 10 seconds
            if (ajaxCounter % 10 === 0) {
                $.ajax({
                    url: "{{ route('updateLoginTime') }}",
                    type: 'POST',
                    data: {
                        login_id: userId,
                        active_seconds: activeDuration,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        // Optionally, handle success feedback
                    },
                    error: function () {
                    }
                });
            }
        });
    }, 1000); // Update every second
    });
</script> --}}
<script>
    $(document).ready(function () {
        let activeSeconds = 0; // Initialize active seconds

        function updateLoginTime() {
            activeSeconds += 10; // Increment by 10 seconds

            $.ajax({
                url: "{{ route('updateNevLoginTime') }}", // Named route
                type: "POST",
                data: {
                    active_seconds: activeSeconds, // Send current active seconds
                    _token: "{{ csrf_token() }}" // CSRF token for security
                },
                success: function (response) {
                    if (response.login_hour) {

                    } else {
                        console.error(response.error || 'Failed to update login time');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }

        // Call the function every 10 seconds
        setInterval(updateLoginTime, 10000);
    });
</script>




