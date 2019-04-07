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

class TemplateView_statement {

  public $type = null;
  
  public function __construct($type) {
  
    $this->type = $tyoe;
  }
};

 class TemplateView extends Viewable {
 
   public $secure_execution = true;
   public $templates = [];
   private $wd_prefix;
   private $wd;
   private $controller;
   public $recurse = false;
   
   private $tmpl_pos = -1;
   public $debug_1 = false;
   
   public function set_secure_execution(bool $new_val) {
   
     $this->secure_execution = $new_val;
   }
   
   public function setTemplate($name) {
   
     $this->templates = [$name];
   }
   
   public function setController($obj) {
   
     $name = $obj;
     $this->controller = $obj;
     if (is_string($obj)) {
     
       $this->controller = new $name();
    }
     $this->templates = [get_class($this->controller)];
     
     while (($c = get_parent_class($name)) !== false) {
       
       $this->templates[] = $c;
       
       $name = $c;
     }
     
     $this->wd = $this->refresh_wd($this->wd_prefix, $this->controller);
  }
  
   protected function refresh_wd($wd, $obj) {

     $append = null;
     $nwd = null;
     
     if (empty($wd)) {
     
       return;
    }
  
     if (is_subclass_of($obj, Basic_Controller, false)) {
       if (is_string($wd)) {
       
         $nwd = [$wd . '/Controllers/'];
       }
       else {
         
         $append = ['/Controllers/'];
       }
     }
     else if (is_a($obj, '\\Viewable\\ContainerView')) {
       
       $nwd = $wd;
       //$this->wd = [$this->wd_prefix, $this->wd_prefix . '/' . get_class($this->controller)];
     }
     else if (is_subclass_of($obj, Viewable, false)) {

       $nwd = $wd;
      // $this->wd = [$this->wd_prefix . '/FieldsByName/', $this->wd_prefix . '/FieldsByClass/'];
     }
     
     
     if (null != $append) {
     
       foreach ($wd as $el) {
    
         foreach ($append as $suffix) {
         
           $nwd[] = $el . $suffix;
         }
      }
     }
     
     return $nwd;
   }
   public function set_wd($path) {

     $this->wd_prefix = $path;
     $this->wd = $this->refresh_wd($path, $this->controller);
     
  }
  
   public function get_secure_execution() {
    
    return $this->secure_execution;
   }
   
   private function __get_next_class($_wd)
   {
      while (0 < count($this->templates) + 1 + $this->tmpl_pos) {

       --$this->tmpl_pos;
       
       $wd_ = $_wd;
       
       if (is_string($wd_)) {
       
         $wd_ = [$wd_];
      }
       
       foreach ($wd_ as $wd) {
         
         if (file_exists($wd . '.template')) {
           
            return $wd . '.template';
         }
    if (file_exists($wd . '/' . $this->templates[count($this->templates)+1+$this->tmpl_pos] . '.template')) {

      return [$wd, $this->templates[count($this->templates)+1+$this->tmpl_pos]];
      }
       }
     }
     
     return NULL;
   }
   
   private function is_template_file($path) {
   
     if (file_exists($path) && is_file($path)) {
     
       return true;
    }
    return false;
  }
  
  private function process_foreach_block($block, $field_name)
  {
    $name = $field_name;
    
    if (isset($this->fields[$name])) $element = $this->fields[$name];
    if (null != $this->controller && !empty($this->controller->get_field($name))) $element = $this->controller->get_field($name);
    
    
    if (null == $element) return null;
    
    if (is_string($element)) return $element;
    
    $scope = new \Basic_Controller($this->controller);
    $this->controller = $scope;
    
    $template = new TemplateView();
    
    $template->setController($this);
    
    $template->load_code_from_string($block);
    $template->set_secure_execution($this->get_secure_execution());
    $template->recurse = true;
    
    
    if (is_a($element, '\\Viewable\\ContainerView', false)) {
      
      $element->set_output_obj($template);
      
      return $element->render();
    }
    
    return null;
  }
   
   private function replace_part($matches, &$current_block_level, &$block_level_to_process)
   {
     
     $name = preg_replace('/\:\:/', ':', $matches[0]);
     $name = substr($name, strlen(':server:(') , strlen($name) -2 - strlen(':server:(') );
     
     
     if (0 === strpos($name,'block ')) {
       
       if ($current_block_level > $block_level_to_process) {
         
         ++$current_block_level;
         return null;
       }
       
       $name = substr($name, strlen('block '));
       $test = trim($name);
       if (0 === strpos($test,'if:')) {
         
         $name = substr($name, strpos($name, 'if:') + strlen('if:'));
         if ($this->secure_execution) {
            throw new Error();
         }
         
         $inString = ini_set('log_errors', false);
         $token = ini_set('display_errors', true);
         ob_start();
         $result = eval('return ' . $name);

         if (true == $result) {
           
           ++$block_level_to_process;
         }
         ob_get_clean();
         ob_end_clean();
         ini_set('log_errors', $inString);
         ini_set('display_errors', $token);
       }
       else if (0 === strpos($test,'foreach:')) {
        
         //return new TemplateView_statement('foreach');
         
         ++$block_level_to_process;
       }
       
       ++$current_block_level;
       return null;
     }
     else if (0 === strpos($name,'block-end ')) {
       
       $name = substr($name, strlen('block-end '));
       $test = trim($name);
       
       --$current_block_level;
       
       if ($current_block_level < $block_level_to_process) {
         
         --$block_level_to_process;
       }
       return null;
     }
     if ($current_block_level > $block_level_to_process) {
       
       return null;
     }
     
     if (0 === strpos($name,'field:')) {
       
       $name = substr($name, strlen('field:'));
       $element = null;
       if (isset($this->fields[$name])) $element = $this->fields[$name];
       if (null != $this->controller && !empty($this->controller->get_field($name))) $element = $this->controller->get_field($name);
       
       if (null == $element) return null;
       
       if (is_string($element)) return $element;
       
       if ("Me" === $name && false == $this->recurse) {
       $tmpl = new TemplateView();
       $tmpl->setController($element);
       $tmpl->set_secure_execution($this->get_secure_execution());
       
       $tmpl->set_field('Me', $element);
       
       $wd_ = [];
       foreach ($this->wd as $path) {
         
         $wd_[] = $path . '/FieldsByName/' . $name . '.template';
       }
       $tmpl->set_wd($wd_);
       
       $result = $tmpl->render();
       if (null !== $result) {
         
         return $result;
       }
       
       $tmpl = new TemplateView();
       $tmpl->setController($element);
       $tmpl->set_secure_execution($this->get_secure_execution());
       $tmpl->set_field('Me', $element);
       $wd = [];
       foreach ($this->wd as $wd_) {
         
         $wd[] = $wd_ . '/FieldsByClass/';
       }
       $tmpl->set_wd($wd);
       
       $result = $tmpl->render();
       
       if (null !== $result) {
         
         return $result;
       }
       
       }
       
       if (is_a($element, '\\Viewable\\ContainerView', false)) {
         
         $tmpl = new TemplateView();
         $tmpl->setController($element);
         $tmpl->set_secure_execution($this->get_secure_execution());
         
         $wd_ = [];
         
         if (is_string($this->wd)) {
           $path = $this->wd;
           
           $wd_[] = $path . '/FieldsByName/' . $name . '/Elements.template';
           $wd_[] = $path . '/FieldsByClass/' . $name . '/Elements.template';
         } else
           foreach ($this->wd as $path) {
             
             $wd_[] = $path . '/FieldsByName/' . $name . '/Elements.template';
             $wd_[] = $path . '/FieldsByClass/' . $name . '/Elements.template';
           }
           
           $element->set_output_obj($tmpl);
         
         $tmpl->set_wd($wd_); 
         return $element->render();
       }
       if (is_subclass_of($element, Viewable, false)) {
         
         return $element->render();
       }
       return '';
     }
     else if (0 === strpos($name,'php:')) {
       if ($this->secure_execution) {
         
         throw new Error();
       }
       $name = substr($name, strlen('php:'));
       $inString = ini_set('log_errors', false);
       $token = ini_set('display_errors', true);
       ob_start();
       
       $result = eval('return ' . $name);
       ob_get_clean();
       ob_end_clean();
       ini_set('log_errors', $inString);
       ini_set('display_errors', $token);
       
       return $result;
     }
     // Call function of model or controller - the same as in stripe
     else if (0 === strpos($name,'function:')) {
       
       return '';
     }
     // same as above, but call an action of another controller or call external page
     else if (0 === strpos($name,'slot:')) {
       
       return '';
     }
     else if (0 === strpos($name,'class:')) {
       
       $name = substr($name, strlen('class:'),  strlen($name) -2);
       
       if (!file_exist(dirname($file_name) . '/' . $name)) {
         
         return '';
       }
       return $this->__render_helper(dirname($file_name) . '/' . $name);
     }
     else if (0 === strpos($name,'class-path:')) {
       
       $name = substr($name, strlen('class-path:'));
       $ok = false;
       foreach ($this->wd as $wd) {
         if (file_exists($wd . '/' . $name . '/Controllers/')) {
           
           $rpath = $wd. '/' . $name;
           $tmp_path = $this->__get_next_class([$wd. '/' . $name . '/Controllers/']);
           $ok = true;
           break;
         }
       }
       
       if (false == $ok) {
         
         return '';
       }
       
       if (null == $tmp_path) return '';
       $tmpl = new TemplateView();
       $tmpl->setController($tmp_path[1]);
       $tmpl->set_secure_execution($this->get_secure_execution());
       $tmpl->set_wd([$rpath . '/']);
       
       return $tmpl->render();
     }
   }
   
   private $code = null;
   
   public function load_code_from_string($string)
   {
     if (null != $this->code) {
       
       return;
     }
     $this->code = $string;
   }
   
   public function load_code_from_file($file_names)
   {
     
     if (null != $this->code) {
     
       return true;
    }
     
     if (empty($file_names) || empty($file_names[0])) {
       
       return false;
     }
     
     $ok = false;
     $file_name = '';
     while (false == $ok) {
       
       if (is_array($file_names))
         foreach ($file_names as $file_name_) {
           
           if ($this->is_template_file($file_name_)) {
             $ok = true;
             
             $file_name = $file_name_;
             break;
           }
         }
         else if ($this->is_template_file($file_names)) {
           
           $file_name = $file_names;
           $ok = true;
         }
         if (true == $ok) {
           
           
           break;
         }
         
         $class = $this->__get_next_class($this->wd);
         if (null == $class) return null;
         
         if (is_string($class)) {
           
           $file_name = $class;
         }
         else {
           $file_name = $class[0] . '/' . $class[1] . '.template';
           
         }
         if ($this->is_template_file($file_name)) {
           
           $ok = true;
         }
     }
     
     
     
     
     $this->code = file_get_contents($file_name);
     return true;
   }
   
   private function __render_helper()
   {
     
     $line = $this->code;
     
     // Checking syntax is correct
     $block_level_to_process = 0;
     $block_stack = [];
     $statements = [];
     preg_match_all('/\:server\:\((block|block-end) (if|foreach)\s*(\:\:)*\:[^\:]+(\:\:)*\:\)/', $line, $statements,PREG_PATTERN_ORDER);
 
 if (!empty($statements) && !empty($statements[0])) {
       $statements = $statements[0];
     foreach ($statements as $match) {
     
       $name = substr($match, strlen(':server:('), strpos(substr($match,  strlen(':server:(')), ':') );
       $name = trim($name);
       if (0 === strpos($name,'block-end ')) {
       
         $name = substr($name, strlen('block-end '));
         if ($block_stack[count($block_stack)-1] != $name) {
         
           die('Syntax error 1: Unclosed block');
        }
         array_pop($block_stack);
      }
       else if (0 === strpos($name,'block ')) {
         
         $name = substr($name, strlen('block '));
         if (!in_array($name, ['foreach', 'if'])) {
           
           die('Syntax error: Unrecognized statement in template');
         }
         
         $block_stack[] = $name;
       }
       else {
         die('Syntax error 2');
       }
     }
     
     if (0 < count($block_stack)) {
     
       die('Syntax error 3');
     }
     }
     
     $current_block_level = 0;
    
     $output = '';
     $prev = true;
     while (preg_match('/\:server\:\([^\:]+(\:\:)*\:[^\:]+(\:\:)*\:\)/', $line, $matches, PREG_OFFSET_CAPTURE)) {
     
       $result = $this->replace_part($matches[0], $current_block_level, $block_level_to_process);
      
      if (0 === strpos($matches[0][0], ':server:(block foreach:') && $current_block_level <= $block_level_to_process) {
      
        $name = substr($matches[0][0], strlen(':server:(block foreach:'), strlen($matches[0][0]) - 2 - strlen(':server:(block foreach:'));
      
        $block = '';
        $block_lines=[];
        $line2 = substr($line, $matches[0][1] + strlen($matches[0][0]));
        ++$current_block_level;
        while (preg_match('/(\:server\:\(block foreach:(\:\:)*[^\:]+(\:\:)*\:\)|(\:server\:\(block-end foreach:(\:\:)*[^\:]+(\:\:)*\:\)))/', substr($line2, strlen(':server:(block foreach:')), $block_lines, PREG_OFFSET_CAPTURE)) {
          
          
          $block = $block . substr($line2, 0, $matches[0][1]);
          $line2 = substr($line2, $block_lines[0][1] + strlen($block_lines[0][0]));
          
          if (0 === strpos($block_lines[0][0], ':server:(block foreach:')) {
          
           
            ++$current_block_level;
          }
          else if (0 === strpos($block_lines[0][0], ':server:(block-end foreach:')) {
          
            --$current_block_level;
            
            if ($current_block_level <= $block_level_to_process ) {
            
              // Process block
              $result = $this->process_foreach_block($block, $name);
              
              break;
            }
          }
        }
        
      }
       if (true == $prev) {
         $output = $output . substr($line, 0, $matches[0][1]);
         
       } 
       
       if ($current_block_level <= $block_level_to_process ) {
         

         $prev = true;
        }
       else {
       
         $prev = false;
      }
      $output = $output . $result;
      
       
       $line = substr($line, $matches[0][1] + strlen($matches[0][0]));
     
     }
       
    $output = $output . $line;
    return $output;
   }
   
   public function render($data=null) {
   
     
     if (null != $data) {
       
       $this->fields = $data;
     }
     if (null == $this->code) {
     
      if (false == $this->load_code_from_file($this->wd)) {
      
        return null;
      }
    }
     
     return  $this->__render_helper($this->wd);

   }
   
   
 };
