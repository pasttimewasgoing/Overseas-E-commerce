<?php

add_action( 'woodmart_after_header', 'my_show_woodmart_categories_nav' );

function my_show_woodmart_categories_nav() {
    if ( function_exists( 'woodmart_product_categories_nav' ) ) {
        // 直接输出 Woodmart 自带的分类导航
        woodmart_product_categories_nav();
    }
}