<?php
/*
Plugin Name: XrenQuery
Plugin URI: https://xren.su/plugins/XrenQuery/
Description: Простейший плагин для WordPress позволяющие сделать цикл вывода статей в нужном на сайте месте.
Version: v0.9x
Author: Lenin-Kerrigan
Author URI: https://vk.com/rustalenin
/*  Copyright 2017 Lenin-Kerrigan (email: rustalenin@mail.ru)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//Добавляем новое меню в админку Wordpress - меню управления плагином
add_action('admin_menu', 'xq_admin_page');
function xq_admin_page() {
    add_options_page('XrenQuery', 'XQ', 8, 'XrenQuery.php', 'xq_options_page');
}

function xq_options_page() {	// Функция создания и обработки страницы настроек плагина
    ?>
	<div class="wrap">
    <h2><?php echo get_admin_page_title() ?></h2> <!--Заголовок страницы настроек плагина-->
	
	<form action="options.php" method="POST">
		<?php
			settings_fields( 'xq_group' );     // скрытые защитные поля
			do_settings_sections( 'xq_page' ); // секции с настройками (опциями). У нас она всего одна 'section_id'
			submit_button();
		?>
	</form>

    <!-- Вывод информации о плагине. -->
	<span>Для того, чтобы вывести посты в нужном вам месте, поместите туда шорт код [XQ] или, если у вас отключены шорткоды - команду <?php echo htmlspecialchars("<?php do_shortcode('[XQ]'); ?>"); ?> </span><br>
    <?php echo __('XrenQuery','XQ_plugin'); ?> <?php echo __('By: <a href="https://xren.su" target="_blank">Lenin-Kerrigan</a>','XQ_plugin'); ?>
	
	<h3> Материалы которые будут выведены и их внешний вид: </h3>
	<b> Проверьте, те ли это материалы, которые вам нужны, перед размещением шорткода </b>
	<div class="xq-example">
	<?php do_shortcode('[XQ]'); ?>
	</div>

	</div>
	<?php
}
//Регистрируем настройки.
add_action('admin_init', 'xq_settings');
function xq_settings(){
	register_setting( 'xq_group', 'xq_options', 'sanitize_callback' ); //Регистрируем нашу группу настроек плагина
	add_settings_section( 'section_id', 'Основные настройки', '', 'xq_page' ); //Задаём первую секцию настроек, все отдельные поля будут привязаны к ней
	add_settings_field('xq_post_type', 'Тип записей', 'xq_field1', 'xq_page', 'section_id' );
	add_settings_field('xq_post_count', 'Количество записей', 'xq_field2', 'xq_page', 'section_id' );
	add_settings_field('xq_post_tax', 'Категории записей', 'xq_field3', 'xq_page', 'section_id' );
	add_settings_field('xq_post_thumb', 'Размер картинки', 'xq_field4', 'xq_page', 'section_id' );
	// Вносим настройки по умолчанию
	if (empty(get_option('xq_options'))) {
		$def_set = array(
		'xq_post_type' => 'post',
		'xq_post_count' => 4,
		'xq_post_thumb' => 'thumbnail'
		);
		update_option('xq_options', $def_set);
	}
}

// Заполняем опцию 1
function xq_field1(){
	$val = get_option('xq_options');
	$val = $val['xq_post_type']; //Получаем текущее значение настройки
	$args=array(
    'public'   => true,
	);
	$output = 'objects';
	$operator = 'and';
	$post_types = get_post_types($args, $output, $operator ); // Получаем все публичные типы записей
	foreach($post_types as $ptype) { //Оборачиваем каждый тип записи в checkbox input
	$ptype_n = $ptype->name; ?>
	<input type="checkbox" name="xq_options[xq_post_type][]" value="<?php echo $ptype_n; ?>" <?php if(in_array($ptype_n, $val)) echo 'checked="checked"'; ?> > <?php echo $ptype_n; ?> <br>
	<?php }

}

// Заполняем опцию 2 и последующие по аналогии
function xq_field2(){
	$val = get_option('xq_options');
	$val = $val['xq_post_count'];
	?>
	<input type="number" name="xq_options[xq_post_count]" value="<?php echo esc_attr( $val ) ?>" />
	<?php
}

// Заполняем опцию 3
function xq_field3(){
	$val = get_option('xq_options');
	$val = $val['xq_post_tax'];

	$categories = get_categories();
	/* var_dump($categories); */
	foreach($categories as $cat) {
	$cat_i = $cat->term_id;
	$cat_n = $cat->name; ?>
	<input type="checkbox" name="xq_options[xq_post_tax][]" value="<?php echo $cat_i; ?>" <?php if(in_array($cat_i, $val)) echo 'checked="checked"'; ?> > <?php echo $cat_n; ?> <br>
	<?php
	}
	/*var_dump($categories);*/ ?>
	
	<?php
}

// Заполняем опцию 4
function xq_field4(){
	$val = get_option('xq_options');
	$val = $val['xq_post_thumb'];
	?>
	<input type="radio" name="xq_options[xq_post_thumb]" value="thumbnail" <?php if( $val == 'thumbnail') echo 'checked="checked"'; ?>> thumbnail<Br>
    <input type="radio" name="xq_options[xq_post_thumb]" value="medium" <?php if( $val == 'medium') echo 'checked="checked"'; ?>> medium<Br>
    <input type="radio" name="xq_options[xq_post_thumb]" value="large" <?php if( $val == 'large') echo 'checked="checked"'; ?>> large<Br>
	<input type="radio" name="xq_options[xq_post_thumb]" value="full" <?php if( $val == 'full') echo 'checked="checked"'; ?>> full<Br>
	<?php
}

## Очистка данных
function sanitize_callback( $options ){ 
	// очищаем
	foreach( $options as $name => & $val ){
		if( $name == 'xq_post_count' )
			$val = intval( $val );
		if( $name == 'xq_post_thumb' )
			$val = strip_tags( $val );
	}
	return $options;
}

// Создаём функцию шорт кода, который можно будет вставить в нужную часть сайта
function XQ_shortcode() {
	// Получаем актуальные настройки
	$val = get_option('xq_options');
	$xpc = $val['xq_post_count'];
	$xpt = $val['xq_post_type'];
	$xptax = $val['xq_post_tax'];
	
	
	//Создаём массив настроек для вызова WP Query
	$XrenArgs = array(
	'post_type' => $xpt,
	'showposts' => $xpc,
	'category__in' => $xptax,
	'post_status' => 'publish'	
	);
	
	// Сохраняем в переменной полученные объекты/посты
	$XrenQuery = new WP_Query($XrenArgs);
	// Выводим посты
	if ( $XrenQuery->have_posts() ) : while ( $XrenQuery->have_posts() ) : $XrenQuery->the_post(); ?>
	
	<article  id="post-<?php the_ID(); ?>" class="content-post">

	<div class="content-header">
		<h3 class="content-title">
			<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
		</h3>
		
		<span class="content-date"><?php echo get_the_date(Y-M-D); ?></span>
		
	</div>

	<div class="content-image">
		<?php $xqim = get_option('xq_options');
		$xqimage = $xqim['xq_post_thumb']; 
		the_post_thumbnail($xqimage); ?>
	</div>
		
	<div class="content-excerpt">
		<?php echo get_the_excerpt();?>
	</div>
		
	
	<div class="content-downmeta">
		<div class="content-tags"><?php echo get_the_tag_list('',' ',''); ?></div>
		<span class="content-comments"> Комментарии: <a href="<?php comments_link(); ?> "><?php comments_number( '0', '1', '%' ); ?></a> </span>
	</div>
	
	</article><!-- #article -->
	
	<?php endwhile;
	wp_reset_postdata();
	endif;
}
add_shortcode( 'XQ', 'XQ_shortcode' );


add_action( 'admin_print_footer_scripts', 'xq_add_quicktags' );
function xq_add_quicktags() {
	if ( ! wp_script_is('quicktags') )
		return;

	?>
	
	<script type="text/javascript">
		QTags.addButton( 'xq-button', 'xq', '[XQ]', '', 'XQ', 'Шорткод вывода постов XQ', 99 );
	</script>
	
	<?php
}


function XQ_register_stylesheet(){
	wp_register_style( 'XQ_stylesheet', plugins_url( 'XrenQuery/styles/style.css') );
	wp_enqueue_style( 'XQ_stylesheet' );
}
add_action( 'enqueue_scripts', 'XQ_register_stylesheet' );
add_action( 'admin_enqueue_scripts', 'XQ_register_stylesheet' );

/*
function XQ_add_script() {
  wp_register_script('XQ_script', plugins_url('scripts/XQ.js', __FILE__), array('jquery') );
  wp_enqueue_script('XQ_script');
}
add_action( 'wp_enqueue_scripts', 'XQ_add_script' );

*/
?>