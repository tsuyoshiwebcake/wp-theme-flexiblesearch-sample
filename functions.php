<?php
/**
 * flexibleSearch関連のJSファイルを読み込みます
 */
add_action( 'wp_enqueue_scripts', 'hook_wp_enqueue_scripts' );
if ( ! function_exists( 'hook_wp_enqueue_scripts' ) ) :

function hook_wp_enqueue_scripts() {
	// "mustache.js" と "flexibleSearch.min.js" の読み込みます
	wp_enqueue_script( 'mustache', get_template_directory_uri() . '/js/flexibleSearch/mustache.js', array( 'jquery' ) );
	wp_enqueue_script( 'flexible-search', get_template_directory_uri() . '/js/flexibleSearch/flexibleSearch.min.js', array( 'jquery' ) );

	// "flexibleSearch-config.js" は "(function ($) {" の部分を "jQuery(function ($) {" とWordPress用に書き換える必要があるため今回は使用しません
	//wp_enqueue_script( 'flexible-search-config', get_template_directory_uri() . '/js/flexibleSearch/flexibleSearch-config.js', array( 'jquery' ) );
}

endif;

/**
 * 改行コードとタグの除去します
 */
if ( ! function_exists( 'del_tags_escapestring' ) ) :

function del_tags_escapestring( $value ) {
	$value = str_replace( array( "\n","\t" ), "", $value );
	$value = strip_tags( $value );

	return $value;
}

endif;

/**
 * flexibleSearchの検索用のJSONファイルを生成します
 * お気軽に試せるようにinitフックを使用していますが、本来は別のタイミングが良いでしょう
 */
add_action( 'init', 'hook_init' );
if ( ! function_exists( 'hook_init' ) ) :

function hook_init() {
	$items = '';

	// 管理画面および、flexibleSearchの検索結果ページではJSONファイルを生成しません
	if( is_admin() || isset( $_GET[ 'search' ] ) ) return;

	// 投稿データを全件取得します
	$posts = get_posts( array( 'posts_per_page'  => -1 ) );
	foreach ( $posts as $post ) {
		$width = '';
		$height = '';

		// 本文を取得します
		$content = apply_filters( 'the_content', $post->post_content );
		$content = del_tags_escapestring( $content );

		// 抜粋を取得します
		$excerpt = $post->post_excerpt;
		$excerpt = del_tags_escapestring( $excerpt );

		// get_the_post_thumbnailを使用するとimgタグを生成するので、タグが検索キーワードとして引っかかってしまい注意が必要です
		//$image = get_the_post_thumbnail( $post->ID );

		// アイキャッチ画像のURLを取得します
		$image_attributes = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'post-thumbnail' );
		$image = '';
		if ( $image_attributes ) {
			$image = $image_attributes[0];

			// 必要に応じてwitdhやheight属性を使用してください
			//$width = $image_attributes[1];
			//$height = $image_attributes[2];
		}


		// カテゴリを取得します
		$post_categorys = get_the_category( $post->ID );
		$categorys = '';
		if ( $post_categorys ) {
			foreach ( $post_categorys as $category ) {
				// カンマ区切りでカテゴリの名称を入れます
				$categorys = $categorys . $category->name;
				if ( $category !== end( $post_categorys ) ) {
					$categorys = $categorys . ',';
				}
			}
		}

		// タグを取得します
		$post_tags = get_the_tags( $post->ID );
		$tags = '';
		if ( $post_tags ) {
			foreach ( $post_tags as $tag ) {
				// カンマ区切りでタグの名称を入れます
				$tags = $tags . $tag->name;
				if ( $tag !== end( $post_tags ) ) {
					$tags = $tags . ',';
				}
			}
		}

		// JSON形式で出力するデータを配列にセットします
		$items[] = array(
			'title' => $post->post_title,
			'excerpt' => $excerpt,
			'content' => $content,
			'permalink' => get_permalink( $post->ID ),
			'tag' => $tags,
			'image' => $image,
			'category' => $categorys,
			// 必要に応じてwitdhやheight属性を使用してください
			//'width' => $width,
			//'height' => $height,
		);
	}

	$result[ 'items' ] = $items;

	// wp-content配下に検索用のJSONファイル "flexiblesearch.json" を書き出します
	file_put_contents( WP_CONTENT_DIR . '/flexiblesearch.json', json_encode( $result ) );
}

endif;

/**
 * テーマの設定を行います
 */
add_action( 'after_setup_theme', 'hook_after_setup_theme' );
if ( ! function_exists( 'hook_after_setup_theme' ) ) :

	function hook_after_setup_theme() {
		// アイキャッチ画像を使用します
		add_theme_support( 'post-thumbnails' );

		// 幅 220px、高さ 165px、切り抜きモードでアイキャッチ画像のサイズを指定します
		set_post_thumbnail_size( 220, 165, true );
	}
endif;
