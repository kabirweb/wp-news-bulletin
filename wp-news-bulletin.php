<?php
/*
Plugin Name: WP News Bulletin
Plugin URI: http://www.a1netsolutions.com/Products/WP-News-Bulletin
Description: <strong>WP News Bulletin</strong>, this wordpress plugin help your to publish your company or blog's news & updates. It also have a user friendly front end UI which appears with a nice auto news slider. Your website's visitors can read the full news through a popup box.
Version: 0.1.1
Author: Ahsanul Kabir
Author URI: http://www.ahsanulkabir.com/
License: GPL2
License URI: license.txt
*/

function wpnb_news_register()
{
	$labels = array(
		'name' => 'News',
		'singular_name' => 'News Item',
		'add_new' => 'Add New',
		'add_new_item' => 'Add New',
		'edit_item' => 'Edit News',
		'new_item' => 'New News',
		'view_item' => 'View News',
		'search_items' => 'Search News',
		'not_found' =>  'Nothing found',
		'not_found_in_trash' => 'Nothing found in Trash',
		'parent_item_colon' => ''
	);
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'menu_icon' => ( plugins_url('lib/img/icon.png', __FILE__) ),
		'rewrite' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('title','editor','thumbnail')
	  ); 
	register_post_type( 'wpnb_news' , $args );
}
add_action( 'init', 'wpnb_news_register' );

function wpnb_frontEndJS()
{
	wp_enqueue_script( 'wpnb-frontEndJS', (plugins_url('lib/js/wpnb_frontEnd.js', __FILE__)), array('jquery') );
}
add_action('wp_enqueue_scripts', 'wpnb_frontEndJS');

function wpnb_colorPicker($hook_suffix)
{
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wpnb-color-handle', plugins_url('lib/js/wpnb_colorScript.js', __FILE__ ), array('wp-color-picker'), false, true );
}
add_action( 'admin_enqueue_scripts', 'wpnb_colorPicker' );

function wpnb_backEndCss()
{
	wp_enqueue_style( 'wpnb-cssB', ( plugins_url('lib/css/wpnb_backEnd.css', __FILE__) ) );
}
add_action( 'admin_init', 'wpnb_backEndCss' );

function wpnb_frontEndCss()
{
	wp_enqueue_style( 'wpnb-cssF', ( plugins_url('lib/css/wpnb_frontEnd.css', __FILE__) ) );
}
add_action( 'wp_enqueue_scripts', 'wpnb_frontEndCss' );

define(WPNB_LIB, "../wp-content/plugins/wp-news-bulletin/lib/");

function wpnb_defaults()
{
	$wpnb_defaults = WPNB_LIB.'wpnb_defaults.php';
	if(is_file($wpnb_defaults))
	{
		require $wpnb_defaults;
		foreach($addOptions as $addOptionK => $addOptionV)
		{
			update_option($addOptionK, $addOptionV);
		}
		unlink($wpnb_defaults);
	}
}

function wpnb_activate()
{
	wpnb_defaults();
}
register_activation_hook( __FILE__, 'wpnb_activate' );

function wpnb_getCr($k, $v)
{
	echo '<div class="postbox wpnb_cr"><h3 class="hndle"><span>'.$k.'</span></h3><div class="inside">'.get_option($v).'</div></div>';
}

function wpnb_printCr()
{
	wpnb_getCr('Plugins &amp; Themes', 'wpnb_other');
	wpnb_getCr('WordPress Development', 'wpnb_hire');
	wpnb_getCr('Support Us', 'wpnb_support');
}

function wpnb_popupTemp()
{
	if( get_option('wpnb_defaults') == 'on' )
	{
		$wpnb_witgetBG = get_option('wpnb_witgetBG');
		$wpnb_titleColor = get_option('wpnb_titleColor');
		$wpnb_textColor = get_option('wpnb_textColor');
		echo '<style type="text/css">';
		if(!empty($wpnb_witgetBG)){echo 'aside.widget_wpnb_widget {background:'.$wpnb_witgetBG.' !important;}';}
		if(!empty($wpnb_titleColor)){echo 'aside.widget_wpnb_widget > h3.widget-title {color:'.$wpnb_titleColor.' !important;}';}
		if(!empty($wpnb_textColor)){echo 'aside.widget_wpnb_widget > div.wpnbnews {color:'.$wpnb_textColor.' !important;}';}
		echo '</style>';
		echo get_option('wpnb_dev');
		echo get_option('wpnb_com');
		?>
		<div id="wpnb_popBoxOut">
			<div id="wpnb_popBox">
			<img src="<?php echo plugins_url('lib/img/close.png', __FILE__); ?>" id="wpnb_popClose" />
			<div id="wpncLoader">
		   <?php 
			wp_reset_query();
			$argswprlist = array
			(
				'post_type' => array('wpnb_news'), 
				'posts_per_page' => -1
			);
			$querywprlist = query_posts( $argswprlist );
			if (have_posts()) : while (have_posts()) : the_post();
			echo '<div id="wpnbpopBoxMin';
			the_ID();
			echo '" class="wpnb"><h1>';
			the_title();
			echo '</h1><div class="clrFixia"></div>';
			if(get_option('wpnb_date')=='on')
			{
				echo '<div class="wpnb_date">';
				echo get_the_date();
				the_date();
				echo '</div>';
			}
			echo '<div class="pop">';
			the_post_thumbnail('medium');
			remove_all_filters('the_content');
			the_content();
			echo '</div></div>';
			endwhile;
			endif;
			?>
			</div>
			</div>
		</div>
		<div id="wpnb_hideBody"></div>
		<script type="text/javascript">
		jQuery(function()
		{
			jQuery(".wpnbnews").wpnbCarousel(
			{
				vertical:true, 
				hoverPause:true, 
				visible:<?php echo get_option('wpnb_boxAmount'); ?>, 
				auto:3000, 
				speed:500
			});
		});
		jQuery(document).ready(function()
		{
			jQuery("#wpnb_popClose").click(function()
			{
				jQuery("#wpnb_popBoxOut").fadeOut();
				jQuery("#wpnb_popBox").fadeOut();
				jQuery("#wpnb_hideBody").fadeOut();
				jQuery(".wpnb").hide();
			});
			jQuery("#wpnb_hideBody").click(function()
			{
				jQuery("#wpnb_popBoxOut").fadeOut();
				jQuery("#wpnb_popBox").fadeOut();
				jQuery("#wpnb_hideBody").fadeOut();
				jQuery(".wpnb").hide();
			});
			jQuery(".wpnb_trg").click(function()
			{
				jQuery("#wpnb_popBoxOut").fadeIn();
				jQuery("#wpnb_popBox").fadeIn();
				jQuery("#wpnb_hideBody").fadeIn();
			});
		});
		jQuery(document).ready(function()
		{
			jQuery('.wpnb_trg').click(function()
			{
				var wpnbID = jQuery(this).attr("ref");
				jQuery("#wpnbpopBoxMin"+wpnbID).show();
			});
		});
		</script>
		<?php
	}
}
add_action('wp_footer', 'wpnb_popupTemp', 100);

function wpnb_trunCate($text, $length = 100, $ending = '...', $exact = true, $considerHtml = false)
{
    if (is_array($ending))
	{
        extract($ending);
    }
    if ($considerHtml)
	{
        if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length)
		{
            return $text;
        }
        $totalLength = mb_strlen($ending);
        $openTags = array();
        $wpnb_trunCate = '';
        preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);
        foreach ($tags as $tag)
		{
            if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2]))
			{
                if (preg_match('/<[\w]+[^>]*>/s', $tag[0]))
				{
                    array_unshift($openTags, $tag[2]);
                }
				else if (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag))
				{
                    $pos = array_search($closeTag[1], $openTags);
                    if ($pos !== false) {
                        array_splice($openTags, $pos, 1);
                    }
                }
            }
            $wpnb_trunCate .= $tag[1];
            $contentLength = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]));
            if ($contentLength + $totalLength > $length)
			{
                $left = $length - $totalLength;
                $entitiesLength = 0;
                if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE))
				{
                    foreach ($entities[0] as $entity)
					{
                        if ($entity[1] + 1 - $entitiesLength <= $left)
						{
                            $left--;
                            $entitiesLength += mb_strlen($entity[0]);
                        }
						else
						{
                            break;
                        }
                    }
                }
                $wpnb_trunCate .= mb_substr($tag[3], 0 , $left + $entitiesLength);
                break;
            }
			else
			{
                $wpnb_trunCate .= $tag[3];
                $totalLength += $contentLength;
            }
            if ($totalLength >= $length)
			{
                break;
            }
        }
    }
	else
	{
        if (mb_strlen($text) <= $length)
		{
            return $text;
        }
		else
		{
            $wpnb_trunCate = mb_substr($text, 0, $length - strlen($ending));
        }
    }
    if (!$exact)
	{
        $spacepos = mb_strrpos($wpnb_trunCate, ' ');
        if (isset($spacepos))
		{
            if ($considerHtml)
			{
                $bits = mb_substr($wpnb_trunCate, $spacepos);
                preg_match_all('/<\/([a-z]+)>/', $bits, $droppedTags, PREG_SET_ORDER);
                if (!empty($droppedTags))
				{
                    foreach ($droppedTags as $closingTag)
					{
                        if (!in_array($closingTag[1], $openTags))
						{
                            array_unshift($openTags, $closingTag[1]);
                        }
                    }
                }
            }
            $wpnb_trunCate = mb_substr($wpnb_trunCate, 0, $spacepos);
        }
    }
    $wpnb_trunCate .= $ending;
    if ($considerHtml)
	{
        foreach ($openTags as $tag)
		{
            $wpnb_trunCate .= '</'.$tag.'>';
        }
    }
    return $wpnb_trunCate;
}

class wpnb_NewsBulletinWidget extends WP_Widget
{
	function wpnb_NewsBulletinWidget()
	{
		parent::__construct(
			'wpnb_widget',
			'News Bulletin',
			array( 'description' => 'WP News Bulletin Widget' )
		);
	}

	function widget( $args, $instance )
	{
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $args['before_widget'];
		if ( ! empty( $title ) )
		{
			echo $args['before_title'] . $title . $args['after_title'];
		}
		wp_reset_query();
		$argswprlist = array
		(
			'post_type' => array('wpnb_news'), 
			'posts_per_page' => 10
		);
		$querywprlist = query_posts( $argswprlist );
		echo '<div class="wpnbnews"><ul>';
		if (have_posts()) : while (have_posts()) : the_post();
		echo '<li class="wpnb_trg" ref="'; the_id(); echo '" rel="'.site_url().'">';
		if(get_option('wpnb_boxImg')=='on')
		{
			the_post_thumbnail('thumbnail');
		}
		switch(get_option('wpnb_boxLetters'))
		{
			case 100:
			echo wpnb_trunCate(get_the_title(), 100, '...', false, true);
			break;
			case 300:
			echo wpnb_trunCate(get_the_excerpt(), 300, '...', false, true);
			break;
			case 500:
			echo wpnb_trunCate(get_the_content(), 500, '...', false, true);
			break;
		}
		if(get_option('wpnb_date')=='on')
		{
			echo '<div class="clrFixia"></div><div class="wpnb_date">';
			the_date();
			echo '</div>';
		}
		echo '<div class="clrFixia"></div></li>';
		endwhile;
		endif;
		echo '</ul></div>';
		echo $args['after_widget'];
	}

	function update( $new_instance, $old_instance )
	{
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}

	function form( $instance )
	{
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'News & Updates', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}
}

function wpnb_register_widgets()
{
	register_widget( 'wpnb_NewsBulletinWidget' );
}
if( get_option('wpnb_defaults') == 'on' )
{
	add_action( 'widgets_init', 'wpnb_register_widgets' );
}

function wpnb_adminMenu()
{
	add_submenu_page( 'edit.php?post_type=wpnb_news', 'WP News Bulletin Settings', 'Settings', 'manage_options', 'settings', 'wpnb_Settings' );
}
add_action( 'admin_menu', 'wpnb_adminMenu' );

function wpnb_Settings()
{
	$newsSuccessMsg = false;
	if( isset($_POST["wpnb_boxAmount"]) && isset($_POST["wpnb_boxLetters"]) && isset($_POST["wpnb_boxImg"]) && isset($_POST["wpnb_date"]) ) 
	{
		update_option( 'wpnb_boxAmount', $_POST["wpnb_boxAmount"] );
		update_option( 'wpnb_boxLetters', $_POST["wpnb_boxLetters"] );
		update_option( 'wpnb_boxImg', $_POST["wpnb_boxImg"] );
		update_option( 'wpnb_date', $_POST["wpnb_date"] );
		update_option( 'wpnb_witgetBG', $_POST["wpnb_witgetBG"] );
		update_option( 'wpnb_titleColor', $_POST["wpnb_titleColor"] );
		update_option( 'wpnb_textColor', $_POST["wpnb_textColor"] );
		$newsSuccessMsg = true;
	}
	?>
    <div id="wpbody">
    	<div class="wpnb_settings">
        	<div id="wpnb_container" class="wrap">
            	<div class="icon32 icon32-posts-wpnb_news" id="icon-edit"><br></div>
                <h2>News Settings</h2>
                <a href="http://www.youtube.com/watch?v=HcgoUrO5loA" target="_blank">
                <img src="<?php echo plugins_url('lib/img/uvg.png', __FILE__); ?>" style="border:0 none;float:right;height:50px;position:relative;width:auto;z-index:200;top:-40px;" />
                </a>
                <?php
				if( get_option('wpnb_defaults') != 'on' )
				{
					echo '<div id="wpwm_errorMSG">Error! please do the following -<br />1. Deactivate and Delete this plugin.<br />2. <a href="http://downloads.wordpress.org/plugin/wp-news-bulletin.zip">Download</a> and Reinstall again.</div>';
				}
                if( $newsSuccessMsg )
				{
					echo '<div class="updated below-h2" id="message"><p>Successfully saved. <a href="'.site_url().'" target="_blank">View site</a></p></div>';
				}
				?>
                <div id="wpnb_scod">
                Use this Short Code <span>[WPNEWSBULLETIN]</span> for display the news archive/section.
                </div>
                <form method="post" action="" id="wpnb_form">
                	<div>
                    	<label>How many news blocks?</label>
                        <select name="wpnb_boxAmount" class="wpnb_settings_select">
                            <option value="5"<?php if(get_option('wpnb_boxAmount')==5){echo ' selected="selected"';} ?>>5 News Blocks</option>
                            <option value="4"<?php if(get_option('wpnb_boxAmount')==4){echo ' selected="selected"';} ?>>4 News Blocks</option>
                            <option value="3"<?php if(get_option('wpnb_boxAmount')==3){echo ' selected="selected"';} ?>>3 News Blocks</option>
                            <option value="2"<?php if(get_option('wpnb_boxAmount')==2){echo ' selected="selected"';} ?>>2 News Blocks</option>
                            <option value="1"<?php if(get_option('wpnb_boxAmount')==1){echo ' selected="selected"';} ?>>Single News Block</option>
                        </select>
                    </div>
                    <div>
                    	<label>How many letters on block?</label>
                        <select name="wpnb_boxLetters" class="wpnb_settings_select">
                        	<option value="100"<?php if(get_option('wpnb_boxLetters')==100){echo ' selected="selected"';} ?>>Up to 100 Letters (Title)</option>
                            <option value="300"<?php if(get_option('wpnb_boxLetters')==300){echo ' selected="selected"';} ?>>Up to 300 Letters (Excerpt)</option>
                            <option value="500"<?php if(get_option('wpnb_boxLetters')==500){echo ' selected="selected"';} ?>>Up to 500 Letters (Content)</option>
                        </select>
                    </div>
                    <div>
                    	<label>Display image on slider?</label>
                        <select name="wpnb_boxImg" class="wpnb_settings_select">
                        	<option value="on"<?php if(get_option('wpnb_boxImg')=='on'){echo ' selected="selected"';} ?>>Display Image</option>
                            <option value="off"<?php if(get_option('wpnb_boxImg')=='off'){echo ' selected="selected"';} ?>>Hide Image</option>
                        </select>
                    </div>
                    <div>
                    	<label>Display Date?</label>
                        <select name="wpnb_date" class="wpnb_settings_select">
                        	<option value="on"<?php if(get_option('wpnb_date')=='on'){echo ' selected="selected"';} ?>>Display Date</option>
                            <option value="off"<?php if(get_option('wpnb_date')=='off'){echo ' selected="selected"';} ?>>Hide Date</option>
                        </select>
                    </div>
					<?php
                    $wpnb_witgetBG = get_option('wpnb_witgetBG');
                    $wpnb_titleColor = get_option('wpnb_titleColor');
                    $wpnb_textColor = get_option('wpnb_textColor');
                    if(!empty($wpnb_witgetBG))
                    {echo '<div><label>Witget background color</label><input type="text" class="wpnb_colorField" name="wpnb_witgetBG" data-default-color="'.$wpnb_witgetBG.'" value="'.$wpnb_witgetBG.'" /></div>';}
                    else
                    {echo '<div><label>Witget background color</label><input type="text" class="wpnb_colorField" name="wpnb_witgetBG" /></div>';}
                    if(!empty($wpnb_titleColor))
                    {echo '<div><label>Witget title color</label><input type="text" class="wpnb_colorField" name="wpnb_titleColor" data-default-color="'.$wpnb_titleColor.'" value="'.$wpnb_titleColor.'" /></div>';}
                    else
                    {echo '<div><label>Witget title color</label><input type="text" class="wpnb_colorField" name="wpnb_titleColor" /></div>';}
                    if(!empty($wpnb_textColor))
                    {echo '<div><label>Witget text color</label><input type="text" class="wpnb_colorField" name="wpnb_textColor" data-default-color="'.$wpnb_textColor.'" value="'.$wpnb_textColor.'" /></div>';}
                    else
                    {echo '<div><label>Witget text color</label><input type="text" class="wpnb_colorField" name="wpnb_textColor" /></div>';}
                    ?>
                	<input type="submit" value="Save" class="button button-primary button-large" />
                </form>
            </div>
        </div>
        <div id="wpnb_sidebar">
			<?php wpnb_printCr(); ?>
          </div>
        <div class="clear"></div>
    </div>
    <?php
}

function wpnb_shortcode()
{
	wp_reset_query();
	$argswprlist = array
	(
		'post_type' => array('wpnb_news'), 
		'posts_per_page' => -1
	);
	$querywprlist = query_posts( $argswprlist );
	if (have_posts()) : while (have_posts()) : the_post();
	echo '<article>';
	echo '<header>';
	the_post_thumbnail('medium');
	echo '<h1>';
	echo '<a href="'.get_permalink().'">';
	the_title();
	echo '</a>';
	echo '</h1>';
	echo '</header>';
	echo '<footer>';
	if(get_option('wpnb_date')=='on')
	{
		echo '<div>';
		echo get_the_date();
		the_date();
		echo '</div>';
	}
	echo '</footer>';
	echo '<div>';
	$permLink2 = ' <a href="'.get_permalink().'" class="continueRead">[...]</a>';
	echo wpnb_trunCate(get_the_content(), 1000, $permLink2, false, true);
	echo '</div>';
	echo '</article>';
	endwhile;
	endif;
	wp_reset_query();
}
add_shortcode( 'WPNEWSBULLETIN', 'wpnb_shortcode' );

?>