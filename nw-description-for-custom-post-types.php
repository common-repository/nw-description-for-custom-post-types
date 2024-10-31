<?php

/**
 * Plugin Name: NW Description For Custom Post Types
 * Description: カスタム投稿タイプのアーカイブページおよびシングルページのディスクリプションを設定できます。
 * Version: 1.3.7
 * Author: NAKWEB
 * Author URI: https://www.nakweb.com/
 * License: GPLv2 or later
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
     exit;
}

register_activation_hook(__FILE__, array('NW_Description_For_Custom_Post_Types', 'myplugin_activation'));
register_uninstall_hook(__FILE__, array('NW_Description_For_Custom_Post_Types', 'myplugin_uninstall'));

class NW_Description_For_Custom_Post_Types
{
     /**
      * prefix
      */
     const PREFIX = 'nw_dfcpt_';

     /**
      * ui version
      */
     const UI_VERSION = '1.1.0';

     /**
      * option_name in wp_options
      */
     const OPTION_NAME = 'nw_description_for_custom_post_types_options';

     /**
      * common option_name in wp_options
      */
     const COMMON_NAME = 'nw_common_options';

     /**
      * name hidden type
      */
     const HIDDEN_NAME = 'submit_hidden';

     /**
      * separator
      */
     const SEPRATOR = '_nw_dfcpt_';

     /**
      * __construct
      */
     public function __construct()
     {
          add_action('plugins_loaded', array($this, 'plugins_loaded'));
     }

     /**
      * Loading translation files.
      */
     public function plugins_loaded()
     {
          if (!class_exists('NW_Common_Menu')) {
               // NW_Common_Menu class is containd in all plugin created by NAKWEB.
               require_once plugin_dir_path(__FILE__) . 'common/class.common.php';
          }

          add_action('admin_init', array($this, 'register_my_script'), 99);
          add_action('admin_menu', array($this, 'add_nw_sub_menu'), 99);
          add_action('wp_head', array($this, 'insert_meta_description'), 99);
          add_action('init', array($this, 'include_admin_plugin'), 99);
     }

     /**
      * Register original stylesheets and scripts.
      */
     public function register_my_script()
     {
          // stylesheet
          wp_register_style(self::PREFIX . 'settings', plugin_dir_url(__FILE__) . 'css/settings.css', array(), self::UI_VERSION);
          // script
          wp_register_script(self::PREFIX . 'counter', plugin_dir_url(__FILE__) . 'js/counter.js', array(), self::UI_VERSION, true);
          wp_register_script(self::PREFIX . 'toggle', plugin_dir_url(__FILE__) . 'js/toggle.js', array(), self::UI_VERSION, true);
     }

     /**
      * Add sub menu page.
      */
     public function add_nw_sub_menu()
     {
          $parent_slug = NW_Common_Menu::SLUG;
          $capability = 'edit_pages';
          $add_page = add_submenu_page($parent_slug, 'ディスクリプション設定', 'ディスクリプション設定', $capability, 'nw_dfcpt_settings', array($this, 'settings_page'));

          add_action('admin_print_styles-' . $add_page, array($this, 'enqueue_my_stylesheet'));
          add_action('admin_print_scripts-' . $add_page, array($this, 'enqueue_my_script'));
     }

     /**
      * Enqueue any stylesheets.
      */
     public function enqueue_my_stylesheet()
     {
          wp_enqueue_style(self::PREFIX . 'settings');
     }

     /**
      * Enqueue any scripts.
      */
     public function enqueue_my_script()
     {
          wp_enqueue_script('jquery');
          wp_enqueue_script(self::PREFIX . 'counter');
          wp_enqueue_script(self::PREFIX . 'toggle');
     }

     public function include_admin_plugin(){
          if(!is_admin() && is_single()){
               include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
          }
          
     }
     public function aio_seo_check(){
          if(is_plugin_active( 'all-in-one-seo-pack/all_in_one_seo_pack.php' )){
               return true;
          }else{
               return false;
          }
     }

     public function acf_check(){
          if(is_plugin_active( 'advanced-custom-fields-pro/acf.php' )){
               return true;
          }else{
               if(is_plugin_active( 'advanced-custom-fields/acf.php' )){
                    return true;
               }else{
                    return false;
               }
          }
     }

     public function get_acf_field_group($post_id){
          global $wpdb;
          $field_group_id  = $wpdb->get_results(
               $wpdb->prepare(
                    "SELECT DISTINCT c.post_parent FROM $wpdb->posts AS p 
                    INNER JOIN $wpdb->postmeta AS a ON ( a.post_id = p.ID ) 
                    LEFT JOIN $wpdb->postmeta AS b ON ( (( b.post_id = a.post_id )) AND (( b.meta_key LIKE CONCAT( '\_', a.meta_key ) )) ) 
				LEFT JOIN $wpdb->posts AS c ON ( ( c.post_name = b.meta_value ) AND ( c.post_type = '%s' ) ) 
                    LEFT JOIN $wpdb->posts AS d ON (d.ID = c.post_parent) 
                    WHERE p.ID = %d AND ( ( b.meta_id IS NOT NULL) AND ( c.ID IS NOT NULL ) ) 
                    AND d.post_parent = 0 
                    ORDER BY d.menu_order asc",
                    'acf-field',
                    $post_id
               ),ARRAY_A
          );
          return $field_group_id;
     }

     public function get_acf_field_meta_key($post_id,$key){
          global $wpdb;
          
          $meta_key = $wpdb->get_results(
               $wpdb->prepare(
                    "SELECT a.meta_key FROM $wpdb->posts AS p 
                    INNER JOIN $wpdb->postmeta AS a ON ( a.post_id = p.ID ) 
                    LEFT JOIN $wpdb->postmeta AS b ON ( (( b.post_id = a.post_id )) AND (( b.meta_key LIKE CONCAT( '\_', a.meta_key ) )) ) 
				LEFT JOIN $wpdb->posts AS c ON ( ( c.post_name = b.meta_value ) AND ( c.post_type = '%s' ) 
				AND (( c.post_content LIKE '%%%s%%' ) OR ( c.post_content LIKE '%%%s%%' ) OR ( c.post_content LIKE '%%%s%%' ) )  ) 
                    LEFT JOIN wp_posts AS d ON (d.ID = c.post_parent) 
                    WHERE p.ID = %d AND ( ( b.meta_id IS NOT NULL) AND ( c.ID IS NOT NULL ) ) 
                    AND d.post_parent != 0 
                    AND a.meta_key LIKE '%s' ",
                    'acf-field',
				':"text"',':"textarea"',':"wysiwyg"',
                    $post_id,
                    $key . '%'
               ),ARRAY_A
          );
          return $meta_key;
     }

     public function get_post_acf_field($post_id){
          $description = "";
          $field_group_id = self::get_acf_field_group($post_id);
          $first_check = true;
          if(empty($field_group_id)){
               return $description;
          }
          foreach( (array) $field_group_id as $id){
               $field_group = acf_get_field_group($id["post_parent"]);
               $acf_get_fields = acf_get_fields( $field_group );
               if(!empty($acf_get_fields)){
                    foreach( (array) $acf_get_fields as $field){
                         $get_field_value = "";
                         if(in_array($field["type"],array("text","textarea","wysiwyg","group","repeater","flexible_content"))){
                              $key = $field["name"];
                              if(in_array($field["type"],array("text","textarea","wysiwyg"))){
                                   $get_field_value = get_field($key);
                              }else{
                                   $get_meta_key = self::get_acf_field_meta_key($post_id,$key);
                                   $get_field_values = "";
                                   if(!empty($get_meta_key)){
                                        foreach( (array) $get_meta_key as $meta_key){
                                             $get_field = get_field($meta_key["meta_key"]);
                                             if(!empty($get_field)){
                                                  if($meta_key === reset($get_meta_key)){
                                                       $get_field_values .= $get_field;
                                                  }else{
                                                       $get_field_values .= " " . $get_field;
                                                  }
                                             }
                                        }
                                        $get_field_value = $get_field_values;
                                   }
                              }
                              if(!empty($get_field_value)){
                                   $get_field_value = wp_strip_all_tags($get_field_value);
                                   $get_field_value = str_replace(array("\r\n", "\r", "\n", "&nbsp;"), '', $get_field_value);
                                   $get_field_value = preg_replace('/\[.*\]/', '', $get_field_value);
                                   if(!empty($get_field_value)){
                                        if($first_check){
                                             $description .= $get_field_value;
                                             $first_check = false;
                                        }else{
                                             $description .= " " . $get_field_value;
                                        }
                                   }
                              }
                              if(mb_strlen($description, 'UTF-8')> 160){
                                   break 2;
                              }
                         }
                    }
               }
          }
          return $description;
     }

     /**
      * Display settings page.
      */
     public function settings_page()
     {
          // Administrator and Editor can run.
          if (!current_user_can('edit_pages')) {
               wp_die(__('You do not have sufficient permissions to access this page.'));
          }

          $descriptions = self::genarate_array();

          if (isset($_POST[self::HIDDEN_NAME]) && $_POST[self::HIDDEN_NAME] == 'Y') :

               check_admin_referer(self::PREFIX . 'field', self::PREFIX . 'field_nonce');

               self::update_descriptions($descriptions);
               ?>
               <div class="updated">
                    <p><strong>設定を保存しました。</strong></p>
               </div>

          <?php endif; ?>

          <div class="wrap">
               <h1>ディスクリプション設定</h1>
               <form id="description" name="<?php echo self::PREFIX ?>form" method="post" action="">
                    <input type="hidden" name="<?php echo self::HIDDEN_NAME ?>" value="Y">
                    <?php foreach ($descriptions as $key => $value) : ?>
                         <?php
                                        $split_key = explode(self::SEPRATOR, $key);
                                        if (count($split_key) === 2) {
                                             $object_type = 'term';
                                             $tax_slug = $split_key[0];
                                             $term_id = $split_key[1];
                                             $label = get_term($term_id, $tax_slug)->name;
                                             $type = 'TERM';
                                        } else {
                                             $slug = $split_key[0];
                                             if (post_type_exists($slug)) {
                                                  $object_type = 'post_type';
                                                  $label = get_post_type_object($slug)->label;
                                                  if ('post' === $slug) {
                                                       $type = 'POST TYPE';
                                                  } else {
                                                       $type = 'CUSTUM POST TYPE';
                                                  }
                                             }
                                        }
                                        $title = '<span>' . $type . '</span><p>' . $label . '</p>';
                                        ?>
                         <dl class="register_description <?php echo $object_type ?>">
                              <dt id="<?php echo $key  . '_term' ?>" class="trigger flexbox fw"><?php echo $title ?></dt>
                              <dd class="contents">
                                   <div class="archdes">
                                        <label>アーカイブ(一覧)</label>
                                        <div class="inputBox">
                                             <textarea class="str_counter" name="<?php echo $key . '_archive_field' ?>"><?php echo esc_html(stripslashes($value[0])) ?></textarea>
                                             <div class="counter"><span class="show-count">0</span>文字</div>
                                             <div class="checkbox"><input type="checkbox" name="<?php echo $key . '_archive_check_field' ?>" value="off" <?php if ($value[2] === 'off') echo ' checked="checked"' ?>>このページにはディスクリプションを自動挿入しない</div>
                                        </div>
                                   </div>
                                   <?php if ($object_type === 'post_type') : ?>
                                        <div class="singdes">
                                             <label>シングル（詳細）</label>
                                             <div class="inputBox">
                                                  <textarea class="str_counter" name="<?php echo $key . '_single_field' ?>"><?php echo esc_html(stripslashes($value[1])) ?></textarea>
                                                  <div class="counter"><span class="show-count">0</span>文字</div>
                                                  <div class="checkbox"><input type="checkbox" name="<?php echo $key . '_single_check_field' ?>" value="off" <?php if ($value[3] === 'off') echo ' checked="checked"' ?>>このページにはディスクリプションを自動挿入しない</div>
                                             </div>
                                        </div>
                                   <?php endif; ?>
                              </dd>
                         </dl>
                    <?php endforeach; ?>
                    <p class="submit"><input id="submit_btn" type="submit" name="Submit" class="button-primary" value="変更を保存" /></p>
                    <?php wp_nonce_field(self::PREFIX . 'field', self::PREFIX . 'field_nonce'); ?>
               </form>
          </div>

<?php
     }

     /**
      * Insert <meta name="description"> into wp_head.
      */
     public function insert_meta_description()
     {
          global $post;
          
          if (is_single() || is_archive()) {

               if (is_tax()) {
                    // Taxonomy archive page.
                    $tax_slug = get_query_var('taxonomy'); // Get the taxonomy slug.
                    $term_id = get_queried_object_id(); // Get the term ID.

                    $request_key = $tax_slug . self::SEPRATOR . $term_id;
               } else {
                    // (Custom) Post archive page or single page.
                    $post_type_slug = get_post_type(); // Get the post type name.

                    if (!$post_type_slug) {
                         // If the post type has no entry...
                         if (is_post_type_archive()) {
                              // Get custom post type name.
                              $post_type_slug = get_query_var('post_type');
                         } else {
                              // Set WP core post type.
                              $post_type_slug = 'post';
                         }
                    }

                    $request_key = $post_type_slug;
               }

               if (get_option(self::OPTION_NAME)) {
                    // Get the options from DB.
                    $options = get_option(self::OPTION_NAME);
                    // Select the require option.
                    $option = $options[$request_key];
               } else {
                    return;
               }

               $is_insert = true;
               if (is_single()) {
                    // Single page
                    if ($option[3] !== 'off') {
                         $description = $post->post_content;
                         if (empty($description)) {
                              $description = esc_html($option[1]);
                         }
                    } else {
                         $is_insert = false;
                    }
               } else {
                    // Archive page
                    if ($option[2] !== 'off') {
                         $description = esc_html($option[0]);
                    } else {
                         $is_insert = false;
                    }
               }

               if ($is_insert) {
                    if(is_single()){
                         $aio_description_check = false;
                         if(self::aio_seo_check()){
                              
                              $aio_description = get_post_meta( $post->ID, '_aioseo_description', true );
                              if(empty($aio_description)){
                                   // AIO SEO old version 
                                   $aio_description = get_post_meta( $post->ID, '_aioseop_description', true );
                              }
                              $aio_description = str_replace(array("\r\n", "\r", "\n", "&nbsp;"), '', $aio_description);
                              if(!empty($aio_description)){
                                   if(preg_match('/#post_content/',$aio_description)){
                                        $aio_description = preg_replace('/#post_content/','',$aio_description);
                                        if( empty($aio_description)){
                                             $post_content = get_the_content();
                                             if(!empty($post_content)){
                                                  $aio_description_check = true;
                                             }
                                        }else{
                                             $aio_description_check = true;
                                        }
                                   }else{
                                        $aio_description_check = true;
                                   }
                              }else{
                                   $post_content = get_the_content();
                                   if(!empty($post_content)){
                                        $aio_description_check = true;
                                   }
                              }
                         }
                         
                         // Priority AIOSEO Description
                         // Not output from NW Description
                         if( $aio_description_check ){
                              return;
                         }
                    }else{
                         $description = str_replace(array("\r\n", "\r", "\n", "&nbsp;"), '', $description);
                         $description = preg_replace('/\[.*\]/', '', $description);
                         $description = wp_strip_all_tags($description);
                    }
                    
                    if (empty($description) && is_single()) {
                         if(is_single()){
                              if(self::acf_check()){
                                   $field_text = "";
                                   $first_value = true;
                                   $description = self::get_post_acf_field($post->ID);
                              }
                         }
                    }
                    
                    
                    if (!empty($description)){
                         $description = str_replace(array("\r\n", "\r", "\n", "&nbsp;"), '', $description);
                         $description = preg_replace('/\[.*\]/', '', $description);
                         $description = wp_strip_all_tags($description);
                         $description = mb_strimwidth($description, 0, 320, "...");
                         echo '<meta name="description" content="' . esc_attr($description) . '">' . PHP_EOL;
                         echo '<meta property="og:description" content="' . esc_attr($description) . '">' . PHP_EOL;
                         echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . PHP_EOL;
                    }
               }
          }
          return;
     }

     /**
      * Set descripsions from db or initialize.
      */
     public function genarate_array()
     {
          $initial_array = self::get_initial_array();

          if (get_option(self::OPTION_NAME)) {
               $options = get_option(self::OPTION_NAME);

               foreach ($initial_array as $key => &$value) {
                    if (isset($options[$key])) {
                         $value[0] = $options[$key][0];
                         $value[1] = $options[$key][1];
                         $value[2] = $options[$key][2];
                         $value[3] = $options[$key][3];
                    }
               }
               unset($value);
          }

          return $initial_array;
     }

     /**
      * Get default array.
      */
     public function get_initial_array()
     {
          $custom_post_types = self::get_custom_post_types();

          $initial_array = array();
          foreach ($custom_post_types as $custom_post_type) {
               $initial_array[$custom_post_type] = array('', '', '', '');
          }
          return $initial_array;
     }

     /**
      * Update descriptions.
      *
      * @param array &$descriptions
      */
     public function update_descriptions(&$descriptions)
     {
          foreach ($descriptions as $key => &$value) {
               $value[0] = sanitize_text_field($_POST[$key . '_archive_field']);
               $value[1] = sanitize_text_field($_POST[$key . '_single_field']);
               $value[2] = !empty($_POST[$key . '_archive_check_field']) ? 'off' : 'on';
               $value[3] = !empty($_POST[$key . '_single_check_field']) ? 'off' : 'on';
          }
          unset($value);

          update_option(self::OPTION_NAME,  $descriptions);
     }

     /**
      * Get custom post type objects.
      *
      * @return array
      */
     public function get_custom_post_types()
     {
          $args = array(
               'public' => true,
               '_builtin' => false
          );
          $cpt_objects = get_post_types($args, 'objects');

          return self::get_slug_list($cpt_objects);
     }

     /**
      * Get custom post type's slug list.
      *
      * @param array $objects
      * @return array
      */
     public function get_slug_list($objects)
     {
          // Get taxonomy objects.
          $args = array(
               'public' => true,
               '_builtin' => false
          );
          $output = 'objects';
          $taxonomies = get_taxonomies($args, $output);

          $slug_list = array('post');

          foreach ($objects as $object) {
               array_push($slug_list, $object->name);
               foreach ($taxonomies as $tax) {
                    $object_types = $tax->object_type;
                    foreach ($object_types as $object_type) {
                         if ($object->name === $object_type) {
                              $terms = get_terms($tax->name, array('hide_empty' => false));
                              foreach ($terms as $term) {
                                   array_push($slug_list, $tax->name . self::SEPRATOR . $term->term_id);
                              }
                         }
                    }
               }
          }

          return $slug_list;
     }

     /**
      * Activation
      */
     public static function myplugin_activation()
     {
          self::activation_common();
     }

     /**
      * Uninstall
      */
     public static function myplugin_uninstall()
     {
          self::uninstall_common();

          $result = get_option(self::OPTION_NAME);
          if ($result) {
               delete_option(self::OPTION_NAME);
          }
     }

     /**
      * Register nw-ish plugin activated at least once.
      */
     public function activation_common()
     {
          $myplugin_name = plugin_basename(__FILE__);
          $myplugin_name = explode('/', $myplugin_name)[0];
          $option_value = get_option(self::COMMON_NAME);
          if (!empty($option_value)) {
               $flg = 0;
               if (!empty($option_value['list'])) {
                    foreach ($option_value['list'] as $plugin) {
                         if ($plugin === $myplugin_name) {
                              $flg = 1;
                              break;
                         }
                    }
               }
               if (!$flg) {
                    array_push($option_value['list'], $myplugin_name);
               }
          } else {
               $option_value['label'] = '';
               $option_value['list'] = array($myplugin_name);
          }
          update_option(self::COMMON_NAME, $option_value);
     }

     /**
      * Delete common option from wp_options.
      */
     public function uninstall_common()
     {
          // Get nw-ish plugins activated at least once.
          $nw_plugins = get_option(self::COMMON_NAME)['list'];
          if (empty($nw_plugins)) {
               return;
          }

          // Get nw-ish plugins installed.
          $installed_plugins = scandir(WP_PLUGIN_DIR);
          $prefix = 'nw-';
          $flg = 0;
          if ($installed_plugins) {
               foreach ($installed_plugins as $installed_plugin) {
                    if (!strncmp($installed_plugin, $prefix, 3)) {
                         foreach ($nw_plugins as $nw_plugin) {
                              if ($installed_plugin === $nw_plugin) {
                                   if ($flg) {
                                        // Exist NW-ish plugins two or more.
                                        $flg++;
                                   } else {
                                        // Exist NW-ish plugin.
                                        $flg = 1;
                                   }
                                   break;
                              }
                         }
                         if ($flg >= 2) {
                              break;
                         }
                    }
               }
          }
          if ($flg < 2) {
               // Uninstall last nw-ish plugin.
               $result = get_option(self::COMMON_NAME);
               if ($result) {
                    delete_option(self::COMMON_NAME);
               }
          }
     }
}
new NW_Description_For_Custom_Post_Types();
