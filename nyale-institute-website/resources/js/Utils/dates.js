// Accepts "2026-09-21" or "2026-09-21T10:00" and always parses as local time.
const parse = (value) => {
    if (!value) return null;
    const text = String(value);
    const date = new Date(text.length === 10 ? `${text}T00:00:00` : text);
    return Number.isNaN(date.getTime()) ? null : date;
};

export const formatDate = (value) => {
    const date = parse(value);
    return date ? date.toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }) : '';
};

export const formatTime = (value) => {
    const date = parse(value);
    return date ? date.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' }) : '';
};

// "10:00 – 12:30", or "10:00 – 24 Sep, 12:30" when the event ends on a later day.
export const formatTimeRange = (start, end) => {
    const from = parse(start);
    const to = parse(end);
    if (!from) return '';
    if (!to) return formatTime(start);
    if (from.toDateString() === to.toDateString()) return `${formatTime(start)} – ${formatTime(end)}`;
    const day = to.toLocaleDateString('en-GB', { day: 'numeric', month: 'short' });
    return `${formatTime(start)} – ${day}, ${formatTime(end)}`;
};

export const formatDay = (value) => parse(value)?.getDate() ?? '';

export const formatMonth = (value) => {
    const date = parse(value);
    return date ? date.toLocaleDateString('en-GB', { month: 'short' }).toUpperCase() : '';
};
