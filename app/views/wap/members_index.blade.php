<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport">
    <title>淘简历 | 聘爱</title>

    {{ HTML::style('assets/css/wap/public.css') }}

</head>

<style type="text/css">
body, h3, span, ul{ margin:0; padding:0;}
img{border:none; vertical-align:top;}
a{ text-decoration:none; }
li{ list-style:none; }
body{
    max-width:640px;
    background:#eeeeee;
    font-size:8px;
    font-family:Microsoft YaHei,SimHei,Arial,Pro LiHei Pro Medium;
}
#top{
    width:100%;
    height:54px;
    background:rgba(255,255,255,0.7);
    position:fixed;
    z-index:20;
}
#top_logo{
    width:46px;
    margin-top:4px;
    margin-left:6px;
    float:left;
}
#top_slogans{
    width:178px;
    margin-top:6px;
    margin-left:6px;
    color:#f76c6c;
    font-size:1.25em;
    font-weight:bold;
    float:left;
}
#top_slogans h3{
    font-size:1.5625em;
    margin-bottom:3px;
}
#top_download{
    position:absolute;
    top:14px;
    right:10px;
    width:70px;
    height:24px;
    line-height:24px;
    text-align:center;
    font-size:1.4em;
    font-weight:bold;
    color:#ffffff;
    background:#f76c6c;
    border-radius:7px;
}
.tab{
    position:absolute;
    top:54px;
    background:#ffffff;
    width:100%;
}
.tab a{
    float:left;
    width:50%;
    text-align:center;
    height:32px;
    line-height:32px;
    font-size:1.75em;
    font-weight:bold;
    color:#ffb7b7;
}
.tab span{
    display:block;
    width:100%;
    height:1px;
    background:#d9d7d7;
}
.on{ color:#44bbfb!important; }
.on span{ background:#3c92e9; }
#list{ padding-top:70px; }
#list li{
    margin-bottom:1px;
    padding-top:9px;
    padding-bottom:9px;
    padding-left:13px;
    background:#ffffff;
}
.list_head{
    float:left;
    display:block;
    width:6em;
    height:6em;
    border-radius:6em;
    overflow:hidden;
}
.list_head img{
    width:6.5em;
    height: 6.5em;
}
.list_introduction{
    float:left;
    margin-left:2em;
    margin-top:4px;
    color: #333;
}
.list_introduction img{
    width: 2.2em;
    margin: 0.1em 0 0 1.5em;
    float:left;
}
.list_introduction span{
    display: block;
    height: 1.4em;
    line-height: 1.4em;
    font-size: 1.6em;
    color: #777;
    float: left;
}
.list_introduction span.nickname {
    color: #333;
    font-size: 1.8em;
}
.list_lable{
    margin-top:5.8em;
    margin-left:8em;
}
.list_lable span{
    float:left;
    width: 6em;
    height:1.7em;
    line-height:1.7em;
    margin-right:10px;
    margin-bottom:4px;
    text-align:center;
    background:#ffa6a6;
    border-radius:10px;
    font-size:12px;
    color:#ffffff;
}

.small_icon_approve {
    background-image: url(../../assets/images/sex/verified-20x20.png);
    background-repeat: no-repeat;
    background-color: #FFF;
    max-width: 20px;
    width: 20px;
    height: 20px;
    max-height: 20px;
    overflow: hidden;
    margin-top: -10px;
    position: relative;
    display: inherit;
    left: 4.4em;
    top: 2em;
    border-radius: 50%;
    -webkit-border-radius: 50%;
    vertical-align: -2px;
}

.list_lable span:nth-of-type(2){ background:#76d2fb; }
.list_lable span:nth-of-type(3){ background:#ffa3e6; }
.list_lable span:nth-of-type(4){ background:#4aed3a; }
.list_lable span:nth-of-type(5){ background:#ffa6a6; }
.list_lable span:nth-of-type(6){ background:#76d2fb; }
.list_lable span:nth-of-type(7){ background:#febe4d; }
.list_lable span:nth-of-type(8){ background:#fbe539; }
.list_lable span:nth-of-type(9){ background:#3c92e9; }
.list_lable span:nth-of-type(10){ background:#4aed3a; }
.list_lable span:nth-of-type(11){ background:#ffa6a6; }
.list_lable span:nth-of-type(12){ background:#76d2fb; }
.list_lable span:nth-of-type(13){ background:#febe4d; }
.list_lable span:nth-of-type(14){ background:#fbe539; }
.list_lable span:nth-of-type(15){ background:#3c92e9; }
.list_lable span:nth-of-type(16){ background:#4aed3a; }
.list_lable span:nth-of-type(17){ background:#ffa6a6; }
.list_lable span:nth-of-type(18){ background:#76d2fb; }
.clear { zoom:1; }
.clear:after { content:''; display:block; clear:both; }

.lu_paging {
    text-align: center;
    margin-bottom: 9em;
}

.lu_paging a {
    margin: 2em auto;
    display: block;
    padding: 0.2em 0.5em;
    top:14px;
    right:10px;
    width:70px;
    height:24px;
    line-height:24px;
    text-align:center;
    font-size:1.4em;
    color:#ffffff;
    background:#f76c6c;
    border-radius:3px;
}

.lu_paging a, .lu_paging span {
    display: none;
}

.lu_paging a[rel=next], .lu_paging a[rel=prev] {
    margin: 2em auto;
    display: block;
    padding: 0.2em 0.5em;
    top:14px;
    right:10px;
    width:80%;
    height:24px;
    line-height:24px;
    text-align:center;
    font-size:1.4em;
    color:#ffffff;
    background:#f76c6c;
    border-radius:6px;
}


</style>

@if($user->sex == 'M')
    <style type="text/css">
        .list_lable span:nth-of-type(1){ background:#ffab50; }
    </style>
@else
    <style type="text/css">
        .list_lable span:nth-of-type(1){ background:#fe949e; }
    </style>
@endif
<body>
    <div id="top">
        {{ HTML::image('assets/images/wechat/logo.png', '', array('id' => 'top_logo')) }}
        <span id="top_slogans"><h3>{{ Lang::get('navigation.pinai') }}</h3>{{ Lang::get('index.title') }}</span>
        <a id="top_download" href="{{ route('wap.redirect') }}">下载{{ Lang::get('navigation.pinai') }}</a>
    </div>
    <ul id="list">
        @foreach($datas as $data)
        <?php
            $data               = User::find($data->id);
            $profile            = Profile::where('user_id', $data->id)->first();

            // Get user's constellation
            $constellationInfo  = getConstellation($profile->constellation);
            $tag_str            = array_unique(explode(',', substr($profile->tag_str, 1)));
        ?>
        <a href="{{ route('wap.get_members_show', $id) }}?user_id={{ $data->id }}">
            <li class="clear">
                @if($data->is_verify == 1)
                    <span class="small_icon_approve"></span>
                @else
                @endif
                <span class="list_head">
                    @if($data->portrait)
                        @if (File::exists('portrait/'.$data->portrait) && File::size('portrait/' . $data->portrait) > 0)
                            {{ HTML::image('portrait/'.$data->portrait) }}
                        @else
                            {{ HTML::image('assets/images/preInfoEdit/peo.png') }}
                        @endif
                    @else
                    {{ HTML::image('assets/images/preInfoEdit/peo.png') }}
                    @endif
                </span>
                <div class="list_introduction">
                    <span class="nickname">{{ $data->nickname }}</span>
                    @if($data->sex == 'M')
                    {{ HTML::image('assets/images/sex/boy.png') }}
                    @elseif($data->sex == 'F')
                    {{ HTML::image('assets/images/sex/girl.png') }}
                    @else
                    {{ HTML::image('assets/images/sex/no_icon.png') }}
                    @endif

                    <br />
                    <span>{{ $data->school }}</span>
                </div>
                <div class="list_lable">
                    @if(isset($tag_str[0]))
                        <span>{{ getTagName($tag_str[0]) }}</span>
                    @endif
                    @if(isset($tag_str[1]))
                        <span>{{ getTagName($tag_str[1]) }}</span>
                    @endif
                </div>
            </li>
        </a>
        @endforeach
    </ul>

    {{ pagination($all_paginate->appends(Input::except('page')), 'layout.paginator') }}

    <footer class="common-foot">
        <a href="{{ route('wap.get_like_jobs', $id) }}"><p>招聘会</p></a>
        <a href="{{ route('wap.get_members_index', $id) }}" class="active"><p>淘简历</p></a>
        <a href="{{ route('wap.office', $id) }}"><p>办公室</p></a>
        <a href="{{ route('wap.get_download_app', $id) }}?type=tab"><p>下载聘爱</p></a>
    </footer>

    @include('wap.wechat_share')
    @yield('content')

    @include('layout.analytics')
    @yield('content')

</body>
</html>