<?php
//------------------------------------------------------------------------------
//	Valerie
//	(c) 2009 Chris Jaure
//	license: MIT License
//	website: http://code.google.com/p/valerie/
//
//	valerie.php
//------------------------------------------------------------------------------

/*
  Class: Valerie
  
  Contains static methods related to Valerie
*/

class Valerie {

  private static $profiler;
  
  public static function startProfiler($time) {
    if (App::get('config:debug')) {
      include App::get('config:root') . 'libs/pqp/classes/PhpQuickProfiler.php';
      self::$profiler = new PhpQuickProfiler($time);
    }
  }
  
  public static function stopProfiler() {
    if (isset(self::$profiler)) {
      self::$profiler->display();
    }
  }

  public static function log($message) {
    if (Console) {
      Console::log($message);
    }
  }
  
  public static function logMemory($object = false, $name = 'PHP') {
    if (Console) {
      Console::logMemory($object, $name);
    }
  }
  
  public static function logError($exception, $message) {
    if (Console) {
      Console::logError($exception, $message);
    }
  }
  
  public static function logSpeed($name = "Point in Time") {
    if (Console) {
      Console::logSpeed($name);
    }
  }

  /*
    Method: render
    
    A shortcut to render the form defined with the config 'single_setup'
  */  

  public static function render($name = null, $style = null) {
    if (isset($name)) {
      $single_form_config = App::get("forms:$name");
      if (!isset($single_form_config['definition'])) {
        trigger_error("No form found at 'forms:$name'", E_USER_ERROR);
      }
    }
    else {
      $single_form_config = App::get('form');
      if (!isset($single_form_config['definition'])) {
        trigger_error("No form found at 'form'", E_USER_ERROR);
      }
    }
    $single_form_config['definition']['attributes'] += array(
      'method' => 'post',
      'action' => App::get('config:source_uri') . 'processform.php'
    );
    
    $style = $single_form_config['style'];
    if (!isset($style)) {
      $style = 'default';
    }
    $form = new ValerieForm($single_form_config['definition'], $style);
    
    $form->render();
  }
  
  /*
    Method: fireHooks
    
    Fires generic hook, form hook, and plugin hook
  */
  
  public static function fireHooks($hook, $args = null) {
    $style = App::get('config:style');
    $id = App::get('form_id');
    App::fire("hooks:$hook", $args);
    App::fire("hooks:$style:$hook", $args);
    App::fire("hooks:$id:$hook", $args);
  }
  
  /*
    Method: loadFormPlugins
    
  */
  
  public static function loadFormPlugins($form) {
    $plugin_path = App::get('config:plugin_path');
    $plugins = App::get("config:plugins:$form");
    if (isset($plugins)) {
      foreach ((array) $plugins as $plugin) {
        if (is_file($plugin_path . $plugin .'/config.php')) {
          include_once $plugin_path . $plugin .'/config.php';
        }
        if (is_file($plugin_path . $plugin . '.php')) {
          include_once $plugin_path . $plugin . '.php';
        }
        
        $plugin_hooks = App::get("plugins:$plugin:hooks");

        if (is_array($plugin_hooks)) {
          foreach ($plugin_hooks as $hook => $fn) {
            App::attach("hooks:$form:$hook", $fn);
          }
        }
      }
    }
  }
  
  /*
    Method: loadAssets
    
  */

  public static function loadAssets($assets = null) {
    echo "\n\n<!-- Loading Valerie Assets -->\n";
    
    self::fireHooks('beforePrintAssets');
    
    if (!App::get('source_loaded')) {
      $source_uri = App::get('config:source_uri');
      echo "\n<!-- Begin source assets -->\n";
      echo "<script type=\"text/javascript\" src=" .
        "\"http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js\">" .
        "</script>\n";
      echo "<script type=\"text/javascript\" " .
        "src=\"{$source_uri}valerieclient.js\"></script>\n";
      App::set('source_loaded', true);
    }
    
    $config_assets = array('plugins' => array(), 'styles' => array());
    foreach (App::get('config:plugins') as $plugin) {
      $config_assets['plugins'] = array_merge($config_assets['plugins'], (array) $plugin);
    }
    $config_assets['styles'] = App::get('config:styles');
    $assets['plugins'] = array_unique(array_merge($config_assets['plugins'], (array) $assets['plugins']));
    $assets['styles'] = array_unique(array_merge($config_assets['styles'], (array) $assets['styles']));
    
    foreach ($assets as $type => $names) {
      foreach ((array) $names as $name) {
        if (!App::get("$type:$name:loaded")) {
          $includes = App::get("$type:$name:assets");
          $uri = App::get("$type:$name:uri");
          if (!$uri) {
            switch($type) {
              case 'plugins':
                $uri = App::get('config:plugin_uri');
                break;
              case 'styles':
                $uri = App::get('config:style_uri');
                break;
              case 'forms':
                $uri = App::get('config:form_uri');
                break;
            }
            $uri .= $name;
          }
          echo "\n<!-- Begin $type:$name assets -->\n";
          foreach ((array) $includes as $include_type => $include_paths) {
            foreach ((array) $include_paths as $include_path) {
              $conditional = (strpos($include_path, '|') !== false);
              if ($conditional) {
                list($conditional, $include_path) = explode('|', $include_path);
                echo "<!--[if $conditional]>\n";
              }
              $include_path = strip_tags($include_path);
              switch ($include_type) {
                case 'css':
                  echo "<link rel=\"stylesheet\" type=\"text/css\" ".
                    "href=\"$uri/$include_path\" />\n";
                  break;
                case 'js':
                  echo "<script type=\"text/javascript\" ".
                    "src=\"$uri/$include_path\"></script>\n";
                  break;
              }
              if ($conditional) {
                echo "<![endif]-->\n";
              }
            }
          }
          App::set("$type:$name:loaded", true);
        }
      }
    }
    
    self::fireHooks('afterPrintAssets');
    
    echo "<!-- Done Loading Valerie Assets -->\n";
  }

}

?>
