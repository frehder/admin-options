<?php
/**
 * PluginSetup class
 *
 * @author     Florian Rehder <code@florianrehder.de>
 * @copyright  2017 Florian Rehder
 * @license    MIT
 */

namespace AdminOptionsPlugin;

if(!class_exists('PluginSetup'))
{
  add_action('plugins_loaded', array(__NAMESPACE__.'\PluginSetup', 'get_instance'), 0);

  /**
   * Basic plugin setup stuff.
   */
  class PluginSetup
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
     * Class constructor.
     *
     * @return void
     */
    function __construct(){
      add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));
    }


    /**
     * Make Plugin available for translation.
     *
     * @return void
     */
    function load_plugin_textdomain(){
      load_plugin_textdomain('admin_options', false, dirname(dirname(plugin_basename(__FILE__))).'/languages/');
    }


  } // class
} // if !class_exists

?>