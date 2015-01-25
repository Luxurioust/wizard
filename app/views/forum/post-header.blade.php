<!DOCTYPE html>
<html>
<head>
	<title>单身公寓 | 聘爱</title>

	@include('layout.meta')
	@yield('content')

	{{ HTML::style('assets/font-awesome-4.2.0/css/font-awesome.min.css') }}

	{{ HTML::style('assets/fancybox-2.1.5/jquery.fancybox.css') }}

	{{ Minify::stylesheet(array(
		'/assets/css/reset.css',
		'/assets/css/nav.css',
		'/assets/css/forum-post.css'
	)) }}

	@include('layout.theme')
	@yield('content')

	{{ HTML::script('assets/js/jquery-1.11.1/jquery.min.js') }}
</head>
<body>