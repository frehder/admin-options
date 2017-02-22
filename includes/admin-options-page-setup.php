<?php
/**
 * Setup a new Admin Options Page.
 *
 * @author     Florian Rehder <code@florianrehder.de>
 * @copyright  2017 Florian Rehder
 * @license    MIT
 */

namespace AdminOptionsPlugin;
use AdminOptionsPlugin\AdminOptions;

if(!function_exists(__NAMESPACE__.'\admin_options_page_setup')){
  /**
   * Create admin options page and its settings fields.
   *
   * @return void
   */
  function admin_options_page_setup(){
    $admin_options = new AdminOptions(array(
      'db_options_name'         => 'admin_test',
      'page_title'              => _x('Custom Admin Options', 'Admin Options', 'admin_options'),
      'menu_title'              => _x('Admin Test', 'Admin Options', 'admin_options'),
      'menu_type'               => true,
      'menu_slug'               => 'custom-admin-options',
      'text_button_save'        => _x('Save all settings', 'Admin Options', 'admin_options'),
      'text_notification_saved' => _x('Settings saved.', 'Admin Options', 'admin_options'),
    ));

    $admin_options->add_section('cool_first_section', _x('First Section Headline', 'Admin Options', 'admin_options'),
      array(
        'some_cool_text_input_field' => array(
          'default' => 'Max Mustermann',
          'title'   => _x('Name', 'Admin Options', 'admin_options'),
          'type'    => 'input',
          'options' => array(
            'description' => _x('Description of the text field', 'Admin Options', 'admin_options'),
            'size'        => 50,
          )
        ),

        'some_cool_dropdown' => array(
          'default' => 'none',
          'title'   => _x('Choose a number', 'Admin Options', 'admin_options'),
          'type'    => 'select',
          'options' => array(
            'description' => _x('Description of the dropdown', 'Admin Options', 'admin_options'),
            'option'      => array(
              'none'  => '- '._x('Choose a number', 'Admin Options', 'admin_options').' -',
              'one'   => 'One',
              'two'   => 'Two',
              'three' => 'Three',
              'four'  => 'Four',
              'five'  => 'Five',
              'six'   => 'Six',
            ),
          )
        ),

        'some_cool_checkbox_element' => array(
          'default' => 1,
          'title'   => _x('Activate', 'Admin Options', 'admin_options'),
          'type'    => 'checkbox',
          'options' => array(
            'label'       => _x('Label of the checkbox', 'Admin Options', 'admin_options'),
            'description' => _x('Description of the checkbox', 'Admin Options', 'admin_options'),
          )
        ),

        'some_cool_radiobox_element' => array(
          'default' => 'male',
          'title'   => _x('Sex', 'Admin Options', 'admin_options'),
          'type'    => 'radio',
          'options' => array(
            'description' => _x('Description of the radio box', 'Admin Options', 'admin_options'),
            'option' => array(
              'male'   => _x('Male', 'Admin Options', 'admin_options'),
              'female' => _x('Female', 'Admin Options', 'admin_options'),
              'other'  => _x('Other', 'Admin Options', 'admin_options'),
            ),
          )
        ),

      )
    );

    $admin_options->add_section('happy_second_section', _x('Second Section Headline', 'Admin Options', 'admin_options'),
      array(
        'second_cool_dropdown_select_element' => array(
          'default' => 'bla',
          'title'   => _x('Dropdown test', 'Admin Options', 'admin_options'),
          'type'    => 'dropdown',
          'options' => array(
            'description' => _x('Dropdown description', 'Admin Options', 'admin_options'),
            'option'      => array(
              'none'  => '- '._x('Choose wisely', 'Admin Options', 'admin_options').' -',
              'blubb' => 'Blubb',
              'bla'   => 'Bla',
              'miep'  => 'Miep',
            )
          )
        ),

        'some_cool_textarea_element' => array(
          'default' => 'Vivamus ultricies velit id quam cursus, nec bibendum elit gravida. Sed commodo a sem id placerat. Cras elementum aliquet sapien, vitae vehicula nulla posuere vitae.',
          'title'   => _x('Biography', 'Admin Options', 'admin_options'),
          'type'    => 'textarea',
          'options' => array(
            'description' => _x('Textarea description', 'Admin Options', 'admin_options'),
            'cols'        => 50,
            'rows'        => 5,
          )
        ),

      )
    );

  }
}

if(function_exists(__NAMESPACE__.'\admin_options_page_setup')){
  add_action('init', __NAMESPACE__.'\admin_options_page_setup');
}

?>