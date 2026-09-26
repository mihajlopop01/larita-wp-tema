<?php
/**
 * Fallback for anything without a specific template (posts, archives, 404).
 */
get_header( null, array( 'variant' => 'default' ) );
?>
<main class="l-page">
	<?php if ( is_404() || ! have_posts() ) : ?>
		<article class="l-page__card l-page__card--center">
			<h1 class="l-page__title">Stranica nije pronađena</h1>
			<p class="l-page__content">Ova stranica ne postoji ili je premeštena.</p>
			<a class="l-btn l-btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">NAZAD NA POČETNU</a>
		</article>
	<?php else : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article class="l-page__card">
				<h1 class="l-page__title"><?php the_title(); ?></h1>
				<div class="l-page__content"><?php the_content(); ?></div>
			</article>
		<?php endwhile; ?>
	<?php endif; ?>
</main>
<?php
get_footer( null, array( 'variant' => 'full' ) );
