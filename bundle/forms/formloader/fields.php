<?php
/**
 * Formloader: Updated by Formloader Module - 2012/04/05 16:03:00
 * --- You may edit the below, changes will not be lost, just appended---
 */
return array(
		'action_name' => array(
				'label' => 'Action Name:',
				'tip' => 'Name for the action, must be unique to the group',
				'attributes' => array(
						'name' => 'name',
				),
				'validations' => array('fuel.required','fuel.valid_string[alpha,dashes,numeric]'),
		),
		'actions' => array(
				'attributes' => array(
						'id' => 'actions',
				),
				'tip' => 'Start typing the name of an action you have already created, or click the "+" to create a new one',
				'input_template' => 'tagit.mustache',
		),
		'attributes_action' => array(
				'attributes' => array(
						'name' => 'attributes',
				),
				'label' => 'Action Attributes',
				'template_path' => 'formloader/fieldsets/dropdown.mustache',
				'fields' => array('id','class','name','value','button_type','style','data','href'),
		),
		'attributes_field' => array(
				'attributes' => array(
						'name' => 'attributes',
				),
				'label' => 'Field Attributes',
				'template_path' => 'formloader/fieldsets/dropdown.mustache',
				'fields' => array('type','id','class','name','value','placeholder','style','data'),
		),
		'attributes_fieldset' => array(
				'attributes' => array(
						'name' => 'attributes',
				),
				'label' => 'Fieldset Attributes',
				'template_path' => 'formloader/fieldsets/dropdown.mustache',
				'fields' => array('id','class','name','data','style'),
		),
		'attributes_form' => array(
				'label' => 'Form Attributes',
				'attributes' => array(
						'name' => 'attributes',
				),
				'template_path' => 'formloader/fieldsets/dropdown.mustache',
				'fields' => array('id','class','name','form_action','form_method','data'),
				'options' => array(),
		),
		'button_type' => array(
				'attributes' => array(
						'name' => 'type',
				),
				'help_inline' => 'either "submit", "button", or "a" for a link',
		),
		'class' => array(),
		'data' => array(
				'label' => 'Data Attributes',
				'tip' => 'Each of the Key=>Val pairs will be added as data attribues to this item',
				'input_template' => 'options.mustache',
				'options' => array(),
		),
		'default' => array(
				'tip' => 'Default value for a field... only used on a GET request... typically not set. Helpful on setting a checked field "checked" by default',
		),
		'field_comments' => array(
				'placeholder' => 'Comments for documentation/code generation',
		),
		'field_html' => array(
				'tip' => 'Custom HTML for just the field section (still wrapped by the template as normal)',
				'attributes' => array(
						'type' => 'textarea',
				),
		),
		'field_label' => array(
				'attributes' => array(
						'name' => 'label',
						'placeholder' => 'Label for Field',
				),
		),
		'field_name' => array(
				'tip' => 'Name for the field, must be unique to the group',
				'attributes' => array(
						'name' => 'name',
				),
				'validations' => array('fuel.required','fuel.valid_string[alpha,dashes,numeric]'),
		),
		'fields' => array(
				'attributes' => array(
						'id' => 'fields',
				),
				'tip' => 'Start typing the name of a field you have already created, or click the "+" to create a new one',
				'input_template' => 'tagit.mustache',
		),
		'fieldset_name' => array(
				'label' => 'Fieldset Name:',
				'tip' => 'Name for the fieldset, must be unique to the group',
				'attributes' => array(
						'name' => 'name',
				),
				'validations' => array('fuel.required','fuel.valid_string[alpha,dashes,numeric]'),
		),
		'fieldsets' => array(
				'attributes' => array(
						'id' => 'fieldsets',
				),
				'tip' => 'Start typing the name of a fieldset you have already created, or click the "+" to create a new one',
				'input_template' => 'tagit.mustache',
		),
		'form_action' => array(
				'attributes' => array(
						'name' => 'action',
						'placeholder' => 'Full URL or relative (/page/location)',
				),
				'inline_tip' => 'URI of form post, defaults to current URL',
		),
		'form_method' => array(
				'attributes' => array(
						'name' => 'method',
						'type' => 'dropdown',
				),
				'options' => array(
						'post' => '',
						'get' => 'get',
				),
		),
		'form_name' => array(
				'tip' => 'Name for the form, must be unique to the group',
				'attributes' => array(
						'name' => 'name',
				),
				'validations' => array('fuel.required','fuel.valid_string[alpha,dashes,numeric]'),
		),
		'group' => array(
				'attributes' => array(
						'id' => 'group',
				),
				'tip' => 'Used mainly to namespace the forms/child items',
				'validations' => array('fuel.required','fuel.valid_string[alpha,numeric]'),
		),
		'help_inline' => array(
				'label' => 'Inline Help:',
				'help_inline' => 'help that goes over here...',
		),
		'hide_name' => array(
				'attributes' => array(
						'type' => 'dropdown',
				),
				'options' => array(
						'No' => '',
						'Yes' => 'true',
				),
		),
		'href' => array(
				'tip' => 'Note: this is only used if the button type is "a" (default #)'
		),
		'id' => array(
				'placeholder' => '#id of the form/field',
		),
		'ignored_groups' => array(
				'attributes' => array(
						'type' => 'checkboxes',
				),
				'option_call' => 'Formloader_Scaffold::ignored_groups',
		),
		'input_template' => array(
				'tip' => 'direct path to just the input part of a field (not the wrapper)...',
				'options' => array(),
		),
		'legend' => array(),
		'name' => array(),
		'option_call' => array(
				'label' => 'Option Call:',
				'tip' => 'Put a function here and it will call/expect an array return to populate the option...',
		),
		'options' => array(
				'attributes' => array(
						'type' => 'dropdown',
				),
				'tip' => 'The left input will be displayed, the right will be the name of the field or the select value, depending on the type of field',
				'input_template' => 'options.mustache',
				'options' => array(),
		),
		'placeholder' => array(
				'attributes' => array(
						'placeholder' => 'Add Placeholder Here',
				),
		),
		'route_error' => array(
				'attributes' => array(
						'class' => 'span6',
						'placeholder' => 'HMVC::path/to/controller or Static::public_method',
				),
				'tip' => 'When the form fails validation, this is called as either a static method or HMVC request...',
		),
		'route_success' => array(
				'tip' => 'When the form validates correctly, this is called as either a static method or HMVC request...',
				'attributes' => array(
						'class' => 'span6',
						'placeholder' => 'HMVC::path/to/controller or Static::public_method',
				),
		),
		'style' => array(
				'tip' => 'Please don\'t use this unless you absolutely need to',
		),
		'subfields' => array(
				'attributes' => array(
						'id' => 'fields',
						'name' => 'fields',
				),
				'tip' => 'Adding fields here will add a depth layer to the array',
				'input_template' => 'tagit.mustache',
		),
		'template' => array(
				'label' => 'Mustache View:',
				'attributes' => array(
						'placeholder' => 'default.mustache',
				),
		),
		'template_directory' => array(
				'attributes' => array(
						'name' => 'template_dir',
						'placeholder' => '/path/to/mustache/template',
				),
				'tip' => 'Directory that the template is in (within the modules/formloader/templates)',
				'options' => array(),
		),
		'template_html' => array(
				'tip' => 'We can specify the raw HTML for an item here... completely overriding every other setting',
				'attributes' => array(
						'type' => 'textarea',
				),
		),
		'template_path' => array(
				'tip' => 'Full path - combined template directory and template path',
				'attributes' => array(
						'placeholder' => '/path/to/template.mustache',
				),
		),
		'tip' => array(
				'tip' => '<strong>Tips are found underneath a form item, and can carry valid html markup</strong>',
				'options' => array(),
		),
		'title' => array(
				'attributes' => array(
						'type' => 'text',
				),
				'label' => 'Title',
		),
		'type' => array(
				'attributes' => array(
						'type' => 'dropdown',
						'data' => array(
								'formloader-fieldtype' => 'true',
						),
				),
				'options' => array(
						'text' => '',
						'password' => 'password',
						'hidden' => 'hidden',
						'dropdown' => 'dropdown',
						'textarea' => 'textarea',
						'radios' => 'radios',
						'checkbox' => 'checkbox',
						'checkboxes' => 'checkboxes',
						'file' => 'file',
						'button' => 'button',
						'submit' => 'submit',
						'uneditable' => 'uneditable',
				),
				'label' => 'Field Type:',
		),
		'use_legend' => array(
				'attributes' => array(
						'type' => 'dropdown',
				),
				'options' => array(
						'No' => '',
						'Yes' => 'true',
				),
		),
		'validations' => array(
				'attributes' => array(
						'id' => 'validations',
				),
				'tip' => 'Validations (other than the native ones) are not checked... you must ensure the validations are correctly setup on the server side.',
				'input_template' => 'tagit.mustache',
		),
		'value' => array(
				'options' => array(),
		),
);
