<?php
namespace Scratch;

class Templater
{
	private $errors = array();
	
	function add_error($prop, $error)
	{
		$this->errors[$prop] = $error;
	}
	
	function error_for($prop)
	{
		if (array_key_exists($prop, $this->errors))
		{
			return $this->errors[$prop];
		}
		return "";
	}
	
	function render_template($template_file, $vars = array())
  	{
    	if(file_exists($template_file))
    	{
      		$vars['ctx'] = $this;
			ob_start();
        	extract($vars);
        	include($template_file);
      		return ob_get_clean();
    	}
		else
		{
      		throw new Scratch\MissingTemplateException("Template: {$template_file} could not be found!");
		}
      }
      
    function render_text($text, $vars = array())
  	{
      		$vars['ctx'] = $this;
			ob_start();
        	extract($vars);
        	echo($text);
      		return ob_get_clean();
  	}
}

class MissingTemplateException extends \Exception
{
	private $pwd;
	public function __construct($message = null, $code = 0, Exception $previous = null)
	{
		$pwd = getcwd();
		parent::__construct($message, $code, $previous);
	}

	final public function getPwd()
	{
		return $pwd;
	}
}
?>