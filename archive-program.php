<?php get_header();

pageBanner(
    array(
        'title' => 'All Programs',
        'subtitle' => 'WE DONT DO ANYTHING'
    )
);
?>

<div class="container container--narrow page-section">
    <ul>

        <?php while (have_posts()) {

            the_post(); ?>
            <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>



        <?php }




        echo paginate_links();



        ?>
    </ul>






</div>



<?php
get_footer();
?>