<?php

namespace gmcvey\barcodr\linear\code128;

/**
 * CharacterType class
 * 
 * This class is used to indicate which subtype the associated sequence is in.  Code 128 supports 3 subtypes.  
 * 
 * * Type A supports the ASCII characters 0..95 (ASCII control characters except DEL, A-Z, 0-9, basic punctuation)
 * * Type B supports the ASCII characters 32..127 (A-Z, 0-9, a-z, basic punctuation, "`", "{", "|", "}", "~", DEL control character)
 * * Type C supports the ASCII characters 48..57 (0-9) and encodes pairs of digits as a single barcode 
 *   symbol for greater efficiency.  As pairs of digits are stored per symbol a Type C sequence must 
 *   contain an even number of digits.  
 * 
 * All three subtypes support the Code128 control codes (Stop, subtype switch, etc)
 * 
 * Naturally there is some overlap between these sybtypes.  
 * 
 * * Even-length strings of only digits can be represented in all three subtypes, though type C would be more efficient.
 * * Character strings that don't contain a-z or any ASCII control codes can be represented by either 
 *   type A or B and would in fact be indistinguishable because the mapping between the characters 
 *   and symbols is the same for both subtypes.
 * 
 * @author gordonmcvey
 */
class CharacterType
{
	/**
	 * Type definitions
	 */
	const TYPE_A = "A";
	const TYPE_B = "B";
	const TYPE_C = "C";
	
	/**
	 * Valid types
	 * 
	 * @var string[]
	 */
	static private $validTypes = [self::TYPE_A, self::TYPE_B, self::TYPE_C];
	
	/**
	 * Type of this instance
	 * 
	 * @var string 
	 */
	private $type = "";
	
	/**
	 * Determine if the given string is a valid character type
	 * 
	 * @param string $type
	 * @return boolean
	 */
	static public function isValid($type)
	{
		return in_array($type, self::$validTypes);
	}
		
	/**
	 * Determine if the other type is the same as this one
	 * 
	 * @param CharacterType $other
	 * @return boolean
	 */
	public function equals(CharacterType $other)
	{
		return $this->type === $other->type;
	}
	
	/**
	 * Get the character type code
	 * 
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}
	
	/**
	 * 
	 * @param type $type
	 * @throws \InvalidArgumentException
	 */
	public function __construct($type)
	{
		if (!static::isValid ($type)) {
			throw new \InvalidArgumentException ("Invalid type");
		}
		
		$this->type = $type;
	}
}
