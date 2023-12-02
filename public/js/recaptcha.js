/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};

var widgetId1;
var widgetId2;
var el1, el2;
var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
window.onloadCallback = function () {
  el1 = document.querySelector('#popup-recaptcha');
  el2 = document.querySelector('#popup-recaptcha-telephone');
  if (el1) {
    widgetId1 = grecaptcha.render('popup-recaptcha', {
      'sitekey': '6LctRwwTAAAAAGTlZjgZPmyTKsJBPdM6UspsaVmw',
      'callback': function callback(code) {
        var item_id = document.querySelector('#show-mail a').getAttribute('data-id');
        var type_id = document.querySelector('#show-mail a').getAttribute('data-type');
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/detsad-post?task=mail', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
        xhr.timeout = 5000;
        xhr.onreadystatechange = function () {
          if (xhr.readyState === 4) {
            if (xhr.status === 200) {
              var data = JSON.parse(xhr.responseText);
              document.querySelector('#show-mail').innerHTML = data[0];
              document.querySelector('#popup-recaptcha').style.display = 'none';
            } else {
              console.log('Error: detsad show mail');
            }
          }
        };
        xhr.send('item_id=' + item_id + '&type_id=' + type_id + '&code=' + code);
      }
    });
  }
  if (el2) {
    widgetId2 = grecaptcha.render('popup-recaptcha-telephone', {
      'sitekey': '6LctRwwTAAAAAGTlZjgZPmyTKsJBPdM6UspsaVmw',
      'callback': function callback(code) {
        var item_id = document.querySelector('#show-telephone a').getAttribute('data-id');
        var type_id = document.querySelector('#show-telephone a').getAttribute('data-type');
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/detsad-post?task=telephone', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
        xhr.timeout = 5000;
        xhr.onreadystatechange = function () {
          if (xhr.readyState === 4) {
            if (xhr.status === 200) {
              var data = JSON.parse(xhr.responseText);
              document.querySelector('#show-telephone').innerHTML = data[0];
              document.querySelector('#popup-recaptcha-telephone').style.display = 'none';
            } else {
              console.log('Error: detsad show telephone');
            }
          }
        };
        xhr.send('item_id=' + item_id + '&type_id=' + type_id + '&code=' + code);
      }
    });
  }
};
var showMailLink = document.querySelector('#show-mail a');
if (showMailLink) {
  showMailLink.addEventListener('click', function (e) {
    e.preventDefault();
    var script = document.createElement('script');
    script.src = 'https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit';
    document.body.appendChild(script);
    script.onload = function () {
      document.querySelector('#popup-recaptcha').style.display = 'block';
    };
  });
}
document.querySelector('#show-telephone a').addEventListener('click', function (e) {
  e.preventDefault();
  var script = document.createElement('script');
  script.src = 'https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit';
  document.body.appendChild(script);
  script.onload = function () {
    document.querySelector('#popup-recaptcha-telephone').style.display = 'block';
  };
});
 })()
;
