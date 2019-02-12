
# Wordpress admin-options plugin

A Wordpress Plugin to easily create an Admin Options Page in the Wordpress Backend.


## Setup

**Create a new Admin Options Page with settings fields**

```php
require_once('class.admin-options-page.php');

if(!function_exists('admin_options_page_setup')){
  function admin_options_page_setup(){

    $admin_options = new AdminOptions(array(
      'db_options_name'         => 'option_name_in_db',
      'page_title'              => 'Headline of Settings Page',
      'menu_title'              => 'Menu Entry Text of Settings Page',
      'menu_type'               => false, // TRUE: Standalone menu item; FALSE options page under WP Settings
      'menu_icon'               => 'dashicons-wordpress-alt', // Menu dashicon. (Standalone menu item only)
      'menu_position'           => 61, // Position of the menu item. (Standalone menu item only)
      'menu_capability'         => 'manage_options', // Capability the user must have to access menu item
      'menu_slug'               => 'options-page-slug',
      'text_button_save'        => 'Text on the Save All Settings button',
      'text_notification_saved' => 'Text of the Settings Saved notification',
    ));

    $admin_options->add_section('section_slug', 'Section Headline',
      array( // Fields
        'field_slug' => array(
          'default' => 'Default Value',
          'title'   => 'Field Title',
          'type'    => 'field_type', // (input|checkbox|radio|dropdown|textarea|wp_editor)
          'options' => array( // Field Options (depends on type)

            // Type dropdown
            'description' => 'Dropdown Description',
            'option'      => array(
              'none'         => '- Choose -',
              'option_1_key' => 'Option 1 Title',
              'option_2_key' => 'Option 2 Title',
            )

            // Type input
            'description' => 'Description of the Input Field',

            // Type checkbox
            'label'       => 'Label of the Checkbox',
            'description' => 'Description of the Checkbox',

            // Type radio
            'option' => array(
              'option_1_key' => 'Option 1 Label',
              'option_2_key' => 'Option 2 Label'
            ),

            // Type textarea
            'options' => array(
              'description' => 'Description of the Textarea Field',
              'cols'        => 50,
              'rows'        => 5,
            ),

            // Type wp_editor
            'options' => array(
              'rows'        => 25,
              'description' => 'Description below the Editor',
            ),

          )
        ),
      )
    );

  }
}

if(function_exists('admin_options_page_setup')){
  add_action('init', 'admin_options_page_setup');
}
```


## License

The source for admin-options is released under the MIT License. See LICENSE.txt for further details.
