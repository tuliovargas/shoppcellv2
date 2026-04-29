window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');


    /* Está tendo conflitos entre o bootstrap e o adminLTE
    Não posso remover o bootstrap por que vários componentes utilizando o BS e obviamente não posso remover o adminLTE pois é o nosso layout

    Ao usar o layout `adminLTE::page` o dropdown que faz o botão de logout aparecer não funciona, o que impede o usuário de fazer logout
    */
    $(function() {
			$(".user-menu a.dropdown-toggle").on("click", function() {
                $('.user-menu').toggleClass('show')
                $('.user-menu .dropdown-menu').toggleClass('show')
			});
		});
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });
