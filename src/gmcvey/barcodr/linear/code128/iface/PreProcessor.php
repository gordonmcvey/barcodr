<?php

namespace gmcvey\barcodr\linear\code128\iface;

interface PreProcessor
{
	public function setSequence($sequence);
	public function getSequences();
}
