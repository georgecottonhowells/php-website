<?php get_header();

pageBanner(
    array(
        'title' => 'All Events',
        'subtitle' => 'WE DONT DO ANYTHING'
    )
);
?>
<div class="container container--narrow page-section">
    <?php while (have_posts()) {

        the_post();

        get_template_part('template-parts/content', 'event');
    }




    echo paginate_links();



    ?>

    <hr class="section-break">

    <p><a href="<?php echo site_url('/past-events') ?>" </a>Looking for a recap?</p>




</div>



<?php
get_footer();
?>