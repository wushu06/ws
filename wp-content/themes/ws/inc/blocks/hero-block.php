
<div class="block-hero container-fluid">
    <?php if(theme('use_slider')): ?>
        <?php if(have_rows('images')): ?>
            <div class="hero-slide slick-desktop">
                <?php while(have_rows('images')): the_row(); $image = theme('image'); ?>
                    <img class="" src="<?php echo $image["url"]; ?>" alt="">
                <?php endwhile; ?>
            </div>
            <div class="slick-mobile ">
                <?php while(have_rows('images')): the_row(); $image = theme('image'); ?>
                    <img class="" src="<?php echo $image["sizes"]["custom-size"]; ?>" alt="" width="700">
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="hide-for-desktop">
            <img class="" src="<?php echo theme('img')['url']; ?>" alt="">
        </div>
        <div class="hide-for-mobile ">
            <img class="" src="<?php echo  theme('img')["sizes"]["custom-size"]; ?>" alt="" width="700">
        </div>
    <?php endif; ?>
</div>
