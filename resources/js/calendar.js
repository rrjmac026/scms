import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';

// Make plugins globally available
window.dayGrid = dayGridPlugin;
window.timeGrid = timeGridPlugin;
window.interaction = interactionPlugin;

// Make FullCalendar globally available with plugins
window.FullCalendar = {
    Calendar,
    plugins: [
        dayGridPlugin,
        timeGridPlugin,
        interactionPlugin
    ]
};