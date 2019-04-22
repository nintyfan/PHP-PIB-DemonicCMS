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
class BrowserCodeBuilder_Controller extends Basic_Controller {

  public $__js_init_code = [
    
  ];
  
  /*
   * $path - path to array (each element is piece of code - block or instruction). Block of code are representing by arrays and instructions by strings.
   * $code - an array, representing block of code or string representing instruction
   */

  private function __construct_code($ins)
  {
    $instructions = '';
    
    if (is_array($ins)) {
    
      foreach ($ins as $single) {
      
        $instructions = $instructions . $this->__construct_code($single);
      }
    }
    else return $ins;
    
    return $instructions;
  }
  
  public function pre_action()
  {
    
    $this->set_ref('js-code', htmlspecialchars($this->__construct_code($this->__js_init_code)));
    
    
  }
  
  protected function append_js_init_code($path, $code)
  {
    $parts = explode('/', $path);
    $arr = &$this->__js_init_code;
    $prev_arr = null;
    
    foreach ($parts as $part) {
    
      if (empty($arr[$part])) {
      
        break;
      }
      
      $prev_arr = $arr;
      $arr = &$arr[$part];
    }
    
    if (is_array($code)) {
    
      if (empty($prev_arr)) return;
      $prev_arr[$parts[count($parts)-2]][$parts[count($parts)-1]] = $code;
    }
    else {
    
    }
    $arr[] = $code;
    
  }
}
