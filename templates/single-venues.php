<?php get_header(); ?>

<div class="section venue-single">
<div class="wrap mcb-wrap">
<div class="column one">

<h1><?php the_title(); ?></h1>

<?php if (has_post_thumbnail()) the_post_thumbnail('full'); ?>

<?php if ($logo = get_post_meta(get_the_ID(),'vm_logo',true)) : ?>
    <?= wp_get_attachment_image($logo,'medium',['class'=>'venue-logo']) ?>
<?php endif; ?>

<div class="venue-info">

<?php if ($address = get_post_meta(get_the_ID(),'vm_address',true)) : ?>
<p><i class="fa fa-location-dot"></i> <?= esc_html($address) ?></p>
<a class="button" target="_blank"
href="https://www.google.com/maps/dir/?api=1&destination=<?= urlencode($address) ?>">
<i class="fa fa-route"></i> Directions</a>
<?php endif; ?>

<?php if ($phone = get_post_meta(get_the_ID(),'vm_phone',true)) : ?>
<p><i class="fa fa-phone"></i> <a href="tel:<?= esc_attr($phone) ?>"><?= esc_html($phone) ?></a></p>
<?php endif; ?>

<?php if ($email = get_post_meta(get_the_ID(),'vm_email',true)) : ?>
<p><i class="fa fa-envelope"></i> <a href="mailto:<?= esc_attr($email) ?>"><?= esc_html($email) ?></a></p>
<?php endif; ?>

</div>

<div class="venue-socials">
<?php foreach (['facebook'=>'facebook-f','instagram'=>'instagram','x'=>'x-twitter','tripadvisor'=>'tripadvisor'] as $k=>$i):
if ($u = get_post_meta(get_the_ID(),"vm_$k",true)) : ?>
<a href="<?= esc_url($u) ?>" target="_blank"><i class="fa-brands fa-<?= $i ?>"></i></a>
<?php endif; endforeach; ?>
</div>

<div class="venue-content"><?php the_content(); ?></div>

<?php
$gallery = array_filter(explode(',',get_post_meta(get_the_ID(),'vm_gallery',true)));
if ($gallery):
echo '<div class="venue-gallery">';
foreach ($gallery as $img) echo wp_get_attachment_image($img,'medium');
echo '</div>';
endif;
?>

</div></div></div>

<?php get_footer(); ?>
