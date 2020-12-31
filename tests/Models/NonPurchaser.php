<?php

namespace Yab\ShoppingCart\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NonPurchaser extends Model
{
    use SoftDeletes;

    /**
     * The database table name to use.
     *
     * @var string
     */
    protected $table = 'customers';
}
