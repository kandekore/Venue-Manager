<?php get_header(); ?>

<div id="Content">
    <div class="content_wrapper clearfix">

        <div class="section venue-single">
            <div class="wrap mcb-wrap">
                <div class="column one">

                    <h1><?php the_title(); ?></h1>

                    <?php if (has_post_thumbnail()) {
                        the_post_thumbnail('full');
                    } ?>

                    <?php
                    $logo = get_post_meta(get_the_ID(), 'vm_logo', true);
                    if ($logo) {
                        echo wp_get_attachment_image($logo, 'medium', false, ['class' => 'venue-logo']);
                    }
                    ?>

                    <div class="venue-info">

                        <?php
                        $address = get_post_meta(get_the_ID(), 'vm_address', true);
                        if ($address) : ?>
                            <p>
                                <i class="fa fa-location-dot"></i>
                                <?php echo esc_html($address); ?>
                            </p>
                            <a class="button" target="_blank"
                               href="https://www.google.com/maps/dir/?api=1&destination=<?php echo urlencode($address); ?>">
                                <i class="fa fa-route"></i> Directions
                            </a>
                        <?php endif; ?>

                        <?php
                        $phone = get_post_meta(get_the_ID(), 'vm_phone', true);
                        if ($phone) : ?>
                            <p>
                                <i class="fa fa-phone"></i>
                                <a href="tel:<?php echo esc_attr($phone); ?>">
                                    <?php echo esc_html($phone); ?>
                                </a>
                            </p>
                        <?php endif; ?>

                        <?php
                        $email = get_post_meta(get_the_ID(), 'vm_email', true);
                        if ($email) : ?>
                            <p>
                                <i class="fa fa-envelope"></i>
                                <a href="mailto:<?php echo esc_attr($email); ?>">
                                    <?php echo esc_html($email); ?>
                                </a>
                            </p>
                        <?php endif; ?>

                        <?php
                        $website = get_post_meta(get_the_ID(), 'vm_website', true);
                        if ($website) : ?>
                            <p>
                                <i class="fa fa-globe"></i>
                                <a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener">
                                    <?php echo esc_html(parse_url($website, PHP_URL_HOST)); ?>
                                </a>
                            </p>
                        <?php endif; ?>

                    </div>

                    <div class="venue-socials">
                        <?php
                        $socials = [
                            'facebook'    => 'facebook-f',
                            'instagram'   => 'instagram',
                            'x'           => 'x-twitter',
                            'tripadvisor' => 'tripadvisor'
                        ];

                        foreach ($socials as $key => $icon) {
                            $url = get_post_meta(get_the_ID(), "vm_$key", true);
                            if ($url) {
                                echo '<a href="' . esc_url($url) . '" target="_blank" rel="noopener">';
                                echo '<i class="fa-brands fa-' . esc_attr($icon) . '"></i>';
                                echo '</a>';
                            }
                        }
                        ?>
                    </div>

                    <div class="venue-content">
                        <?php the_content(); ?>
                    </div>

                    <?php
                    $gallery = get_post_meta(get_the_ID(), 'vm_gallery', true);
                    if ($gallery) {
                        $images = array_filter(explode(',', $gallery));
                        if ($images) {
                            echo '<div class="venue-gallery">';
                            foreach ($images as $img_id) {
                                echo wp_get_attachment_image($img_id, 'medium');
                            }
                            echo '</div>';
                        }
                    }
                    ?>

                </div>
            </div>
        </div>

    </div>
</div>

<?php get_footer(); ?>
