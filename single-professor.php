<?php get_header();

while (have_posts()) {
    the_post();

    pageBanner();

    //echo get_the_ID();




?>



    <div id="professor-id" type="hidden" data-professor-id="<?php echo get_the_ID(); ?>"></div>
    <div class="container container--narrow page-section">





        <div class="generic-content">
            <div class="row group">
                <div class="one-third">
                    <?php the_post_thumbnail('professorPortrait'); ?>
                </div>
                <div class="two-thirds">
                    <?php

                    $likecount = new WP_Query(array(
                        'posts_per_page' => -1,
                        'post_type' => 'like',
                        'meta_key' => 'liked_professor_id',
                        'meta_query' => array(
                            array(
                                'key' => 'liked_professor_id',
                                'compare' => '=',
                                'value' => get_the_ID(),
                            )
                        )
                    ));

                    //print_r($likecount);



                    $posts = $likecount->posts;

                    print "<pre>";
                    print_r($posts);
                    print "</pre>";

                    $myLikePost = null;
                    $liked = false;



                    foreach ($posts as $post) {


                        if ($post->post_author == get_current_user_id()) {
                            $liked = true;
                            $myLikePost = $post;
                        }
                    }

                    print "<pre>";
                    print_r($myLikePost);
                    print "</pre>";



                    echo $likecount->found_posts;

                    echo "my like post id is " . $myLikePost->ID;

                    ?>
                    <span class="like-box" data-like_id="<?php echo $myLikePost->ID ?>" data-exists="<?php if ($liked == true) {
                                                                                                            echo "yes";
                                                                                                        } ?>">
                        <i class=" fa fa-heart-o" aria-hidden="true"></i>
                        <i class="fa fa-heart" aria-hidden="true"></i>
                        <span class="like-count"><?php echo $likecount->found_posts; ?></span>
                    </span>
                    <?php

                    the_content();
                    ?>

                </div>
            </div>
        </div>


        <?php
        $relatedPrograms = get_field('related_programs');


        if ($relatedPrograms) {

            echo '<hr class="section-break">';
            echo '<h2 class="headline headline--medium">Subject(s) Taught</h2>';
            echo '<ul class="link-list min-list">';

            foreach ($relatedPrograms as $program) { ?>
                <li><a href="<?php echo get_the_permalink($program); ?>"><?php echo get_the_title($program); ?></a></li>

        <?php }
            echo '</ul>';
        } ?>
    </div>
<?php }

get_footer();
