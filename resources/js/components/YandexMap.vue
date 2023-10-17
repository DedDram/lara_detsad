<template>
    <div>
        <div id="yandex-map" style="width: 100%; height: 400px;"></div>
        <div id="cluster-count"></div>
    </div>
</template>

<script>
export default {
    mounted() {
        this.loadYandexMaps();
    },
    methods: {
        loadYandexMaps() {
            const script = document.createElement("script");
            script.src = "https://api-maps.yandex.ru/2.1/?apikey=067bbf35-de27-4de2-bb1c-72d958556cad&load=package.standard&lang=ru-RU";
            script.onload = () => {
                this.initMap();
            };
            document.head.appendChild(script);
        },
        initMap() {
            if (window.ymaps) {
                const ymaps = window.ymaps;
                const addressGeo = window.address;
                ymaps.ready(() => {
                    const map = new ymaps.Map("yandex-map", {
                        center: [addressGeo[0].geo_lat, addressGeo[0].geo_long],
                        zoom: 10,
                    });

                    var clusterer = new ymaps.Clusterer();
                    var placemarks = [];

                    addressGeo.forEach((address) => {
                        var placemark = new ymaps.Placemark(
                            [address.geo_lat, address.geo_long],
                            {
                                iconContent:
                                    '<a href="' + address.url + '" style="text-decoration: none;">' + address.text + "</a>",
                            },
                            {
                                preset: "islands#blueStretchyIcon",
                            }
                        );
                        placemarks.push(placemark);
                    });

                    clusterer.add(placemarks);
                    map.geoObjects.add(clusterer);
                });
            }
        },
    },
};
</script>
