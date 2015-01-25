<!DOCTYPE html>
<html>
<head>
	<title>编辑资料 | 聘爱</title>

	@include('layout.meta')
	@yield('content')

	{{ HTML::style('assets/font-awesome-4.2.0/css/font-awesome.min.css') }}

	{{ Minify::stylesheet(array(
		'/assets/css/reset.css',
		'/assets/css/nav.css',
		'/assets/css/preInfoEdit.css'
	)) }}

	@include('layout.theme')
	@yield('content')
</head>
<body>