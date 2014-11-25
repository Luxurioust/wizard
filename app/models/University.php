<?php

/**
 * University List
 */

use Illuminate\Database\Eloquent\SoftDeletingTrait;


class University extends BaseModel
{
    /**
     * Soft delete
     * @var boolean
     */
    use SoftDeletingTrait;

    protected $softDelete = ['deleted_at'];
    /**
     * Database table (without prefix)
     * @var string
     */
    protected $table = 'university';

}