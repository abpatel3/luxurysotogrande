<?php
/**
 * Renders the content section template
 */

$html = get_sub_field('html');

?>

<?php if($html) : ?>
	<?php echo $html; ?>
<?php endif;?>

