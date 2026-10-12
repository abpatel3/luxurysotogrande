<?php $video = get_sub_field('video'); ?>
<?php $heading = get_sub_field('heading'); ?>

<?php $text = get_sub_field('text'); ?>

<?php $logo = get_sub_field('logo'); ?>


<section class="home-video">
	<?php if ( $video ): ?>
        <div class="home-bg__block">
            <div class="home-bg__block-video">
                <div class="videoHolder" data-ratio="<?php echo get_post_meta( get_the_ID(), 'video_aspect_ratio', true ) ?: '16:9'; ?>">
					<?php
					$allowed_video_format = array(
						'webm' => 'video/webm',
						'mp4' => 'video/mp4',
						'ogv' => 'video/ogg',
					);
					$file_info            = wp_check_filetype( $video, $allowed_video_format );?>
                    <video src="<?php echo $video; ?>" autoplay playsinline preload="none" muted="muted" loop="loop" class="video video--local"></video>
                </div>
                <div class="video-content">
	                <?php if ( $heading ): ?>
                        <h2 class="text-3xl font-normal lg:text-4xl">
			                <?php echo $heading; ?>
                        </h2>
	                <?php endif; ?>
	                <?php if ( $text ): ?>
                        <p class="font-light">
			                <?php echo $text; ?>
                        </p>
	                <?php endif; ?>
	                <?php if ( $logo ): ?>
		                <?php echo wp_get_attachment_image( $logo, 'full', false, ['class' => 'w-full h-full object-cover object-center'] ); ?>
	                <?php endif; ?>
                </div>
            </div>
        </div>
	<?php endif; ?>
</section>

