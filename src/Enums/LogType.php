<?php

namespace EventSoft\ServiceKit\Enums;

enum LogType: string
{
    case HTTP = 'http';
    case BUSINESS = 'business';
    case ERROR = 'error';
    case PERFORMANCE = 'performance';
    case SECURITY = 'security';
    case AUDIT = 'audit';
    case SYSTEM = 'system';
}

