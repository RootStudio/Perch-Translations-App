# Perch Translations

This is a small utility to enable dynamic locale strings to be injected into dynamic Perch templates.

## Installation
To install the app, upload to the `perch/addons/apps` directory and add *jw_translations* to your `config/apps.php`.

Example:

	<?php
		$apps_list = array(
			'content', 
			'categories',
			'jw_translations'
		);
		
## Translation Directory
Translation files should be stored in `perch/translations`. The top level directories inside should be the locales. An example structure could be:

	- translations
		-- en
		-- fr
		-- gr
		-- jp
		-- ru
		
## Translation Files
The translation files are plain PHP files that return an array. For example, a translation file named `controls.php` may contain:

	<?php return array(
	    'buttons' => array(
	        'save'    => 'Save Post',
	        'payment' => 'Submit Payment',
	        'delete'  => 'Delete Post'
	    )
	);
	
## Using Translations in Templates
To use translations in template files, use the tag `<perch:trans />`. Using the example file above, to output a button using the save string would look like this:

	<button>
		<perch:trans id="controls.buttons.save" />
	</button>
	
The ID attribute uses a dot notation syntax to load the correct string. Nested directories require additional dots:

	/en/blog/controls.php ~> blog.controls.buttons.save
	
It is also possible to set a default fallback, in the event a translation cannot be found:

	<button>
		<perch:trans id="controls.buttons.save" default="Save" />
	</button>
	
The global locale can be granularly overwritten using the `lang` attribute:

	<button>
		<perch:trans id="controls.buttons.save" lang="fr" />
	</button>	

### Placeholders

In some cases, you may need to pass template data back into the translation strings, for example a user may be presented with a personalised welcome message on logging in. This can be acheived using placeholders.

In the translation file, setting a placeholder looks like this:


	<?php return array(
	    'messages' => array(
	        'welcome'    => 'Hello, :username - welcome back'
	    )
	);
	
To pass data from the template, prepend 'placeholder-' to the name and use as an attribute:

	<div class="alert">
		<perch:trans id="alerts.messages.welcome" placeholder-username="<perch:content id="member_name" />" />
	</div>
	
## Using Translations in PHP

If translations are required outside of Perch templates, the `get_translation` function is available.

It takes 3 parameters:

* The ID string
* An options array (optional)
* Return true / false

A full example looks like:

	get_translation('controls.button.save', array(
	    'default' => 'Save',
	    'lang'    => 'fr',
	    'placeholders' => array(
	        'username' => 'John Smith'
	    )
	), false);
	
Setting the third parameter to `true` will return the value rather than output:

	$button_label = get_translation('controls.button.save', array(), true); 