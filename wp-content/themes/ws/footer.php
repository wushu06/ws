<?php wp_footer();?>
<?php

 ?>
<div class="container-fluid footer-top">
	<div class="container">
		<p><?php echo theme('footer_top'); ?></p>
	</div>
</div>
<footer>
	<div class="container">
		<div class="row">
			<?php wp_nav_menu(
			array(
				'theme_location'  => 'footer',
				'container_class' => 'col-lg-9 col-12',
				'container_id'    => '',
				'menu_class'      => 'row',
				'fallback_cb'     => '',
				'menu_id'         => 'footer-menu',
				'depth'           => 2
				)
			); ?>
			<div class="col-lg-3 col-6">
				<img src="<?php echo get_template_directory_uri(); ?>/img/worldpay.png" alt="" width="160px">
				<img src="<?php echo get_template_directory_uri(); ?>/img/rapid-ssl.png" alt="" width="100px">
			</div>
		</div>
	</div>
	<div class="container">
        <p class="encompass" style="line-height: 18px;">Created, developed and managed by <a href="http://www.encompassprint.co.uk/">Encompass Print Solutions Ltd</a>.<br />© Waterstones, 2018. Waterstones Booksellers Limited. Registered in England and Wales. Company number 00610095. Registered office address: 203-206 Piccadilly, London, W1J 9HD.</p>
	</div>
</footer>

<script src="//cdnjs.cloudflare.com/ajax/libs/gsap/1.18.0/TweenMax.min.js"></script>
<div class="note">
</div>

</body>
</html>
