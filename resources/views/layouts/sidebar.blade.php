<div class="vertical-menu">
    <div data-simplebar class="h-100">
      <!--- Sidemenu -->
      <div id="sidebar-menu">
        <!-- Left Menu Start -->
        <ul class="metismenu list-unstyled" id="side-menu">
          <li class="menu-title" data-key="t-menu">Menu</li>

          <li>
            <a href="{{ route('admin.dashboard') }}">
              <i class="bx bx-layer"></i>
              <span data-key="t-dashboard">Dashboard</span>
            </a>
          </li>
          <li>
            <a href="{{ route('booking.index') }}" class="">
              <i class="bx bxs-report"></i>
              <span data-key="t-ui-elements">Booking</span>
            </a>
          </li>
          <li>
            <a href="{{ route('register.index') }}">
              <i class="bx bx-user"></i>
              <span data-key="t-dashboard">Registration</span>
            </a>
          </li>
          <li>
            <a href="javascript: void(0);" class="has-arrow">
              <i class="bx bx-image"></i>
              <span data-key="t-components">Slider</span>
            </a>
            <ul class="sub-menu" aria-expanded="false">
              <li>
                <a href="{{ route('slider.create') }}" data-key="t-alerts">Add Slider</a>
              </li>
              <li>
                <a href="{{ route('slider.index') }}" data-key="t-buttons"
                  >View Slider</a
                >
              </li>
            </ul>
          </li>
          <li>
            <a href="javascript: void(0);" class="has-arrow">
              <i class="bx bx-pen"></i>
              <span data-key="t-components">Blogs</span>
            </a>
            <ul class="sub-menu" aria-expanded="false">
              <li>
                <a href="{{ route('blog.create') }}" data-key="t-alerts">Add Blogs</a>
              </li>
              <li>
                <a href="{{ route('blog.index') }}" data-key="t-buttons">View Blogs</a>
              </li>
            </ul>
          </li>

          <li>
            <a href="{{ route('contact.index') }}" class="">
              <i class="bx bx-envelope"></i>
              <span data-key="t-ui-elements">Contact</span>
            </a>
          </li>
        </ul>
      </div>
      <!-- Sidebar -->
    </div>
  </div>
