const STATUS_CLASS_MAP = {
    request: 'bg-primary',
    draft: 'bg-cyan',
    completed: 'bg-success',
    sent: 'bg-success',
    cancelled: 'bg-danger',
    upcoming: 'bg-primary',
    paid: 'bg-success',
};

const STATUS_LABEL_MAP = {
    request: 'New',
    draft: 'On Going',
    sent: 'Sent',
    approved: 'Approved',
    rejected: 'Rejected',
    upcoming: 'Upcoming',
    paid: 'Paid',
    cancelled: 'Cancelled',
};

const STATUS_TEXT_MAP = {
    request: 'text-primary',
    draft: 'text-cyan',
    completed: 'text-success',
    sent: 'text-success',
    cancelled: 'text-danger',
    upcoming: 'text-primary',
    paid: 'text-success',
};

export function statusBg(status) {
    return STATUS_CLASS_MAP[status] ?? 'bg-dark';
}

export function statusLabel(status) {
    return STATUS_LABEL_MAP[status] ?? status;
}

export function statusText(status) {
    return STATUS_TEXT_MAP[status] ?? 'bg-dark';
}
