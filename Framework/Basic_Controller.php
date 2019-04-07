<?php
/*
 DemonicCMS - CMS & Framework provides interfaces to talk with browser
 Copyright (C) 2019 Sławomir Lach <slawek@lach.art.pl>
 
 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.
 
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 
 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */
class Basic_Controller {

  private static $actions = [];
  
  private $fields = [];
  
  private $model;
  
  
  public function __construct($model=null)
  {
    
    try {
    
      $reflector = new \ReflectionMethod($this, 'index');
      $this->actions[] = 'index';
    }
    catch (Exception $error) {
    
      return;
    }
    
   $c = get_class($this);
    
    do  {
      if (isset($c::$actions)) {
      
        $this->actions = array_merge($this->actions, $c::$actions);
      }
    } while (($c = get_parent_class($c)) !== false);
    
    $this->model = $model;
  }
  
  public function call_action($full_path, $path, $get_params, $post_params, $language, $headers)  {
    if (!empty($this->actions)) {
    
      if (in_array($path, $this->actions)) {
      
        $action_name = $path;
        
        return $this->$action_name($get_params, $post_params, $language, $headers);

      }
    }
    
    return "Page doesn't exist";
  }
  
  public function get_field($name)
  {
    if (isset($this->fields[$name])) return $this->fields[$name];
    if (isset($this->model)) return $this->model->get_field($name);
    
    return NULL;
  }
  
  public function set_field($name, $value)
  {
    $this->fields[$name] = $value;
  }
};
