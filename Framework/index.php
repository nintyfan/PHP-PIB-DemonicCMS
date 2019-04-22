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
include_once('autoload.php');

function load_controller($name)
{
  $breadcumbs = explode('/', $name);
  $main_class_ctrl_name = $breadcumbs[count($breadcumbs)-1] . 'Controller';
  $result = find_class_file($name . 'Controller');

  if (null != $result && null != $result[1]) {
  
    include_once($result[1]);
    $obj = new $main_class_ctrl_name();
    return [$obj, null, $result[0]];
  }
  
  if (2 > count($breadcumbs)) {
    
    $action_name = 'index';
  }
  else {
    
    $action_name = $breadcumbs[count($breadcumbs)-1];
  }
  array_pop($breadcumbs);
  
  if (empty($breadcumbs) || empty($breadcumbs[count($breadcumbs)-1])) {
  
    return [null, null, null];
  }
  $main_class_ctrl_name = $breadcumbs[count($breadcumbs)-1] . 'Controller';
  $controller_name = implode("/", $breadcumbs);
  $result = find_class_file($controller_name . 'Controller');

  if (null != $result && null != $result[1]) {
    
    include_once($result[1]);
    $obj = new $main_class_ctrl_name();
    return [$obj, $action_name, $result[0]];
    
  }
  
  $breadcumbs = explode('/', $name);
  $name = 'Page';
  
  $result = find_class_file($name . 'Controller');
  
  if (null != $result && null != $result[1]) {
    
    include_once($result[1]);
    $obj = new $main_class_ctrl_name();
    return [$obj, null, $result[0]];
  }
  
  if (2 > count($breadcumbs)) {
    
    $action_name = 'index';
  }
  else {
    
    $action_name = $breadcumbs[count($breadcumbs)-1];
  }
  array_pop($breadcumbs);
  
  if (empty($breadcumbs) || empty($breadcumbs[count($breadcumbs)-1])) {
    
    return [null, null, null];
  }
  $main_class_ctrl_name = 'PageController';
  $controller_name = implode("/", $breadcumbs) . '/Page';
  $result = find_class_file($controller_name . 'Controller');
  
  if (null != $result && null != $result[1]) {
    
    include_once($result[1]);
    $obj = new $main_class_ctrl_name();
    return [$obj, $action_name, $result[0]];
    
  }
  return [null, null, null];
}

$query_get = [];
if (isset($_SERVER['DemonicCMS_Controller_query_string'])) {

  $query_get_helper = explode('&',$_SERVER['DemonicCMS_Controller_query_string']);
  
  foreach ($query_get_helper as $element) {
  
    $key_val = explode('=', $element);
    
    if (empty($key_val[0])) {
    
      continue;
    }
    
    if (!isset($key_val[1]) && empty($key_val[1])) {
      
      $key_val[1] = true;
    }
    
    $query_get[$key_val[0]] = $key_val[1];
  }
}

if (!empty($_GET['Controller']))
foreach ($autoload_data as $module) {

  if (isset($module['files-path'])) {
  
    if (0 === strpos('/' . $_GET['Controller'], $module ['prefix'] . $module['files-path'])) {
    
      set_time_limit(0);
      $withoutPrefix = substr('/' . $_GET['Controller'], strlen($module ['prefix']));
      $withoutPrefix = realpath( $module['directory'] . '/' . $withoutPrefix);
      if (!file_exists($withoutPrefix)) {
      
        die('Bad');
      }
      
      $fp = fopen($withoutPrefix, "r");
      fpassthru($fp);
      exit();
    }
  }
}

$main_controller = 'Page';
$main_controler_params = ['Page' => 'Home'];
if (!empty($_GET['Controller'])) {

  $main_controller = $_GET['Controller'];
}

  $breadcumbs = explode('/', $main_controller);
  
  $main_class_ctrl_name = $main_controller;
  
  $result = load_controller($main_class_ctrl_name );
  $controller_obj = $result[0];
  $action_name = $result[1];
  
  if (null == $controller_obj) {
  
    die("Page doesn't exist");
  }
  
  if (null == $action_name) {
  
    $action_name = 'index';
  }
  $breadcumbs_obj = new \Viewable\ArrayList();
  $breadcumbs_relative_obj = new \Viewable\ArrayList();
  foreach ($breadcumbs as $br) {
  
    if (empty($br) || $br ==  "") {
      
      continue;
    }
    $breadcumbs_obj->push($br);
  }
  
  $module_path = [];
  if (isset($result[2]) && !empty($result[2]['prefix'])) {
  
    $module_path = explode('/', $result[2]['prefix']);
    foreach ($module_path as $key => $part) {
    
      if (empty($part) || $part ==  "") {
      
        unset($module_path[$key]);
      }
    }
    
    $brcpy = $breadcumbs;
    foreach ($module_path as $a) {
      
      array_shift($brcpy);
    }
    
    foreach ($brcpy as $br) {
     
      if (empty($br) || $br ==  "") {
        
        continue;
      }
      $breadcumbs_relative_obj->push($br);
    }
  }
  
  $controller_obj->set_field('breadcumbs', $breadcumbs_obj);
  $controller_obj->set_field('module_breadcumbs', $breadcumbs_relative_obj);
  
  $result = $controller_obj->call_action($main_controller, $action_name, $query_get, NULL, NULL, NULL);
  
  if (is_string($result)) {
  
    new \Viewable\StringView($result, true);
  }
  else if (!isset($result)) {
  
    $tmpl = new \Viewable\TemplateView(null, true);
    
    $tmpl->set_secure_execution(false);
    
    $tmpl->set_wd('main-app/template/public/');
    
    $tmpl->setController($controller_obj);
  }
