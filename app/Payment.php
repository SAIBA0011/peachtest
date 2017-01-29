<?php

namespace App;

/**
* Payment
*/
class Payment
{
	public function __construct($data)
	{
		foreach ($data as $key => $value) {
			$this->$key = $value;
		}
	}

	public function successful()
	{
		return $this->result['code'] == '000.100.110';
	}
}