<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Resource extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	/*if(version_compare(phpversion(),'5.6.0','>=') === true)//new feature for variable  function parameters in 5.6.0 above
	{
		public function javascript(...$params)
		{
			if(count($params) == 0)
			{
				show_404();
				return
			}
			if(is_numeric($params[count($params)-1]))
			{
				array_pop($params);
			}
			$name = implode('/',$params);
			$this->load->view('javascript',array('scriptname' => $name , 'base_path' => APPPATH));
		}
		
		public function css(...$params)
		{
			if(count($params) == 0)
			{
				show_404();
				return;
			}
			if(is_numeric($params[count($params)-1]))
			{
				array_pop($params);
			}
			$name = implode('/',$params);
			$this->load->view('css',array('cssname' => $name , 'base_path' => APPPATH));
		}
	}
	else
	{*/
		public function javascript()
		{
			$arr = func_get_args();
			
			if(count($arr) == 0)
			{
				show_404();
				return;
			}
			if(is_numeric($arr[sizeof($arr)-1]))
			{
				array_pop($arr);
			}
			$name = implode('/',$arr);
			$this->load->view('javascript',array('scriptname' => $name , 'base_path' => APPPATH));
		}
		
		public function css()
		{
			$arr = func_get_args();
			
			if(sizeof($arr) == 0)
			{
				show_404();
				return;
			}
			if(is_numeric($arr[sizeof($arr)-1]))
			{
				array_pop($arr);
			}
			$name = implode('/',$arr);
			
			$this->load->view('css',array('cssname' => $name , 'base_path' => APPPATH));
		}
		
		public function png()
		{
			$arr = func_get_args();
			
			if(sizeof($arr) == 0)
			{
				show_404();
				return;
			}
			if(is_numeric($arr[sizeof($arr)-1]))
			{
				array_pop($arr);
			}
			$name = implode('/',$arr);
			
			$this->load->view('png',array('pngname' => $name , 'base_path' => APPPATH));
		}
	//}
}