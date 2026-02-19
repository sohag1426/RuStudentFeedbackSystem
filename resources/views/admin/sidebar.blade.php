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
                    <a href="{{ route('admin-dashboard') }}" class="nav-link @if ($menu['0']) active @endif">
                        <i class="fas fa-palette"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <!--/Dashboard-->

                {{-- Login History --}}
                <li class="nav-item">
                    <a href="{{ route('admin-login-logs') }}" class="nav-link @if ($menu['1']) active @endif">
                        <i class="fas fa-user-shield"></i>
                        <p>Login History</p>
                    </a>
                </li>
                {{-- Login History --}}

            </ul>

        </nav>
        <!-- /sidebar-menu -->

    </div>
    <!-- /sidebar -->

</aside>