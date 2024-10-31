<?php

/**
 * NW_Common_Menu
 * 
 * NW_Common_Menu class is contained in all plugin created by NAKWEB. 
 * This class adds original menu as top lebel menu.
 * Sub level menus created by NAKWEB are added to the original menu.
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
     exit;
}

class NW_Common_Menu
{
     /**
      * prefix
      */
     const PREFIX = 'nw_';

     /**
      * ui version
      */
     const UI_VERSION = '1.0.0';

     /**
      * common option_name in wp_options
      */
     const COMMON_NAME = 'nw_common_options';

     /**
      * default label
      */
     const LABEL = 'NAKWEB';

     /**
      * icon
      */
     const ICON = 'dashicons-portfolio';

     /**
      * top level menu slug
      */
     const SLUG = 'nw_main_menu';

     /**
      * __construct
      */
     public function __construct()
     {
          if (!has_action('admin_menu', 'add_nw_menu')) {
               add_action('admin_init', array($this, 'register_my_script'), 9);
               add_action('admin_menu', array($this, 'add_nw_menu'), 9);
               add_action('admin_menu', array($this, 'change_menu_position'), 999);
          }
     }

     /**
      * Register original stylesheets and scripts.
      */
     public function register_my_script()
     {
          // stylesheet
          wp_register_style(self::PREFIX . 'common', plugin_dir_url(__FILE__) . 'common.css', array(), self::UI_VERSION);
          // script
     }

     /**
      * Add menu page.
      */
     public function add_nw_menu()
     {
          $main_label = stripslashes(get_option(self::COMMON_NAME)['label']);
          if (!$main_label) {
               $main_label = self::LABEL;
          }
          $main_slug = self::SLUG;
          $capability = 'manage_options';

          add_menu_page($main_label, $main_label, $capability, $main_slug,  array($this, 'nw_main_page'), self::ICON);
     }

     /**
      * Change positions nw_main_page.
      */
     public function change_menu_position()
     {
          $main_slug = self::SLUG;
          $capability = 'manage_options';

          remove_submenu_page($main_slug, 'nw_main_menu');
          $add_page = add_submenu_page($main_slug, '共通設定', '共通設定', $capability, $main_slug, array($this, 'nw_main_page'));

          add_action('admin_print_styles-' . $add_page, array($this, 'enqueue_my_stylesheet'));
     }

     /**
      * Enqueue any stylesheets.
      */
     public function enqueue_my_stylesheet()
     {
          wp_enqueue_style(self::PREFIX . 'common');
     }

     /**
      * Display settings page.
      */
     public function nw_main_page()
     {
          if (!current_user_can('manage_options')) {
               wp_die(__('You do not have sufficient permissions to access this page.'));
          }

          $hidden_field_name = 'submit_hidden';
          $data_field_name = 'menu_label';

          $opt_val = get_option(self::COMMON_NAME);
          if (!$opt_val) {
               $opt_val = array();
               $opt_val['label'] = '';
               $opt_val['list'] = array();
          }

          if (isset($_POST[$hidden_field_name]) && $_POST[$hidden_field_name] == 'Y') {

               check_admin_referer(self::PREFIX . 'common', self::PREFIX . 'common_nonce');

               $opt_val['label'] = sanitize_text_field($_POST[$data_field_name]);
               update_option(self::COMMON_NAME, $opt_val);
               ?>
               <div class="updated">
                    <p><strong>設定を保存しました。</strong></p>
               </div>

          <?php } ?>

          <div class="wrap">
               <h1>共通設定</h1>
               <form name="<?php echo self::PREFIX ?>form" method="post" action="">
                    <input type="hidden" name="<?php echo $hidden_field_name ?>" value="Y">
                    <p>トップレベルメニューのラベル<br /><input type="text" name="<?php echo $data_field_name ?>" value="<?php echo esc_attr(stripslashes($opt_val['label'])) ?>"></p>
                    <p class="submit"><input type="submit" name="Submit" class="button-primary" value="変更を保存" /></p>
                    <?php wp_nonce_field(self::PREFIX . 'common', self::PREFIX . 'common_nonce'); ?>
               </form>
          </div>
<?php
     }
}
new NW_Common_Menu();
