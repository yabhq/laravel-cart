<?php

namespace Yab\ShoppingCart\Tests\Models;

use Yab\ShoppingCart\Traits\Purchaser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Yab\ShoppingCart\Contracts\Purchaser as PurchaserInterface;

class Customer extends Model implements PurchaserInterface
{
    use SoftDeletes, Purchaser;

    /**
     * The database table name to use.
     *
     * @var string
     */
    protected $table = 'customers';
}
