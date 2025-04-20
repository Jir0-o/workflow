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

    .notifications-read {
    opacity: 0.6; 
    }

    body.dark-mode {
        background-color: #121212;
        color: #ffffff;
    }

    /* Navbar in Dark Mode */
    body.dark-mode .navbar,
    body.dark-mode {
        background-color: #333333;
        color: #ffffff;
    }

    body.dark-mode .navbar input,
    body.dark-mode .navbar a {
        color: #ffffff;
    }

    /* Sidebar in Dark Mode */
    body.dark-mode .layout-menu {
        background-color: #222222;
        color: #ffffff;
    }

    body.dark-mode .layout-menu a {
        color: #ffffff;
    }

    body.dark-mode .menu-inner {
        background-color: #222222;
        color: #ffffff;
    }

    /* Cards in Dark Mode */
    body.dark-mode .card {
        background-color: #444444;
        color: #ffffff;
        border: none;
    }

    /* Tables in Dark Mode */
    body.dark-mode .table {
        background-color: #333333;
        color: #ffffff;
    }

    body.dark-mode .table th,
    body.dark-mode .table td {
        border-color: #555555;
        color: #ffffff;
    }

    /* Additional Elements */
    body.dark-mode a {
        color: #00bcd4; /* Adjust color for links */
    }

    body.dark-mode .tab-content {
        background-color: #333333;
        color: #ffffff;
    }

    body.dark-mode .table .permissions-item {
    color: #ffffff !important;
    }

    /* Dark mode for the top navbar */
    body.dark-mode .layout-navbar {
        background-color: #333333; /* Dark background */
        color: #181616; /* Light text color */
        border-bottom: 1px solid #444444; /* Optional: subtle border for separation */
    }

    /* Dark mode for the navbar links and icons */
    body.dark-mode .layout-navbar a,
    body.dark-mode .layout-navbar .nav-item i,
    body.dark-mode .layout-navbar .form-control {
        color: #1d1b1b; /* Adjust text color for better visibility */
    }

    /* Dark mode for the notification dropdown */
    body.dark-mode .notification-dropdown {
        background-color: #444444;
        color: #ffffff;
        border: none;
    }

    /* Dark mode for the notification badge */
    body.dark-mode #notification-count {
        background-color: #ff4444; /* Red for better visibility */
        color: #ffffff;
    }

    /* Dark mode for the search input */
    body.dark-mode .layout-navbar .form-control {
        background-color: #555555; /* Darker background for inputs */
        color: #ffffff;
        border: 1px solid #666666; /* Optional border */
    }

    /* Dark mode for dropdown menus */
    body.dark-mode .dropdown-menu {
        background-color: #444444;
        color: #ffffff;
        border: none;
    }

    /* Dark mode for user profile dropdown */
    body.dark-mode .dropdown-menu .dropdown-item {
        color: #ffffff;
    }

    body.dark-mode .dropdown-menu .dropdown-item:hover {
        background-color: #555555; /* Highlight on hover */
    }

    body.dark-mode .text-500 {
    color: #ffffff !important; /* Forces white text */
    }

    /* Make all h5 tags white */
    body.dark-mode .h5 {
        color: #ffffff !important;
    }

    /* For Dark Mode */
    body.dark-mode h5 {
        color: #ffffff !important; /* Ensures white text in dark mode */
    }

    /* Dark mode styles for the notice board */
    body.dark-mode .notice-board {
        background-color: #444444; /* Dark background for notice board */
        color: #ffffff; /* White text for visibility */
        border: 1px solid #555555; /* Softer border for dark mode */
    }

    /* Dark mode styles for notice card */
    body.dark-mode .notice-card {
        background-color: #555555; /* Darker background for each notice card */
        color: #ffffff; /* Ensure text is visible */
        border: 1px solid #666666; /* Slight border for card separation */
        border-radius: 8px;
        padding: 10px;
    }

    /* Dark mode for header text */
    body.dark-mode .notice-header h4 {
        color: #ffffff;
    }
    body.dark-mode h3 {
        color: #ffffff;
    }

    /* Dark mode for notice details and message */
    body.dark-mode .notice-details,
    body.dark-mode .notice-message {
        color: #dddddd; /* Slightly lighter text for better readability */
    }

    /* Dark mode for author and date */
    body.dark-mode .notice-author,
    body.dark-mode .notice-date {
        color: #bbbbbb; /* Subtle color for secondary info */
    }

    /* Ensure list points are also visible */
    body.dark-mode .notice-points li {
        color: #dddddd;
    }

    /* Ensure the entire top section adapts to dark mode */
    body.dark-mode .app-brand {
        background-color: #222222; /* Match the dark mode background */
        color: ##222222; /* Ensure text color is visible */
        border-bottom: 1px solid #333333; /* Optional: Add a divider */
    }

    /* Default image styling */
    .side-nav-logo {
        height: 40px;
        background-color: transparent;
        display: block;
        margin: auto; /* Center the image */
    }

    /* Dark mode adjustments for the image container */
    body.dark-mode .app-brand-logo .side-nav-logo {
        filter: brightness(0.9);
        background-color: #222222;
    }

    /* Modal Background and Content */
    body.dark-mode .modal-content {
        background-color: #333; /* Dark background for modal content */
        color: #ffffff; /* White text for better visibility */
    }

    /* Modal Header */
    body.dark-mode .modal-header {
        background-color: #444; /* Slightly darker background for header */
        color: #ffffff; /* Ensure the text in the header is white */
    }

    body.dark-mode .modal-header .btn-close {
        filter: invert(1); /* Inverts the close button icon for dark mode */
    }

    /* Modal Body */
    body.dark-mode .modal-body {
        background-color: #444; /* Dark background for body */
    }

    /* Form Inputs, Selects, and Textareas */
    body.dark-mode .form-control {
        background-color: #555; /* Dark background for inputs */
        color: #ffffff; /* White text inside inputs */
        border: 1px solid #888; /* Lighter border for inputs */
    }

    /* Input and Select Focus State */
    body.dark-mode .form-control:focus {
        border-color: #007bff; /* Highlight border color on focus */
        background-color: #444; /* Maintain the dark background on focus */
    }

    /* Modal Footer */
    body.dark-mode .modal-footer {
        background-color: #444; /* Dark background for footer */
    }

    /* Button Styling */
    body.dark-mode .btn-primary {
        background-color: #007bff; /* Primary button color */
        border-color: #007bff;
    }

    body.dark-mode .btn-secondary {
        background-color: #555; /* Secondary button in dark mode */
        border-color: #555;
    }

    /* Error Messages */
    body.dark-mode .text-danger {
        color: #ff6b6b; /* Red color for error text */
    }

    /* Dark Mode Styles for Profile */
    .dark-mode #profile-container {
        background-color: #121212;
        color: #ffffff;
    }

    .dark-mode #profile-container .card,
    .dark-mode #profile-container .form-section,
    .dark-mode #profile-container .table {
        background-color: #222222;
        color: #ffffff;
        border: 1px solid #444444;
    }

    /* Form Labels, Inputs, Buttons */
    .dark-mode #profile-container label {
        color: #cccccc;
    }

    .dark-mode #profile-container input,
    .dark-mode #profile-container textarea,
    .dark-mode #profile-container select {
        background-color: #333333;
        color: #ffffff;
        border: 1px solid #555555;
    }

    .dark-mode #profile-container button {
        background-color: #444444;
        color: #1a1717;
        border: 1px solid #666666;
    }

    .dark-mode #profile-container button:hover {
        background-color: #555555;
    }

    .dark-mode #profile-container .underline {
        color: #bbbbbb;
    }

    .dark-mode #profile-container .underline:hover {
        color: #ffffff;
    }

    /* Header in Dark Mode */
    .dark-mode header, 
    .dark-mode header h2 {
        background-color: #121212; /* Match dark mode background */
        color: #ffffff; /* Match dark mode text */
    }

    /* General Container in Dark Mode */
    .dark-mode .max-w-7xl {
        background-color: #121212; /* Match dark mode background */
        color: #ffffff; /* Match dark mode text */
        border: 1px solid #444444; /* Optional border for distinction */
    }

    /* Section Borders in Dark Mode */
    .dark-mode .section-border {
        border-top: 1px solid #444444;
    }

    /* Additional Livewire Forms in Dark Mode */
    .dark-mode .form-section,
    .dark-mode .livewire-component {
        background-color: #222222;
        color: #ffffff;
        border: 1px solid #444444;
    }

    /* Buttons in Dark Mode */
    .dark-mode button {
        background-color: #444444;
        color: #ffffff;
        border: 1px solid #666666;
    }

    .dark-mode button:hover {
        background-color: #555555;
    }

    /* Select2 Dropdown in Dark Mode */
    .dark-mode .select2-container--default .select2-selection--multiple {
        background-color: #222222; /* Dropdown background */
        color: #ffffff; /* Text color */
        border: 1px solid #444444; /* Optional border */
    }

    .dark-mode .select2-container--default .select2-results__option {
        background-color: #222222; /* Dropdown options background */
        color: #ffffff; /* Options text color */
    }

    .dark-mode .select2-container--default .select2-results__option--highlighted {
        background-color: #444444; /* Highlighted option background */
        color: #ffffff; /* Highlighted option text */
    }

    .dark-mode .select2-container--default .select2-search--dropdown .select2-search__field {
        background-color: #222222; /* Search input background */
        color: #ffffff; /* Search input text */
        border: 1px solid #444444; /* Search input border */
    }
    
    .layout-navbar a {
        text-decoration: none !important;
    }

    .dark-mode .dark-mode-text {
    color: black !important;
    }

    .dark-mode .white-mode {
    color: white !important;
    }

    
        /* Dark Mode Styles */
        body.dark-mode {
            background-color: #1a1a1a;
            color: #c0c0c0;
        }

        body.dark-mode .profile-container {
            background: #222;
        }

        body.dark-mode .profile-header {
            background: #333;
        }

        body.dark-mode .username {
            color: #f0f0f0;
        }

        body.dark-mode .stats,
        body.dark-mode .time {
            color: #bbb;
        }

        body.dark-mode .buttons button {
            background: #444;
        }

        body.dark-mode .buttons button:hover {
            background: #555;
        }

        body.dark-mode .tablink {
            background: #333;
        }

        body.dark-mode .tablink.active {
            background: #555;
        }

        body.dark-mode .tabcontent {
            background: #282828;
        }

        body.dark-mode .section {
            background: #333;
        }

        body.dark-mode th {
            background: #333;
        }

        body.dark-mode tbody tr:hover {
            background: #444;
        }

        body.dark-mode .signature {
            color: #bbb;
        }

        /* Mode Toggle Button */
        .mode-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #444;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .mode-toggle:hover {
            background: #555;
        }

        body.dark-mode .mode-toggle {
            background: #444;
        }

        body.dark-mode .mode-toggle:hover {
            background: #555;
        }
        /* dark mode for user profile pop up */
        body.dark-mode .user-profile-popup {
            background: #333 !important;  /* Darker background */
            color: #fff !important; /* Light text for contrast */
            border: 1px solid #777; /* Subtle border */
        }

        body.dark-mode .user-profile-popup h5,
        body.dark-mode .user-profile-popup h6,
        body.dark-mode .user-profile-popup p {
            color: #ddd !important; /* Make all text readable */
        }

        body.dark-mode .user-profile-popup .badge {
            background: #444 !important; /* Adjust badge colors */
            color: #fff !important;
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
            <div class="dropdown-menu dropdown-menu-end p-3 notification-dropdown" aria-labelledby="notification-icon" style="width: 350px; max-height: 400px; overflow-y: auto;">
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
                            <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" alt="Profile Picture" width="50" height="50" class="rounded-circle">
                        @else
                            <img src={{ asset ('default-profile.jpg')}} alt="Default Profile" width="50" height="50" class="rounded-circle">
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
                                            <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" alt="Profile Picture" width="50" height="50" class="rounded-circle">
                                        @else
                                            <img src={{ asset ('default-profile.jpg')}} alt="Default Profile" width="50" height="50" class="rounded-circle">
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
                    <li>
                        <a class="dropdown-item" href="{{ route('working_profile.index') }}">
                            <i class="bx bx-briefcase me-2"></i>
                            <span class="align-middle">Work Profile</span>
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

    // Stop the notification dropdown from closing when clicking inside it
    $('.notification-dropdown').on('click', function (e) {
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
                $(element).addClass('notification-read')
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
                    let readClass = notification.is_read == 1 ? 'notifications-read' : '';

                    // Check if dark mode is enabled
                    const isDarkMode = document.body.classList.contains('dark-mode');

                    let notificationItem = `
                    <div class="d-flex justify-content-between align-items-center mb-2 ${readClass}">
                        <a href="${notification.link}" class="text-decoration-none ${isDarkMode ? 'text-light' : 'text-dark'}" 
                        onclick="markAsRead(${notification.id}, this)" style="flex-grow: 1;">
                            <div class="d-flex align-items-center">
                                <!-- User Image -->
                                <img src="${notification.user.profile_photo_path ? '/storage/' + notification.user.profile_photo_path : '/default-profile.jpg'}" 
                                alt="User Image" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                <div>
                                    <strong class="${isDarkMode ? 'text-light' : 'text-dark'}">${notification.title}</strong>
                                    <p class="mb-1" style="font-size: 0.9em; color: ${isDarkMode ? '#bbb' : '#555'};">${notification.text}</p>
                                    <small class="text-muted">${createdAt}</small>
                                </div>
                            </div>
                        </a>
                        <button class="btn btn-sm ${isDarkMode ? 'btn-dark text-light' : 'btn-light text-danger'}" onclick="deleteNotification(${notification.id})">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                    <hr style="border-color: ${isDarkMode ? '#555' : '#ddd'};">`;

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

    if (hours < 18) {  
        $('#logoutReasonModal').modal('show'); 
    } else {
        
        $('#logout-button').text('Logging Out...'); 
        
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




