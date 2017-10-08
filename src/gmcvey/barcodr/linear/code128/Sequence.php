<?php

namespace gmcvey\barcodr\linear\code128;

use gmcvey\barcodr\linear\code128\CharacterType;

/**
 *
 */
class Sequence
{

	/**
	 *
	 * @var CharacterType
	 */
	private $type = null;
	
	/**
	 * @var string
	 */
	private $sequence = "";

	/**
	 * 
	 * @param \gmcvey\barcodr\linear\code128\Sequence $other
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function prepend (Sequence $other)
	{
		$new = $other -> sequence .= $this -> sequence;
		if ($this -> getCharacterClass ($new) !== $this -> type -> getType ())
		{
			throw new \InvalidArgumentException ("Character types are not compatible");
		}

		$this -> sequence = $new;
		return $this;
	}

	/**
	 * 
	 * @param \gmcvey\barcodr\linear\code128\Sequence $other
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function append (Sequence $other)
	{
		$new = $this -> sequence .= $other -> sequence;
		if ($this -> getCharacterClass ($new) !== $this -> type -> getType ())
		{
			throw new \InvalidArgumentException ("Character types are not compatible");
		}

		$this -> sequence = $new;
		return $this;
	}

	/**
	 * 
	 * @param string $sequence
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function setSequence ($sequence)
	{
		if (!mb_detect_encoding ($sequence, ["ASCII"], true))
		{
			throw new \InvalidArgumentException ("Given character sequence must be compatible with ASCII");
		}

		if ($this -> getCharacterClass ($sequence) !== $this -> type)
		{
			throw new \InvalidArgumentException ("Sequence incompatible with current character class");
		}

		$this -> sequence = $sequence;
		return $this;
	}

	
	/**
	 * 
	 * @return CharacterType
	 */
	public function getType ()
	{
		return $this->type;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getSequence ()
	{
		return $this -> sequence;
	}

	/**
	 * 
	 * @return string
	 */
	public function __toString ()
	{
		return $this -> getSequence ();
	}

	/**
	 * 
	 * @param string $sequence
	 * @return string
	 * @todo Distinguish between all three character classes
	 * @todo This doesn't really belong here
	 */
	private function getCharacterClass ($sequence)
	{
		return preg_match ("/\D/", $sequence) ?
			CharacterType::TYPE_B :
			CharacterType::TYPE_C;
	}

	/**
	 * 
	 * @param string $sequence
	 */
	public function __construct ($sequence)
	{
		$this -> type = new CharacterType($this -> getCharacterClass ($sequence));
		$this -> setSequence ($sequence);
	}
}
