<?php

class GoodResponse
{
	public $body = 'SCV ready!';

	public function hasErrors()
	{
		return false;
	}
}

class BadResponse
{
	public $body = 'Not enough minerals.';

	public function hasErrors()
	{
		return true;
	}
}
