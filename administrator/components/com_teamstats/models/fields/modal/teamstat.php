<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_teamstats
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * Supports a modal type picker.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_teamstats
 * @since       1.6
 */
class JFormFieldModal_Teamstat extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since   1.6
	 */
	protected $type = 'Modal_Teamstat';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string	The field input markup.
	 *
	 * @since   1.6
	 */
	protected function getInput()
	{
		$allowEdit		= ((string) $this->element['edit'] == 'true') ? true : false;
		$allowClear		= ((string) $this->element['clear'] != 'false') ? true : false;

		// Load language
		JFactory::getLanguage()->load('com_teamstats', JPATH_ADMINISTRATOR);

		// Load the javascript
		JHtml::_('behavior.framework');
		JHtml::_('behavior.modal', 'a.modal');
		JHtml::_('bootstrap.tooltip');

		// Build the script.
		$script = array();

		// Select button script
		$script[] = '	function jSelectType_' . $this->id . '(id, name, object) {';
		$script[] = '		document.id("' . $this->id . '_id").value = id;';
		$script[] = '		document.id("' . $this->id . '_name").value = name;';

		if ($allowEdit)
		{
			$script[] = '		jQuery("#' . $this->id . '_edit").removeClass("hidden");';
		}

		if ($allowClear)
		{
			$script[] = '		jQuery("#' . $this->id . '_clear").removeClass("hidden");';
		}

		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';

		// Clear button script
		teamstatic $scriptClear;

		if ($allowClear && !$scriptClear)
		{
			$scriptClear = true;

			$script[] = '	function jClearType(id) {';
			$script[] = '		document.getElementById(id + "_id").value = "";';
			$script[] = '		document.getElementById(id + "_name").value = "'.htmlspecialchars('Select a teamstat', ENT_COMPAT, 'UTF-8').'";';
			$script[] = '		jQuery("#"+id + "_clear").addClass("hidden");';
			$script[] = '		if (document.getElementById(id + "_edit")) {';
			$script[] = '			jQuery("#"+id + "_edit").addClass("hidden");';
			$script[] = '		}';
			$script[] = '		return false;';
			$script[] = '	}';
		}

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Setup variables for display.
		$html	= array();
		$link	= 'index.php?option=com_teamstats&amp;view=teamstats&amp;layout=modal&amp;tmpl=component&amp;function=jSelectType_' . $this->id;

		if (isset($this->element['language']))
		{
			$link .= '&amp;forcedLanguage=' . $this->element['language'];
		}

		// Get the title of the linked chart
		if ((int) $this->value > 0)
		{
			$db = JFactory::getDbo();
			$query = " SELECT name "
					." FROM #__teamstats "
					." WHERE id = '".(int) $this->value."'";

			$db->setQuery($query);

			try
			{
				$title = $db->loadResult();
			}
			catch (RuntimeException $e)
			{
				JError::raiseWarning(500, $e->getMessage());
			}
		}

		if (empty($title))
		{
			$title = 'Select a teamstat';
		}

		$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

		// The active type id field.
		if (0 == (int) $this->value)
		{
			$value = '';
		}
		else
		{
			$value = (int) $this->value;
		}

		// The current type display field.
		$html[] = '<span class="input-append">';
		$html[] = '<input type="text" class="input-medium" id="' . $this->id . '_name" value="' . $title . '" disabled="disabled" size="35" />';
		$html[] = '<a class="modal btn hasTooltip" title="Change teamstat"  href="' . $link . '&amp;' . JSession::getFormToken() . '=1" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="icon-file"></i> ' . JText::_('JSELECT') . '</a>';

		// Edit article button
		if ($allowEdit)
		{
			$html[] = '<a class="btn hasTooltip' . ($value ? '' : ' hidden') . '" href="index.php?option=com_teamstats&layout=modal&tmpl=component&task=type.edit&id=' . $value . '" target="_blank" title="Edit teamstat" ><span class="icon-edit"></span> ' . JText::_('JACTION_EDIT') . '</a>';
		}

		// Clear type button
		if ($allowClear)
		{
			$html[] = '<button id="' . $this->id . '_clear" class="btn' . ($value ? '' : ' hidden') . '" onclick="return jClearType(\'' . $this->id . '\')"><span class="icon-remove"></span> ' . JText::_('JCLEAR') . '</button>';
		}

		$html[] = '</span>';

		// class='required' for client side validation
		$class = '';

		if ($this->required)
		{
			$class = ' class="required modal-value"';
		}

		$html[] = '<input type="hidden" id="' . $this->id . '_id"' . $class . ' name="' . $this->name . '" value="' . $value . '" />';

		return implode("\n", $html);
	}
}
