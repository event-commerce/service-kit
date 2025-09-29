<?php

namespace EventSoft\ServiceKit\Helpers;

use Illuminate\Http\Request;

class Ids
{
    public static function tenantId(?Request $request = null): ?string
    {
        $request ??= request();
        return self::header($request, config('service-kit.headers.tenant'));
    }

    public static function userId(?Request $request = null): ?string
    {
        $request ??= request();
        return self::header($request, config('service-kit.headers.user'));
    }

    public static function customerId(?Request $request = null): ?string
    {
        $request ??= request();
        return self::header($request, config('service-kit.headers.customer'));
    }

    public static function sellerId(?Request $request = null): ?string
    {
        $request ??= request();
        return self::header($request, config('service-kit.headers.seller'));
    }

    public static function adminId(?Request $request = null): ?string
    {
        $request ??= request();
        return self::header($request, config('service-kit.headers.admin'));
    }

    public static function sessionId(?Request $request = null): ?string
    {
        $request ??= request();
        return self::header($request, config('service-kit.headers.session'));
    }

    public static function requestId(?Request $request = null): ?string
    {
        $request ??= request();
        return self::header($request, config('service-kit.headers.request'));
    }

    private static function header(Request $request, ?string $name): ?string
    {
        if (!$name) {
            return null;
        }
        $value = (string) $request->headers->get($name, '');
        return $value !== '' ? $value : null;
    }
}
