<?php

class concrete_optimization_2 extends base_optimization
{
  public function getRow()
  {
    $this->row = $this->db->queryAll('SELECT * FROM campaigns_archived');
  }

  /**
   * concrete_optimization_1 constructor.
   */
  public function __construct()
  {
    parent::__construct();
    $this->concrete_network_setting = array('runtime' => 'website');
  }
}
