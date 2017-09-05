<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<title><?php wp_title( '|', true, 'right' ); ?><?php bloginfo( 'name' ); ?></title>
	<link rel="stylesheet" href="<?php bloginfo ( 'stylesheet_url' ); ?>" >
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Rancho" />
	
	<?php /** wp_head();を定義していない場合、functions.phpで定義しているスクリプトの読み込みが行われません */ ?>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<script>
	// WordPressでは "(function($){" ではなく、"jQuery(function($){" のように書くようにしましょう
	jQuery(function($){
			// flexibleSearchの設定を行います
			$( '#search' ).flexibleSearch({
				// 検索中のローディング画像のパスを指定します
				loadingImgPath : "<?php echo get_template_directory_uri(); ?>/js/flexibleSearch/loading.gif",
				
				// 検索用のJSONファイルまでのパスを指定します
				searchDataPath : "<?php echo esc_url( home_url() ); ?>/wp-content/flexiblesearch.json",
				
				// 検索結果を表示するHTML要素のidを指定します
				//resultBlockId : "content",
				
				// 検索結果のメッセージのテンプレートを指定します
				resultMsgTmpl: ['<div id="fs-result-msg">',
									'<p>{{#keywords}}「{{keywords}}」が {{/keywords}}{{count}} 件見つかりました。',
									'（{{firstPage}}〜{{lastPage}} ページ中 {{currentPage}} ページ目を表示）</p>',
								'</div>'].join(""),
								
				// 検索結果のテンプレートを指定します
				resultItemTmpl: ['<div id="fs-result-item">',
									'<ul>',
									'{{#items}}',
										'<li><a href="{{permalink}}">',
											'{{#image}}',
												'<img src="{{image}}" alt=""{{#width}} width="{{width}}"{{/width}}{{#height}} height="{{height}}"{{/height}} />',
											'{{/image}}',
											'<br />{{&title}}',
										'</a></li>',
									'{{/items}}',
									'</ul>',
								'</div>'
								].join(""),
								
				// 1ページあたりの最大表示件数を指定します
				paginateCount: 5
			});
		});
	</script>

	<div id="wrapper">
		<header id="header" role="banner">
			<a href="<?php echo esc_url( home_url() ); ?>">
				<h1><?php bloginfo( 'name' ); ?></h1>
			</a>
		</header><!-- #header -->
		<div id="main">