@include('members.show-header')
@yield('content')

	@include('layout.navigation')
	@yield('content')

	<div id="lu_content">
		<div class="lu_con_title">资料详情</div>
		<div class="lu_con_img">
			<span class="lu_line1"></span>
			<span class="lu_line2"></span>
			{{ HTML::image('assets/images/hert.png') }}
		</div>
		<div class="lu_content_box clear">
			<div class="lu_content_main clear">
				<span class="pi_red lu_left"></span>
				<h2 class="pi_inf lu_left" >{{ $data->nickname }}的资料</h2>
				<div class="pi_content_center">

					@if ($message = Session::get('error'))
					<div class="callout-warning">{{ $message }}</div>
					@endif

					<div class="pi_center_top">
						@if($data->portrait)
						{{ HTML::image('portrait/'.$data->portrait, '', array('class' => 'pi_userhead lu_left')) }}
						@else
						{{ HTML::image('assets/images/preInfoEdit/peo.png', '', array('class' => 'pi_userhead lu_left')) }}
						@endif
						<h3 class="pi_person lu_left">个人简介</h3>
						<p class="pi_introduce lu_left">{{ $profile->self_intro}}</p>
					</div>
					<div class="pi_center_user">
						@if($data->sex == 'M')
						{{ HTML::image('assets/images/symbol.png', '', array('class' => 'pi_sex')) }}
						@elseif($data->sex == 'F')
						{{ HTML::image('assets/images/g.jpg', '', array('class' => 'pi_sex')) }}
						@else
						{{ HTML::image('assets/images/g.jpg', '', array('class' => 'pi_sex')) }}
						@endif
						<span class="pi_name">{{ $data->nickname }}</span>
					</div>
					<ul class="pi_center_main">
						<li>
							<span>出生年:</span>
							<p>{{ $data->born_year }}</p>
						</li>
						<li>
							<span>学校:</span>
							<p>{{ $data->school }}</p>
						</li>
						<li>
							<span>入学年:</span>
							<p>{{ $profile->grade }}</p>
						</li>
						<li>
							<span>星座:</span>
							{{ HTML::image('assets/images/preInfoEdit/constellation/'.$constellationInfo['icon'], '', array('width' => '30', 'height' => '30')) }}
							<p class="pi_special">{{ $constellationInfo['name'] }}</p>
						</li>
						<li>
							<span>标签:</span>
							<p>
							@foreach($tag_str as $tag)
								{{ getTagName($tag) }} &nbsp;
							@endforeach
							</p>
						</li>
						<li><div class="pi_line"></div></li>
						<li>
							<span>爱好:</span>
							<p>{{ $profile->hobbies }}</p>
						</li>
						<li>
							<span>真爱寄语:</span>
							<p>{{ $data->bio }}</p>
						</li>
						<li><div class="pi_line"></div></li>

				{{-- Other user like this user --}}

				{{-- User profile --}}

				@if(Auth::user()->id == $data->id)
						<li>
							<span class="pi_trial">我的爱情考验：{{ $profile->question }}</span>
						</li>
					</ul>

				@elseif($like)

					{{-- Receiver block user --}}

					@if($like->status == 3)

							<li>
								<span class="pi_trial">
								{{ $sex }}的爱情考验：{{ $profile->question }}</span>
							</li>
						</ul>
						<div class="callout-warning">{{ $sex }}已经把你拉黑了。</div>

					{{-- Sender block receiver user --}}

					@elseif($like->status == 4)
						<li>
								<span class="pi_trial">
								{{ $sex }}的爱情考验：{{ $profile->question }}</span>
							</li>
						</ul>
						<div class="callout-warning">你已经把对方拉黑了，考虑下是不是要恢复和{{ $sex }}的朋友关系呢？</div>

					{{-- User like other user ago --}}

					@else
							<li>
								<span class="pi_trial">
								{{ $sex }}的爱情考验：{{ $profile->question }}</span>
							</li>
						</ul>
						{{ Form::open() }}
						<input name="status" type="hidden" value="like" />
						{{ $errors->first('answer', '<div class="callout-warning">:message</div>') }}
						<textarea name="answer" class="answer" rows="3" placeholder="输入你的回答"></textarea>
						<div class="pi_center_bottom">
							<button type="submit">再追一次</button>
						{{ Form::close() }}
						</div>
					@endif

				{{-- Normal --}}

				@elseif($like_me)

					{{-- Receiver block user --}}

					@if($like_me->status == 4)

							<li>
								<span class="pi_trial">
								{{ $sex }}的爱情考验：{{ $profile->question }}</span>
							</li>
						</ul>
						<div class="callout-warning">{{ $sex }}已经把你拉黑了。</div>

					{{-- Sender block receiver user --}}

					@elseif($like_me->status == 3)
						<li>
								<span class="pi_trial">
								{{ $sex }}的爱情考验：{{ $profile->question }}</span>
							</li>
						</ul>
						<div class="callout-warning">你已经把对方拉黑了，考虑下是不是要恢复和{{ $sex }}的朋友关系呢？</div>

					{{-- Receiver accept like --}}

					@elseif($like_me->status == 1)
							<li>
								<span class="pi_trial">
								{{ $sex }}的爱情考验：{{ $profile->question }}</span>
							</li>
						</ul>
						<div class="callout-warning">你已接受{{ $sex }}的邀请。</div>
					@else

							<li>
								<span class="pi_trial">
								{{ $sex }}的爱情考验：{{ $profile->question }}</span>
							</li>
						</ul>
						<div class="callout-warning">{{ $sex }}给我的爱情考验答案 {{ $like_me->answer }}</div>
						<div class="pi_center_bottom">
						{{ Form::open() }}
							<input name="status" type="hidden" value="accept" />
							<input type="submit" value="同意" />
						{{ Form::close() }}
						{{ Form::open() }}
							<input name="status" type="hidden" value="reject" />
							<input type="submit" value="拒绝" />
						{{ Form::close() }}
						</div>

					@endif

				@else
						<li>
							<span class="pi_trial">{{ $sex }}的爱情考验：{{ $profile->question }}</span>
						</li>
					</ul>
					{{ Form::open() }}
					<input name="status" type="hidden" value="like" />
					{{ $errors->first('answer', '<div class="callout-warning">:message</div>') }}
					<textarea name="answer" class="answer" rows="3" placeholder="输入你的回答"></textarea>
					<div class="pi_center_bottom">
						<button type="submit">追{{ $sex }}</button>
					{{ Form::close() }}
					</div>
				@endif

				</div>
			</div>
			<div class="lu_content_right">
				{{ HTML::image('assets/images/sidebar_2.jpg') }}
			</div>
		</div>
	</div>

	@include('layout.copyright')
	@yield('content')

</body>
</html>