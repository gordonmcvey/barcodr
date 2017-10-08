<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require "../vendor/autoload.php";

use gmcvey\barcodr\linear\code128\preprocessor\Shortest;
use gmcvey\barcodr\linear\code128\Sequence;

use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;
$logger = new Logger("Code-128", [new ErrorLogHandler ()]);
$test = new Shortest();
$test->setLogger($logger);

echo "=============\n";
try
{
	$test -> setSequence ("11ZZ12345ABCDEabcde6789012345fghijFGHIJ12345ZZ11Q1234");
	$test -> getSequences ();
}
catch (Exception $ex)
{
	echo $ex . PHP_EOL;
}
echo "=============\n";
try
{
	$test -> setSequence ("11112222333344445");
	$test -> getSequences ();
}
catch (Exception $ex)
{
	echo $ex . PHP_EOL;
}
echo "=============\n";
try
{
	$test -> setSequence ("%123456789012345678901234567");
	$test -> getSequences ();
}
catch (Exception $ex)
{
	echo $ex . PHP_EOL;
}
echo "=============\n";
