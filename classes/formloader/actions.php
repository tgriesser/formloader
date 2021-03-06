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
class Formloader_Actions extends Formloader_Bridge
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
		 * The Loopforge array for composing an "Action" (a button to perform an action on the form)
		 * @var array
		 */
		static::$_defaults = array(

			/**
			 * Determines the type of Formloader object we're inheriting from
			 * in the Formloader_Bridge class
			 */
			'object_type' => 'actions',

			/**
			 * Required name of the group (used to namespace the form items)
			 * @var string
			 * @throws FormloaderException
			 */
			'group'  => function()
			{
				throw new FormloaderException('A group is needed in order to deal with this properly');
			},

			/**
			 * Required name of the action
			 * @var string
			 * @throws FormloaderException
			 */
			'name'   => function()
			{
				throw new FormloaderException('A name is needed in order to deal with this properly');
			},
			
			/**
			 * The _id of the form item... prefixed with "fs-" so that we can easily add
			 * mongoDB form items and be able to differentiate between the two
			 * @param array   $f - current field array
			 * @return string  the _id of the field
			 */			
			'_id'        => function($f)
			{
				return 'fs-'.$f['group'].'-'.$f['name'];
			},

			/**
			 * The attributes array is passed to array_filter 
			 * and used to set the attributes
			 * @var array
			 */
			'attributes' => array(

				/**
				 * Id tag for the action
			 	 * @param array  - reference to the current action object
				 * @return string name
				 */
				'id'    => function($f)
				{
					return Formloader_Bridge::unique_id($f);
				},
				
				/**
				 * Button "type"
				 * @param array $f - current action array
				 * @return string
				 */
				'type'   => function ($f)
				{
					return $f['name'] === 'submit' ? 'submit' : 'button';
				},

				/**
				 * Lowercase the type...
				 * @param array $f - reference to current action array
				 * @return string (empry)
				 */
				'_type'   => function (&$f)
				{
					$f['attributes']['type'] === strtolower($f['attributes']['type']);
					return '';
				},
				
				/**
				 * If the attribute 'type' is 'A'
				 * then we will expect it to have an href value
				 * 
				 */
				'href' => function($f)
				{
					return $f['attributes']['type'] === 'a' ? '#' : '';
				},

				/**
				 * String of the current class
				 * @param string
				 */
				'class'  => '',
				
				/**
				 * The "name" attribute for the button...typically an empty value
				 * @var string
				 */
				'name' => '',
				
				/**
				 * The "value" of the button (text displayed on the button)
				 * if empty, it will be constructed from the formloader name of the button
				 * @var string
				 */
				'value' => '',
				
				/**
				 * Style attribute for the action (don't use too many inline styles)
				 * @var string
				 */
				'style' => '',

				/**
				 * Array of key => value pairs, each key will be filtered below,
				 * prefixed with 'data-' and added to the attributes...
				 * @var array
				 */
				'data'  => array()  // All data-attr will be filtered below				
			),

			/**
			 * Returns all attributes, filtered an put in string form for manual
			 * tag formation
			 * @param array - current action array
			 */
			'attribute_string' => function(&$f)
			{
				return array_to_attr(Formloader_Bridge::filter_attributes($f));
			},
			
			/**
			 * The action button... we can override this to directly set the action's HTML
			 * @param array $f - current action array
			 * @return string
			 */
			'action' => function (&$f)
			{
				if ($f['attributes']['type'] === 'a')
				{
					return html_tag(
						'a',
						\Arr::filter_keys(Formloader_Bridge::filter_attributes($f), array('type', 'value', 'name'), true),
						$f['attributes']['value']
					);
				}
				else
				{
					return \Form::button(\Arr::filter_keys(Formloader_Bridge::filter_attributes($f), array('html'), true));
				}
			},
			
			/**
			 * Default template for the action
			 * @var string
			 */
			'template'       => 'default.mustache',

			/**
			 * Resolves the template directory for the action
			 * @param array $f - current action array
			 * @return string
			 */
			'template_dir'   => function($f)
			{
				return Formloader_Bridge::template_directory($f);
			},
			
			/**
			 * Path to the template relative to the "modules/formloader/templates" directory
			 * @param array $f - current action array
			 * @return string
			 */
			'template_path'  => function($f)
			{
				return $f['template_dir'].DS.$f['template'];
			},
			
			/**
			 * Output HTML for the action
			 * @param array
			 * @return string  rendered \View object
			 */
			'template_html' => function($f)
			{
				return Formloader_Mustache::forge($f['template_path'], $f, false)->render();
			}
		);
	}
}