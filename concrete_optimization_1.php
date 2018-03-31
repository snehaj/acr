<?php

class concrete_optimization_1 extends base_optimization
{
  /**
   * concrete_optimization_1 constructor.
   */
  public function __construct()
  {
    parent::__construct();
    $this->concrete_network_setting = array('runtime' => 'lastrun');
  }

  public function getRow()
  {
    $this->row = $this->db->queryAll('SELECT * FROM campaigns WHERE active = 1');
  }
}
