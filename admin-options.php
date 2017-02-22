<?php
/**
 * Plugin Name: Admin Options
 * Description: Admin Options Page Plugin for Wordpress
 * Version: 1.0
 * Author: Florian Rehder <code@florianrehder.de>
 * Text Domain: admin_options
 *
 * @author     Florian Rehder <code@florianrehder.de>
 * @copyright  2017 Florian Rehder
 * @license    MIT
 * @version    1.0
 */

namespace AdminOptionsPlugin;

// Pssst
if(!defined('ABSPATH')){
  die('Silence is golden');
  exit;
}

$plugin_files = array(
  // Plugin setup
  'includes/class.plugin-setup',

  // Admin options menu
  'includes/class.admin-options-page',
  'includes/admin-options-page-setup',
);

foreach($plugin_files as $file){
  require_once(plugin_basename($file.'.php', __FILE__));
}

?>