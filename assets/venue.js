jQuery(document).ready(function($) {

    /* ======================================================
       ADMIN MEDIA UPLOADER (Logo & Gallery)
    ====================================================== */
    let mediaFrame;
    $('.vm-media').on('click', function(e) {
        e.preventDefault();
        const button = $(this);
        const target = $('#' + button.data('target'));
        const isGallery = button.data('target') === 'vm_gallery';

        mediaFrame = wp.media({
            title: isGallery ? 'Select Gallery Images' : 'Select Logo',
            button: { text: 'Use selected' },
            multiple: isGallery
        });

        mediaFrame.on('select', function() {
            if (isGallery) {
                let ids = mediaFrame.state().get('selection').map(a => a.id).join(',');
                target.val(ids);
            } else {
                let id = mediaFrame.state().get('selection').first().id;
                target.val(id);
            }
        });
        mediaFrame.open();
    });

    /* ======================================================
       GOOGLE MAP INITIALIZATION (Archive)
    ====================================================== */
    if (typeof google !== 'undefined') {
        initVenueMap();
    }

    /* ======================================================
       SLICK SLIDER SYNC (Single Venue)
    ====================================================== */
    $('.venue-main-slider').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: true,
        fade: true,
        asNavFor: '.venue-nav-thumbnails'
    });

    $('.venue-nav-thumbnails').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        asNavFor: '.venue-main-slider',
        dots: false,
        arrows: false, // Clean thumbnails without bulky arrows
        focusOnSelect: true,
        responsive: [
            { breakpoint: 768, settings: { slidesToShow: 3 } }
        ]
    });

    /* ======================================================
       PHOTOSWIPE LIGHTBOX LOGIC
    ====================================================== */
    $('.ps-link').on('click', function(e) {
        e.preventDefault();
        var items = [];
        $('.ps-link').each(function() {
            var size = $(this).data('size').split('x');
            items.push({
                src: $(this).attr('href'),
                w: parseInt(size[0], 10),
                h: parseInt(size[1], 10)
            });
        });
        var index = $(this).closest('.slick-slide').data('slick-index');
        var gallery = new PhotoSwipe($('.pswp')[0], PhotoSwipeUI_Default, items, { index: index });
        gallery.init();
    });

});

/* ======================================================
   MAP FUNCTION (Defined outside to stay global)
====================================================== */
function initVenueMap() {
    const mapEl = document.getElementById('venue-map');
    if (!mapEl) return;

    // Get default center from admin settings via data attributes
    const defaultLat = parseFloat(mapEl.getAttribute('data-default-lat')) || 52.4862;
    const defaultLng = parseFloat(mapEl.getAttribute('data-default-lng')) || -1.8904;

    const map = new google.maps.Map(mapEl, {
        zoom: 12,
        center: { lat: defaultLat, lng: defaultLng },
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    const bounds = new google.maps.LatLngBounds();
    const infoWindow = new google.maps.InfoWindow(); 
    const venues = document.querySelectorAll('.venue-card');
    let hasMarkers = false;

    venues.forEach(venue => {
        const lat = parseFloat(venue.getAttribute('data-lat'));
        const lng = parseFloat(venue.getAttribute('data-lng'));
        const title = venue.getAttribute('data-title');
        const link = venue.getAttribute('data-link');
        const logo = venue.getAttribute('data-logo');
        const address = venue.querySelector('.venue-card-address')?.innerText || '';

        if (!isNaN(lat) && !isNaN(lng) && lat !== 0) {
            const marker = new google.maps.Marker({
                position: { lat: lat, lng: lng },
                map: map,
                title: title
            });

            marker.addListener('click', () => {
               const logoHtml = logo ? `<img src="${logo}" style="width:100px; height:auto; display:block; margin: 0 auto 10px auto; border-radius:4px;">` : '';
    
    const content = `
        <div style="padding:10px; font-family: 'Inter', sans-serif; text-align:center; min-width:180px;">
            ${logoHtml}
            <h4 style="margin:0 0 5px 0; font-size:16px; color:#333;">${title}</h4>
            <p style="margin:0 0 10px 0; font-size:12px; color:#666; line-height:1.4;">${address}</p>
            <a href="${link}" style="display:inline-block; color:#bf882d; text-decoration:none; font-weight:bold; font-size:13px; border-top:1px solid #eee; padding-top:8px; width:100%;">
                View Venue →
            </a>
        </div>
                `;
                infoWindow.setContent(content);
                infoWindow.open(map, marker);
            });

            bounds.extend({ lat: lat, lng: lng });
            hasMarkers = true;
        }
    });

    // Only fit bounds if there are markers, otherwise use the admin's default center
    if (hasMarkers) {
        map.fitBounds(bounds);
    }
}