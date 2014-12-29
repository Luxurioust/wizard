<!DOCTYPE html>
<html lang="en">

<head>
    <title>管理中心 | 聘爱网</title>

    @include('layout.meta')
    @yield('content')

    <!-- Bootstrap Core CSS -->
    {{ HTML::style('assets/bootstrap-3.3.0/css/bootstrap.min.css') }}


    {{-- MetisMenu CSS --}}
    {{ HTML::style('assets/css/admin/plugins/metisMenu/metisMenu.min.css') }}


    <!-- Timeline CSS -->
    {{ HTML::style('assets/css/admin/plugins/timeline.css') }}

    <!-- Custom CSS -->
    {{ HTML::style('assets/css/admin/admin.css') }}


    <!-- Morris Charts CSS -->
    {{ HTML::style('assets/css/admin/plugins/morris.css') }}

    <!-- Custom Fonts -->
    {{ HTML::style('assets/font-awesome-4.2.0/css/font-awesome.min.css') }}

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>
