<aside class="main-sidebar sidebar-dark-danger elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <span class="brand-text font-weight-light">University of Rajshahi</span>
    </a>
    <!--/Brand Logo -->

    <!-- Sidebar -->
    <div class="sidebar">

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                @php

                    $menu = [
                        '0' => 0,
                        '1' => 0,
                        '2' => 0,
                        '3' => 0,
                        '4' => 0,
                        '5' => 0,
                        '6' => 0,
                        '7' => 0,
                    ];

                    $link = [
                        '0' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '1' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '2' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '3' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '4' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0],
                        '5' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '6' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '7' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                    ];

                    if (isset($active_menu)) {
                        $menu[$active_menu] = 1;
                    }

                    if (isset($active_link)) {
                        $link[$active_menu][$active_link] = 1;
                    }

                @endphp

                <!--Dashboard menu[0]-->
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link @if ($menu['0']) active @endif ">
                        <i class="fas fa-palette"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <!--/Dashboard-->

                {{-- Teachers --}}
                <li class="nav-item">
                    <a href="{{ route('users.index') }}"
                        class="nav-link @if ($menu['1']) active @endif">
                        <i class="fas fa-users-cog"></i>
                        <p>Teachers</p>
                    </a>
                </li>
                {{-- Teachers --}}

                {{-- Officers/Staffs --}}
                <li class="nav-item">
                    <a href="{{ route('department-manager.index') }}"
                        class="nav-link @if ($menu['6']) active @endif">
                        <i class="fas fa-users-cog"></i>
                        <p>Officers/Staffs</p>
                    </a>
                </li>
                {{-- Officers/Staffs --}}

                {{-- courses --}}
                <li class="nav-item">
                    <a href="{{ route('courses.index') }}"
                        class="nav-link @if ($menu['2']) active @endif">
                        <i class="fas fa-book"></i>
                        <p>Courses</p>
                    </a>
                </li>
                {{-- courses --}}

                {{-- student group --}}
                <li class="nav-item">
                    <a href="{{ route('student_groups.index') }}"
                        class="nav-link @if ($menu['3']) active @endif">
                        <i class="fas fa-user-graduate"></i>
                        <p>Students</p>
                    </a>
                </li>
                {{-- student group --}}

                {{-- questions --}}
                @if (auth('web')->user()->role != 'DepartmentManager')
                    <li class="nav-item">
                        <a href="{{ route('questions.index') }}"
                            class="nav-link @if ($menu['4']) active @endif">
                            <i class="fas fa-question"></i>
                            <p>Questions For Feedback</p>
                        </a>
                    </li>
                @endif
                {{-- questions --}}

                {{-- Feedback events --}}
                <li class="nav-item">
                    <a href="{{ route('assessment_events.index') }}"
                        class="nav-link @if ($menu['5']) active @endif">
                        <i class="fas fa-calendar-alt"></i>
                        <p>Feedback Events</p>
                    </a>
                </li>
                {{-- Feedback events --}}

                {{-- change Logs --}}
                <li class="nav-item">
                    <a href="{{ route('change-logs') }}"
                        class="nav-link @if ($menu['7']) active @endif">
                        <i class="fas fa-user-shield"></i>
                        <p>Change Logs</p>
                    </a>
                </li>
                {{-- change Logs --}}

            </ul>

        </nav>
        <!-- /sidebar-menu -->

    </div>
    <!-- /sidebar -->

</aside>
