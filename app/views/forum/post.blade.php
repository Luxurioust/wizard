@include('forum.post-header')
@yield('content')

	@include('layout.navigation')
	@yield('content')

	<div id="lu_content">
		<div class="lu_con_title">单身公寓</div>
		<div class="lu_con_img">
			<span class="lu_line1"></span>
			<span class="lu_line2"></span>
			{{ HTML::image('assets/images/preInfoEdit/hert.png') }}
		</div>
		<div class="lu_content_box clear">

			<div id="if_success"></div>

			@if ($message = Session::get('success'))
				<div class="callout-warning">{{ $message }}</div>
			@endif

			{{ $errors->first('reply_content', '<div class="callout-warning">:message</div>') }}

			<div class="lu_content_main clear">
				<div class="message-re message-border clear">

					@if($author->is_verify == 1)
						<a href="javascript:void(0);" class="large_icon_verify" title="实名认证" alt="实名认证"><span class="large_icon_approve"></span></a>
					@else
					@endif
					<div class="re-headImg-box">
						<div class="re-headImg">
							<a href="{{ route('members.show', $author->id) }}">
								@if($author->portrait)
								{{ HTML::image('portrait/'.$author->portrait) }}
								@else
								{{ HTML::image('assets/images/preInfoEdit/peo.png') }}
								@endif
							</a>
						</div>
						@if($author->sex == 'M')
						{{ HTML::image('assets/images/symbol.png', '', array('class' => 'lu_left sexImg')) }}
						@else
						{{ HTML::image('assets/images/g.jpg', '', array('class' => 'lu_left sexImg')) }}
						@endif

						{{--  Determine user renew status --}}

						@if($author_profile->crenew >= 30)
							@if($author->is_admin)
								<span class="admin">管理员</span>
							@else
							@endif
							<a href="{{ route('members.show', $author->id) }}" class="m-h3" style="color: #FF9900;">{{ $author->nickname }}</a>
						@else
							@if($author->is_admin)
								<span class="admin">管理员</span>
							@else
							@endif
							<a href="{{ route('members.show', $author->id) }}" class="m-h3">{{ $author->nickname }}</a>
						@endif

					</div>
					<h3 class="re-title">{{ badWordsFilter($data->title) }}</h3>
					<p class="m-reply">{{ badWordsFilter($data->content) }}</p>

					<ul class="reply">
						<li><a href="{{ route('support.index') }}" class="a-color-grey">举报</a></li>
						<li>1楼</li>
						<li>{{ date("Y-m-d G:i",strtotime($data->created_at)) }}</li>
						<li><a href="#create_comment" class="a-color-pink smooth">回复</a></li>
					</ul>

				</div>
				<div id="post-ajax">
					<div class="clear guest" style="width;100%; border:1px solid #ededed; border-radius:5px;" id="g-list">

						@foreach($comments as $comment)
						<?php

							// Retrieve comment user profile
							$user			= User::where('id', $comment->user_id)->first();

							// Retrieve user profile
							$user_profile	= Profile::where('user_id', $comment->user_id)->first();
						?>
						<div class="message-re clear">

							@if($user->is_verify == 1)
								<a href="javascript:void(0);" class="large_icon_verify" title="实名认证" alt="实名认证"><span class="large_icon_approve"></span></a>
							@else
							@endif

							<div class="re-headImg-box">
								<div class="re-headImg">
									<a href="{{ route('members.show', $user->id) }}">
										@if($user->portrait)
										{{ HTML::image('portrait/'.$user->portrait) }}
										@else
										{{ HTML::image('assets/images/preInfoEdit/peo.png') }}
										@endif
									</a>
								</div>
								@if($user->sex == 'M')
								{{ HTML::image('assets/images/symbol.png', '', array('class' => 'lu_left sexImg')) }}
								@else
								{{ HTML::image('assets/images/g.jpg', '', array('class' => 'lu_left sexImg')) }}
								@endif

								{{--  Determine user renew status --}}

								@if($user_profile->crenew >= 30)
									@if($user->is_admin)
										<span class="admin">管理员</span>
									@else
									@endif
									<a href="{{ route('members.show', $user->id) }}" class="m-h3" style="color: #FF9900;">{{ $user->nickname }}</a>
								@else
									@if($user->is_admin)
										<span class="admin">管理员</span>
									@else
									@endif
									<a href="{{ route('members.show', $user->id) }}" class="m-h3">{{ $user->nickname }}</a>
								@endif

							</div>
							<p class="g-reply">{{ badWordsFilter($comment->content) }}</p>

							<ul class="reply">
								<li><a href="{{ route('support.index') }}" class="a-color-grey">举报</a></li>
								<li>{{ $floor ++ }}楼</li>
								<li>{{ date("Y-m-d G:i",strtotime($comment->created_at)) }}</li>
								<li><a href="javascript:void(0);" class="a-color-pink reply_comment">回复</a></li>
							</ul>
							<section class="form_box_first">
								{{ Form::open(array(
									'autocomplete'	=> 'off',
									'class'			=> 'reply_comment_form',
									))
								}}
								<textarea class="reply_comment_textarea" id="reply_id_{{ $comment->id }}" name="reply_content">{{ Input::old('content', '回复 '.$user->nickname.':') }}</textarea>
								{{ Form::button('发表', array('class' => 'reply_comment_submit', 'data-nickname' => $user->nickname, 'data-comment-id' => $comment->id, 'data-reply-id' => $user->id)) }}
								{{ Form::close() }}
							</section>
							<div class="message-other">
								<div class="o-others">
									<?php
										$replies = ForumReply::where('comments_id', $comment->id)->get();
									?>
									@foreach($replies as $reply)
									<?php
										$reply_user			= User::where('id', $reply->user_id)->first();

										// Retrieve reply user profile
										$reply_user_profile	= Profile::where('user_id', $reply->user_id)->first();
									?>
									<div>
										<span class="imgSpan">

											@if($reply_user->is_verify == 1)
												<a href="javascript:void(0);" class="small_icon_verify" title="实名认证" alt="实名认证"><span class="small_icon_approve"></span></a>
											@else
											@endif

											<a href="{{ route('members.show', $reply_user->id) }}">
												@if($reply_user->portrait)
												{{ HTML::image('portrait/'.$reply_user->portrait) }}
												@else
												{{ HTML::image('assets/images/preInfoEdit/peo.png') }}
												@endif
											</a>
										</span>
										@if($reply_user->sex == 'M')
										{{ HTML::image('assets/images/symbol.png', '', array('class' => 'o-sexImg')) }}
										@else
										{{ HTML::image('assets/images/g.jpg', '', array('class' => 'o-sexImg')) }}
										@endif

										{{--  Determine user renew status --}}

										@if($reply_user_profile->crenew >= 30)
											@if($reply_user->is_admin)
												<span class="reply_from_admin">管理员</span>
											@else
											@endif
											<a href="{{ route('members.show', $reply_user->id) }}" target="_blank" class="g-h3" style="color: #FF9900;">{{ $reply_user->nickname }}:</a>
										@else
											@if($reply_user->is_admin)
												<span class="reply_from_admin">管理员</span>
											@else
											@endif
											<a href="{{ route('members.show', $reply_user->id) }}" target="_blank" class="g-h3">{{ $reply_user->nickname }}:</a>
										@endif

										<p class="r-value">{{ date("Y-m-d G:i",strtotime($reply->created_at)) }}  {{ badWordsFilter($reply->content) }}</p>
										<a class="replay-a reply_inner">回复</a>

										<section class="form_box_second">
											{{ Form::open(array(
												'autocomplete'	=> 'off',
												'class'			=> 'reply_inner_form'
												))
											}}
											<textarea class="textarea" name="reply_content" id="reply_id_{{ $reply->id }}">{{ Input::old('content', '回复 '.$reply_user->nickname.':') }}</textarea>
											{{ Form::button('回复', array('class' => 'submit', 'data-nickname' => $reply_user->nickname, 'data-comment-id' => $comment->id, 'data-reply-id' => $reply->id)) }}
											{{ Form::close() }}
										</section>
										<span class="span-line"></span>
									</div>
									@endforeach
								</div>
							</div>
						</div>
						@endforeach
					</div>

					{{ pagination($comments->appends(Input::except('page')), 'layout.paginator') }}
				</div>

				<div class="if_error"></div>

				<div class="g-box clear" id="create_comment">
					<h2 class="color" style="margin: 2em 0 1em 2em;">发表评论</h2>
					{{ $errors->first('content', '<div class="callout-warning">:message</div>') }}
					<div class="g-r-box clear" class="clear">
						{{ Form::open(array(
							'autocomplete' 	=> 'off'
							))
						}}
							<input type="hidden" name="type" value="comments">
							{{ Umeditor::css() }}
							{{ Umeditor::content(Input::old('content'), ['id'=>'create_comment_editor', 'class'=>'g-r-value', 'name' => 'content', 'height' => '220']) }}
							{{ Umeditor::js() }}
							{{ Form::button('发表', array('class' => 'g-replay bbs_bottom_btn bbs_bottom_btn', 'id' => 'g-replay')) }}
						{{ Form::close() }}
					</div>
				</div>
			</div>

			<div class="lu_content_right">
				{{ HTML::image('assets/images/sidebar_4.jpg') }}
			</div>
		</div>
	</div>

	@include('layout.copyright')
	@yield('content')

@include('forum.post-footer')
@yield('content')