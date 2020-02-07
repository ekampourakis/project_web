<?php


class Charts extends FrontEnd
{

  function __construct()
  {
    parent:: __construct();
    $this->load->model('charts_model');
  }
function index1()
{
    $data['connections']= $this->charts_model->userconnections();
    $this->view('projectistos/admin_graph',$data);
function index2()
  {
    $data['month']= $this->charts_model->timemonth();
    $this->view('projectistos/admin_graph',$data);
function index3()
    {
    $data['day']= $this->charts_model->timedays();
    $this->view('projectistos/admin_graph',$data);
function index4()
    {
    $data['hour']= $this->charts_model->timehour();
    $this->view('projectistos/admin_graph',$data);
function index5()
    {
    $data['year']= $this->charts_model->timeyear();
    $this->view('projectistos/admin_graph',$data);

}

}
