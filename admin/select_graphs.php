<?php
  if (!defined("BASEPATH")) {
    exit('Not allowed')
  }

  class charts_model extends CI_model
  {
//kleidi gia user
  function userconnections()
    {
      return $this->db->query('
      SELECT data.activity_type ,COUNT(data.foreignkey) FROM users LEFT JOIN data ON users.userid = data.foreignkey WHERE data.foreignkey = users.userid')->result_array();
    }
  }
  function timemonth()
  {
    return $this->db->query('
    SELECT COUNT(userid), timestamp FROM data WHERE ####timestamp')->result_array();

  }
  function timeday()
  {
    return $this->db->query('
    SELECT COUNT(userid), timestamp FROM data WHERE ####timestamp')->result_array();

  }
  function timehour()
  {
    return $this->db->query('
    SELECT COUNT(userid), timestamp FROM data WHERE ####timestamp')->result_array();

  }
  function timeyear()
  {
    return $this->db->query('
    SELECT COUNT(userid), timestamp FROM data WHERE ####timestamp')->result_array();

  }



 ?>
