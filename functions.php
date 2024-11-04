<?php

add_action( 'after_setup_theme', 'crb_load' );
function crb_load() {
    require_once( 'includes/carbon-fields/vendor/autoload.php' );
    \Carbon_Fields\Carbon_Fields::boot();
}

add_action('carbon_fields_register_fields', 'register_carbon_fields');
function register_carbon_fields() {
    // require_once( 'includes/carbon-fields-options/theme-options.php' );
    require_once( 'includes/carbon-fields-options/post-meta.php' );
}




add_action( 'init', 'register_post_types' );
function register_post_types() {
    register_post_type('product', [
        'labels' => [
            'name'               => 'Товары', // основное название для типа записи
            'singular_name'      => 'Товар', // название для одной записи этого типа
            'add_new'            => 'Добавить товар', // для добавления новой записи
            'add_new_item'       => 'Добавление товара', // заголовка у вновь создаваемой записи в админ-панели.
            'edit_item'          => 'Редактирование товара', // для редактирования типа записи
            'new_item'           => 'Новый товар', // текст новой записи
            'view_item'          => 'Смотреть товар', // для просмотра записи этого типа.
            'search_items'       => 'Искать товар', // для поиска по этим типам записи
            'not_found'          => 'Не найдено', // если в результате поиска ничего не было найдено
            'not_found_in_trash' => 'Не найдено в корзине', // если не было найдено в корзине
            'menu_name'          => 'Товары', // название меню
        ],
        'menu_icon'          => 'dashicons-cart',
        'public'             => true,
        'menu_position'      => 5,
        'supports'           => ['title', 'editor', 'thumbnail', 'excerpt'],
        'has_archive'        => true,
        'rewrite'            => ['slug' => 'products']
    ] );

    register_taxonomy('product-categories', 'product', [
        'labels'        => [
            'name'                        => 'Категории товаров',
            'singular_name'               => 'Категория товароа',
            'search_items'                =>  'Искать категории',
            'popular_items'               => 'Популярные категории',
            'all_items'                   => 'Все категории',
            'edit_item'                   => 'Изменить категорию',
            'update_item'                 => 'Обновить категорию',
            'add_new_item'                => 'Добавить новую категорию',
            'new_item_name'               => 'Новое название категории',
            'separate_items_with_commas'  => 'Отделить категории запятыми',
            'add_or_remove_items'         => 'Добавить или удалить категорию',
            'choose_from_most_used'       => 'Выбрать самую популярную категорию',
            'menu_name'                   => 'Категории',
        ],
        'hierarchical'  => true,
    ]);
}
function enqueue_custom_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('custom-ajax-script', get_template_directory_uri() . '/js/custom-ajax.js', array('jquery'), null, true);

    wp_localize_script('custom-ajax-script', 'ajax_params', array(
        'ajax_url' => admin_url('admin-ajax.php'),
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');

add_action('wp_ajax_filter_products', 'ajax_filter_products');
add_action('wp_ajax_nopriv_filter_products', 'ajax_filter_products');

function ajax_filter_products() {
    $order_by = isset($_POST['orderby']) ? sanitize_text_field($_POST['orderby']) : 'date';
    $order = isset($_POST['order']) ? sanitize_text_field($_POST['order']) : 'DESC';
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;

    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 3,
        'paged' => $paged,
        'order' => $order,
    );

    switch ($order_by) {
        case 'price':
            $args['meta_key'] = '_price';
            $args['orderby'] = 'meta_value_num';
            break;

        case 'width':
            $args['meta_key'] = '_width';
            $args['orderby'] = 'meta_value_num';
            break;

        case 'title':
            $args['orderby'] = 'title';
            break;

        case 'date':
        default:
            $args['orderby'] = 'date';
            break;
    }

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            ?>
            <div class="product-item">
                <h2><?php the_title(); ?></h2>
                <p>Цена: <?php echo carbon_get_the_post_meta('price'); ?></p>
                <p>Ширина: <?php echo carbon_get_the_post_meta('width'); ?></p>

            </div>
            <?php
        }// Пагинация
        $total_pages = $query->max_num_pages; // Получаем общее количество страниц
        if ($total_pages > 1) {
            echo '<div class="pagination">';
            for ($i = 1; $i <= $total_pages; $i++) {
                echo '<a href="#" class="page-numbers" data-page="'.$i.'">'.$i.'</a>';
            }
            echo '</div>';
        }
    } else {
        echo '<p>Записи не найдены.</p>';
    }

// Сброс постов
    wp_reset_postdata();
    die();
}

function custom_pagination($total_pages) {
    if ($total_pages > 1) {
        echo '<div class="pagination">';
        for ($i = 1; $i <= $total_pages; $i++) {
            echo '<a href="#" class="page-numbers" data-page="'.$i.'">'.$i.'</a>';
        }
        echo '</div>';
    }
}
