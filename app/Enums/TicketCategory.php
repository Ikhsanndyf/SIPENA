<?php

namespace App\Enums;

enum TicketCategory: string
{
    case Bug = 'bug';
    case Access = 'access';
    case Data = 'data';
    case Display = 'display';
    case Other = 'other';
}
