@include('admin.header')
@yield('content')

    <div id="wrapper">

        @include('admin.navigation')
        @yield('content')

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">{{ Lang::get('admin/support/promotion.promotion_management') }}</h1>
                </div>
                {{-- /.col-lg-12 --}}
            </div>
            {{-- /.row --}}
            <div class="row">
                <div class="col-lg-12">
                    @include('layout.notification')
                </div>

                <div class="col-lg-6">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="{{-- dataTables-example --}}">
                            <thead>
                                <tr>
                                    <th colspan="2" style="text-align:center;">{{ Lang::get('admin/support/promotion.master_complete_profile') }}</th>
                                </tr>
                                <tr>
                                    <th style="text-align:center;">{{ Lang::get('admin/support/promotion.master_agent') }} ID</th>
                                    <th style="text-align:center;">{{ Lang::get('admin/support/promotion.total_users') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                    // Foreach master agent ID index of complete profile user list
                                    foreach ($completeProfileUserListAnalyticsIndexArray as $completeProfileUserListAnalyticsIndexArrayKey) {

                                        // Initialization register user count of complete profile user list
                                        $completeProfileUserListAnalyticsSum = 0;

                                        // Calculate register user count according to master agent ID of complete profile user list
                                        foreach ($completeProfileUserListAnalytics as $key => $completeProfileUserListAnalyticsValue)
                                        {
                                            // Determin masert agent ID is in master agent ID index array
                                            if (substr($key, 0, -2) == $completeProfileUserListAnalyticsIndexArrayKey)

                                                // The cumulative number of users
                                                $completeProfileUserListAnalyticsSum += $completeProfileUserListAnalyticsValue;
                                        }

                                        // Print result
                                        echo '<tr class="odd gradeX"><td>' , $completeProfileUserListAnalyticsIndexArrayKey , '</td><td>' , $completeProfileUserListAnalyticsSum , '</td></tr>';
                                    }
                                ?>
                            </tbody>
                        </table>

                        <table class="table table-striped table-bordered table-hover" id="{{-- dataTables-example --}}">
                            <thead>
                                <tr>
                                    <th colspan="2" style="text-align:center;">{{ Lang::get('admin/support/promotion.complete_profile') }}</th>
                                </tr>
                                <tr>
                                    <th style="text-align:center;">{{ Lang::get('admin/support/promotion.agent') }} ID</th>
                                    <th style="text-align:center;">{{ Lang::get('admin/support/promotion.users') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($completeProfileUserList as $completeProfileUserListKey => $completeProfileUserListVaule)
                                <tr class="odd gradeX">
                                    <td>{{ $completeProfileUserList[$completeProfileUserListKey] }}</td>
                                    <td>{{ Support::whereRaw("content regexp '^[0-9]{3,4}$'")
                                        ->where('content', $completeProfileUserList[$completeProfileUserListKey])
                                        ->whereHas('hasOneUser', function($hasUncompleteProfile) {
                                        $hasUncompleteProfile->whereNotNull('school')
                                                ->whereNotNull('bio')
                                                ->whereNotNull('portrait')
                                                ->whereNotNull('born_year');
                                        })
                                        ->distinct()
                                        ->get(array('user_id'))
                                        ->count() }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                    {{-- /.table-responsive --}}
                </div>
                {{-- /.col-lg-6 --}}

                <div class="col-lg-6">
                    <div class="table-responsive">

                        <table class="table table-striped table-bordered table-hover" id="{{-- dataTables-example --}}">
                            <thead>
                                <tr>
                                    <th colspan="2" style="text-align:center;">{{ Lang::get('admin/support/promotion.master_uncomplete_profile') }}</th>
                                </tr>
                                <tr>
                                    <th style="text-align:center;">{{ Lang::get('admin/support/promotion.master_agent') }} ID</th>
                                    <th style="text-align:center;">{{ Lang::get('admin/support/promotion.total_users') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                    // Foreach master agent ID index of uncomplete profile user list
                                    foreach ($uncompleteProfileUserListAnalyticsIndexArray as $uncompleteProfileUserListAnalyticsIndexArrayKey) {

                                        // Initialization register user count of uncomplete profile user list
                                        $uncompleteProfileUserListAnalyticsSum = 0;

                                        // Calculate register user count according to master agent ID of uncomplete profile user list
                                        foreach ($uncompleteProfileUserListAnalytics as $key => $uncompleteProfileUserListAnalyticsValue)
                                        {
                                            // Determin masert agent ID is in master agent ID index array
                                            if (substr($key, 0, -2) == $uncompleteProfileUserListAnalyticsIndexArrayKey)

                                                // The cumulative number of users
                                                $uncompleteProfileUserListAnalyticsSum += $uncompleteProfileUserListAnalyticsValue;
                                        }

                                        // Print result
                                        echo '<tr class="odd gradeX"><td>' , $uncompleteProfileUserListAnalyticsIndexArrayKey , '</td><td>' , $uncompleteProfileUserListAnalyticsSum , '</td></tr>';
                                    }
                                ?>
                            </tbody>
                        </table>

                        <table class="table table-striped table-bordered table-hover" id="{{-- dataTables-example --}}">
                            <thead>
                                <tr>
                                    <th colspan="2" style="text-align:center;">{{ Lang::get('admin/support/promotion.uncomplete_profile') }}</th>
                                </tr>
                                <tr>
                                    <th style="text-align:center;">{{ Lang::get('admin/support/promotion.agent') }} ID</th>
                                    <th style="text-align:center;">{{ Lang::get('admin/support/promotion.users') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($uncompleteProfileUserList as $uncompleteProfileUserListKey => $uncompleteProfileUserListVaule)
                                <tr class="odd gradeX">
                                    <td>{{ $uncompleteProfileUserList[$uncompleteProfileUserListKey] }}</td>
                                    <td>{{ Support::whereRaw("content regexp '^[0-9]{3,4}$'")
                                        ->where('content', $uncompleteProfileUserList[$uncompleteProfileUserListKey])
                                        ->distinct()
                                        ->get(array('user_id'))
                                        ->count() -
                                        Support::whereRaw("content regexp '^[0-9]{3,4}$'")
                                            ->where('content', $uncompleteProfileUserList[$uncompleteProfileUserListKey])
                                            ->whereHas('hasOneUser', function($hasUncompleteProfile) {
                                            $hasUncompleteProfile->whereNotNull('school')
                                                    ->whereNotNull('portrait')
                                                    ->whereNotNull('born_year');
                                            })
                                            ->distinct()
                                            ->get(array('user_id'))
                                            ->count() }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                    {{-- /.table-responsive --}}
                </div>
                {{-- /.col-lg-12 --}}
            </div>
        </div>
        {{-- /#page-wrapper --}}

    </div>
    {{-- /#wrapper --}}

    {{-- jQuery Version 1.11.0 --}}
    {{ HTML::script('assets/js/jquery-1.11.1/jquery.min.js') }}

    {{-- Bootstrap Core JavaScript --}}
    {{ HTML::script('assets/bootstrap-3.3.0/js/bootstrap.min.js') }}

    {{-- Metis Menu Plugin JavaScript --}}
    {{ HTML::script('assets/js/admin/plugins/metisMenu/metisMenu.min.js') }}

    {{-- DataTables JavaScript --}}
    {{ HTML::script('assets/js/admin/plugins/dataTables/jquery.dataTables.js') }}
    {{ HTML::script('assets/js/admin/plugins/dataTables/dataTables.bootstrap.js') }}

    {{-- Custom Theme JavaScript --}}
    {{ HTML::script('assets/js/admin/admin.js') }}
</body>

</html>