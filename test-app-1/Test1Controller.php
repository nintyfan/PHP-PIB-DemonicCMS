<?php


class Test1Controller extends Test {
  
  public function index() {
  
    return '1-' . parent::index();
  }
  
};
