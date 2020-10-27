import '../../../css/renting.scss'
import 'jquery-mask-plugin';
import 'jquery-zoom';

const createMap = (id) => {
    const elem = document.getElementById(id)
    const center = new google.maps.LatLng(elem.dataset.lat, elem.dataset.lng)
    const map = new google.maps.Map(elem, {
        center,
        zoom: 16
    })
    return [ map, center ]
}

window.initMaps = () => {
    const [ simpleMap, latlng1 ] = createMap('map-view')
    new google.maps.Marker({position: latlng1, map: simpleMap})

    const [ schoolMap, latlng2 ] = createMap('schools-view')
    new google.maps.Marker({position: latlng2, map: schoolMap})

    const request = {
        location: latlng2,
        radius: '1500',
        type: ['school']
    }

    const service = new google.maps.places.PlacesService(schoolMap);
    service.nearbySearch(request, (results, status) => {
        if (status == google.maps.places.PlacesServiceStatus.OK) {
            const bounds = new google.maps.LatLngBounds()
            for (let place of results) {
                const image = {
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(25, 25)
                }
                let marker = new google.maps.Marker({
                    map: schoolMap,
                    icon: image,
                    title: place.name,
                    position: place.geometry.location
                })
                bounds.extend(place.geometry.location)
            }
            schoolMap.fitBounds(bounds)
            schoolMap.setZoom(16)
        }
    })
}

$('.carousel-image').zoom();    
$('#offer_offerValue').mask("#,##0.00", {reverse: true});