<!-- 轮播图 -->
<section class="carousel">
    <div class="container">
        <div class="carousel-wrapper">
            <?php
            // 获取轮播图文章
            $carousel_query = new WP_Query(array(
                'post_type' => 'post',
                'posts_per_page' => 4,
                'category_name' => 'carousel',
                'orderby' => 'date',
                'order' => 'DESC'
            ));
            
            $slide_index = 0;
            $has_slides = false;
            
            if ($carousel_query->have_posts()) :
                $has_slides = true;
                // 显示实际找到的轮播图数量（有多少显示多少）
                while ($carousel_query->have_posts()) : $carousel_query->the_post();
            ?>
                <div class="carousel-item <?php echo $slide_index === 0 ? 'active' : ''; ?>">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="carousel-img" style="background-image: url('<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>'); background-size: cover; background-position: center;">
                        </div>
                    <?php else : ?>
                        <div class="carousel-img"></div>
                    <?php endif; ?>
                    <div class="carousel-caption">
                        <h2><?php the_title(); ?></h2>
                        <p><?php echo get_the_excerpt(); ?></p>
                    </div>
                </div>
            <?php
                $slide_index++;
                endwhile;
                wp_reset_postdata();
            endif;
            
            // 如果没有轮播图，显示空状态
            if (!$has_slides) :
            ?>
                <div class="carousel-item active">
                    <div class="carousel-img" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="empty-carousel-message">
                            <div class="empty-icon" style="font-size: 64px; margin-bottom: 20px; opacity: 0.7;">🖼️</div>
                            <h2 style="color: #fff; margin-bottom: 10px;"><?php _e('暂无轮播内容', 'techvision'); ?></h2>
                            <p style="color: rgba(255,255,255,0.9);"><?php _e('敬请期待', 'techvision'); ?></p>
                        </div>
                    </div>
                </div>
            <?php
            endif;
            
            // 只在有轮播图时显示控制按钮
            if ($has_slides && $carousel_query->post_count > 1) :
            ?>
                <button class="carousel-btn prev" onclick="changeSlide(-1)">❮</button>
                <button class="carousel-btn next" onclick="changeSlide(1)">❯</button>
                
                <div class="carousel-dots">
                    <?php for ($i = 0; $i < $carousel_query->post_count; $i++) : ?>
                        <span class="dot <?php echo $i === 0 ? 'active' : ''; ?>" onclick="currentSlide(<?php echo $i; ?>)"></span>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
.empty-carousel-message {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    width: 100%;
    max-width: 600px;
    padding: 40px 20px;
}

.empty-carousel-message .empty-icon {
    filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
}
</style>
