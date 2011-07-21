<?php

class App_Filter_Number implements Zend_Filter_Interface
{
	/**
	 * @param string $value
	 */
	public function filter($value) {
		$farisNumbers=array(
			'۰',
			'۱',
			'۲',
			'۳',
			'۴',
			'۵',
			'۶',
			'۷',
			'۸',
			'۹'
		);
		$englishNumbers=array(
			'0',
			'1',
			'2',
			'3',
			'4',
			'5',
			'6',
			'7',
			'8',
			'9'
		);
		$filteredValue=str_replace($farisNumbers,$englishNumbers,$value);
		return $filteredValue;
	}

}

