(function () {
    'use strict';

    var form = document.getElementById('submit-message');

    if (!form || !window.fetch) {
        return;
    }

    var status = document.getElementById('form-status');
    var submit = document.getElementById('submit');

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        submit.disabled = true;
        status.className = 'form-status';
        status.textContent = 'Sending your message…';

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(function (response) {
                return response.text();
            })
            .then(function (message) {
                if (message === 'YES') {
                    form.reset();
                    status.className = 'form-status success';
                    status.textContent = 'Your message has been sent. Thank you.';
                    return;
                }

                if (message === 'NO') {
                    status.className = 'form-status error';
                    status.textContent = 'The message could not be sent. Please try again later.';
                    return;
                }

                status.className = 'form-status error';
                status.textContent = message.trim();
            })
            .catch(function () {
                status.className = 'form-status error';
                status.textContent = 'A network error interrupted the request. Please try again.';
            })
            .then(function () {
                submit.disabled = false;
            });
    });
}());
