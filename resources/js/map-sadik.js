let mapVisible = false;
let map;

// Функция для инициализации карты
function initializeMap() {
    map = new ymaps.Map('map-container', {
        center: [window.address[0].geo_lat, window.address[0].geo_long],
        zoom: 10,
    });

    window.address.forEach((item) => {
        const placemark = new ymaps.Placemark(
            [item.geo_lat, item.geo_long],
            {
                iconContent: item.street_address,
            },
            {
                preset: 'islands#blueStretchyIcon',
            }
        );
        map.geoObjects.add(placemark);
    });
}

// Функция для переключения видимости карты
function toggleMap() {
    const mapContainer = document.getElementById('map-container');
    const mapToggle = document.getElementById('mapToggle');

    if (mapVisible) {
        mapContainer.style.display = 'none';
        mapToggle.textContent = 'Открыть карту';
    } else {
        if (typeof ymaps === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://api-maps.yandex.ru/2.1/?apikey=067bbf35-de27-4de2-bb1c-72d958556cad&lang=ru-RU';
            script.async = true;
            script.onload = () => {
                ymaps.ready(initializeMap);
            };
            document.head.appendChild(script);
        } else {
            initializeMap();
        }
        mapContainer.style.display = 'block';
        mapToggle.textContent = 'Закрыть карту';
    }

    mapVisible = !mapVisible;
}

document.getElementById('mapToggle').addEventListener('click', toggleMap);
