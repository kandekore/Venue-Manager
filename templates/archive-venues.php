<?php get_header(); ?>

<div id="Content">
    <div class="content_wrapper clearfix">

        <div class="section venue-archive">
            <div class="wrap mcb-wrap">

                <form method="get">
                    <?php
                    wp_dropdown_categories([
                        'taxonomy'        => 'venue_category',
                        'name'            => 'venue_cat',
                        'show_option_all' => 'All Categories',
                        'selected'        => isset($_GET['venue_cat']) ? intval($_GET['venue_cat']) : 0
                    ]);
                    ?>
                    <button type="submit" class="button">Filter</button>
                </form>

                <div id="venue-map"></div>

                <?php
                $args = [
                    'post_type'      => 'venues',
                    'posts_per_page' => -1
                ];

                if (!empty($_GET['venue_cat'])) {
                    $args['tax_query'] = [
                        [
                            'taxonomy' => 'venue_category',
                            'terms'    => intval($_GET['venue_cat'])
                        ]
                    ];
                }

                $q = new WP_Query($args);

                if ($q->have_posts()) :
                    while ($q->have_posts()) : $q->the_post();

                        $lat   = get_post_meta(get_the_ID(), 'vm_lat', true);
                        $lng   = get_post_meta(get_the_ID(), 'vm_lng', true);
                        $title = get_the_title();
                        ?>

                        <div class="venue-card"
                             data-lat="<?php echo esc_attr($lat); ?>"
                             data-lng="<?php echo esc_attr($lng); ?>"
                             data-title="<?php echo esc_attr($title); ?>">

                            <h3><?php the_title(); ?></h3>

                            <a class="button" href="<?php the_permalink(); ?>">
                                View Venue
                            </a>

                        </div>

                    <?php endwhile;
                    wp_reset_postdata();
                endif;
                ?>

            </div>
        </div>

    </div>
</div>

<?php get_footer(); ?>
