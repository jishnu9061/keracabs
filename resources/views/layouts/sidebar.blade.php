<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
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
            <a href="javascript: void(0);" class="has-arrow">
                <i class="bx bx-rupee"></i>
                <span data-key="t-pages">Price</span>
            </a>
            <ul class="sub-menu" aria-expanded="false">
                <li><a href="price.html" data-key="t-starter-page">Fare Fee</a></li>
                <li><a href="studentFee.html" data-key="t-maintenance">Student Fee </a></li>
            </ul>
        </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="bx bx-file"></i>
                        <span data-key="t-pages">Reports</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="tripReport.html" data-key="t-starter-page">Trip Report</a></li>
                        <li><a href="collectionReport.html" data-key="t-maintenance">collection Report</a></li>
                        <li><a href="farewiseReport.html" data-key="t-maintenance">Fare Wise Report</a></li>
                        <li><a href="stageReport.html" data-key="t-maintenance">Stage Report</a></li>
                        <li><a href="inspectorReport.html" data-key="t-maintenance">Inspector Report</a></li>
                    </ul>
                </li>

            </ul>


        </div>
        <!-- Sidebar -->
    </div>
</div>
