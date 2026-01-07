<?php get_header(); ?>
<div id="Content">

<div class="section venue-archive">
<div class="wrap mcb-wrap">

<form method="get">
<?php wp_dropdown_categories([
    'taxonomy'=>'venue_category',
    'name'=>'venue_cat',
    'show_option_all'=>'All Categories',
    'selected'=>$_GET['venue_cat'] ?? ''
]); ?>
<button class="button">Filter</button>
</form>

<div id="venue-map"></div>

<?php
$args = ['post_type'=>'venues','posts_per_page'=>-1];
if (!empty($_GET['venue_cat'])) {
    $args['tax_query'][] = [
        'taxonomy'=>'venue_category',
        'terms'=>intval($_GET['venue_cat'])
    ];
}

$q = new WP_Query($args);

while ($q->have_posts()): $q->the_post(); ?>
<div class="venue-card"
data-lat="<?= esc_attr(get_post_meta(get_the_ID(),'vm_lat',true)) ?>"
data-lng="<?= esc_attr(get_post_meta(get_the_ID(),'vm_lng',true)) ?>"
data-title="<?= esc_attr(get_the_title()) ?>">
<h3><?php the_title(); ?></h3>
<a class="button" href="<?php the_permalink(); ?>">View Venue</a>
</div>
<?php endwhile; wp_reset_postdata(); ?>

</div></div>
</div>
<?php get_footer(); ?>
