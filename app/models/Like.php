<?php

/**
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @uses        Laravel The PHP frameworks for web artisans http://laravel.com
 * @author      Ri Xu http://xuri.me
 * @copyright   Copyright (c) Harbin Wizard Techonlogy Co., Ltd.
 * @link        http://www.jinglingkj.com
 * @since       25th Nov, 2014
 * @license     Licensed under The MIT License http://www.opensource.org/licenses/mit-license.php
 * @version     0.1
 */

/**
 * Like List
 */

use Illuminate\Database\Eloquent\SoftDeletingTrait;


class Like extends BaseModel
{
    /**
     * Soft delete
     * @var boolean
     */
    use SoftDeletingTrait;

    /**
     * softDelete
     * @var array
     */
    protected $softDelete = ['deleted_at'];

    /**
     * Database table (without prefix)
     * @var string
     */
    protected $table = 'like';
}