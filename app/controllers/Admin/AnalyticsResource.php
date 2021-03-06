<?php

/**
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * PHP Version 5.5
 */

/**
 * Class for analytics total number of registered users, active users and generate data reports and charts.
 *
 * @uses        Laravel The PHP frameworks for web artisans http://laravel.com
 * @author      Ri Xu http://xuri.me <xuri.me@gmail.com>
 * @copyright   Copyright (c) Harbin Wizard Techonlogy Co., Ltd.
 * @link        http://www.jinglingkj.com
 * @license     Licensed under The MIT License http://www.opensource.org/licenses/mit-license.php
 * @version     Release: 0.1 2014-12-25
 */

class Admin_AnalyticsResource extends BaseResource
{
    /**
     * Resource view directory
     * @var string
     */
    protected $resourceView = 'admin.analytics';

    /**
     * Model name of the resource, after initialization to a model instance
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $model = 'User';

    /**
     * Resource identification
     * @var string
     */
    protected $resource = 'users';

    /**
     * Resource database tables
     * @var string
     */
    protected $resourceTable = 'users';

    /**
     * Resource name (Chinese)
     * @var string
     */
    protected $resourceName = '统计';

    /**
     * Resource Analytics user form view
     * GET         /resource
     * @return Response
     */
    public function userForm()
    {
        $analyticsUsers = AnalyticsUser::orderby('id', 'desc')->remember(60)->paginate(10);
        return View::make($this->resourceView.'.user-form')->with(compact('analyticsUsers'));
    }

    /**
     * Resource Analytics forum form view
     * GET         /resource
     * @return Response
     */
    public function forumForm()
    {
        $analyticsForums = AnalyticsForum::orderby('id', 'desc')->remember(60)->paginate(10);
        return View::make($this->resourceView.'.forum-form')->with(compact('analyticsForums'));
    }

    /**
     * Resource Analytics likes form view
     * GET         /resource
     * @return Response
     */
    public function likeForm()
    {
        $analyticsLikes = AnalyticsLike::orderby('id', 'desc')->remember(60)->paginate(10);
        return View::make($this->resourceView.'.like-form')->with(compact('analyticsLikes'));
    }

    /**
     * Resource Analytics user charts view
     * GET         /resource
     * @return Response
     */
    public function userCharts()
    {

        $analyticsUser = AnalyticsUser::select(
                            'all_user',
                            'daily_active_user',
                            'weekly_active_user',
                            'monthly_active_user',
                            'all_male_user',
                            'daily_active_male_user',
                            'weekly_active_male_user',
                            'monthly_active_male_user',
                            'all_female_user',
                            'daily_active_female_user',
                            'weekly_active_female_user',
                            'monthly_active_female_user',
                            'complete_profile_user_ratio',
                            'from_web',
                            'from_android',
                            'from_ios',
                            'created_at'
                        )->where('created_at', '>=', Carbon::now()->subMonth())->get()->toArray(); // Retrive analytics data

        /*
        |--------------------------------------------------------------------------
        | User Basic Analytics Section
        |--------------------------------------------------------------------------
        |
        */

        $allUser = array(); // Create all user array
        foreach ($analyticsUser as $key){ // Structure array elements
            $allUser[] = array(
                date('Y', strtotime($key['created_at'])),
                date('m', strtotime($key['created_at'])),
                date('d', strtotime($key['created_at'])),
                $key['all_user']);
        }

        $fromWeb = array(); // Create all from web user array
        foreach ($analyticsUser as $key){ // Structure array elements
            $fromWeb[] = array(
                date('Y', strtotime($key['created_at'])),
                date('m', strtotime($key['created_at'])),
                date('d', strtotime($key['created_at'])),
                $key['from_web']);
        }

        $fromAndroid = array(); // Create all from Android user array
        foreach ($analyticsUser as $key) { // Structure array elements
            $fromAndroid[] = array(
                date('Y', strtotime($key['created_at'])),
                date('m', strtotime($key['created_at'])),
                date('d', strtotime($key['created_at'])),
                $key['from_android']);
        }

        $fromiOS = array(); // Create all from iOS user array
        foreach ($analyticsUser as $key) { // Structure array elements
            $fromiOS[] = array(
                date('Y', strtotime($key['created_at'])),
                date('m', strtotime($key['created_at'])),
                date('d', strtotime($key['created_at'])),
                $key['from_ios']);
        }

        $allMaleUser = array(); // Create all male user array
        foreach ($analyticsUser as $key) { // Structure array elements
            $allMaleUser[] = array(
                date('Y', strtotime($key['created_at'])),
                date('m', strtotime($key['created_at'])),
                date('d', strtotime($key['created_at'])),
                $key['all_male_user']);
        }

        $allFemaleUser = array(); // Create all female user array
        foreach ($analyticsUser as $key) { // Structure array elements
            $allFemaleUser[] = array(
                date('Y', strtotime($key['created_at'])),
                date('m', strtotime($key['created_at'])),
                date('d', strtotime($key['created_at'])),
                $key['all_female_user']);
        }

        // Build Json data (remove double quotes from Json return data)
        $userBasicAnalytics = '{
            "' . Lang::get('admin/index.total') .'":'.preg_replace('/["]/', '' ,json_encode($allUser)).
            ', "' . Lang::get('admin/index.male_users') .'":'.preg_replace('/["]/', '' ,json_encode($allMaleUser)).
            ', "' . Lang::get('admin/index.female_users') .'":'.preg_replace('/["]/', '' ,json_encode($allFemaleUser)).
            ', "Web ' . Lang::get('admin/index.users') .'":'.preg_replace('/["]/', '' ,json_encode($fromWeb)).
            ', "Android ' . Lang::get('admin/index.users') .'":'.preg_replace('/["]/', '' ,json_encode($fromAndroid)).
            ', "iOS ' . Lang::get('admin/index.users') .'":'.preg_replace('/["]/', '' ,json_encode($fromiOS)).
            '}';

        /*
        |--------------------------------------------------------------------------
        | User Daily Active Analytics Section
        |--------------------------------------------------------------------------
        |
        */

        $dailyActiveUser = array(); // Create daily active user array
        foreach ($analyticsUser as $key) { // Structure array elements
            $dailyActiveUser[] = array(
                date('Y', strtotime($key['created_at'])),
                date('m', strtotime($key['created_at'])),
                date('d', strtotime($key['created_at'])),
                $key['daily_active_user']);
        }

        $dailyActiveMaleUser = array(); // Create daily active male user array
        foreach ($analyticsUser as $key) { // Structure array elements
            $dailyActiveMaleUser[] = array(
                date('Y', strtotime($key['created_at'])),
                date('m', strtotime($key['created_at'])),
                date('d', strtotime($key['created_at'])),
                $key['daily_active_male_user']);
        }

        $dailyActiveFemaleUser = array(); // Create daily active female user array
        foreach ($analyticsUser as $key) { // Structure array elements
            $dailyActiveFemaleUser[] = array(
                date('Y', strtotime($key['created_at'])),
                date('m', strtotime($key['created_at'])),
                date('d', strtotime($key['created_at'])),
                $key['daily_active_female_user']);
        }

        // Build Json data (remove double quotes from Json return data)
        $dailyActiveUserAnalytics = '{
            "日活用户":'.preg_replace('/["]/', '' ,json_encode($dailyActiveUser)).
            ', "日活男用户":'.preg_replace('/["]/', '' ,json_encode($dailyActiveMaleUser)).
            ', "日活女用户":'.preg_replace('/["]/', '' ,json_encode($dailyActiveFemaleUser)).
            '}';

        /*
        |--------------------------------------------------------------------------
        | User Weekly Active Analytics Section
        |--------------------------------------------------------------------------
        |
        */

        $weeklyActiveUser = array(); // Create weekly active user array
        foreach ($analyticsUser as $key) { // Structure array elements
            $weeklyActiveUser[] = array(
                date('Y', strtotime($key['created_at'])),
                date('m', strtotime($key['created_at'])),
                date('d', strtotime($key['created_at'])),
                $key['weekly_active_user']);
        }

        $weeklyActiveMaleUser = array(); // Create weekly active male user array
        foreach ($analyticsUser as $key) { // Structure array elements
            $weeklyActiveMaleUser[] = array(
                date('Y', strtotime($key['created_at'])),
                date('m', strtotime($key['created_at'])),
                date('d', strtotime($key['created_at'])),
                $key['weekly_active_male_user']);
        }

        $weeklyActiveFemaleUser = array(); // Create weekly active female user array
        foreach ($analyticsUser as $key) { // Structure array elements
            $weeklyActiveFemaleUser[] = array(
                date('Y', strtotime($key['created_at'])),
                date('m', strtotime($key['created_at'])),
                date('d', strtotime($key['created_at'])),
                $key['weekly_active_female_user']);
        }

        // Build Json data (remove double quotes from Json return data)
        $weeklyActiveUserAnalytics = '{
            "周活用户":'.preg_replace('/["]/', '' ,json_encode($weeklyActiveUser)).
            ', "周活男用户":'.preg_replace('/["]/', '' ,json_encode($weeklyActiveMaleUser)).
            ', "周活女用户":'.preg_replace('/["]/', '' ,json_encode($weeklyActiveFemaleUser)).
            '}';

        /*
        |--------------------------------------------------------------------------
        | User Monthly Active Analytics Section
        |--------------------------------------------------------------------------
        |
        */

        $monthlyActiveUser = array(); // Create monthly active user array
        foreach ($analyticsUser as $key) { // Structure array elements
            $monthlyActiveUser[] = array(
                date('Y', strtotime($key['created_at'])),
                date('m', strtotime($key['created_at'])),
                date('d', strtotime($key['created_at'])),
                $key['monthly_active_user']);
        }

        $monthlyActiveMaleUser = array(); // Create monthly active male user array
        foreach ($analyticsUser as $key) { // Structure array elements
            $monthlyActiveMaleUser[] = array(
                date('Y', strtotime($key['created_at'])),
                date('m', strtotime($key['created_at'])),
                date('d', strtotime($key['created_at'])),
                $key['monthly_active_male_user']);
        }

        $monthlyActiveFemaleUser = array(); // Create monthly active female user array
        foreach ($analyticsUser as $key) { // Structure array elements
            $monthlyActiveFemaleUser[] = array(
                date('Y', strtotime($key['created_at'])),
                date('m', strtotime($key['created_at'])),
                date('d', strtotime($key['created_at'])),
                $key['monthly_active_female_user']);
        }

        // Build Json data (remove double quotes from Json return data)
        $monthlyActiveUserAnalytics = '{
            "月活用户":'.preg_replace('/["]/', '' ,json_encode($monthlyActiveUser)).
            ', "月活男用户":'.preg_replace('/["]/', '' ,json_encode($monthlyActiveMaleUser)).
            ', "月活女用户":'.preg_replace('/["]/', '' ,json_encode($monthlyActiveFemaleUser)).
            '}';

        /*
        |--------------------------------------------------------------------------
        | User Profile Complete Ratio Analytics Section
        |--------------------------------------------------------------------------
        |
        */

        $completeProfileUserRatio = array();
        foreach ($analyticsUser as $key) { // Structure array elements
            $completeProfileUserRatio[] = array(
                date('Y', strtotime($key['created_at'])),
                date('m', strtotime($key['created_at'])),
                date('d', strtotime($key['created_at'])),
                $key['complete_profile_user_ratio']);
        }

        // Build Json data (remove double quotes from Json return data)
        $completeProfileUserRatioAnalytics = '{"完整用户资料比例":'.preg_replace('/["]/', '' ,json_encode($completeProfileUserRatio)).'}';

        return View::make($this->resourceView.'.user-charts')->with(compact('userBasicAnalytics', 'dailyActiveUserAnalytics', 'weeklyActiveUserAnalytics', 'monthlyActiveUserAnalytics', 'completeProfileUserRatioAnalytics'));
    }

    /**
     * Resource Analytics like charts view
     * GET         /resource
     * @return Response
     */
    public function likeCharts()
    {
        if (Cache::has('analyticsLike')) {
            $analyticsLike = Cache::get('analyticsLike');
        } else {

            $analyticsLike = AnalyticsLike::select(
                                'daily_like',
                                'weekly_like',
                                'monthly_like',
                                'all_male_like',
                                'all_female_like',
                                'daily_male_like',
                                'daily_female_like',
                                'weekly_male_like',
                                'weekly_female_like',
                                'monthly_male_like',
                                'monthly_female_like',
                                'all_male_accept_ratio',
                                'all_female_accept_ratio',
                                'average_like_duration',
                                'created_at'
                )->where('created_at', '>=', Carbon::now()->subMonth())->get()->toArray(); // Retrive analytics data

            Cache::put('analyticsLike', $analyticsLike, 60);
        }

        /*
        |--------------------------------------------------------------------------
        | Likes Analytics Section
        |--------------------------------------------------------------------------
        |
        */

        if (Cache::has('basicLikes')) {
            $basicLikes = Cache::get('basicLikes');
        } else {

            $allMaleLike = array(); // Create all male likes array
            foreach ($analyticsLike as $key) { // Structure array elements
                $allMaleLike[] = array(
                    date('Y', strtotime($key['created_at'])),
                    date('m', strtotime($key['created_at'])),
                    date('d', strtotime($key['created_at'])),
                    $key['all_male_like']);
            }

            $allFemaleLike = array(); // Create all female likes array
            foreach ($analyticsLike as $key) { // Structure array elements
                $allFemaleLike[] = array(
                    date('Y', strtotime($key['created_at'])),
                    date('m', strtotime($key['created_at'])),
                    date('d', strtotime($key['created_at'])),
                    $key['all_female_like']);
            }

            // Build Json data (remove double quotes from Json return data)
            $basicLikes = '{
                "累计男生追女生次数":'.preg_replace('/["]/', '' ,json_encode($allMaleLike)).
                ', "累计女生追男生次数":'.preg_replace('/["]/', '' ,json_encode($allFemaleLike)).
                '}';
            Cache::put('basicLikes', $basicLikes, 60);
        }

        /*
        |--------------------------------------------------------------------------
        | Daily Likes Analytics Section
        |--------------------------------------------------------------------------
        |
        */

        if (Cache::has('dailyLikes')) {
            $dailyLikes = Cache::get('dailyLikes');
        } else {

            $dailyLike = array(); // Create daily likes array
            foreach ($analyticsLike as $key) { // Structure array elements
                $dailyLike[] = array(
                    date('Y', strtotime($key['created_at'])),
                    date('m', strtotime($key['created_at'])),
                    date('d', strtotime($key['created_at'])),
                    $key['daily_like']);
            }

            $dailyMaleLike = array(); // Create daily male likes array
            foreach ($analyticsLike as $key) { // Structure array elements
                $dailyMaleLike[] = array(
                    date('Y', strtotime($key['created_at'])),
                    date('m', strtotime($key['created_at'])),
                    date('d', strtotime($key['created_at'])),
                    $key['daily_male_like']);
            }

            $dailyFemaleLike = array(); // Create daily female likes array
            foreach ($analyticsLike as $key) { // Structure array elements
                $dailyFemaleLike[] = array(
                    date('Y', strtotime($key['created_at'])),
                    date('m', strtotime($key['created_at'])),
                    date('d', strtotime($key['created_at'])),
                    $key['daily_female_like']);
            }

            // Build Json data (remove double quotes from Json return data)
            $dailyLikes = '{
                "每日用户互动次数":'.preg_replace('/["]/', '' ,json_encode($dailyLike)).
                ', "每日男生追女生次数":'.preg_replace('/["]/', '' ,json_encode($dailyMaleLike)).
                ', "每日女生追男生次数":'.preg_replace('/["]/', '' ,json_encode($dailyFemaleLike)).
                '}';

            Cache::put('dailyLikes', $dailyLikes, 60);
        }
        /*
        |--------------------------------------------------------------------------
        | Weekly Likes Analytics Section
        |--------------------------------------------------------------------------
        |
        */

        if (Cache::has('weeklyLikes')) {
            $weeklyLikes = Cache::get('weeklyLikes');
        } else {

            $weeklyLike = array(); // Create weekly likes array
            foreach ($analyticsLike as $key) { // Structure array elements
                $weeklyLike[] = array(
                    date('Y', strtotime($key['created_at'])),
                    date('m', strtotime($key['created_at'])),
                    date('d', strtotime($key['created_at'])),
                    $key['weekly_like']);
            }

            $weeklyMaleLike = array(); // Create weekly male likes array
            foreach ($analyticsLike as $key) { // Structure array elements
                $weeklyMaleLike[] = array(
                    date('Y', strtotime($key['created_at'])),
                    date('m', strtotime($key['created_at'])),
                    date('d', strtotime($key['created_at'])),
                    $key['weekly_male_like']);
            }

            $weeklyFemaleLike = array(); // Create weekly female likes array
            foreach ($analyticsLike as $key) { // Structure array elements
                $weeklyFemaleLike[] = array(
                    date('Y', strtotime($key['created_at'])),
                    date('m', strtotime($key['created_at'])),
                    date('d', strtotime($key['created_at'])),
                    $key['weekly_female_like']);
            }

            // Build Json data (remove double quotes from Json return data)
            $weeklyLikes = '{
                "每周用户互动次数":'.preg_replace('/["]/', '' ,json_encode($weeklyLike)).
                ', "每周男生追女生次数":'.preg_replace('/["]/', '' ,json_encode($weeklyMaleLike)).
                ', "每周女生追男生次数":'.preg_replace('/["]/', '' ,json_encode($weeklyFemaleLike)).
                '}';
            Cache::put('weeklyLikes', $weeklyLikes, 60);
        }

        /*
        |--------------------------------------------------------------------------
        | Monthly Likes Analytics Section
        |--------------------------------------------------------------------------
        |
        */

        if (Cache::has('monthlyLikes')) {
            $monthlyLikes = Cache::get('monthlyLikes');
        } else {

            $monthlyLike = array(); // Create monthly likes array
            foreach ($analyticsLike as $key) { // Structure array elements
                $monthlyLike[] = array(
                    date('Y', strtotime($key['created_at'])),
                    date('m', strtotime($key['created_at'])),
                    date('d', strtotime($key['created_at'])),
                    $key['monthly_like']);
            }

            $monthlyMaleLike = array(); // Create monthly male likes array
            foreach ($analyticsLike as $key) { // Structure array elements
                $monthlyMaleLike[] = array(
                    date('Y', strtotime($key['created_at'])),
                    date('m', strtotime($key['created_at'])),
                    date('d', strtotime($key['created_at'])),
                    $key['monthly_male_like']);
            }

            $monthlyFemaleLike = array(); // Create monthly female likes array
            foreach ($analyticsLike as $key) { // Structure array elements
                $monthlyFemaleLike[] = array(
                    date('Y', strtotime($key['created_at'])),
                    date('m', strtotime($key['created_at'])),
                    date('d', strtotime($key['created_at'])),
                    $key['monthly_female_like']);
            }

            // Build Json data (remove double quotes from Json return data)
            $monthlyLikes = '{
                "每月用户互动次数":'.preg_replace('/["]/', '' ,json_encode($monthlyLike)).
                ', "每月男生追女生次数":'.preg_replace('/["]/', '' ,json_encode($monthlyMaleLike)).
                ', "每月女生追男生次数":'.preg_replace('/["]/', '' ,json_encode($monthlyFemaleLike)).
                '}';
            Cache::put('monthlyLikes', $monthlyLikes, 60);
        }

        /*
        |--------------------------------------------------------------------------
        | Users Accept Ratio Analytics Section
        |--------------------------------------------------------------------------
        |
        */

        if (Cache::has('allUsersAcceptRatio')) {
            $allUsersAcceptRatio = Cache::get('allUsersAcceptRatio');
        } else {

            $allMaleAcceptRatio = array(); // Create all male accept ratio array
            foreach ($analyticsLike as $key) { // Structure array elements
                $allMaleAcceptRatio[] = array(
                    date('Y', strtotime($key['created_at'])),
                    date('m', strtotime($key['created_at'])),
                    date('d', strtotime($key['created_at'])),
                    $key['all_male_accept_ratio']);
            }

            $allFemaleAcceptRatio = array(); // Create all female accept ratio array
            foreach ($analyticsLike as $key) { // Structure array elements
                $allFemaleAcceptRatio[] = array(
                    date('Y', strtotime($key['created_at'])),
                    date('m', strtotime($key['created_at'])),
                    date('d', strtotime($key['created_at'])),
                    $key['all_female_accept_ratio']);
            }

            // Build Json data (remove double quotes from Json return data)
            $allUsersAcceptRatio = '{
                "女生追男生成功比率":'.preg_replace('/["]/', '' ,json_encode($allFemaleAcceptRatio)).
                ', "男生追女生成功比率":'.preg_replace('/["]/', '' ,json_encode($allMaleAcceptRatio)).
                '}';

            Cache::put('allUsersAcceptRatio', $allUsersAcceptRatio, 60);
        }

        /*
        |--------------------------------------------------------------------------
        | Average Like Duration Analytics Section
        |--------------------------------------------------------------------------
        |
        */

        if (Cache::has('averageLikeDurations')) {
            $averageLikeDurations = Cache::get('averageLikeDurations');
        } else {

            $averageLikeDuration = array(); // Create average like duration ratio array
            foreach ($analyticsLike as $key) { // Structure array elements
                $averageLikeDuration[] = array(
                    date('Y', strtotime($key['created_at'])),
                    date('m', strtotime($key['created_at'])),
                    date('d', strtotime($key['created_at'])),
                    $key['average_like_duration']);
            }

            // Build Json data (remove double quotes from Json return data)
            $averageLikeDurations = '{"平均交友历经时长":'.preg_replace('/["]/', '' ,json_encode($averageLikeDuration)).'}';

            Cache::put('averageLikeDurations', $averageLikeDurations, 60);
        }

        return View::make($this->resourceView.'.like-charts')->with(compact(
            'basicLikes',
            'dailyLikes',
            'weeklyLikes',
            'monthlyLikes',
            'allUsersAcceptRatio',
            'averageLikeDurations'
        ));
    }

    /**
     * Resource Analytics forum charts view
     * GET         /resource
     * @return Response
     */
    public function forumCharts()
    {
        if (Cache::has('analyticsForum')) {
            $analyticsForum = Cache::get('analyticsForum');
        } else {

            $analyticsForum = AnalyticsForum::select(
                                'all_post',
                                'cat1_post',
                                'cat2_post',
                                'cat3_post',
                                'daily_post',
                                'cat1_daily_post',
                                'cat2_daily_post',
                                'cat3_daily_post',
                                'daily_male_post',
                                'daily_female_post',
                                'created_at'
                )->where('created_at', '>=', Carbon::now()->subMonth())->get()->toArray(); // Retrive analytics data

            Cache::put('analyticsForum', $analyticsForum, 60);
        }

        /*
        |--------------------------------------------------------------------------
        | Forum Posts Analytics Section
        |--------------------------------------------------------------------------
        |
        */

        if (Cache::has('basicForumPosts')) {
            $basicForumPosts = Cache::get('basicForumPosts');
        } else {

            $allPost = array(); // Create all posts array
            foreach ($analyticsForum as $key) { // Structure array elements
                $allPost[] = array(
                    date('Y', strtotime($key['created_at'])),
                    date('m', strtotime($key['created_at'])),
                    date('d', strtotime($key['created_at'])),
                    $key['all_post']);
            }

            $cat1Post = array(); // Create category 1 post array
            foreach ($analyticsForum as $key) { // Structure array elements
                $cat1Post[] = array(
                    date('Y', strtotime($key['created_at'])),
                    date('m', strtotime($key['created_at'])),
                    date('d', strtotime($key['created_at'])),
                    $key['cat1_post']);
            }

            $cat2Post = array(); // Create category 2 post array
            foreach ($analyticsForum as $key) { // Structure array elements
                $cat2Post[] = array(
                    date('Y', strtotime($key['created_at'])),
                    date('m', strtotime($key['created_at'])),
                    date('d', strtotime($key['created_at'])),
                    $key['cat2_post']);
            }

            $cat3Post = array(); // Create category 3 post array
            foreach ($analyticsForum as $key) { // Structure array elements
                $cat3Post[] = array(
                    date('Y', strtotime($key['created_at'])),
                    date('m', strtotime($key['created_at'])),
                    date('d', strtotime($key['created_at'])),
                    $key['cat3_post']);
            }

            // Build Json data (remove double quotes from Json return data)
            $basicForumPosts = '{
                "累计发帖量":'.preg_replace('/["]/', '' ,json_encode($allPost)).
                ', "'.ForumCategories::where('id', 1)->first()->name.'发帖量":'.preg_replace('/["]/', '' ,json_encode($cat1Post)).
                ', "'.ForumCategories::where('id', 2)->first()->name.'发帖量":'.preg_replace('/["]/', '' ,json_encode($cat2Post)).
                ', "'.ForumCategories::where('id', 3)->first()->name.'发帖量":'.preg_replace('/["]/', '' ,json_encode($cat3Post)).
                '}';

            Cache::put('basicForumPosts', $basicForumPosts, 60);
        }

        /*
        |--------------------------------------------------------------------------
        | Daily Forum Posts Analytics Section
        |--------------------------------------------------------------------------
        |
        */

        if (Cache::has('dailyForumPosts')) {
            $dailyForumPosts = Cache::get('dailyForumPosts');
        } else {

            $allDailyPost = array(); // Create all daily posts array
            foreach ($analyticsForum as $key) { // Structure array elements
                $allDailyPost[] = array(
                    date('Y', strtotime($key['created_at'])),
                    date('m', strtotime($key['created_at'])),
                    date('d', strtotime($key['created_at'])),
                    $key['daily_post']);
            }

            $cat1DailyPost = array(); // Create category 1 daily post array
            foreach ($analyticsForum as $key) { // Structure array elements
                $cat1DailyPost[] = array(
                    date('Y', strtotime($key['created_at'])),
                    date('m', strtotime($key['created_at'])),
                    date('d', strtotime($key['created_at'])),
                    $key['cat1_daily_post']);
            }

            $cat2DailyPost = array(); // Create category 2 daily post array
            foreach ($analyticsForum as $key) { // Structure array elements
                $cat2DailyPost[] = array(
                    date('Y', strtotime($key['created_at'])),
                    date('m', strtotime($key['created_at'])),
                    date('d', strtotime($key['created_at'])),
                    $key['cat2_daily_post']);
            }

            $cat3DailyPost = array(); // Create category 3 daily post array
            foreach ($analyticsForum as $key) { // Structure array elements
                $cat3DailyPost[] = array(
                    date('Y', strtotime($key['created_at'])),
                    date('m', strtotime($key['created_at'])),
                    date('d', strtotime($key['created_at'])),
                    $key['cat3_daily_post']);
            }

            $dailyMalePost = array(); // Create category 3 daily post array
            foreach ($analyticsForum as $key) { // Structure array elements
                $dailyMalePost[] = array(
                    date('Y', strtotime($key['created_at'])),
                    date('m', strtotime($key['created_at'])),
                    date('d', strtotime($key['created_at'])),
                    $key['daily_male_post']);
            }

            $dailyFemalePost = array(); // Create category 3 daily post array
            foreach ($analyticsForum as $key) { // Structure array elements
                $dailyFemalePost[] = array(
                    date('Y', strtotime($key['created_at'])),
                    date('m', strtotime($key['created_at'])),
                    date('d', strtotime($key['created_at'])),
                    $key['daily_female_post']);
            }
            // Build Json data (remove double quotes from Json return data)
            $dailyForumPosts = '{
                "累计日发帖量":'.preg_replace('/["]/', '' ,json_encode($allDailyPost)).
                ', "'.ForumCategories::where('id', 1)->first()->name.'日发帖量":'.preg_replace('/["]/', '' ,json_encode($cat1DailyPost)).
                ', "'.ForumCategories::where('id', 2)->first()->name.'日发帖量":'.preg_replace('/["]/', '' ,json_encode($cat2DailyPost)).
                ', "'.ForumCategories::where('id', 3)->first()->name.'日发帖量":'.preg_replace('/["]/', '' ,json_encode($cat3DailyPost)).
                ', "男用户日发帖量":'.preg_replace('/["]/', '' ,json_encode($dailyMalePost)).
                ', "女用户日发帖量":'.preg_replace('/["]/', '' ,json_encode($dailyFemalePost)).
                '}';

            Cache::put('dailyForumPosts', $dailyForumPosts, 60);
        }

        return View::make($this->resourceView.'.forum-charts')->with(compact('basicForumPosts', 'dailyForumPosts'));
    }

}
