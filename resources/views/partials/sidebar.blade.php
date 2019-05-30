@inject('request', 'Illuminate\Http\Request')
<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <ul class="sidebar-menu">



            <li class="{{ $request->segment(1) == 'home' ? 'active' : '' }}">
                <a href="{{ url('/') }}">
                    <i class="fa fa-wrench"></i>
                    <span class="title">@lang('quickadmin.qa_dashboard')</span>
                </a>
            </li>

            @can('scheme_management_access')
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-bank"></i>
                    <span class="title">@lang('quickadmin.scheme-management.title')</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">

                @can('scheme_access')
                <li class="{{ $request->segment(2) == 'schemes' ? 'active active-sub' : '' }}">
                        <a href="{{ route('admin.schemes.index') }}">
                            <i class="fa fa-briefcase"></i>
                            <span class="title">
                                @lang('quickadmin.schemes.title')
                            </span>
                        </a>
                    </li>
                @endcan
                </ul>
            </li>
            @endcan

            @can('member_management_access')
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-users"></i>
                        <span class="title">@lang('quickadmin.member-management.title')</span>
                        <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                    </a>
                    <ul class="treeview-menu">

                        @can('member_access')
                            <li class="{{ $request->segment(2) == 'members' ? 'active active-sub' : '' }}">
                                <a href="{{ route('admin.members.index') }}">
                                    <i class="fa fa-user"></i>
                                    <span class="title">
                                @lang('quickadmin.members.title')
                            </span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('ledger_management_access')
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-money"></i>
                    <span class="title">@lang('quickadmin.ledger-management.title')</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">

                @can('ledger_category_access')
                <li class="{{ $request->segment(2) == 'ledger_categories' ? 'active active-sub' : '' }}">
                        <a href="{{ route('admin.ledger_categories.index') }}">
                            <i class="fa fa-list"></i>
                            <span class="title">
                                @lang('quickadmin.ledger-category.title')
                            </span>
                        </a>
                    </li>
                @endcan
                @can('ledger_entry_access')
                <li class="{{ $request->segment(2) == 'ledger_entries' ? 'active active-sub' : '' }}">
                        <a href="{{ route('admin.ledger_entries.index') }}">
                            <i class="fa fa-arrow-circle-left"></i>
                            <span class="title">
                                @lang('quickadmin.ledger-entry.title')
                            </span>
                        </a>
                    </li>
                @endcan
                @can('monthly_report_access')
                <li class="{{ $request->segment(2) == 'monthly_reports' ? 'active active-sub' : '' }}">
                        <a href="{{ route('admin.monthly_reports.index') }}">
                            <i class="fa fa-line-chart"></i>
                            <span class="title">
                                @lang('quickadmin.monthly-report.title')
                            </span>
                        </a>
                    </li>
                @endcan
                @can('currency_access')
                <li class="{{ $request->segment(2) == 'currencies' ? 'active active-sub' : '' }}">
                        <a href="{{ route('admin.currencies.index') }}">
                            <i class="fa fa-gears"></i>
                            <span class="title">
                                @lang('quickadmin.currency.title')
                            </span>
                        </a>
                    </li>
                @endcan
                </ul>
            </li>
            @endcan




            @can('user_management_access')
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-users"></i>
                        <span class="title">@lang('quickadmin.user-management.title')</span>
                        <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                    </a>
                    <ul class="treeview-menu">

                        @can('role_access')
                            <li class="{{ $request->segment(2) == 'roles' ? 'active active-sub' : '' }}">
                                <a href="{{ route('admin.roles.index') }}">
                                    <i class="fa fa-briefcase"></i>
                                    <span class="title">
                                @lang('quickadmin.roles.title')
                            </span>
                                </a>
                            </li>
                        @endcan
                        @can('user_access')
                            <li class="{{ $request->segment(2) == 'users' ? 'active active-sub' : '' }}">
                                <a href="{{ route('admin.users.index') }}">
                                    <i class="fa fa-user"></i>
                                    <span class="title">
                                @lang('quickadmin.users.title')
                            </span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan




            <li class="{{ $request->segment(1) == 'change_password' ? 'active' : '' }}">
                <a href="{{ route('auth.change_password') }}">
                    <i class="fa fa-key"></i>
                    <span class="title">@lang('quickadmin.qa_change_password')</span>
                </a>
            </li>

            <li>
                <a href="#logout" onclick="$('#logout').submit();">
                    <i class="fa fa-arrow-left"></i>
                    <span class="title">@lang('quickadmin.qa_logout')</span>
                </a>
            </li>
        </ul>
    </section>
</aside>

