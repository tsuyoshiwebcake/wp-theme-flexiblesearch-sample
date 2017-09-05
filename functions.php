<?php
/**
 * flexibleSearch�֘A��JS�t�@�C����ǂݍ��݂܂�
 */
add_action( 'wp_enqueue_scripts', 'hook_wp_enqueue_scripts' );
if ( ! function_exists( 'hook_wp_enqueue_scripts' ) ) :
 
function hook_wp_enqueue_scripts() {   
	// "mustache.js" �� "flexibleSearch.min.js" �̓ǂݍ��݂܂�
	wp_enqueue_script( 'mustache', get_template_directory_uri() . '/js/flexibleSearch/mustache.js', array( 'jquery' ) );
	wp_enqueue_script( 'flexible-search', get_template_directory_uri() . '/js/flexibleSearch/flexibleSearch.min.js', array( 'jquery' ) );
	
	// "flexibleSearch-config.js" �� "(function ($) {" �̕����� "jQuery(function ($) {" ��WordPress�p�ɏ���������K�v�����邽�ߍ���͎g�p���܂���
	//wp_enqueue_script( 'flexible-search-config', get_template_directory_uri() . '/js/flexibleSearch/flexibleSearch-config.js', array( 'jquery' ) );
}
 
endif;

/**
 * ���s�R�[�h�ƃ^�O�̏������܂�
 */
if ( ! function_exists( 'del_tags_escapestring' ) ) :

function del_tags_escapestring( $value ) {
	$value = str_replace( array( "\n","\t" ), "", $value );
	$value = strip_tags( $value );
		
	return $value;
}

endif;

/**
 * flexibleSearch�̌����p��JSON�t�@�C���𐶐����܂�
 * ���C�y�Ɏ�����悤��init�t�b�N���g�p���Ă��܂����A�{���͕ʂ̃^�C�~���O���ǂ��ł��傤
 */
add_action( 'init', 'hook_init' );
if ( ! function_exists( 'hook_init' ) ) :

function hook_init() {
	$items = '';
	
	// �Ǘ���ʂ���сAflexibleSearch�̌������ʃy�[�W�ł�JSON�t�@�C���𐶐����܂���
	if( is_admin() || isset( $_GET[ 'search' ] ) ) return;
	
	// ���e�f�[�^��S���擾���܂�
	$posts = get_posts( array( 'posts_per_page'  => -1 ) );
	foreach ( $posts as $post ) {
		$width = '';
		$height = '';
	
		// �{�����擾���܂�
		$content = apply_filters( 'the_content', $post->post_content );
		$content = del_tags_escapestring( $content );
		 
		// �������擾���܂�
		$excerpt = $post->post_excerpt;
		$excerpt = del_tags_escapestring( $excerpt );
		
		// get_the_post_thumbnail���g�p�����img�^�O�𐶐�����̂ŁA�^�O�������L�[���[�h�Ƃ��Ĉ����������Ă��܂����ӂ��K�v�ł�
		//$image = get_the_post_thumbnail( $post->ID );
		
		// �A�C�L���b�`�摜��URL���擾���܂�
		$image_attributes = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'post-thumbnail' );
		$image = '';
		if ( $image_attributes ) {
			$image = $image_attributes[0];
			
			// �K�v�ɉ�����witdh��height�������g�p���Ă�������
			//$width = $image_attributes[1];
			//$height = $image_attributes[2];
		}
		
		
		// �J�e�S�����擾���܂�
		$post_categorys = get_the_category( $post->ID );
		$categorys = '';
		if ( $post_categorys ) {
			foreach ( $post_categorys as $category ) {
				// �J���}��؂�ŃJ�e�S���̖��̂����܂�
				$categorys = $categorys . $category->name;
				if ( $category !== end( $post_categorys ) ) {
					$categorys = $categorys . ','; 
				}
			}
		}
		 
		// �^�O���擾���܂�
		$post_tags = get_the_tags( $post->ID );
		$tags = '';
		if ( $post_tags ) {
			foreach ( $post_tags as $tag ) {
				// �J���}��؂�Ń^�O�̖��̂����܂�
				$tags = $tags . $tag->name;
				if ( $tag !== end( $post_tags ) ) {
					$tags = $tags . ','; 
				}
			}
		}
		
		// JSON�`���ŏo�͂���f�[�^��z��ɃZ�b�g���܂�
		$items[] = array(
			'title' => $post->post_title,
			'excerpt' => $excerpt,
			'content' => $content,
			'permalink' => get_permalink( $post->ID ),
			'tag' => $tags,
			'image' => $image,
			'category' => $categorys,
			// �K�v�ɉ�����witdh��height�������g�p���Ă�������
			//'width' => $width,
			//'height' => $height,
		);
	}
		
	$result[ 'items' ] = $items;
	
	// wp-content�z���Ɍ����p��JSON�t�@�C�� "flexiblesearch.json" �������o���܂�
	file_put_contents( WP_CONTENT_DIR . '/flexiblesearch.json', json_encode( $result ) );
}

endif;

/**
 * �e�[�}�̐ݒ���s���܂�
 */
add_action( 'after_setup_theme', 'hook_after_setup_theme' );
if ( ! function_exists( 'hook_after_setup_theme' ) ) :

	function hook_after_setup_theme() {
		// �A�C�L���b�`�摜���g�p���܂�
		add_theme_support( 'post-thumbnails' );
		
		// �� 220px�A���� 165px�A�؂蔲�����[�h�ŃA�C�L���b�`�摜�̃T�C�Y���w�肵�܂�
		set_post_thumbnail_size( 220, 165, true );
	}	
endif;
