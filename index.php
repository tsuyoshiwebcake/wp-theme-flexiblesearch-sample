<?php /** header.phpを読み込みます */ ?>
<?php get_header(); ?>

<!-- #primary -->
<div id="primary">
	<?php /** flexibleSearchの検索フォームを使用するための要素です */ ?>
	<div id="search"></div>
	
	<?php /** flexibleSearchの検索結果を表示するための要素です */ ?>
	<div id="fs-result"></div>
	
	<!-- #content -->
	<div id="content" role="main">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				
				<!-- #post -->
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				
					<!-- .entry-header -->
					<header class="entry-header">
						<h1 class="entry-title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h1>
						<?php the_post_thumbnail(); ?>
					</header><!-- /.entry-header -->
					
					
					<?php /** シングルページの時のみ投稿の本文を表示します */ ?>
					<?php if ( is_single() ) : ?>
						<!-- .entry-content -->
						<div class="entry-content">
							<?php the_content(); ?>
						</div><!-- /.entry-content -->
					<?php endif; ?>
					
				</article><!-- /#post -->
				
			<?php endwhile; ?>
		<?php else : ?>
			<p>投稿がありません。</p>
		<?php endif; ?>
	</div><!-- /#content -->
</div><!-- /#primary -->

<?php /** siderbar.phpを読み込みます */ ?>
<?php get_sidebar(); ?>

<?php /** footer.phpを読み込みます */ ?>
<?php get_footer(); ?>