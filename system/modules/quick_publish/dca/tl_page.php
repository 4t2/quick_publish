<?php

$arrKeys = array_keys($GLOBALS['TL_DCA']['tl_page']['list']['operations'], TRUE);
$toggleKey = array_search('toggle', $arrKeys);

if ($toggleKey !== FALSE)
{
	$GLOBALS['TL_DCA']['tl_page']['list']['operations'] = array_merge(
		array_slice($GLOBALS['TL_DCA']['tl_page']['list']['operations'], 0, $toggleKey+1),
		array(
			'publish' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_page']['toggle'],
				'href'				  => 'mode=publish&amp;childs=1',
				'icon'                => 'system/modules/quick_publish/assets/images/publish_pages.png',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['quickPublishConfirm'] . '\'))return false;Backend.getScrollOffset()"',
				'button_callback'     => array('tl_quick_publish', 'publishPagesIcon')
			)
		),
		array_slice($GLOBALS['TL_DCA']['tl_page']['list']['operations'], $toggleKey+1, count($GLOBALS['TL_DCA']['tl_page']['list']['operations']))
	);
}


class tl_quick_publish extends Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}

	public function publishPagesIcon($row, $href, $label, $title, $icon, $attributes)
	{
		if (!$this->User->isAdmin)
		{
			return '';
		}

		if ($this->Input->get('mode') == 'publish' && $this->Input->get('childs') == 1)
		{
			$this->publishPages($this->Input->get('id'));
			$this->redirect($this->getReferer());
		}

		return '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ';
	}
	
	protected function publishPages($id)
	{
		$this->publishPageAndArticles($id);
		$this->publishChildPages($id);
	}

	protected function publishChildPages($pid)
	{
		$objPages = $this->Database->prepare("SELECT `id` FROM `tl_page` WHERE `pid`=?")->execute($pid);

		while ($objPages->next())
		{
			$this->publishPageAndArticles($objPages->id);
			$this->publishChildPages($objPages->id);
		}
	}
	
	protected function publishPageAndArticles($id)
	{
		$this->Database->prepare("UPDATE `tl_page` SET `published`=1 WHERE id=?")->execute($id);
		$this->Database->prepare("UPDATE `tl_article` SET `published`=1 WHERE pid=?")->execute($id);
	}
}