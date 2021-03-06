<?php

/**
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * PHP Version 5.5
 */

/**
 * Class for website forum, include mapping forum category, single post shows, post author create, update and destory the posts and administators management the posts.
 *
 * @uses        Laravel The PHP frameworks for web artisans http://laravel.com
 * @author      Ri Xu http://xuri.me <xuri.me@gmail.com>
 * @copyright   Copyright (c) Harbin Wizard Techonlogy Co., Ltd.
 * @link        http://www.jinglingkj.com
 * @license     Licensed under The MIT License http://www.opensource.org/licenses/mit-license.php
 * @version     Release: 0.1 2014-12-25
 */

class Admin_ForumResource extends BaseResource
{
    /**
     * Resource view directory
     * @var string
     */
    protected $resourceView = 'admin.forum';

    /**
     * Model name of the resource, after initialization to a model instance
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $model = 'ForumPost';

    /**
     * Resource identification
     * @var string
     */
    protected $resource = 'admin.forum';

    /**
     * Resource database tables
     * @var string
     */
    protected $resourceTable = 'forum';

    /**
     * Resource name (Chinese)
     * @var string
     */
    protected $resourceName = '论坛';

    /**
     * Resource list view
     * GET         /resource
     * @return Response
     */
    public function index()
    {
        // Get all forum categories
        $categories     = ForumCategories::get();

        // Get sort conditions
        $orderColumn    = Input::get('sort_up', Input::get('sort_down', 'created_at'));
        $direction      = Input::get('sort_up') ? 'asc' : 'desc' ;

        // Get search conditions
        $category       = Input::get('category');
        $from           = Input::get('from');
        $like           = Input::get('like');

        // Construct query statement
        $query          = $this->model->orderBy($orderColumn, $direction);

        if ($category) {
            isset($category) AND $query->where('category_id', $category);
        }

        isset($like) AND $query->where('content', 'like', "%{$like}%");
        $datas = $query->paginate(10);
        return View::make($this->resourceView.'.index')->with(compact('datas', 'categories'));
    }

    /**
     * Unclock forum post
     * GET /{id}/block
     * @param integer $id  Post ID in forum
     * @return Response     View
     */
    public function unlock($id)
    {
        // Retrieve post
        $data           = $this->model->find($id);

        // Set block to unblock post in forum
        $data->block    = false;

        if (is_null($data)) {
            return Redirect::back()->with('error', '没有找到对应的'.$this->resourceName.'帖子。');
        } elseif ($data->save()) {
            return Redirect::back()->with('success', $this->resourceName.'帖子解锁成功。');
        } else {
            return Redirect::back()->with('warning', $this->resourceName.'帖子解锁失败。');
        }
    }

    /**
     * Block forum post
     * GET /{id}/block
     * @param $id integer Post ID in forum
     * @return Response     View
     */
    public function block($id)
    {
        // Retrieve post
        $data           = $this->model->find($id);

        // Set block of post in forum
        $data->block    = true;

        if (is_null($data)) {
            return Redirect::back()->with('error', '没有找到对应的'.$this->resourceName.'帖子。');
        } elseif ($data->save()) {
            return Redirect::back()->with('success', $this->resourceName.'帖子屏蔽成功。');
        } else {
            return Redirect::back()->with('warning', $this->resourceName.'帖子屏蔽失败。');
        }
    }

    /**
     * Block forum post
     * GET /{id}/block
     * @param integer $id Post ID in forum
     * @return Response     View
     */
    public function top($id)
    {
        // Retrieve post
        $data           = $this->model->find($id);

        // Set fix top of post in forum
        $data->top  = true;

        if (is_null($data)) {
            return Redirect::back()->with('error', '没有找到对应的'.$this->resourceName.'帖子。');
        } elseif ($data->save()) {
            return Redirect::back()->with('success', $this->resourceName.'帖子置顶成功。');
        } else {
            return Redirect::back()->with('warning', $this->resourceName.'帖子置顶失败。');
        }
    }

    /**
     * GET forum post
     * POST /{id}/block
     * @param integer $id  Post ID in forum
     * @return Response     View
     */
    public function untop($id)
    {
        // Retrieve post
        $data           = $this->model->find($id);

        // Unset fix top of post in forum
        $data->top  = false;

        if (is_null($data)) {
            return Redirect::back()->with('error', '没有找到对应的'.$this->resourceName.'帖子。');
        } elseif ($data->save()) {
            return Redirect::back()->with('success', $this->resourceName.'帖子取消置顶成功。');
        } else {
            return Redirect::back()->with('warning', $this->resourceName.'帖子取消置顶失败。');
        }
    }
    /**
     * Resource destory action
     * DELETE      /resource/{id}
     * @param  integer  $id
     * @return Response
     */
    public function destroy($id)
    {
        $data       = $this->model->find($id);

        if (is_null($data)) {
            return Redirect::back()->with('error', '没有找到对应的'.$this->resourceName.'。');
        } else {

            // Using expression get all picture attachments (Only with pictures stored on this server.)
            preg_match_all( '@_src="(' . route('home') . '/upload/image[^"]+)"@' , $data->content, $match );

            // Construct picture attachments list
            $srcArray   = array_pop($match);

            if (!empty( $srcArray )) { // This post have picture attachments
                // Foreach picture attachments list array
                foreach ($srcArray as $key => $field) {
                    $srcArray[$key] = str_replace(route('home'), '', $srcArray[$key]); // Convert to correct real storage path
                    File::delete(public_path($srcArray[$key])); // Destory upload picture attachments in this post
                }

                // Delete post in forum
                if ($data->delete()) {
                    return Redirect::back()->with('success', $this->resourceName.'删除成功。');
                } else {
                    return Redirect::back()->with('warning', $this->resourceName.'删除失败。');
                }

            } else {
                // Delete post in forum
                if ($data->delete()) {
                    return Redirect::back()->with('success', $this->resourceName.'删除成功。');
                } else {
                    return Redirect::back()->with('warning', $this->resourceName.'删除失败。');
                }
            }
        }

    }

    /**
     * User friendly relation details
     * @param  integer $id User ID
     * @return Response     View
     */
    public function detail($id) {
        $data   = $this->model->where('id', $id)->first();
        $sends  = Like::where('sender_id', $id)->get();
        $inboxs = Like::where('receiver_id', $id)->get();
        $count  = 1;
        return View::make($this->resourceView.'.detail')->with(compact('data', 'sends', 'inboxs', 'count'));
    }

}
