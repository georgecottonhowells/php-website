<?php get_header();

pageBanner(
    array(
        'title' => 'Search results',
        'subtitle' => 'You searched for &ldquo;' . get_search_query() . '&ldquo;'
    )
);
?>
<div class="container container--narrow page-section">


    <?php if (have_posts()) {



        while (have_posts()) {

            the_post();

            get_template_part('template-parts/content', get_post_type());
        }

        echo paginate_links();
    } else {
        echo "<h1>No results</h1>";
    }


    get_search_form(); ?>








</div>



<?php
get_footer();
?>