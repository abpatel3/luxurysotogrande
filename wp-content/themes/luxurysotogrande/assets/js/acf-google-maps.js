/**
 * Handle the Google Maps Api
 */
 window.addEventListener('load', initialize, false)

let map
 
/**
 * Set initial map data
 */
function initialize() {
    let mapEl = document.querySelector('#map');

    let location = { lat: parseFloat(mapEl.dataset.lat), lng: parseFloat(mapEl.dataset.lng) }
    // Create initial map
    map = new google.maps.Map(mapEl, {
        center: location,
        zoom: 14
    })

    let marker = new google.maps.Marker({
        map,
        position: location,
        animation: google.maps.Animation.DROP,
    })

    infoWindow = new google.maps.InfoWindow
}