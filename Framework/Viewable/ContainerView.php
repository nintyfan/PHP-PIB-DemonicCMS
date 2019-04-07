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

abstract class ContainerView extends Viewable {

  private $elements = null;
  private $output = null;
  
  protected function __render_helper($element)
  {
    $this->output->set_field('Me', (string) $element);
    return $this->output->render();
  }
  
  public function set_output_obj($obj) {
    
    $this->output = $obj;
  }
  
  public function render($data=null)
  {
  
   return  $this->foreachelement(function ($element, $param) {
     return $this->__render_helper($element);
    }, null);
  }
  
  abstract public function foreachelement($function, $param);
};
