<?php

/**
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @uses 		Laravel The PHP frameworks for web artisans http://laravel.com
 * @author 		Ri Xu http://xuri.me
 * @copyright 	Copyright (c) Harbin Wizard Techonlogy Co., Ltd.
 * @link 		http://www.jinglingkj.com
 * @since 		25th Nov, 2014
 * @license 	Licensed under The MIT License http://www.opensource.org/licenses/mit-license.php
 * @version 	0.1
 */

class Admin_UserResource extends BaseResource
{
	/**
	 * Resource view directory
	 * @var string
	 */
	protected $resourceView = 'admin.users';

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
	protected $resourceName = '用户';

	/**
	 * Custom validation message
	 * @var array
	 */
	protected $validatorMessages = array(
		'email.required'		=> '请输入邮箱地址。',
		'email.email'			=> '请输入正确的邮箱地址。',
		'email.unique'			=> '此邮箱已被使用。',
		'password.required'		=> '请输入密码。',
		'password.alpha_dash'	=> '密码格式不正确。',
		'password.between'		=> '密码长度请保持在:min到:max位之间。',
		'password.confirmed'	=> '两次输入的密码不一致。',
		'is_admin.in'			=> '非法输入。',
	);

	/**
	 * Resource list view
	 * GET         /resource
	 * @return Response
	 */
	public function index()
	{
		// Get sort conditions
		$orderColumn	= Input::get('sort_up', Input::get('sort_down', 'created_at'));
		$direction		= Input::get('sort_up') ? 'asc' : 'desc' ;
		// Get search conditions
		switch (Input::get('status')) {
			case '0':
				$is_admin = 0;
				break;
			case '1':
				$is_admin = 1;
				break;
		}
		switch (Input::get('target')) {
			case 'email':
				$email = Input::get('like');
				break;
		}
		// Construct query statement
		$query = $this->model->orderBy($orderColumn, $direction);
		isset($is_admin) AND $query->where('is_admin', $is_admin);
		isset($email)    AND $query->where('email', 'like', "%{$email}%");
		$datas = $query->paginate(10);
		return View::make($this->resourceView.'.index')->with(compact('datas'));
	}

	public function edit($id) {
		$data		= $this->model->where('id', $id)->first();
		$profile	= Profile::where('user_id', $id)->first();
		return View::make($this->resourceView.'.edit')->with(compact('data', 'profile'));
	}

	public function detail($id) {
		$data	= $this->model->where('id', $id)->first();
		$sends	= Like::where('sender_id', $id)->get();
		$inboxs	= Like::where('receiver_id', $id)->get();
		$count = 1;
		return View::make($this->resourceView.'.detail')->with(compact('data', 'sends', 'inboxs', 'count'));
	}
}