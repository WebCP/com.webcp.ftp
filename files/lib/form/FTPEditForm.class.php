<?php
/*
 * Copyright (c) 2009 Tobias Friebel
 * Authors: Tobias Friebel <TobyF@Web.de>
 *
 * Lizenz: GPL
 *
 * $Id$
 */

require_once (CP_DIR . 'lib/form/FTPAddForm.class.php');

class FTPEditForm extends FTPAddForm
{
	/**
	 * @see Page::readData()
	 */
	public function readData()
	{
		if (isset($_REQUEST['ftpUserID']))
			$this->ftpAccount = new FTPUserEditor($_REQUEST['ftpUserID']);

		if (is_null($this->ftpAccount))
			throw new SystemException('no such account');

		if ($this->ftpAccount->userID != WCF :: getUser()->userID)
			throw new SystemException('invalid user');

		$this->path = str_replace(WCF :: getUser()->homeDir, '', $this->ftpAccount->homedir);

		parent :: readData();
	}

	/**
	 * @see Form::validate()
	 */
	public function validate()
	{
		AbstractSecureForm :: validate();

		if (empty($this->password))
			throw new UserInputException('password', 'notempty');

		if (empty($this->path))
			throw new UserInputException('path', 'notempty');

		if (!CPUtils :: validatePath(WCF :: getUser()->homeDir . '/'. $this->path, WCF :: getUser()->homeDir, true))
			throw new UserInputException('path', 'invalid');
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables()
	{
		parent :: assignVariables();

		WCF :: getTPL()->assign(array (
			'password' => '',
			'path' => $this->path,
			'description' => $this->ftpAccount->description,
		));
	}

	/**
	 * @see Form::save()
	 */
	public function save()
	{
		parent :: save();

		// update
		$this->ftpAccount->update($this->password,
								  WCF :: getUser()->homeDir . '/'. $this->path,
								  $this->description
								 );
		$this->saved();

		$url = 'index.php?page=FTPList'. SID_ARG_2ND_NOT_ENCODED;
		HeaderUtil::redirect($url);
	}

	/**
	 * @see Page::show()
	 */
	public function show()
	{
		require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');
		PageMenu::setActiveMenuItem('cp.header.menu.ftp');

		parent::show();
	}
}
?>