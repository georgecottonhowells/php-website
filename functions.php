<?php

require get_theme_file_path('/includes/search-route.php');
require get_theme_file_path('/includes/like-route.php');

add_action('rest_api_init', 'blaster_custom_rest');
function blaster_custom_rest()
{

    register_rest_field('post', 'authorName', array(

        'get_callback' => function () {
            return get_the_author();
        }

    ));

    register_rest_field('like', 'authorName', array(

        'get_callback' => function () {
            return get_the_author();
        }

    ));

    register_rest_field('note', 'userNoteCount', array(

        'get_callback' => function () {
            return count_user_posts(get_current_user_id(), 'note');
        }

    ));
}

add_action('wp_enqueue_scripts', 'blaster_files');
function blaster_files()
{

    wp_enqueue_script('main-blaster-javascript', get_theme_file_uri('/build/index.js'), array('jquery'), 1.0, true);
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('custom-google-fonts', 'https://fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('blaster_main_styles', get_theme_file_uri('/build/style-index.css'), array(), 2, 'all');
    wp_enqueue_style('blaster_extra_styles', get_theme_file_uri('/build/index.css'), array(), 2, 'all');


    wp_localize_script('main-blaster-javascript', 'blasterData', array(
        'root_url' => get_site_url(),
        'nonce' => wp_create_nonce('wp_rest'),
    ));
}

function pageBanner($args = NULL)
{

    if (!$args['title']) {
        $args['title'] = get_the_title();
    }
    if (!$args['subtitle']) {
        $args['subtitle'] = get_field('page_banner_subtitle');
    }
    if (!$args['photo']) {

        if (get_field('page_banner_background_image') and !is_archive() and !is_home()) {
            $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
        } else {
            $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
        }
    } ?>

    <div class="page-banner">
        <div class="page-banner__bg-image" style="background-image: url(<?php echo $args['photo']; ?>)"></div>
        <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title"><?php echo $args['title'] ?></h1>
            <div class="page-banner__intro">
                <p><?php echo $args['subtitle'] ?></p>
            </div>
        </div>
    </div>

<?php
}

add_action('after_setup_theme', 'blaster_features');
function blaster_features()
{
    register_nav_menu('headerMenuLocation', 'Header Menu Location');
    register_nav_menu('footerLocationOne', 'Footer Location One');
    register_nav_menu('footerLocationTwo', 'Footer Location Two');

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_image_size('professorLandscape', 400, 260, true);
    add_image_size('professorPortrait', 260, 330, true);
    add_image_size('pageBanner', 1500, 350, true);
}
add_action('pre_get_posts', 'blaster_adjustment_queries');
function blaster_adjustment_queries($query)
{

    if (!is_admin() and is_post_type_archive('program') and is_main_query()) {
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
        $query->set('posts_per_page', -1);
    }

    if (!is_admin() and is_post_type_archive('event') and $query->is_main_query()) {




        $today = date('Ymd');


        $query->set('meta_key', 'event_date');
        $query->set('orderby', 'meta_value_num');
        $query->set('order', 'ASC');
        $query->set('meta_query', array(
            array(
                'key' => 'event_date',
                'compare' => '>=',
                'value' => $today,
                'type' => 'numeric'



            )
        ));
    }
}


add_action('admin_init', 'redirectSubsToFrontend');
function redirectSubsToFrontend()
{
    $ourCurrentUser = wp_get_current_user();

    if (count($ourCurrentUser->roles) == 1 and $ourCurrentUser->roles[0] == 'subscriber') {
        wp_redirect(site_url('/'));
        exit;
    }
}

add_action('wp_loaded', 'noSubsAdminBar');
function noSubsAdminBar()
{
    $ourCurrentUser = wp_get_current_user();

    if (count($ourCurrentUser->roles) == 1 and $ourCurrentUser->roles[0] == 'subscriber') {
        show_admin_bar(false);
    }
}

add_filter('login_headerurl', 'ourHeaderUrl');
function ourHeaderUrl()
{

    return esc_url(site_url('/'));
}

add_action('login_enqueue_scripts', 'ourLoginCSS');
function ourLoginCSS()
{

    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('custom-google-fonts', 'https://fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('blaster_main_styles', get_theme_file_uri('/build/style-index.css'), array(), 2, 'all');
    wp_enqueue_style('blaster_extra_styles', get_theme_file_uri('/build/index.css'), array(), 2, 'all');
}

add_filter('login_headertitle', 'ourLoginTitle');
function ourLoginTitle()
{
    return get_bloginfo('name');
}

add_filter('wp_insert_post_data', 'insert_post_filter', 10, 2);
function insert_post_filter($data, $postarr)
{
    if ($data['post_type'] == 'note') {

        return makeNotePrivate($data, $postarr);
    } else if ($data['post_type'] == 'like') {
        return rejectLike($data, $postarr);
    }

    return $data;
}


function rejectLike($data, $postarr)
{





    $professorID = $postarr['meta_input']['liked_professor_id'];


    $likes = new WP_Query(array(
        'posts_per_page' => -1,
        'post_type' => 'like',
        'author' => get_current_user_id(),
        'meta_query' => array(
            array(
                'key' => 'liked_professor_id',
                'compare' => '=',
                'value' => $professorID,
            ),




        )
    ));




    error_log(print_r($likes->posts, true));


    if ($likes->found_posts > 0) {
        error_log(print_r("already liked", true));

        die("limit reached");
    }
    return $data;
}




function makeNotePrivate($data, $postarr)
{

    if ($data['post_type'] == 'note') {

        error_log(print_r(count_user_posts(get_current_user_id(), 'note'), true));


        if (count_user_posts(get_current_user_id(), 'note') >= 5 and !$postarr['ID']) {

            error_log(print_r("dying", true));


            die("Limit reached");
        }



        $data['post_content'] = sanitize_textarea_field($data['post_content']);
        $data['post_title'] = sanitize_text_field($data['post_title']);
    }

    if ($data['post_type'] == 'note' and $data['post_status'] != 'trash') {

        $data['post_status'] = "private";
    }

    error_log(print_r($data, true));


    return $data;
}
