import dayjs from 'dayjs';

const dateFromNow = (date, unit = 'day') => {
    const days = dayjs(date).startOf(unit).diff(dayjs().startOf(unit), unit);

    if (days === 0) return 'Today';
    if (days === 1) return 'Tomorrow';
    if (days === -1) return 'Yesterday';
    if (days > 0) return `in ${days} days`;
    return `${Math.abs(days)} days ago`;
};

export { dateFromNow };
