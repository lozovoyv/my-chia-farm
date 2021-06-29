window._ = require('lodash');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
let token = document.head.querySelector('meta[name="csrf-token"]').content;
window.axios.defaults.headers['X-CSRF-TOKEN'] = token;

window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response.status === 419 && error.response.config && !error.response.config['__isRetryRequest']) {
            return new Promise((resolve, reject) => {
                axios.get('api/token')
                    .then((resp) => {
                        const token = resp.data.token;
                        document.head.querySelector('meta[name="csrf-token"]').content = token;
                        error.response.config['__isRetryRequest'] = true;
                        error.response.config.headers['X-CSRF-TOKEN'] = token;
                        resolve(axios(error.response.config));
                        console.log('New token retrieved.');
                    })
                    .catch((err) => {
                        console.log('Can not retrieve new token', err);
                        reject(err);
                    });
            });
        }

        return Promise.reject(error);
    },
);

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
