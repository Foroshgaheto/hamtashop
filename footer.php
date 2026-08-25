<?php
/**
 * Theme footer.
 *
 * @package Hamta_Base
 */
?>
<footer class="hamta-footer mt-auto border-t border-gray-200 bg-white py-8" role="contentinfo">
	<div class="container mx-auto px-4 text-center text-sm text-[var(--color-muted)]">
		<p>
			&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?>
			<?php bloginfo( 'name' ); ?>
		</p>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
