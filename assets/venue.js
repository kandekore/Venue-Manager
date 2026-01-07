jQuery(document).ready(function($){

    $('.vm-media').click(function(e){
        e.preventDefault();
        let target = $('#' + $(this).data('target'));
        wp.media({
            multiple: false
        }).on('select', function(){
            let id = wp.media.frame.state().get('selection').first().id;
            target.val(id);
        }).open();
    });

});
