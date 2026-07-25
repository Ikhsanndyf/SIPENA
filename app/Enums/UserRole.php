<?php

namespace App\Enums;

enum UserRole: string
{
    case Reporter = 'reporter';
    case Developer = 'developer';
}
