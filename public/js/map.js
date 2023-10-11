var map = (function() {
  var _private = {
    'geoMap': null,
    'moscow': [55.755381, 37.619044],
    'init': function(el) {
	_private.geoMap = new ymaps.Map(el, {
	  center: _private.moscow,
	  zoom: 9,
	  controls: [],
	},
	{
  	  suppressMapOpenBlock: true,
	  suppressObsoleteBrowserNotifier: true,
	}
	);
		_private.geoMap.controls.add('zoomControl');
	_private.geoMap.controls.add(
		new ymaps.control.SearchControl({useMapBounds: true}), {top: 6,	left: 250});
    },
    'setCenter': function(res, zoom) {
	_private.geoMap.setCenter(res, zoom);
    },
    'setClusterer': function(res) {
	var clusterer = new ymaps.Clusterer();
	var placemarks = [];
	for(var i = 0; i < res.length; i++) {
		var placemark = new ymaps.Placemark([res[i].geo_lat, res[i].geo_long], {
			iconContent: '<a href="' + res[i].url + '" style="text-decoration: none;">' + res[i].text + '</a>'
		}, {
			preset: 'islands#blueStretchyIcon'
		});
		placemarks.push(placemark);
	}
	clusterer.add(placemarks);
	_private.geoMap.geoObjects.add(clusterer);
    }
  };
  return {
    init: function(el) {
	_private.init(el);
    },
    setCenter: function(res, zoom) {
	_private.setCenter(res, zoom);
    },
    setClusterer: function(res) {
	_private.setClusterer(res);
    }
  }
}());

