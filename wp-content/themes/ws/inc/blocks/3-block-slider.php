<?php if(have_rows('slides')): ?>
    <div class="blocks-3-slider">
        <div class="container no-padding">
            <ul>
                <?php while(have_rows('slides')): the_row(); $img = theme('image'); $title = theme('title'); $text = theme('text'); $link = site_url().'/'.theme('link'); ?>

                    <li class="slide">
                        <div class="slide-img">
                            <a href="<?php echo $link; ?>"> <img src="<?php echo $img['url']; ?>" alt=""> </a>
                        </div>
                        <div class="slide-content">
                            <a class="no-style" href="<?php echo $link; ?>"> <h4><?php echo $title; ?></h4> </a>
                            <p class="slider-p"> <?php echo $text; ?></p>
                        </div>

                    </li>

                <?php endwhile; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>

<script>
    jQuery(document).ready(function($){

        if($(window).width() < 480) {
          $('.blocks-3-slider .slick-track').css({
              'display': ' flex'
          })
        }
    });
</script>
