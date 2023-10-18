<template>
    <a href="#" class="map-wrapper-toogle" @click.prevent="toggleMap">Открыть карту</a>
    <div class="map-wrapper" v-if="mapVisible">
        <div id="map-container" style="width: 100%; height: 215px; margin: 10px 0;"></div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            mapVisible: false,
        };
    },
    methods: {
        initializeMap() {
            // Инициализация карты
            const map = new ymaps.Map('map-container', {
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
        },
        toggleMap() {
            this.mapVisible = !this.mapVisible;

            if (this.mapVisible) {
                if (typeof ymaps === 'undefined') {
                    const script = document.createElement('script');
                    script.src = 'https://api-maps.yandex.ru/2.1/?apikey=067bbf35-de27-4de2-bb1c-72d958556cad&lang=ru-RU';
                    script.async = true;
                    script.onload = () => {
                        ymaps.ready(this.initializeMap);
                    };
                    document.head.appendChild(script);
                } else {
                    this.initializeMap();
                }
            }
        },
    },
};
</script>
