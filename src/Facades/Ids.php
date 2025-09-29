<?php

namespace EventSoft\ServiceKit\Facades;

use EventSoft\ServiceKit\Helpers\Ids as IdsHelper;
use Illuminate\Support\Facades\Facade;

/**
 * Ids facade'i: header'lardan kimlik bilgileri erişimi.
 *
 * @method static string|null tenantId() Tenant ID
 * @method static string|null userId() User ID
 * @method static string|null customerId() Customer ID
 * @method static string|null sellerId() Seller ID
 * @method static string|null adminId() Admin ID
 * @method static string|null sessionId() Session ID
 * @method static string|null requestId() Request ID
 */
class Ids extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return IdsHelper::class;
    }
}
