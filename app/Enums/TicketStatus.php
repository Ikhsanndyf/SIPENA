<?php

namespace App\Enums;

enum TicketStatus: string
{
    case New = 'new';
    case Analyzed = 'analyzed';
    case InProgress = 'in_progress';
    case WaitingConfirmation = 'waiting_confirmation';
    case Resolved = 'resolved';
    case Rejected = 'rejected';
}
