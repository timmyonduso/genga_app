<div id="sidebar" class="c-sidebar c-sidebar-fixed c-sidebar-lg-show">

    <div class="c-sidebar-brand d-md-down-none">
        <a class="c-sidebar-brand-full h4 text-decoration-none text-white" href="#">
            {{ trans('panel.site_title') }}
        </a>
    </div>

    <ul class="c-sidebar-nav">
        <li class="c-sidebar-nav-item">
            <a href="{{ route("admin.home") }}" class="c-sidebar-nav-link">
                <div class="avatar flex-shrink-0 mr-3">
                    <img src="{{asset('assets/svg/icons/dashboard.png')}}" alt="Dashboard" class="rounded">
                </div>
                {{ trans('global.dashboard') }}
            </a>
        </li>

        @can('user_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.users.index") }}" class="c-sidebar-nav-link {{ request()->is('admin/users') || request()->is('admin/users/*') ? 'active' : '' }}">
                    <div class="avatar flex-shrink-0 mr-3">
                        <img src="{{asset('assets/png/users.png')}}" alt="chart success" class="rounded">
                    </div>
                    {{ trans('cruds.user.title') }}
                </a>
            </li>
        @endcan

{{--    @can('user_management_access')--}}
{{--            <li class="c-sidebar-nav-dropdown">--}}
{{--                <a class="c-sidebar-nav-dropdown-toggle" href="#">--}}
{{--                    <div class="avatar flex-shrink-0 mr-3">--}}
{{--                        <img src="{{asset('assets/png/user.png')}}" alt="chart success" class="rounded">--}}
{{--                    </div>--}}
{{--                    {{ trans('cruds.userManagement.title') }}--}}
{{--                </a>--}}
{{--                <ul class="c-sidebar-nav-dropdown-items">--}}
{{--                    @can('permission_access')--}}
{{--                        <li class="c-sidebar-nav-item">--}}
{{--                            <a href="{{ route("admin.permissions.index") }}" class="c-sidebar-nav-link {{ request()->is('admin/permissions') || request()->is('admin/permissions/*') ? 'active' : '' }}">--}}
{{--                                <div class="avatar flex-shrink-0 mr-3">--}}
{{--                                    <img src="{{asset('assets/png/permissions.png')}}" alt="Perimission" class="rounded">--}}
{{--                                </div>--}}
{{--                                {{ trans('cruds.permission.title') }}--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    @endcan--}}
{{--                    @can('role_access')--}}
{{--                        <li class="c-sidebar-nav-item">--}}
{{--                            <a href="{{ route("admin.roles.index") }}" class="c-sidebar-nav-link {{ request()->is('admin/roles') || request()->is('admin/roles/*') ? 'active' : '' }}">--}}
{{--                                <div class="avatar flex-shrink-0 mr-3">--}}
{{--                                    <img src="{{asset('assets/png/roles.png')}}" alt="chart success" class="rounded">--}}
{{--                                </div>--}}
{{--                                {{ trans('cruds.role.title') }}--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    @endcan--}}
{{--                    @can('user_access')--}}
{{--                        <li class="c-sidebar-nav-item">--}}
{{--                            <a href="{{ route("admin.users.index") }}" class="c-sidebar-nav-link {{ request()->is('admin/users') || request()->is('admin/users/*') ? 'active' : '' }}">--}}
{{--                                <div class="avatar flex-shrink-0 mr-3">--}}
{{--                                    <img src="{{asset('assets/png/users.png')}}" alt="chart success" class="rounded">--}}
{{--                                </div>--}}
{{--                                {{ trans('cruds.user.title') }}--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    @endcan--}}
{{--                </ul>--}}
{{--            </li>--}}
{{--        @endcan--}}
{{--        @can('status_access')--}}
{{--            <li class="c-sidebar-nav-item">--}}
{{--                <a href="{{ route("admin.statuses.index") }}" class="c-sidebar-nav-link {{ request()->is('admin/statuses') || request()->is('admin/statuses/*') ? 'active' : '' }}">--}}
{{--                    <div class="avatar flex-shrink-0 mr-3">--}}
{{--                        <img src="{{asset('assets/png/status.png')}}" alt="chart success" class="rounded">--}}
{{--                    </div>--}}
{{--                    {{ trans('cruds.status.title') }}--}}
{{--                </a>--}}
{{--            </li>--}}
{{--        @endcan--}}
        @can('loan_application_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.loan-applications.index") }}" class="c-sidebar-nav-link {{ request()->is('admin/loan-applications') || request()->is('admin/loan-applications/*') ? 'active' : '' }}">
                    <div class="avatar flex-shrink-0 mr-3">
                        <img src="{{asset('assets/png/application.png')}}" alt="chart success" class="rounded">
                    </div>
                    {{ trans('cruds.loanApplication.title') }}
                </a>
            </li>
        @endcan
        @can('comment_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.comments.index") }}" class="c-sidebar-nav-link {{ request()->is('admin/comments') || request()->is('admin/comments/*') ? 'active' : '' }}">
                    <div class="avatar flex-shrink-0 mr-3">
                        <img src="{{asset('assets/png/comment.png')}}" alt="chart success" class="rounded">
                    </div>
                    {{ trans('cruds.comment.title') }}
                </a>
            </li>
        @endcan
        @if(file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php')))
            @can('profile_password_edit')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->is('profile/password') || request()->is('profile/password/*') ? 'active' : '' }}" href="{{ route('profile.password.edit') }}">
                        <div class="avatar flex-shrink-0 mr-3">
                            <img src="{{asset('assets/png/padlock.png')}}" alt="chart success" class="rounded">
                        </div>
                        {{ trans('global.change_password') }}
                    </a>
                </li>
            @endcan
        @endif
        <li class="c-sidebar-nav-item">
            <a href="#" class="c-sidebar-nav-link" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                <div class="avatar flex-shrink-0 mr-3">
                    <img src="{{asset('assets/svg/icons/logout.svg')}}" alt="chart success" class="rounded">
                </div>
                {{ trans('global.logout') }}
            </a>
        </li>
    </ul>

</div>
