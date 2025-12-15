setTimeout(function () {
    let lastCheck = 0;

    let inputs, index;

    inputs = document.getElementsByTagName('input');
    for (index = 0; index < inputs.length; ++index) {
        let input = inputs[index];
        input.addEventListener('change', function () {
            if (lastCheck + 10000 > Date.now()) return;
            lastCheck = Date.now();
            fetch('/auth/check').then(function (response) {
                response.json().then(function (json) {
                    if (!json.auth) {
                        alert('Je sessie is verlopen!');
                    } else {
                        console.log('Session is still valid');
                    }
                });
            })
        });
    }
}, 1000);
