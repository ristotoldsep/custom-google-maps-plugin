
(function ($) {
    /**
     * initMap
     *
     * Renders a Google Map onto the selected jQuery element
     *
     * @date    22/10/19
     * @since   5.8.6
     *
     * @param   jQuery $el The jQuery element.
     * @return  object The map instance.
     */
    function initMap($el) {

        // Find marker elements within map.
        var $markers = $el.find('.marker');

        // Create generic map with styles.
        var mapArgs = {
            mapId: 'ccd47f4b37710d34',
            // mapId: '74d751c0213cf72e',
            zoom: $el.data('zoom') || 16,
        };

        var map = new google.maps.Map($el[0], mapArgs);

        // Add markers.
        map.markers = [];
        $markers.each(function () {
            initMarker($(this), map);
        });

        // Center map based on markers.
        centerMap(map);

        // Handle map resize and retain center.
        window.addEventListener("resize", function () {
            var center = map.getCenter();
            google.maps.event.trigger(map, "resize");
            map.setCenter(center);
        });

        // Return map instance.
        return map;
    }

    /**
     * initMarker
     *
     * Creates a marker for the given jQuery element and map.
     *
     * @date    22/10/19
     * @since   5.8.6
     *
     * @param   jQuery $el The jQuery element.
     * @param   object The map instance.
     * @return  object The marker instance.
     */
    function initMarker($marker, map) {

        // Get position from marker.
        var lat = $marker.data('lat');
        var lng = $marker.data('lng');
        var icon = $marker.data('icon'); // Get the custom icon URL
        var name = $marker.data('name');

        var latLng = new google.maps.LatLng(parseFloat(lat), parseFloat(lng));

        // Create marker content
        var content = document.createElement('div');
        content.classList.add('custom-marker');
        content.innerHTML = `<img src="${icon}" alt="Marker Icon">`;

        // Create AdvancedMarkerElement instance
        const marker = new google.maps.marker.AdvancedMarkerElement({
            position: latLng,
            map: map,
            content: content,
            title: name,
            gmpClickable: true,
        });

        // Store the marker position in the marker object for later use
        marker.latLng = latLng;

        // Append to reference for later use.
        map.markers.push(marker);

        const mapContent = $marker.html();

        if (mapContent) {
            const infowindow = new google.maps.InfoWindow({
                content: mapContent,
                maxWidth: 310,
                ariaLabel: name,
            });

            marker.addListener("click", () => {
                infowindow.open({
                    anchor: marker,
                    map,
                });

                // Store current map center before opening the infowindow
                var currentCenter = map.getCenter();

                // Close the infowindow and reset map center when clicking anywhere on the map
                map.addListener("click", () => {
                    infowindow.close();
                    map.panTo(currentCenter); // Smoothly re-center map to the stored position
                });

                // Close the InfoWindow and smoothly reset the center when the InfoWindow is closed
                google.maps.event.addListener(infowindow, 'closeclick', function () {
                    map.panTo(currentCenter); // Smoothly re-center map when infowindow is closed
                });
            });
        }
    }

    /**
     * centerMap
     *
     * Centers the map showing all markers in view.
     *
     * @date    22/10/19
     * @since   5.8.6
     *
     * @param   object The map instance.
     * @return  void
     */
    function centerMap(map) {

        // Create map boundaries from all map markers.
        var bounds = new google.maps.LatLngBounds();
        map.markers.forEach(function (marker) {
            bounds.extend(marker.latLng);  // Use the latLng stored in the marker
        });

        // Case: Single marker.
        if (map.markers.length == 1) {
            map.setCenter(bounds.getCenter());

            // Case: Multiple markers.
        } else {
            map.fitBounds(bounds);
        }
    }

    // Render maps on page load.
    $(document).ready(function () {
        $('.acf-map').each(function () {
            var map = initMap($(this));
        });
    });

})(jQuery);
