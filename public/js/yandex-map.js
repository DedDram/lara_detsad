/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!************************************!*\
  !*** ./resources/js/yandex-map.js ***!
  \************************************/
function loadYandexMaps() {
  var script = document.createElement("script");
  script.src = "https://api-maps.yandex.ru/2.1/?apikey=067bbf35-de27-4de2-bb1c-72d958556cad&load=package.standard&lang=ru-RU";
  script.onload = function () {
    initMap();
  };
  document.head.appendChild(script);
}
function initMap() {
  if (window.ymaps) {
    var ymaps = window.ymaps;
    var addressGeo = window.address;
    ymaps.ready(function () {
      var map = new ymaps.Map("yandex-map", {
        center: [addressGeo[0].geo_lat, addressGeo[0].geo_long],
        zoom: 10
      });
      var clusterer = new ymaps.Clusterer();
      var placemarks = [];
      addressGeo.forEach(function (address) {
        var placemark = new ymaps.Placemark([address.geo_lat, address.geo_long], {
          iconContent: '<a href="' + address.url + '" style="text-decoration: none;">' + address.text + "</a>"
        }, {
          preset: "islands#blueStretchyIcon"
        });
        placemarks.push(placemark);
      });
      clusterer.add(placemarks);
      map.geoObjects.add(clusterer);
    });
  }
}
loadYandexMaps();
/******/ })()
;