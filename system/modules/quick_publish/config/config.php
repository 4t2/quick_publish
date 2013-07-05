<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

#die('<pre>'.var_export($GLOBALS['BE_MOD']['system'], true));

/**
 * Back end modules
 */
#$GLOBALS['BE_MOD']['system']['settings']['stylesheet'] = 'system/modules/quick_publish/assets/styles/quick_publish.css';

if (TL_MODE == 'BE')
{
	$GLOBALS['TL_CSS'][] = 'system/modules/quick_publish/assets/styles/quick_publish.css|screen';
}

