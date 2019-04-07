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
namespace Viewable;

abstract class Viewable {
  
  abstract public function render($data=null);
  
  public $autoflush;
  protected $fields;
  
  public function __construct($data = null, bool $autoflush = false) {
  
    $this->fields = $data;
    $this->autoflush = $autoflush;
  }
  
  public function set_field($name, $value) {
  
    $this->fields[$name] = $value;
  }
  
  public function get_field($name) {
    
    return $this->fields[$name];
  }
  
  public function __destruct() {
  
    if (true == $this->autoflush) {
    
      echo $this->render();
    }
  }
};
