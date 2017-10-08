<?php

namespace gmcvey\barcodr\linear\code128\abstr;

use gmcvey\barcodr\linear\code128\iface\PreProcessor as iPreProcessor;
use gmcvey\barcodr\linear\code128\Sequence;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

/**
 * Base class for Code128 preprocessors
 *
 * @author gordonmcvey
 */
abstract class PreProcessor implements iPreProcessor, LoggerAwareInterface
{
	use LoggerAwareTrait;
	
	/**
	 * The original un-split character sequence
	 * 
	 * @var string 
	 */
	protected $rawSequence = "";
	
	/**
	 * The processed subsequences
	 * 
	 * @var Sequence[]
	 */
	protected $subSequences = [];
	
	/**
	 * Set the sequence
	 * 
	 * @param Sequence $sequence
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function setSequence($sequence)
	{
		if (!mb_detect_encoding($sequence, ["ASCII"], true))
		{
			throw new \InvalidArgumentException("Given character sequence must be compatible with ASCII");
		}
		
		$this->rawSequence = trim($sequence);
		$this->subSequences = [];
		return $this;
	}
	
	public function __construct ()
	{
		$this -> setLogger (new NullLogger ());
	}
}
