<?php

/**
 * @Author: Stephan<srandriamahenina@bocasay.com>
 * @Date:   2017-10-10 14:42:23
 * @Last Modified by:   stephan
 * @Last Modified time: 2019-05-06 13:19:58
 */

namespace App\Security\Voter;

class Attributes
{
	const VIEW = 'view';
	const CREATE = 'create';
	const SHOW = 'show';
	const EDIT = 'edit';
	const DELETE = 'delete';
	// STATUS
	const SUBMIT = 'submit';
	const VALIDATE = 'validate';
	const INVALIDATE = 'invalidate';
	const LOSE = 'lose';
}