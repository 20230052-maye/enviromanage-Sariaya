// ----- Configuration -----
const idleTimeLimit = 5 * 60 * 1000; // 5 minutes
const heartbeatInterval = 60 * 1000; // 1 minute

let idleTimer;
let isIdle = false;

// Send event to server
function sendEvent(eventType) {
    fetch('session-tracker.php', {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `event=${eventType}`
    })
    .then(res => res.json())
    .then(data => {
        // Optional: console.log('Session Event:', data);
    })
    .catch(err => console.error(err));
}

// Reset idle timer
function resetIdleTimer() {
    clearTimeout(idleTimer);

    if (isIdle) {
        // User became active again
        isIdle = false;
        sendEvent('active');
    }

    idleTimer = setTimeout(() => {
        isIdle = true;
        sendEvent('idle');
    }, idleTimeLimit);
}

// Listen to user interactions
['mousemove','keydown','scroll','touchstart'].forEach(event => {
    window.addEventListener(event, resetIdleTimer);
});

// Start idle timer
resetIdleTimer();

// Heartbeat: only if user is active
setInterval(() => {
    if (!isIdle) sendEvent('heartbeat');
}, heartbeatInterval);