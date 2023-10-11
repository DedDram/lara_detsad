<script>
export default {
    data() {
        return {
            geoMap: null,
            moscow: [55.755381, 37.619044],
        };
    },
    methods: {
        initMap() {
            this.geoMap = new ymaps.Map(this.$refs.mapContainer, {
                center: this.moscow,
                zoom: 9,
                controls: [],
            }, {
                suppressMapOpenBlock: true,
                suppressObsoleteBrowserNotifier: true,
            });
            this.geoMap.controls.add('zoomControl');
            this.geoMap.controls.add(
                new ymaps.control.SearchControl({ useMapBounds: true }),
                { top: 6, left: 250 }
            );
        },
        setCenter(res, zoom) {
            this.geoMap.setCenter(res, zoom);
        },
        setClusterer(res) {
            const clusterer = new ymaps.Clusterer();
            const placemarks = [];
            for (let i = 0; i < res.length; i++) {
                const placemark = new ymaps.Placemark([res[i].geo_lat, res[i].geo_long], {
                    iconContent: '<a href="' + res[i].url + '" style="text-decoration: none;">' + res[i].text + '</a>',
                }, {
                    preset: 'islands#blueStretchyIcon',
                });
                placemarks.push(placemark);
            }
            clusterer.add(placemarks);
            this.geoMap.geoObjects.add(clusterer);
        },
    },
    mounted() {
        this.initMap();
    },
};
</script>
