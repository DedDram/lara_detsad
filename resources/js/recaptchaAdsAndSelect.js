document.addEventListener('DOMContentLoaded', function () {
    var widgetId;
    var el;
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var currentShowLink;
    var recaptchaLoaded = false;

    window.onloadCallback = function () {
        el = document.querySelector('.show-popup-recaptcha');

        if (el && !recaptchaLoaded) { // Проверка флага перед загрузкой Recaptcha
            widgetId = grecaptcha.render('popup-recaptcha', {
                'sitekey': '6LdECbcoAAAAADRa7I10HGtK7kt5R46u9VRXWR8T',
                'callback': function (code) {
                    if (currentShowLink) {
                        sendAjaxRequest(currentShowLink.getAttribute('data-task'), currentShowLink.getAttribute('data-id'));
                    }
                }
            });

            recaptchaLoaded = true;
        }
    };

    function sendAjaxRequest(task, item_id) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/ajax?task=' + task + '&item_id=' + item_id, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
        xhr.timeout = 5000;
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    currentShowLink.closest('.show-popup-recaptcha').innerHTML = xhr.responseText;
                    document.querySelector('#popup-recaptcha').style.display = 'none';
                } else {
                    console.log('Error: detsad show ' + task);
                }
            }
        };
        xhr.send();
    }

    document.querySelectorAll('.show-popup-recaptcha a').forEach(function (showLink) {
        showLink.addEventListener('click', function (e) {
            e.preventDefault();
            currentShowLink = showLink;
            if (!recaptchaLoaded) {
                var script = document.createElement('script');
                script.src = 'https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit';
                document.body.appendChild(script);
                script.onload = function () {
                    el = showLink.closest('.show-popup-recaptcha');
                    var rect = el.getBoundingClientRect();
                    var popupRecaptcha = document.querySelector('#popup-recaptcha');
                    popupRecaptcha.style.display = 'block';
                    popupRecaptcha.style.position = 'fixed';
                    popupRecaptcha.style.top = rect.bottom + 'px';
                    popupRecaptcha.style.left = rect.left + 'px';
                };
            }else{
                sendAjaxRequest(currentShowLink.getAttribute('data-task'), currentShowLink.getAttribute('data-id'));
            }
        });
    });

    document.getElementById('citySelect').addEventListener('change', function() {
        var cityValue = this.value;
        var mSelect = document.getElementById('mSelect');

        if (cityValue === '4-moskva') {
            mSelect.style.display = 'block';
        }
    });

    var searchObmenButton = document.getElementById('search-obmen');
    if(searchObmenButton){
        document.getElementById('search-obmen').addEventListener('click', function(event) {
            event.preventDefault();

            var citySelect = document.getElementById('citySelect');
            var metroSelect = document.getElementById('metroSelect');

            var city = citySelect.options[citySelect.selectedIndex].value;
            var metro = metroSelect.options[metroSelect.selectedIndex].value;
            if (city !== '0') {
                var url = '/obmen-mest/' + city;

                if (metro !== '0') {
                    url += '/' + metro;
                }
                console.log(location.href)
                location.href = url;
            }
        });
    }


    var searchRabotaButton = document.getElementById('search-rabota');

    if(searchRabotaButton){
        document.getElementById('search-rabota').addEventListener('click', function(event) {
            event.preventDefault();

            var city = document.getElementById("citySelect").value;
            var metro = document.getElementById("metroSelect").value;
            var sType = document.getElementById("type").value;
            var teachers = document.getElementById("teachers").value;
            var req = [];
            var request = '';

            if (sType !== "0") {
                req.push('type=' + sType);
            }

            if (teachers !== "0") {
                req.push('teachers=' + teachers);
            }

            if (req.length > 0) {
                request = '/?' + req.join('&');
            }

            if (city !== "0") {
                if (metro !== "0") {
                    location.href = '/rabota/' + city + '/' + metro + request;
                } else {
                    location.href = '/rabota/' + city + request;
                }
            } else {
                if (req.length > 0) {
                    location.href = '/rabota' + request;
                }
            }
        });
    }
});
