<!-- 视频区域 -->
<section class="video-section">
    <div class="container">
        <h2 class="section-title"><?php echo esc_html(get_theme_mod('video_section_title', __('产品视频', 'techvision'))); ?></h2>
        <div class="video-grid">
            <?php
            // 查询视频文章
            $video_query = new WP_Query(array(
                'post_type' => 'post',
                'posts_per_page' => 3,
                'category_name' => 'videos',
                'orderby' => 'date',
                'order' => 'DESC'
            ));
            
            if ($video_query->have_posts()) :
                // 显示实际找到的视频数量（有多少显示多少）
                while ($video_query->have_posts()) : $video_query->the_post();
                    $video_url = get_post_meta(get_the_ID(), 'video_url', true);
            ?>
                <div class="video-card">
                    <div class="video-placeholder" <?php if ($video_url) : ?>onclick="window.open('<?php echo esc_url($video_url); ?>', '_blank')" style="cursor: pointer;"<?php endif; ?>>
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="video-img" style="background-image: url('<?php echo get_the_post_thumbnail_url(get_the_ID(), 'medium'); ?>'); background-size: cover; background-position: center;"></div>
                        <?php else : ?>
                            <div class="video-img"></div>
                        <?php endif; ?>
                        <div class="play-btn">▶</div>
                    </div>
                    <h3><?php the_title(); ?></h3>
                    <p><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                </div>
            <?php
                endwhile;
                wp_reset_postdata();
            else :
                // 显示空状态提示
            ?>
                <div class="empty-products-message" style="grid-column: 1 / -1;">
                    <div class="empty-icon">🎬</div>
                    <p><?php _e('暂无产品视频', 'techvision'); ?></p>
                </div>
            <?php
            endif;
            ?>
        </div>
    </div>
</section>
