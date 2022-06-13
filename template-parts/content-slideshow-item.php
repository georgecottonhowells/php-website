<div class="hero-slider__slide" style="background-image: url(<?php echo get_post_meta(the_ID(), 'slideshow_image'); ?>);">
    <div class="hero-slider__interior container">
        <div class="hero-slider__overlay">
            <h2 class="headline headline--medium t-center"><?php the_title() ?></h2>
            <p class="t-center"><?php the_content() ?></p>
            <p class="t-center no-margin"><a href="#" class="btn btn--blue">Learn more</a></p>
        </div>
    </div>
</div>