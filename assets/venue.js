jQuery(document).ready(function($){

    let mediaFrame;

    $('.vm-media').on('click', function(e){
        e.preventDefault();

        const button = $(this);
        const target = $('#' + button.data('target'));
        const isGallery = button.data('target') === 'vm_gallery';

        mediaFrame = wp.media({
            title: isGallery ? 'Select Gallery Images' : 'Select Logo',
            button: { text: 'Use selected' },
            multiple: isGallery
        });

        mediaFrame.on('select', function(){
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

});
