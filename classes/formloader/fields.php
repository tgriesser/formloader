<?php

namespace Formloader;

/**
 * Part of the Formloader package for Fuel
 *
 * @package   Formloader
 * @version   1.0
 * @author    Tim Griesser
 * @license   MIT License
 * @copyright 2012 Tim Griesser
 * @link      http://formloader.tgriesser.com
 */
class Formloader_Fields extends Formloader_Bridge
{
	/**
	 * The defaults array set in the _init()
	 * @var array
	 */
	public static $_defaults;

	/**
	 * Called by fuelPHP when the class is first initialized
	 * ...almost like a static constructor
	 */
	public static function _init()
	{
		/**
		 * The Loopforge array for composing a "Field"
		 * @var array
		 */
		static::$_defaults = array(

			/**
			 * Determines the type of Formloader object we're inheriting from
			 * in the Formloader_Bridge class
			 */
			'object_type' => 'fields',
			
			/**
			 * Required name of the group (used to namespace the form items)
			 * @var String
			 * @throws \FormloaderException
			 */			
			'group'  => function()
			{
				throw new \FormloaderException('A group is needed in order to deal with this properly');
			},
			
			/**
			 * Required name of the action
			 * @var String
			 * @throws \FormloaderException
			 */
			'name'   => function()
			{
				throw new \FormloaderException('A name is needed in order to deal with this properly');
			},

			/**
			 * The _id of the form item... prefixed with "fs-" so that we can easily add
			 * mongoDB form items and be able to differentiate between the two
			 * @param  array   $f - current field array
			 * @return string  the _id of the field
			 */
			'_id'        => function($f)
			{
				return 'fs-'. $f['group'] . '-'.$f['name'];
			},

			/**
			 * A dot-separated list of parent fields, if this is set then we add 
			 * a nest level to the name=""
			 * @var string
			 */
			'parent'     => '', // If this has a parent, we array nest the name=""

			/**
			 * Set to true to remove the "name" attribute of the field for some reason
			 * @var bool
			 */
			'hide_name'  => false,
						
			/**
			 * Options used for the "option" tags on a select element
			 * key => value   ->   Display Label  =>  value=""
			 * @var array
			 */
			'options' => array(),

			/**
			 * If this is a dropdown/select and this is set, we will expect a 
			 * query to provide the array of dropdown objects in the format
			 * ($option, $value, $selected = false)
			 * @var string
			 */
			'option_static_call' => '',

			/**
			 * Help that is shown inline with the forms
			 * @var string
			 */
			'help_inline'    => '',
			
			/**
			 * A list of validations for the array
			 * @var array
			 */
			'validations' => array(),
			
			/**
			 * The attributes array is passed to array_filter 
			 * and used to set the attributes
			 * @var array
			 */
			'attributes' => array(

				/**
				 * Default input type is 'value'
				 * @var string
				 */
				'type'  => 'text',
				
				/**
				 * By default, the id will be the lowercased name of the form...
			   * @param  array   $f - current field array
			   * @return string
				 */
				'id'    => function($f)
				{
					return Formloader_Bridge::unique_id($f);
				},

				/**
				 * Initial processing for the name attribute... by default this will be the name
				 * of the form...
				 * @param  array   $f - current field array
				 * @return string
				 */
				'name'  => function($f)
				{
					return $f['hide_name'] !== true ? $f['name'] : '';
				},
				
				/**
				 * Field's class attribute
				 * @var string 
				 */				
				'class' => '',

				/**
				 * Used rarely to set inline styles on the field item (e.g. textarea sizing)
				 * @var string
				 */
				'style' => '',

				/**
				 * Also used rarely to determine a default value for a field, good
				 * for setting checked="checked" for a checkbox...
				 * @var string
				 */
				'value' => '',

				/**
				 * Array of key => value pairs, each key will be filtered below,
				 * prefixed with 'data-' and added to the attributes...
				 * @var array
				 */
				'data'  => array()
			),
			
			/**
			 * Filters all data- attributes
			 * @param  array - reference to the field object
			 * @return string  __remove__
			 */
			'_data' => function(&$f)
			{
				return Formloader_Bridge::data_filter($f);
			},
			
			/**
			 * The 'name_with_dots' is a dot-separated field name (for nested fields)
			 * this is important because it allows us to easily set values at the correct depth
			 * in the mustache
			 * @param  array   $f - current field array
			 * @return string
			 */
			'name_with_dots'   => function($f)
			{
				return (empty($f['parent']) ? '' : $f['parent'] . '.') . $f['attributes']['name'];
			},
			
			/**
			 * Using the "name with dots"
			 * @param array  - reference to the field object
			 */
			'_name' => function(&$f)
			{				
				$exp = explode('.', $f['name_with_dots']);
				$field = array_shift($exp);
				$f['attributes']['name'] = $field . ( ! empty($exp) ? '['.implode('][', $exp) . ']' : '');
			},

			/**
			 * Set the appropriate default value for the field
			 * @param array  - reference to the field object
			 */
			'_value' => function(&$f)
			{
				$f['attributes']['value'] = ( ! empty($f['attributes']['value'])
					? '{%^'.($f['name_with_dots']).'%}'.$f['attributes']['value'].'{%/'.$f['name_with_dots'].'%}'
						: '') . '{%' . $f['name_with_dots'] . '%}';
				return '__remove__';
			},
			
			/**
			 * Array of the fields that are children of this field...
			 * @var array
			 */
			'fields'     => array(),

			/**
			 * Processes each of the fields and sets the result
			 * @param array  - reference to the field object
			 * @return string  __remove__
			 */
			'_fields'  => function(&$f)
			{
				$f['fields'] = call_user_func("\\Formloader_Fields::_process_items", $f);
				return '__remove__';
			},

			/**
			 * If there are fields that are children of this field
			 * the field type becomes "nested" by default
			 * @param array  - reference to the field object
			 * @return string  __remove__
			 */
			'_type'       => function(&$f)
			{
				$f['attributes']['type'] = ! empty($f['fields']) ? 'nested' : $f['attributes']['type'];
				return '__remove__';
			},
				
			/**
			 * The label for the field... by default this is the 
			 * uppercase of the field's name
			 * @param  array  - current field object
			 * @return string
			 */
			'label'      => function($f)
			{
				return ucwords(str_replace('_', ' ', $f['name'])) . ':';
			},

			/**
			 * The html for the label... using the "label" above
			 * @param  array - current field object
			 * @return string - current form label
			 */
			'label_html' => function($f)
			{
				return \Form::label(array(
					'label' => $f['label'],
					'for'   => $f['attributes']['id'],
					'class' => 'control-label'
				));
			},
			
			/**
			 * If we have a specific input html template, that is set here, while
			 * still being wrapped by the regular template template...
			 * @var string
			 */
			'input_template' => '',
			
			/**
			 * Determines the input template for the form...
			 * @param  array - reference to the current field object
			 * @return string  __remove__
			 */
			'_input_template' => function(&$f)
			{
				$f['input_template'] = Formloader_Fields::template($f);
				return '__remove__';
			},
			
			/**
			 * The field's html, wrapped in the field's template
			 * @param array  - reference to the current field object
			 * @return string __remove__
			 */ 
			'field_html'      => function(&$f)
			{
				$field = Formloader_Fields::forge_field($f);
				return ( ! empty($f['input_template']) ? Formloader_Template::forge($f['input_template'], $f, false) : $field);
			},
	
			/** 
			 * Default template for the action
			 * @return string
			 */
			'template'       => function($f)
			{
				return $f['attributes']['type'] === 'nested' ? 'nested.mustache' : 'default.mustache';
			},

			/**
			 * Resolves the template directory for the field
			 * @param  array $f - current action array
			 * @return string
			 */
			'template_dir'   => function($f)
			{
				return Formloader_Bridge::template_directory($f);
			},

			/**
			 * Path to the template relative to the "modules/formloader/templates" directory
			 * @param  array $f - current action field
			 * @return string
			 */
			'template_path'  => function($f)
			{
				return $f['template_dir'].DS.$f['template'];
			},
			
			/**
			 * Output HTML for the field
			 * @param array
			 * @return string  rendered \View object
			 */
			'template_html' => function($f)
			{
				return Formloader_Template::forge($f['template_path'], $f, false)->render();
			}
		);
	}

	/**
	 * Routes the field creation based on the field type
	 * @param  Array   the entire processed field object
	 * @return String  output mustache HTML for the dropdown
	 */	
	public static function forge_field(&$f)
	{
		switch ($f['attributes']['type'])
		{
			case "text":
			case "password":
				return \Form::input(array_filter($f['attributes']));
			break;
			case "textarea":
				return \Form::textarea(array_filter($f['attributes']));
			break;
			case "button":
				return \Form::button(array_filter($f['attributes']));
			break;
			case "dropdown":
				return self::select($f);
			break;
			case "checkbox":
				return self::check($f);
			case "file":
				return \Form::file(array_filter($f['attributes']));
			break;
			case "hidden":
				return \Form::hidden($f['attributes']['name'], $f['value']) . PHP_EOL;
			break;
			case "checkboxes":
			case "radios":
				$multi = self::multi($f);
				# Don't actually process the form here, this will be taken care of in the template
				if ( ! empty($multi))
				{
					foreach ($multi as $k => $item)
					{
						$f['options'][] = \Loopforge::process_arrays(self::$_defaults, array(
							'parent' => $f['name_with_dots'],
							'group'  => $f['group'],
							'name'   => $k,
							'label'  => ucfirst($k) . ' Group',
							'attributes' => array(
								'type' => \Inflector::singularize($f['attributes']['type'])
							)
						));
					}
				}
			break;
			case "uneditable":
				return \Form::hidden($f['attributes']['name'], $f['value']) . PHP_EOL;
			break;
		}
	}

	/**
	 * Generates a select field for Formbuilder
	 * @param  Array   the entire processed field object
	 * @return String  output mustache HTML for the dropdown
	 */	
	private static function select($f)
	{
		$options = array();
		$content = '';

		if ( ! empty($f['options']))
		{
			foreach ($f['options'] as $k => $v)
			{
				// We need a key (field name) in order to be able to put this together
				if ( ! empty($k))
				{
					$attr = ! empty($v) ? array(
						'{%#__selected.'.$f['name_with_dots'].'.'.$v.'%}selected' => 'selected" {%/__selected.'.$f['name_with_dots'].'.'.$v.'%}value="'.$v.'',
					) : array('value' => '');
					// This is pretty messy, but combined with Formloader::render, it gives the best workaround for now for the mustache...
					$content .= "\t\t" . html_tag('option', $attr, $k) . PHP_EOL;
				}
			}
		}
		// We need to maintain the empty value as an empty string...
		$val = $f['attributes']['value'];
		return html_tag('select', array_merge(array_filter($f['attributes']), array('value' => $val)), $content);
	}

	/**
	 * Determine how this multi-item is populated...
	 */
	private static function multi($f)
	{
		return ! empty($f['option_static_call']) ? '' : $f['options'];
	}
	
	/**
	 * Generates a checkbox for Formbuilder
	 * @param  Array   the entire processed field object
	 * @return String  output mustache HTML for the checkbox
	 */
	private static function check($f)
	{
		$attr = array_merge($f['attributes'], array(
			'{%#__checked.'.$f['name_with_dots'].'%}checked' => 'checked"{%/__checked.'.$f['name_with_dots'].'%} type="checkbox'
		));
		return \Form::input($f['name'], "yes", array_filter($attr));
	}
	
	/**
	 * Determines which template we are using by default
	 * based on the "type" attribute of the field...
	 * @param array      a loopforge array from the Formloader classes
	 * @return string    the template name we're using
	 */
	public static function template($f)
	{
		if (strpos($f['input_template'], DS) !== false)
		{
			return $f['input_template'];
		}
		else
		{
			// We can just add input.mustache as a custom field and have it relative to the group it's in...
			$input = ! empty($f['input_template']) ? str_replace('.mustache', '', $f['input_template']) : $f['attributes']['type'];
			
			$path = \Config::get('output_path').DS.'templates'.DS;

			// If the template_dir isn't a closure, we have a definite value for the template_dir, use that
			if (($f['template_dir'] instanceof \Closure) === false)
			{
				$dirs = array($f['template_dir']);
			}
			else
			{
				$dirs = array(
					'group_dir'   => $path.$f['group'].DS.'input',
					'regular_dir' => $path.\Config::get('formloader.template_dir').DS.'input',
				);		
			}
		
			// See if there is a field that matches the function name in the callable directories, 
			// otherwise use the default...
			foreach ($dirs as $dir)
			{
				$match = false;
				if (file_exists($dir .DS. $input . '.mustache'))
				{
					$match = $dir . DS . $input . '.mustache';
					break;
				}
			}

			return ($match !== false ? $match : '');
		}
	}
}