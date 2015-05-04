<?php

namespace Application\Validator;

use Zend\Validator\AbstractValidator;

class MoneyValidator extends AbstractValidator
{
	const INVALID = 'floatInvalid';
	const NOT_FLOAT = 'notFloat';
	
	/**
     * @var array
     */
	protected $messageTemplates = array (
		self::INVALID => "Invalid type given. String, integer or float expected",
		self::NOT_FLOAT => "'%value%' does not appear to be a float" 
	);

	public function isValid($value)
	{
		$this->setValue($value);
		
		$value = str_replace(',', '.', $value);
		
		if (! is_string($value) && ! is_int($value) && ! is_numeric($value)) {
			$this->error(self::INVALID);
			return false;
		}
		
		if (is_numeric($value)) {
			return true;
		}
		
		$this->error(self::NOT_FLOAT);
		return false;
	}
}
