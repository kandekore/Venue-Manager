<?php get_header(); ?>

<div class="section venue-single">
<div class="wrap mcb-wrap">
<div class="column one">

<h1><?php the_title(); ?></h1>

<?php if (has_post_thumbnail()) the_post_thumbnail('full'); ?>

<div class="venue-info">
<?php
$address = get_post_meta(get_the_ID(),'vm_address',true);
$lat = get_post_meta(get_the_ID(),'vm_lat',true);
$lng = get_post_meta(get_the_ID(),'vm_lng',true);

if ($address):
?>
<p><i class="fa fa-location-dot"></i> <?= esc_html($address) ?></p>
<a class="button" target="_blank"
href="https://www.google.com/maps/dir/?api=1&destination=<?= urlencode($address) ?>">
<i class="fa fa-route"></i> Directions</a>
<?php endif; ?>
</div>

<div class="venue-content"><?php the_content(); ?></div>

<?php
$gallery = explode(',', get_post_meta(get_the_ID(),'vm_gallery',true));
if ($gallery):
echo '<div class="venue-gallery">';
foreach ($gallery as $id) echo wp_get_attachment_image($id,'medium');
echo '</div>';
endif;
?>

</div></div></div>

<?php get_footer(); ?>
