<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('home') }}" class="brand-link">
      <img src="{{URL::to('public/bower_components/admin-lte/dist/img/AdminLTELogo.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">{{ config('app.name', 'Laravel') }}</span>
    </a> 

    <!-- Sidebar Left -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
        @if(\Auth::user())
            @if(Auth::user()->avatar != null) 
            <img src="{{URL::to('storage/app/public').'/'.Auth::user()->avatar}}" class="img-circle elevation-2" alt="User Image">
            @endif
            @if(Auth::user()->avatar == null)
               @if(Auth::user()->gender == 'Male')
               <img src="{{URL::asset('public/bower_components/admin-lte/dist/img/fb-male.jpg')}}" class="img-circle elevation-2" alt="User Image">
               @else 
               <img src="{{URL::asset('public/bower_components/admin-lte/dist/img/female-fb.jpg')}}" class="img-circle elevation-2" alt="User Image">
               @endif
            @endif
        @else 
        <img src="{{URL::asset('public/bower_components/admin-lte/dist/img/avatar5.png')}}" class="img-circle elevation-2" alt="User Image">
        @endif
        </div>
        <div class="info">
          @if(\Auth::user())
          <a href="{{ route('profile.show', ['username' => Auth::user()->username]) }}" class="d-block">{{Auth::user()->name}}</a>
          @endif
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <!-- Authentication Links -->
          @can('View Roles')
          <li class="nav-item">
              <a href="{{ route('roles.index') }}" class="nav-link">
                <i class="nav-icon fas fa-tasks"></i>
                <p>Manage Roles</p>
              </a>
          </li>
          @endcan 
          @can('View Users')
          <li class="nav-item">
              <a href="{{ route('users.index') }}" class="nav-link">
                <i class="nav-icon fas fa-user"></i>
                <p>Manage Users</p>
              </a>
          </li>
          @endcan
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-registered"></i>
              <p>
                Students Registrars
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('student-registrars.index') }}" class="nav-link">
                  <i class="far fa-dot-circle nav-icon"></i>
                  <p>Student</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('father-registrars.index') }}" class="nav-link">
                  <i class="far fa-dot-circle nav-icon"></i>
                  <p>Father</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('mother-registrars.index') }}" class="nav-link">
                  <i class="far fa-dot-circle nav-icon"></i>
                  <p>Mother</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="{{ route('menus.index') }}" class="nav-link">
              <i class="nav-icon fas fa-bars"></i>
              <p>Menus</p>
            </a>
          </li>
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-school"></i>
              <p>
                Fakultas & Prodi
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Daftar Fakultas & Prodi</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Daftar Matkul per Prodi</p>
                </a>
              </li>
            </ul>
          </li>    
            <li class="nav-item">
                <a href="" class="nav-link">
                  <i class="nav-icon fas fa-building"></i>
                  <p>Denah & Tempat</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="" class="nav-link">
                  <i class="nav-icon fas fa-info"></i>
                  <p>About Us</p>
                </a>
            </li>
            <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-copy"></i>
              <p>
                Master Data
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Fasilitas</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Gedung</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Lantai</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Ruangan</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Kelas</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Kantor</p>
                </a>
              </li>
            </ul>
          </li> <!-- Master Data -->
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
  