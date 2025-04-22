        <!-- Menu -->
        <style>
            .layout-menu a {
                text-decoration: none !important;
            }
            .unicorn-logo {
                width: 60px;
            }

            .side-nav-logo {
                width: 200px;       
                position: relative;
                top: -10px; 
            }
        </style>
        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
            <div class="app-brand demo">
                <a href="{{route('dashboard.index')}}" class="app-brand-link">
                    <span class="app-brand-logo demo">
                        <svg width="25" viewBox="0 0 25 42" version="1.1" xmlns="http://www.w3.org/2000/svg"
                            xmlns:xlink="http://www.w3.org/1999/xlink">
                            <!-- SVG content here -->
                        </svg>
                    <img 
                        src="{{ asset('storage/profile-photos/store_photos/unicorn-removebg-preview.png') }}" 
                        alt="Unicorn Logo" 
                        class="side-nav-logo"
                    />
                    </span>
                </a>

                <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                    <i class="bx bx-chevron-left bx-sm align-middle"></i>
                </a>
            </div>

            <div class="menu-inner-shadow"></div>

            <ul class="menu-inner py-1">
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">Dashboard Section</span>
                </li>
                <!-- Dashboard -->
                <li class="menu-item">
                    <a href="{{route('dashboard.index')}}"
                        class="menu-link">
                        <i class="menu-icon tf-icons bx bx-home"></i>
                        <div data-i18n="Dashboard">Dashboard</div>
                    </a>
                </li>
                <!-- Layouts -->
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">Manage Task Section</span>
                </li>
                @can('View Assign Task')
                <li class="menu-item">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-task"></i>
                        <div data-i18n="Layouts">Manage Task & Work Plan</div>
                    </a>
                    @endcan
                    <ul class="menu-sub">
                        @can('View manage work plan')
                        <li class="menu-item">
                            <a href="{{ route('manage_work.index') }}" class="menu-link">
                                <div data-i18n="Without menu">Manage Work Plan</div>
                            </a>
                        </li>
                        @endcan
                        @can('View Assign Task')
                        <li class="menu-item">
                            <a href="{{ route('asign_tasks.index') }}" class="menu-link">
                                <div data-i18n="Without menu">Assign Task</div>
                            </a>
                        </li>
                        @endcan
                        @can('View Project Details')
                        <li class="menu-item">
                            <a href="{{route('project_title.index')}}" class="menu-link">
                                <div data-i18n="Container">Project Details</div>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @can('View Task & Plan Details')
                <li class="menu-item">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-calendar-check"></i>
                        <div data-i18n="Layouts">Task & Work Plan</div>
                    </a>
                    <ul class="menu-sub">
                        @can('View Work Plan')
                        <li class="menu-item">
                            <a href="{{ route('work_plan.index') }}" class="menu-link">
                                <div data-i18n="Without navbar">Work Plan</div>
                            </a>
                        </li>
                        @endcan
                        @can('View Task Details')
                        <li class="menu-item">
                            <a href="{{ route('tasks.index') }}" class="menu-link">
                                <div data-i18n="Without navbar">Task Details</div>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan
                @can('View Login Details Tab')
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">Login Details Section</span>
                </li>
                 <li class="menu-item">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-user-check"></i>
                        <div data-i18n="Layouts">Login Details</div>
                    </a>
                    @can('View User login Details')
                    <ul class="menu-sub">
                        <li class="menu-item">
                            <a href="{{ route('login_details.index') }}" class="menu-link">
                                <div data-i18n="Without menu">User Login Details</div>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan
                @if(auth()->user()->can('View Other Tabs') || auth()->user()->can('View Application'))
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">@if (auth()->user()->hasRole('Super Admin')) Report & Other Section @else Other Section @endif</span>
                </li>
                <li class="menu-item">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                        <div data-i18n="Layouts">@if (auth()->user()->hasRole('Super Admin')) Other Tabs @else Application Tab @endif</div>
                    </a>
                    <ul class="menu-sub">
                        @can('View Notice Board')
                        <li class="menu-item">
                            <a href="{{ route('notice.index') }}" class="menu-link">
                                <div data-i18n="Without menu">Notice Board</div>
                            </a>
                        </li>
                        @endcan
                        @can('View Application')
                        <li class="menu-item">
                            <a href="{{ route('application.index') }}" class="menu-link">
                                <div data-i18n="Without navbar">Application</div>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan
                @if(auth()->user()->can('View Report') || auth()->user()->can('View Login Report'))
                <li class="menu-item">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-bar-chart"></i>
                        <div data-i18n="Layouts">Report Section</div>
                    </a>
                    <ul class="menu-sub">
                        @can('View Report')
                        <li class="menu-item">
                            <a href="{{route('report.index')}}" class="menu-link">
                                <div data-i18n="Container">Work Plan Report</div>
                            </a>
                        </li>
                        @endcan
                        @can('View Login Report')
                        <li class="menu-item">
                            <a href="{{ route('loginReport.view') }}" class="menu-link">
                                <div data-i18n="Without navbar">Login Report</div>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">@if (auth()->user()->hasRole('Super Admin')) Mail Management @endif</span>
                </li>
                <li class="menu-item">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                        <div data-i18n="Layouts">@if (auth()->user()->hasRole('Super Admin')) Mail Management @endif</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item">
                            <a href="{{route('mail_send.index')}}" class="menu-link">
                                <div data-i18n="Container">Mail Create</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{ route('mail_send.create') }}" class="menu-link">
                                <div data-i18n="Without navbar">Mail Logs</div>
                            </a>
                        </li>
                    </ul>
                </li>
                @can('View Role Permission Menu')
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text"> Role and Permission</span>
                </li>
                <!-- Settings -->
                <li class="menu-item">
                    <a href="{{route('settings')}}"
                        class="menu-link">
                        <i class="menu-icon bx bx-cog"></i>
                        <div data-i18n="Settings">Role Permission & User</div>
                    </a>
                </li>
                @endcan
            </ul>
        </aside>
        <!-- / Menu -->
