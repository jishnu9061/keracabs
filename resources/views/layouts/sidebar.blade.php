<div class="vertical-menu">

    <div data-simplebar class="h-100">
        <div id="sidebar-menu">
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" data-key="t-menu">Menu</li>


                <li>
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="bx bx-home"></i>
                        <span data-key="t-dashboard">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('manager.index') }}">
                        <i class="bx bx-user"></i>
                        <span data-key="t-dashboard">Manager</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('device.index') }}">
                        <i class="bx bx-mobile"></i>
                        <span data-key="t-dashboard">Devices</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('route.index') }}">
                        <i class="bx bx-map"></i>
                        <span data-key="t-dashboard">Routes</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <i class="bx bx-rupee"></i>
                        <span data-key="t-pages">Price</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('fare.index') }}" data-key="t-starter-page">Fare Fee</a></li>
                        <li><a href="{{ route('student.index') }}" data-key="t-maintenance">Student Fee </a></li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <i class="bx bx-file"></i>
                        <span data-key="t-pages">Reports</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('trip.index') }}" data-key="t-starter-page">Trip Report</a></li>
                        <li><a href="{{ route('trip.collection') }}" data-key="t-maintenance">collection Report</a></li>
                        <li><a href="{{ route('trip.fare') }}" data-key="t-maintenance">Fare Wise Report</a></li>
                        <li><a href="{{ route('trip.stage') }}" data-key="t-maintenance">Stage Report</a></li>
                        <li><a href="{{ route('trip.inspector') }}" data-key="t-maintenance">Inspector Report</a></li>
                    </ul>
                </li>

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
