<header class="bg-primary py-2">
    <div class="container">
        <div class="header-container">
            <!-- Logo and brand name -->
            <a class="header-brand" href="{{ route('home') }}" title="Home">
                <i class="fas fa-graduation-cap"></i>
                <span class="d-none d-lg-inline">Classroom</span>
                <span class="d-lg-none">CMS</span>
            </a>
            
            <!-- Main navigation -->
            @auth
            <nav class="header-nav d-none d-md-flex">
                <a class="nav-item py-1 px-2" href="{{ route('assignments.index') }}" title="Assignments">
                    <i class="fas fa-tasks"></i> <span>Assignments</span>
                </a>
                <a class="nav-item py-1 px-2" href="{{ route('challenges.index') }}" title="Challenges">
                    <i class="fas fa-puzzle-piece"></i> <span>Challenges</span>
                </a>
                
                @if(Auth::user()->isTeacher())
                <a class="nav-item py-1 px-2" href="{{ route('students.index') }}" title="Manage">
                    <i class="fas fa-users"></i> <span>Manage</span>
                </a>
                <a class="nav-item py-1 px-2" href="{{ route('submissions.index') }}" title="Submissions">
                    <i class="fas fa-clipboard-check"></i> <span>Submissions</span>
                </a>
                @endif
                
                @if(Auth::user()->isStudent())
                <a class="nav-item py-1 px-2" href="{{ route('submissions.my') }}" title="My Submissions">
                    <i class="fas fa-file-upload"></i> <span>Submissions</span>
                </a>
                @endif
            </nav>
            
            <!-- Mobile menu button -->
            <button class="header-menu-toggle d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#mobileMenu" title="Menu">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- User profile and logout -->
            <div class="header-user">
                <a class="header-profile py-1 px-2" href="{{ route('profile.show', Auth::id()) }}" title="Profile">
                    @if(!empty(Auth::user()->avatar))
                        <img src="{{ Auth::user()->avatar }}" class="avatar-sm" alt="Profile">
                    @else
                        <i class="fas fa-user-circle"></i>
                    @endif
                    <span>{{ Auth::user()->fullname }}</span>
                    <span class="badge bg-light text-dark">{{ ucfirst(Auth::user()->role) }}</span>
                </a>
                <form action="{{ route('logout') }}" method="post" class="d-inline">
                    @csrf
                    <button type="submit" class="header-logout btn btn-link" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
            @endauth
        </div>
        
        <!-- Mobile navigation menu -->
        @auth
        <div class="collapse navbar-collapse d-md-none mt-1" id="mobileMenu">
            <div class="mobile-nav">
                <div class="d-flex flex-wrap">
                    <a class="mobile-nav-item py-1 px-2" href="{{ route('home') }}">
                        <i class="fas fa-home"></i> Home
                    </a>
                    <a class="mobile-nav-item py-1 px-2" href="{{ route('assignments.index') }}">
                        <i class="fas fa-tasks"></i> Assignments
                    </a>
                    <a class="mobile-nav-item py-1 px-2" href="{{ route('challenges.index') }}">
                        <i class="fas fa-puzzle-piece"></i> Challenges
                    </a>
                    
                    @if(Auth::user()->isTeacher())
                    <a class="mobile-nav-item py-1 px-2" href="{{ route('students.index') }}">
                        <i class="fas fa-users"></i> Manage
                    </a>
                    <a class="mobile-nav-item py-1 px-2" href="{{ route('submissions.index') }}">
                        <i class="fas fa-clipboard-check"></i> Submissions
                    </a>
                    @endif
                    
                    @if(Auth::user()->isStudent())
                    <a class="mobile-nav-item py-1 px-2" href="{{ route('submissions.my') }}">
                        <i class="fas fa-file-upload"></i> My Submissions
                    </a>
                    @endif
                    
                    <a class="mobile-nav-item py-1 px-2" href="{{ route('profile.show', Auth::id()) }}">
                        <i class="fas fa-user-circle"></i> Profile
                    </a>
                    
                    <form action="{{ route('logout') }}" method="post" class="d-inline">
                        @csrf
                        <button type="submit" class="mobile-nav-item py-1 px-2 btn btn-link text-start w-100">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endauth
    </div>
</header>
