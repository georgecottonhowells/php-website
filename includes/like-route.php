<?php

add_action('rest_api_init', 'blasterLikeRoutes');

function blasterLikeRoutes()
{

    register_rest_route('blaster/v1', 'like', array(
        'methods' => 'POST',
        'callback' => 'createLike'
    ));

    register_rest_route('blaster/v1', 'like', array(
        'methods' => 'DELETE',
        'callback' => 'deleteLike'
    ));
}

function createLike($data)
{


    error_log("\n creating like \n");
    $professor = sanitize_text_field($data['professorID']);

    if (get_post_type($professor) == 'professor') {
        $post = array(
            'post_type' => 'like',
            'post_status' => 'publish',
            'meta_input' => array(
                'liked_professor_id' => $professor
            ),
        );

        $post_id = wp_insert_post($post);

        return $post_id;
    }
}

function deleteLike($data)
{





    $like_id = sanitize_text_field($data['like_id']);

    if (get_current_user_id() == get_post_field('post_author', $like_id) and get_post_type($like_id) == 'like') {

        wp_delete_post($like_id);

        return "succesfully deleted";
    } else {

        die("no permission");
    }
}
