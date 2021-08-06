import Echo from 'laravel-echo';
window.io = require('socket.io-client');
window.laravel_echo_port='{{env("LARAVEL_ECHO_PORT")}}';   
window.Echo = new Echo({
    broadcaster: 'socket.io',
    host: window.location.hostname + ":" + window.laravel_echo_port
});

window.Echo.channel('room_database_roominfo')
    .listen('PublicEvent', (e) => {
        console.log(e);
    });
