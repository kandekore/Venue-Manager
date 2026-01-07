jQuery(document).ready(function($){

    $('.vm-media').click(function(e){
        e.preventDefault();
        let target = $('#' + $(this).data('target'));
        wp.media({ multiple: true }).on('select', function(){
            let ids = wp.media.frame.state().get('selection').map(a=>a.id).join(',');
            target.val(ids);
        }).open();
    });

    if ($('#venue-map').length) {

        let map = new google.maps.Map(document.getElementById('venue-map'), {
            zoom: 8,
            center: {lat: 54.5, lng: -2.5}
        });

        let bounds = new google.maps.LatLngBounds();

        $('.venue-card').each(function(){
            let lat = parseFloat($(this).data('lat'));
            let lng = parseFloat($(this).data('lng'));
            if (!lat || !lng) return;

            let marker = new google.maps.Marker({
                position: {lat, lng},
                map: map,
                title: $(this).data('title')
            });

            bounds.extend(marker.position);
        });

        if (!bounds.isEmpty()) map.fitBounds(bounds);
    }
});
