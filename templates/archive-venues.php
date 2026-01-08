<?php get_header(); ?>
<div id="Content">

<div class="venue-container">
    <div class="section venue-archive">
        
        <header class="archive-header">
            <h1>Explore Venues</h1>
            <form method="get" class="venue-filter-form">
                <?php
                wp_dropdown_categories([
                    'taxonomy'        => 'venue_category',
                    'name'            => 'venue_cat',
                    'show_option_all' => 'All Categories',
                    'selected'        => isset($_GET['venue_cat']) ? intval($_GET['venue_cat']) : 0,
                    'class'           => 'venue-select'
                ]);
                ?>
                <button type="submit" class="vm-btn">Filter Results</button>
            </form>
        </header>

        <div id="venue-map"></div>

        <div class="venue-grid">
            <?php
            $args = ['post_type' => 'venues', 'posts_per_page' => -1];
            if (!empty($_GET['venue_cat'])) {
                $args['tax_query'] = [['taxonomy' => 'venue_category', 'terms' => intval($_GET['venue_cat'])]];
            }

            $q = new WP_Query($args);

            if ($q->have_posts()) :
                while ($q->have_posts()) : $q->the_post();
                    $lat   = get_post_meta(get_the_ID(), 'vm_lat', true);
                    $lng   = get_post_meta(get_the_ID(), 'vm_lng', true);
                    $address = get_post_meta(get_the_ID(), 'vm_address', true);
                    ?>

                    <div class="venue-card" 
                         data-lat="<?php echo esc_attr($lat); ?>" 
                         data-lng="<?php echo esc_attr($lng); ?>" 
                         data-title="<?php echo esc_attr(get_the_title()); ?>">
                        
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="venue-card-image">
                                <?php the_post_thumbnail('medium_large'); ?>
                            </div>
                        <?php endif; ?>

                        <div class="venue-card-content">
                               <?php
                                $logo = get_post_meta(get_the_ID(), 'vm_logo', true);
                                if ($logo) {
                                    echo wp_get_attachment_image($logo, 'medium', false, ['class' => 'venue-sidebar-logo']);
                                }
                                ?>
                            <h3><?php the_title(); ?></h3>
                            <?php if($address): ?>
                                <p class="venue-card-address"><i class="fa-solid fa-location-dot"></i> <?php echo esc_html($address); ?></p>
                            <?php endif; ?>
                            <a class="vm-btn btn-full" href="<?php the_permalink(); ?>">View Details</a>
                        </div>
                    </div>

                <?php endwhile; wp_reset_postdata(); ?>
            <?php else : ?>
                <p>No venues found in this category.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
</div>
<?php get_footer(); ?>