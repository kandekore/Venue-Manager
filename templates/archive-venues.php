<?php get_header(); ?>

<div class="section venue-archive">
<div class="wrap mcb-wrap">

<form method="get">
<?php wp_dropdown_categories([
    'taxonomy'=>'venue_category',
    'name'=>'cat',
    'show_option_all'=>'All Categories'
]); ?>
<button class="button">Filter</button>
</form>

<div id="venue-map"></div>

<?php while (have_posts()): the_post(); ?>
<div class="venue-card" data-lat="<?= get_post_meta(get_the_ID(),'vm_lat',true) ?>"
data-lng="<?= get_post_meta(get_the_ID(),'vm_lng',true) ?>">
<h3><?php the_title(); ?></h3>
<a href="<?php the_permalink(); ?>" class="button">View Venue</a>
</div>
<?php endwhile; ?>

</div></div>

<?php get_footer(); ?>
