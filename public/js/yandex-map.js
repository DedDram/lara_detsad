(()=>{var e;(e=document.createElement("script")).src="https://api-maps.yandex.ru/2.1/?apikey=067bbf35-de27-4de2-bb1c-72d958556cad&load=package.standard&lang=ru-RU",e.onload=function(){!function(){if(window.ymaps){var e=window.ymaps,a=window.address;e.ready((function(){var n=new e.Map("yandex-map",{center:[a[0].geo_lat,a[0].geo_long],zoom:10}),o=new e.Clusterer,t=[];a.forEach((function(a){var n=new e.Placemark([a.geo_lat,a.geo_long],{iconContent:'<a href="'+a.url+'" style="text-decoration: none;">'+a.text+"</a>"},{preset:"islands#blueStretchyIcon"});t.push(n)})),o.add(t),n.geoObjects.add(o)}))}}()},document.head.appendChild(e)})();