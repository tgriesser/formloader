<?php

namespace Formloader;

/**
 * Part of the Formloader package for Fuel
 *
 * @package   Formloader
 * @version   1.1
 * @author    Tim Griesser
 * @license   MIT License
 * @copyright 2012 Tim Griesser
 * @link      http://formloader.tgriesser.com
 */
class Formloader_Bridge extends \Loopforge
{
	/**
	 * Ensures that each item is only filtered once
	 */
	protected static $filtered = array();

	/**
	 * Ensures that each item is given a unique ID... at least within the current form
	 */
	protected static $id_stack = array();
	
	/**
	 * Validations are loaded into this static variable from inheriting classes,
	 * specifically the Formloader_Fields class as we loop through each...
	 * thus creating a single validation object for a form
	 */
	protected static $validations = array();

	/**
	 * Selects are stored here and are used to render the correct selected value in a mustache form
	 */	
	protected static $selects = array();	
	protected static $checks  = array();
	protected static $defaults = array();
	
	// Used to populate a dropdown at runtime rather than with a set default list of params
	protected static $option_calls = array();
	
	/**
	 * Process items based on the previously called item...
	 * @var array
	 */
 	public static function _process_items($f, $direct = false)
	{
		// Data we're returning to the calling tree
		$return = array();
		
		// Find the type of class {form|fieldset|field|action}
		$a = explode('_', get_called_class());
		$type = strtolower($a[1]);
				
		// If the type's array key isn't empty, run through each
		if ( ! empty($f[$type]))
		{			
			foreach ($f[$type] as $field)
			{
				// This is already in the 'fs-group-name' format
				if (count(explode('-', $field)) === 3)
				{
					$id = $field;
				}
				else
				{
					if (substr($f['_id'], 0, 2) === 'fs')
					{					
						if ( ! is_string($field))
						{
							throw new FormloaderException('Field ' . $field . ' must be a string');
						}
						$item = explode('.', $field);
						$id   = 'fs-';
						switch (count($item))
						{
						 	case 1:
								$id .= $f['group'] . '-' . $field;
							break;
						 	case 2:
								$id .= str_replace('.', '-', $field);
							break;
							default:
								throw new FormloaderException("$item is improperly formatted");
							break;
						}
					}
					else
					{
						throw new FormloaderException("{$f['_id']} is improperly formatted");
					}
				}
				
				// Grab the specific item with the formloader api
				$data = Formloader_Fs::fetch_item($id, $type);
				
				// Allows for a consistent pattern of nested name=""
				if ($f['object_type'] === 'fields')
				{
					$data['parent'] = $f['name_with_dots'];
				}
								
				// Run through the array in \Loopforge
				$result = static::process_arrays($data);
				
				// Specific post processing rules for fields...
				if ($type === 'fields') 
				{
					if ( ! empty($result['validations']))
					{
						static::$validations[] = array($result['name_with_dots'], $result['label'], $result['validations']);
					}
					if ($result['attributes']['type'] === 'dropdown' and empty($result['option_call']))
					{
						static::$selects[] = $result['name_with_dots'];
					}
					if (in_array($result['attributes']['type'], array('checkbox', 'radios')))
					{
						static::$checks[] = $result['name_with_dots'];
					}
					if ( ! empty($result['option_call']))
					{
						static::$option_calls[$result['name_with_dots']] = $result['option_call'];
					}
					if ( ! empty($result['default']))
					{
						static::$defaults[$result['name_with_dots']] = $result['default'];
					}
				}
				
				$return[] = $result;
			}
		}
		return $return;
	}

	/**
	 * Filters the attributes going into the form items...
	 * @param Reference to the current form object
	 */
	public static function filter_attributes(&$f)
	{
		// Filters all data- attributes
		if ( ! empty($f['attributes']['data']))
		{
			foreach ($f['attributes']['data'] as $k => $v)
			{
				$f['attributes']["data-$k"] = $v;
			}
			$f['attributes']['data'] = '';
		}

		if ( ! in_array($f['_id'], static::$filtered))
		{
			array_push(static::$filtered, $f['_id']);
			
			switch ($f['object_type'])
			{
				case "fields":
					if ($f['attributes']['type'] !== 'radios' and $f['attributes']['type'] !== 'dropdown')
					{
						if ( ! empty($f['attributes']['value']))
						{
							$f['attributes']['value'] = '{%^'.($f['name_with_dots']).'%}'
								. $f['attributes']['value']
								. '{%/'.$f['name_with_dots'].'%}';
						}
						
						$f['attributes']['value'] .= '{%' . $f['name_with_dots'] . '%}';
					}
				break;
				case "actions":
					// Puts an attribute value in the action item
					if (empty($f['attributes']['value']))
					{
						$f['attributes']['value'] = ucwords(str_replace('_', ' ', $f['name']));
					}
					// Ensures that every button has a "btn" class
					$f['attributes']['class'] .= ( ! empty($f['attributes']['class']) ? ' btn' : 'btn');
				break;
			}
		}

		// Ensures we still have the data- attributes we need for the form
		$data = \Arr::filter_prefixed($f['attributes'], "data-", false);

		// Send back the filtered items plus the data-attributes
		return array_merge(array_filter($f['attributes']), $data);
	}

	/**
	 * Return the static validations, and reset to a new array in case
	 * we're processing multiple forms
	 * @return array
	 */
	public static function get($type)
	{
		if (isset(static::$$type))
		{
			$val = static::$$type;
			static::$$type = array();
			return $val;
		}
		throw new FormloaderException("Unspecified 'get()' for $type");
	}

	/**
	 * Ensures that a unique id is generated for the current item...
	 * @param Reference to the current object
	 */
	public static function unique_id($f)
	{
		$id = $f['object_type'] . '_' . strtolower($f['name']);
		// Add it to the array if it's not there yet...
		if ( ! isset(static::$id_stack[$f['name']]))
		{
			static::$id_stack[$f['name']] = 1;
			return $id;
 		}
		else
		{
			$current = static::$id_stack[$f['name']];
			$current += 1;
			static::$id_stack[$f['name']] = $current;
			return $id . '_' . $current;
		}
	}
	
	/**
	 * Preps the validation...
	 * @param Reference to the current form object
	 * @return string  validations
	 */
	public static function prep_validation()
	{
		$val = self::get('validations');
		if ( ! empty($val))
		{
			// Quick find/replace for 'fuel.' prefixed...
			$val = json_decode(str_replace('fuel.', '', json_encode($val)));

			foreach ($val as &$arr)
			{
				$arr[1] = substr($arr[1], -1) === ':' ? substr($arr[1], 0, -1) : $arr[1];
				$arr[2] = implode('|', $arr[2]);
			}
			unset($arr);
		}
		return $val;
	}

	/**
	 * Gets the appropriate template directory based on the naming
	 * and availability of templates in the directories
	 * @param array    a loopforge array from the Formloader classes
	 * @return string   the directory we are using
	 */
	public static function template_directory($f)
	{
		$path        = \Config::get('formloader.output_path').'templates/';
		$group_dir   = $path.$f['group'].DS.$f['object_type'];
		$regular_dir = $path.\Config::get('formloader.template_dir').DS.$f['object_type'];
		return (is_dir($group_dir) and file_exists($group_dir.DS.$f['template'])) ? $group_dir : $regular_dir;
	}	

}