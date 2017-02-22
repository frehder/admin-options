<?php
/**
 * AdminOptions class
 *
 * @author     Florian Rehder <code@florianrehder.de>
 * @copyright  2017 Florian Rehder
 * @license    MIT
 */

namespace AdminOptionsPlugin;

if(!class_exists('AdminOptions'))
{
  /**
   * Plugin settings admin menu.
   */
  class AdminOptions
  {

    /**
     * @var object|null $instance  Refers to a single instance of this class.
     */
    private static $instance = null;

    /**
     * Creates or returns an instance of this class.
     *
     * @return A single instance of this class. (Singleton Pattern)
     */
    public static function get_instance(){
      if(self::$instance === null){
        self::$instance = new self;
      }
      return self::$instance;
    }


    /**
     * @var object $db_options  Contains plugin options from the DB
     */
    private static $db_options;

    /**
     * @var string $db_options_name  option_name in the wp_options table which holds all the settings in DB
     */
    public static $db_options_name;

    /**
     * @var string $page_title  Headline of the whole settings page
     */
    private static $page_title;

    /**
     * @var string $menu_title  Menu entry text of the settings page
     */
    private static $menu_title;

    /**
     * @var bool $menu_type  Switch between standalone menu item in the menu sidebar and options page under the WP 'Settings' menu
     */
    private static $menu_type;

    /**
     * @var string $menu_icon  Menu item icon. Use key of a dashicon
     */
    private static $menu_icon;

    /**
     * @var int $menu_position  Position of the menu item in the menu sidebar
     */
    private static $menu_position;

    /**
     * @var string $menu_capability  Capability the user must have to access menu item
     */
    private static $menu_capability;

    /**
     * @var string $menu_slug  The slug name to refer to this menu page
     */
    private static $menu_slug;

    /**
     * @var string $text_button_save  Translatable text on the 'Save all settings' button
     */
    private static $text_button_save;

    /**
     * @var string $text_notification_saved  Translatable text of the 'Settings saved' notification
     */
    private static $text_notification_saved;

    /**
     * @var array $items  Contains every admin options page settings section
     */
    private static $items = array();

    /**
     * @var array $fields  Contains every admin options page settings field
     */
    private static $fields = array();


    /**
     * Constructor for the admin options page.
     *
     * @param array $args  Array of mixed arguments that will override the values in the $defaults array
     * @return void
     */
    function __construct($args){
      /**
       * $defaults array options:
       * string 'db_options_name'          option_name in the wp_options table which holds all the settings in DB
       * string 'page_title'               Headline of the whole settings page
       * string 'menu_title'               Menu entry text of the settings page
       * bool   'menu_type'                TRUE: Standalone menu item; FALSE options page under WP Settings
       * string 'menu_icon'                Menu dashicon. (Standalone menu item only; https://developer.wordpress.org/resource/dashicons/)
       * int    'menu_position'            Position of the menu item. (Standalone menu item only; https://developer.wordpress.org/reference/functions/add_menu_page/#menu-structure)
       * string 'menu_capability'          Capability the user must have to access menu item (https://codex.wordpress.org/Roles_and_Capabilities#Capability_vs._Role_Table)
       * string 'menu_slug'                The slug name to refer to this menu page
       * string 'text_button_save'         Translatable text on the 'Save all settings' button
       * string 'text_notification_saved'  Translatable text of the 'Settings saved' notification
       */
      $defaults = array(
        'db_options_name'         => 'default_options_page',
        'page_title'              => 'Default Title',
        'menu_title'              => 'Default Title',
        'menu_type'               => false,
        'menu_icon'               => 'dashicons-admin-generic',
        'menu_position'           => 81,
        'menu_capability'         => 'manage_options',
        'menu_slug'               => 'default-options-page',
        'text_button_save'        => 'Save all settings',
        'text_notification_saved' => 'Settings saved.'
      );

      foreach(wp_parse_args($args, $defaults) as $option => $value){
        if(property_exists(__CLASS__, $option)){
          self::${$option} = $value;
        }
      }

      self::$db_options = get_option(self::$db_options_name);

      add_action('admin_menu', array(__CLASS__, 'save_default_settings'));
      add_action('admin_menu', array(__CLASS__, 'add_menu_item'));
      add_action('admin_init', array(__CLASS__, 'display_panel_fields'));
    }


    /**
     * Get the main plugin file from a subfolder.
     *
     * @return string  Absolute path and filename of main plugin file.
     */
    public static function get_plugin_file(){
      require_once(ABSPATH.'/wp-admin/includes/plugin.php');
      $plugins       = get_plugins();
      $plugin_path   = explode('/', rtrim(plugin_dir_path(dirname(__FILE__)), '/'));
      $plugin_folder = array_pop($plugin_path);

      foreach($plugins as $plugin_file => $plugin_info){
        if(strpos($plugin_file, $plugin_folder.'/') !== false){
          return WP_PLUGIN_DIR.'/'.$plugin_file;
        }
      }
      return null;
    }


    /**
     * Set the default setting.
     *
     * @return array  Array with default values
     */
    static function define_default_settings(){
      $options = array();
      foreach(self::$fields as $slug => $options){
        $options[$slug] = (isset($options['default']) && !empty($options['default'])) ? $options['default'] : '';
      }
      return $options;
    }

    /**
     * Save options to DB.
     *
     * @return void
     */
    static function save_default_settings(){
      if(self::$db_options === false){
        self::$db_options = self::define_default_settings();
        update_option(self::$db_options_name, self::$db_options);
      }
    }


    /**
     * Create admin page.
     *
     * @return void
     */
    static function add_menu_item(){
      if(self::$menu_type === true){
        // Options page as a standalone menu item. (See theme_settings_page() about how to display notifications.)
        add_menu_page(self::$page_title, self::$menu_title, self::$menu_capability, sanitize_title(self::$menu_slug), array(__CLASS__, 'settings_page'), self::$menu_icon, self::$menu_position);
      }else{
        // Options page under Wordpress settings menu.
        add_options_page(self::$page_title, self::$menu_title, self::$menu_capability, sanitize_title(self::$menu_slug), array(__CLASS__, 'settings_page'));
      }
    }

    /**
     * Create admin page template.
     *
     * @return string  Echo the HTML template
     */
    static function settings_page(){
      ?>
      <div class="wrap">
        <h1><?php echo self::$page_title; ?></h1>
        <?php
        if(self::$menu_type === true){
          settings_errors(self::$db_options_name.'_page_errors');
        }
        ?>
        <form method="post" action="options.php">
          <?php settings_fields(self::$db_options_name); ?>
          <?php do_settings_sections(self::$db_options_name); ?>
          <table class="form-table settings_page_submit">
            <tbody>
              <tr>
                <th scope="row">&nbsp;</th>
                <td><input name="<?php echo self::$db_options_name; ?>[submit]" id="submit_settings_form" type="submit" class="button-primary" value="<?php echo self::$text_button_save; ?>"></td>
              </tr>
            </tbody>
          </table>
        </form>
      </div>
      <?php
    }


    /**
     * Public function to add a settings section with fields via config file.
     *
     * @param string $section_slug      Slug of the section
     * @param string $section_headline  Headline of the section
     * @param array $fields             Array of settings fields under this section
     * @return void
     */
    public static function add_section($section_slug = '', $section_headline = '', $fields = array()){
      self::$items[$section_slug] = array(
        'section_slug'     => $section_slug,
        'section_headline' => $section_headline,
        'fields'           => $fields
      );

      foreach($fields as $field_slug => $field_options){
        self::$fields[$field_slug] = $field_options;
      }
    }


    /**
     * Sanitize inputs.
     *
     * @param array $input  Name-attributes as array from fields.
     * @return array  Sanitized values in array
     */
    static function sanitize_settings($input){
      global $allowedposttags;
      $submit      = (empty($input['submit'])) ? false : true;
      // $valid_input = self::define_default_settings();
      $valid_input = array();

      if($submit)
      {
        foreach(self::$fields as $slug => $options){
          // Use data validation functions from Wordpress (https://codex.wordpress.org/Data_Validation)
          switch($options['type'])
          {
            case 'checkbox':
              $valid_input[$slug] = (isset($input[$slug])) ? 1 : 0;
              break;

            case 'select':
            case 'dropdown':
              $valid_input[$slug] = esc_attr($input[$slug]);
              break;

            case 'radio':
              $valid_input[$slug] = esc_attr($input[$slug]);
              break;

            case 'textarea':
              $valid_input[$slug] = wp_kses($input[$slug], $allowedposttags);
              break;

            case 'input':
              $valid_input[$slug] = esc_attr($input[$slug]);
              break;

            default:
              $valid_input[$slug] = esc_attr($input[$slug]);
          }
        }

        if(!empty($valid_input)){
          $error_message = self::$text_notification_saved;
          $error_type    = 'updated';
        }

        add_settings_error(
          self::$db_options_name.'_page_errors',
          esc_attr('settings_updated'),
          $error_message,
          $error_type
        );
      }

      return $valid_input;
    }

    /**
     * Echo custom settings section headline.
     *
     * @param array  Arguments
     * @return void
     */
    static function display_setting__headline($args){
      echo '';
    }

    /**
     * Echo input settings field.
     *
     * string $args['option_slug']  Slug of setting option. Required.<br>
     * string $args['description']  Description of input field. Optional.<br>
     * int    $args['size']         Length of input field. Optional.
     *
     * @param array $args  Arguments to set the parameter of the input element.
     * @return string  HTML string of the input field element.
     */
    static function display_setting_field__input($args){
      if(!empty($args['option_slug']))
      {
        $size            = (isset($args['size']) && !empty($args['size']) && is_numeric($args['size'])) ? $args['size'] : 50;
        $db_option_value = (empty(self::$db_options[$args['option_slug']])) ? '' : self::$db_options[$args['option_slug']];

        $html = "\n".'<div id="theme_setting_'.$args['option_slug'].'"><p>';
        $html .= "\n".'<input type="text" name="'.self::$db_options_name.'['.$args['option_slug'].']" size="'.$size.'" value="'.$db_option_value.'">';
        if(!empty($args['description'])) $html .= "\n".'<br><p class="description">'.$args['description'].'</p>';
        $html .= "\n".'</p></div>';

        echo $html;
      }
    }

    /**
     * Echo checkbox settings field.
     *
     * string $args['option_slug']  Slug of setting option. Required.<br>
     * string $args['label']        Label of checkbox. Optional.<br>
     * string $args['description']  Description of checkbox field. Optional.
     *
     * @param array $args  Arguments to set the parameter of the checkbox element.
     * @return string  HTML string of the checkbox field element.
     */
    static function display_setting_field__checkbox($args){
      if(!empty($args['option_slug']))
      {
        $checked = (isset(self::$db_options[$args['option_slug']])) ? checked(self::$db_options[$args['option_slug']] == 1, true, false) : 'checked="checked"';

        $html = "\n".'<div id="theme_setting_'.$args['option_slug'].'"><p>';
        $html .= "\n".'<input type="checkbox" name="'.self::$db_options_name.'['.$args['option_slug'].']" id="theme_setting_'.$args['option_slug'].'_checkbox" value="1" '.$checked.'>';
        if(!empty($args['label'])) $html .= ' <label for="theme_setting_'.$args['option_slug'].'_checkbox">'.$args['label'].'</label>';
        if(!empty($args['description'])) $html .= "\n".'<br><p class="description">'.$args['description'].'</p>';
        $html .= "\n".'</p></div>';

        echo $html;
      }
    }

    /**
     * Echo select settings field.
     *
     * string $args['option_slug']  Slug of setting option. Required.<br>
     * string $args['description']  Description of select field. Optional.<br>
     * array  $args['option']       Option 'value' => 'Title' pairs. Optional.
     *
     * @param array $args  Arguments to set the parameter of the select element.
     * @return string  HTML string of the select field element.
     */
    static function display_setting_field__select($args){
      if(!empty($args['option_slug']))
      {
        $html = "\n".'<div id="theme_setting_'.$args['option_slug'].'"><p>';
        $html .= "\n".'<select name="'.self::$db_options_name.'['.$args['option_slug'].']">';

        $settings_option_value = (isset(self::$db_options[$args['option_slug']])) ? self::$db_options[$args['option_slug']] : '';
        $i                     = 0;

        if(isset($args['option']) && is_array($args['option'])){
          foreach($args['option'] as $value => $title){
            $chosen = (isset($settings_option_value) and $settings_option_value == $value) ? ' selected="selected"' : '';
            $chosen = ((empty($chosen) or !isset($settings_option_value)) and $i == 0) ? ' selected="selected"' : $chosen;
            $i      = $i+1;
            $html   .= "\n\t".'<option value="'.$value.'"'.$chosen.'>'.$title.'</option>';
          }
        }else{
          $html   .= "\n\t".'<option value="">-</option>';
        }

        $html .= "\n".'</select>';
        if(!empty($args['description'])) $html .= "\n".'<br><p class="description">'.$args['description'].'</p>';
        $html .= "\n".'</p></div>';

        echo $html;
      }
    }

    /**
     * Same as display_setting_field__select() from above.
     *
     * @param array $args  Arguments to set the parameter of the select element.
     * @return void
     */
    static function display_setting_field__dropdown($args){
      self::display_setting_field__select($args);
    }

    /**
     * Echo radio button settings field.
     *
     * string $args['option_slug']  Slug of setting option. Required.
     * array  $args['option']       Option 'value' => 'Label' pairs. Optional.
     *
     * @param array $args  Arguments to set the parameter of the radio element.
     * @return string  HTML string of the radio field element.
     */
    static function display_setting_field__radio($args){
      if(!empty($args['option_slug']))
      {
        $html = "\n".'<div id="theme_setting_'.$args['option_slug'].'"><p>';
        $settings_option_value = (isset(self::$db_options[$args['option_slug']])) ? self::$db_options[$args['option_slug']] : '';
        $i = 0;

        if(isset($args['option']) && is_array($args['option'])){
          $count = count($args['option']);

          foreach($args['option'] as $value => $label){
            $chosen = (isset($settings_option_value) and $settings_option_value == $value) ? ' checked="checked"' : '';
            $chosen = ((empty($chosen) or !isset($settings_option_value)) and $i == 0) ? ' checked="checked"' : $chosen;
            $i      = $i+1;
            $html   .= "\n\t".'<input type="radio" id="theme_setting_'.$args['option_slug'].'_radio_'.$value.'" name="'.self::$db_options_name.'['.$args['option_slug'].']" value="'.$value.'"'.$chosen.'>';
            $html .= ' <label for="theme_setting_'.$args['option_slug'].'_radio_'.$value.'">'.$label.'</label>';
            if($i < $count) $html .= '<br>';
          }
        }

        if(!empty($args['description'])) $html .= "\n".'<br><p class="description">'.$args['description'].'</p>';
        $html .= "\n".'</p></div>';
        echo $html;
      }
    }

    /**
     * Echo textarea settings field.
     *
     * string $args['option_slug']  Slug of setting option. Required.<br>
     * string $args['description']  Description of textarea field. Optional.<br>
     * int    $args['cols']         Width of textrea field. Optional.<br>
     * int    $args['rows']         Height of textrea field. Optional.
     *
     * @param array $args  Arguments to set the parameter of the textarea element.
     * @return string  HTML string of the textarea field element.
     */
    static function display_setting_field__textarea($args){
      if(!empty($args['option_slug']))
      {
        $cols            = (isset($args['cols']) && !empty($args['cols']) && is_numeric($args['cols'])) ? $args['cols'] : 50;
        $rows            = (isset($args['rows']) && !empty($args['rows']) && is_numeric($args['rows'])) ? $args['rows'] : 8;
        $db_option_value = (empty(self::$db_options[$args['option_slug']])) ? '' : self::$db_options[$args['option_slug']];

        $html = "\n".'<div id="theme_setting_'.$args['option_slug'].'"><p>';
        $html .= "\n".'<textarea name="'.self::$db_options_name.'['.$args['option_slug'].']" cols="'.$cols.'" rows="'.$rows.'" id="theme_setting_'.$args['option_slug'].'_textarea">'.$db_option_value.'</textarea>';
        if(!empty($args['description'])) $html .= "\n".'<br><p class="description">'.$args['description'].'</p>';
        $html .= "\n".'</p></div>';

        echo $html;
      }
    }


    /**
     * Create admin page form fields.
     *
     * @return void
     */
    static function display_panel_fields(){
      register_setting(self::$db_options_name, self::$db_options_name, array(__CLASS__, 'sanitize_settings'));

      foreach(self::$items as $section_slug => $values){
        add_settings_section(
          'settings_section__'.$section_slug,
          $values['section_headline'],
          array(__CLASS__, 'display_setting__headline'),
          self::$db_options_name
        );

        foreach($values['fields'] as $field_slug => $options){
          $field_options = array('option_slug' => $field_slug);
          $field_title   = (isset($options['title']) && !empty($options['title'])) ? $options['title'] : '';
          $field_types   = array('input', 'checkbox', 'radio', 'select', 'dropdown', 'textarea');
          $field_type    = (isset($options['type']) && in_array($options['type'], $field_types)) ? $options['type'] : 'input';

          if(isset($options['options']) && is_array($options['options'])){
            foreach($options['options'] as $key => $value){
              $field_options[$key] = $value;
            }
          }

          add_settings_field(
            'render_setting__'.$field_slug,
            $field_title,
            array(__CLASS__, 'display_setting_field__'.$field_type),
            self::$db_options_name,
            'settings_section__'.$section_slug,
            $field_options
          );
        }
      }
    }


  } // class
} // if !class_exists

?>