<?php
/**
 * The template for displaying oauth1 authorized pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Refactored Fox Rest API OAuth
 */

// Start session
session_start();

use RefactoredFox\OAuth1\Oauth;

// Work out where we are.
if (isset($_GET['oauth_token']) && isset($_GET['oauth_verifier'])) {
	Oauth\get_token_credentials();
}elseif (isset($_GET['authorize']))  {
	Oauth\authorize_site();
}

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<section class="container page-content">
				<div class="flex-grid align-center">
					<?php
					while ( have_posts() ) : the_post();
					?>

					<div class="col-xs-12">
						<?php get_template_part( 'template-parts/content', 'page' ); ?>
					</div>
					<?php
					endwhile; // End of the loop.
					?>
				</div>
			</section><!-- .page-content -->

		</main><!-- #main -->
	</div><!-- #primary -->

<?php

get_footer();
