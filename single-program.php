<?php

get_header();
pageBanner();

?>

<div class="container container--narrow page-section">
    <?php
    while (have_posts()) {

        the_post(); ?>



        <div class="metabox metabox--position-up metabox--with-home-link">
            <p><a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('program') ?>">
                    <i class="fa fa-home" aria-hidden="true"></i> All Programs</a>
                <span class="metabox__main"><?php the_title(); ?></span>
            </p>
        </div>
        <?php

        the_field('main_body_content');

        $relatedProfessors = new WP_Query(array(


            'posts_per_page' => -1,
            'post_type' => 'professor',

            'meta_query' => array(

                array(
                    'key' => 'related_programs',
                    'compare' => 'LIKE',
                    'value' => '"' . get_the_ID() . '"'

                )


            )
        ));

        if ($relatedProfessors->have_posts()) {


        ?>
            <hr class="section-break">
            <h2 class="headline headline--small"><?php the_title(); ?> Professors</h2>





            <?php

            echo '<ul class="professor-cards>';
            while ($relatedProfessors->have_posts()) {
                $relatedProfessors->the_post(); ?>

                <li class="professor-card__list-item">
                    <a class="professor-card" href="<?php echo get_the_permalink(); ?>">
                        <img class="professor-card__image" src="<?php the_post_thumbnail_url('professorLandscape') ?>">
                        <span class="professor-card__name"><?php the_title(); ?></span>
                    </a>
                </li>
            <?php
            }
            echo '</ul>';
        }

        wp_reset_postdata();

        $today = date('Ymd');
        $homepageEvents = new WP_Query(array(


            'posts_per_page' => 2,
            'post_type' => 'event',
            'meta_key' => 'event_date',
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'meta_query' => array(

                array(
                    'key' => 'related_programs',
                    'compare' => 'LIKE',
                    'value' => '"' . get_the_ID() . '"'

                )


            )
        ));



        if ($homepageEvents->have_posts()) {

            ?>
            <hr class="section-break">
            <h2 class="headline headline--small">Upcoming <?php the_title(); ?> Events</h2>





        <?php


            while ($homepageEvents->have_posts()) {
                $homepageEvents->the_post();

                get_template_part('template-parts/content', 'event');
            }
        } ?>
    <?php
    } ?>
</div>



<?php
get_footer(); ?>