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
$autoload_data = [];
$autoload_data[] = [];
$autoload_data[count($autoload_data)-1]['path'] = [realpath('.')];
$current_module = null;
$dir = opendir(__DIR__ . '/..');
while (NULL != ($file = readdir($dir))) {
  
  if ($file == '..' || $file == '.') {
    
    continue;
  }
  $file = realpath('../' . $file);
  if (is_dir($file) && file_exists($file . '/meta')) {
    
    $lines = file_get_contents($file . '/meta');
    $lines = explode("\n", $lines);
    
    $autoload_data[] = [];
    /* Path for any kind of class */
    $autoload_data[count($autoload_data)-1]['directory'] = $file;
    $autoload_data[count($autoload_data)-1]['path'] = [];
    /* Path for controllers classes */
    $autoload_data[count($autoload_data)-1]['ctrl_path'] = [];
    
    foreach ($lines as $line) {

      if (0 === strpos($line, 'include:')) {
      
        $path = $file . '/' . substr($line, strlen('include:'));
        
        if (0 != strpos($path, realpath(__DIR__ . '/..'))) {
        
          continue;
        }
        $autoload_data[count($autoload_data)-1]['path'][] = $path;
      }
      else if (0 === strpos($line, 'controllers-include:')) {
        
        $path = $file . '/' . substr($line, strlen('controllers-include:'));
        
        if (0 != strpos($path, realpath(__DIR__ . '/..'))) {
          
          continue;
        }
        $autoload_data[count($autoload_data)-1]['ctrl_path'][] = $path;
      }
      else if (0 === strpos($line, 'prefix:')) {
      
        $prefix = substr($line, strlen('prefix:'));
        if (!empty($prefix)) {
        
          $autoload_data[count($autoload_data)-1]['prefix'] = $prefix;
        }
        
      }
      else if (0 === strpos($line, 'files-path:')) {
        
        $prefix = substr($line, strlen('files-path:'));
        if (!empty($prefix)) {
          
          $autoload_data[count($autoload_data)-1]['files-path'] = $prefix;
        }
        
      }
    }
  }
}

function find_class_file($className)
{
  global $autoload_data;
  global $current_module;
  $namespace=str_replace("\\","/",__NAMESPACE__);
  $className=str_replace("\\","/",$className);
  
  if (null != $current_module && isset($current_module['ctrl_path'])) {
    $classWithoutPrefix  = $className;
    foreach ($current_module['ctrl_path'] as $path) {
      
      if (0 === strpos('/' . $className, $current_module ['prefix'])) 
      {
        
        $classWithoutPrefix = substr('/' . $className, strlen($current_module ['prefix']));
        
      }
      $class=$path . '/' . (empty($namespace)?"":$namespace."/")."{$classWithoutPrefix}.php";

      if (file_exists($class)) {
        return [$current_module , $class];
      }
    }
    
  }
  
  foreach ($autoload_data as $module) {
    
    $classWithoutPrefix  = $className;
    if (null == $current_module && isset($module['ctrl_path'])) {
      if (isset($module['prefix']) ) {
      
      if (0 === strpos('/' . $className, $module['prefix'])) 
      {
    
        $classWithoutPrefix = substr('/' . $className, strlen($module['prefix']));

      }
      else {
        continue;
      }
    }
  
    foreach ($module['ctrl_path'] as $path) {
      $class=$path . '/' . (empty($namespace)?"":$namespace."/")."{$classWithoutPrefix}.php";

      if (file_exists($class)) {
        $current_module = $module;
        return [$module, $class];
      }
    }
    }
    
    
    
    foreach ($module['path'] as $path) {
    $class=$path . '/' . (empty($namespace)?"":$namespace."/")."{$classWithoutPrefix}.php";

    if (file_exists($class)) {
      $current_module = $module;
      return [$module, $class];
    }
    }
  }
  return null;
}

spl_autoload_register(function($className)
{
  
  $obj = find_class_file($className);
  if (null == $obj) {
  
    die('Class not found');
  }
  else {
  
    include_once($obj[1]);
  }
});
