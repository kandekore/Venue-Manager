<?php get_header(); ?>

<div id="Content">
    <div class="content_wrapper clearfix">
        <div class="sections_group">
            <div class="section">
                <div class="section_wrapper mcb-section-inner">
                    
                    <div class="venue-container">
                         <nav class="venue-breadcrumb">
                                    <a href="<?php echo get_post_type_archive_link('venues'); ?>">← Back to all Venues</a>
                                </nav>

                        <div class="venue-flex-layout">
                        
                             <aside class="venue-sidebar">
                                <?php
                                $logo = get_post_meta(get_the_ID(), 'vm_logo', true);
                                if ($logo) {
                                    echo wp_get_attachment_image($logo, 'medium', false, ['class' => 'venue-sidebar-logo']);
                                }
                                ?>

                                <div class="venue-info-block">
                                    <?php
                                    $address = get_post_meta(get_the_ID(), 'vm_address', true);
                                    if ($address) : ?>
                                        <div class="info-item">
                                            <strong>Location</strong>
                                            <p><?php echo esc_html($address); ?></p>
                                            <a class="vm-btn btn-outline btn-small" target="_blank" href="https://www.google.com/maps/dir/?api=1&destination=<?php echo urlencode($address); ?>">
                                                <i class="fa fa-route"></i> Get Directions
                                            </a>
                                        </div>
                                    <?php endif; ?>

                                    <?php
                                    $contact_data = [
                                        'phone'   => ['icon' => 'fa-phone', 'label' => 'Telephone'],
                                        'email'   => ['icon' => 'fa-envelope', 'label' => 'Email'],
                                        'website' => ['icon' => 'fa-globe', 'label' => 'Website']
                                    ];

                                    foreach ($contact_data as $key => $data) :
                                        $val = get_post_meta(get_the_ID(), "vm_$key", true);
                                        if ($val) : ?>
                                            <div class="info-item">
                                                <strong><?php echo esc_html($data['label']); ?></strong>
                                                <p>
                                                    <i class="fa <?php echo esc_attr($data['icon']); ?>"></i> 
                                                    <?php if ($key === 'website') : ?>
                                                        <a href="<?php echo esc_url($val); ?>" target="_blank" rel="noopener">
                        Visit Website
                    </a>
                                                    <?php elseif ($key === 'email') : ?>
                                                        <a href="mailto:<?php echo esc_attr($val); ?>"><?php echo esc_html($val); ?></a>
                                                    <?php else : ?>
                                                        <a href="tel:<?php echo esc_attr($val); ?>"><?php echo esc_html($val); ?></a>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        <?php endif;
                                    endforeach; ?>
                                </div>

                                <div class="venue-social-grid">
                                    <?php
                                    $socials = [
                                        'facebook'    => 'fa-facebook-f',
                                        'instagram'   => 'fa-instagram',
                                        'x'           => 'fa-x-twitter',
                                        'tripadvisor' => 'fa-tripadvisor'
                                    ];
                                    foreach ($socials as $key => $icon) {
                                        $url = get_post_meta(get_the_ID(), "vm_$key", true);
                                        if ($url) {
                                            echo '<a href="'.esc_url($url).'" target="_blank" rel="noopener"><i class="fa-brands '.$icon.'"></i></a>';
                                        }
                                    }
                                    ?>
                                </div>
                            </aside>
                            <main class="venue-main">
                               
                                <h1><?php the_title(); ?></h1>
   <div class="venue-content">
                                    <?php the_content(); ?>
                                </div>
                                <div class="venue-gallery-wrapper">
                                    <div class="venue-main-slider">
                                        <?php 
                                        if (has_post_thumbnail()) {
                                            $full_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                                            echo "<div><a href='".esc_url($full_url)."' class='ps-link' data-size='1200x800'>".get_the_post_thumbnail(get_the_ID(), 'large')."</a></div>";
                                        }
                                        
                                        $gallery = get_post_meta(get_the_ID(), 'vm_gallery', true);
                                        if ($gallery) {
                                            $images = array_filter(explode(',', $gallery));
                                            foreach ($images as $img_id) {
                                                $full = wp_get_attachment_image_src($img_id, 'full');
                                                if ($full) {
                                                    echo "<div><a href='".esc_url($full[0])."' class='ps-link' data-size='{$full[1]}x{$full[2]}'>".wp_get_attachment_image($img_id, 'large')."</a></div>";
                                                }
                                            }
                                        }
                                        ?>
                                    </div>

                                    <div class="venue-nav-thumbnails">
                                        <?php if (has_post_thumbnail()) echo "<div>".get_the_post_thumbnail(get_the_ID(), 'thumbnail')."</div>"; ?>
                                        <?php 
                                        if (!empty($images)) {
                                            foreach ($images as $img_id) {
                                                echo "<div>".wp_get_attachment_image($img_id, 'thumbnail')."</div>";
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>

                             
                            </main>

                           

                        </div> </div> </div>
            </div>
        </div>
    </div>
</div>

<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="pswp__bg"></div>
    <div class="pswp__scroll-wrap">
        <div class="pswp__container">
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
        </div>
        <div class="pswp__ui pswp__ui--hidden">
            <div class="pswp__top-bar">
                <div class="pswp__counter"></div>
                <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
                <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
                <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
                <div class="pswp__preloader"><div class="pswp__preloader__icn"><div class="pswp__preloader__cut"><div class="pswp__preloader__donut"></div></div></div></div>
            </div>
            <button class="pswp__button pswp__button--arrow--left" title="Previous"></button>
            <button class="pswp__button pswp__button--arrow--right" title="Next"></button>
            <div class="pswp__caption"><div class="pswp__caption__center"></div></div>
        </div>
    </div>
</div>

<?php get_footer(); ?>