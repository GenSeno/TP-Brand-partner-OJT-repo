<?php

namespace App\Enums;

use App\Enums\Traits\HasExclude;
use App\Enums\Traits\HasOptions;

/**
 * - DRAFT: Prepared by staff
 * - REQUEST: Quote by customer
 * - SENT: Sent to customer
 * - APPROVED: Approved by customer
 * - REJECTED: Rejected by customer
 * - INVOICED: Converted to invoice
 * 
 * - COMPLETED: Save quotation not draft
 */
enum QuoteStatus: string
{
    use HasOptions;
    use HasExclude;

    case DRAFT = 'draft'; // on going
    case REQUEST = 'request'; // new
    case SENT = 'sent';
    case APPROVED = 'approved'; 
    case REJECTED = 'rejected';
    case INVOICED = 'invoiced';
    
    // QUOTATION status
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        return ucfirst($this->value);
    }
}
