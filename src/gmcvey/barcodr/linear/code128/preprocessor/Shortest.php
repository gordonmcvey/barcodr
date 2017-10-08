<?php

namespace gmcvey\barcodr\linear\code128\preprocessor;

use gmcvey\barcodr\linear\code128\abstr\PreProcessor;
use gmcvey\barcodr\linear\code128\CharacterType;
use gmcvey\barcodr\linear\code128\Sequence;

/**
 * Shortest sequence preprocessor
 * 
 * This preprocessor attempts to come up with an ideal solution for splitting 
 * the given character sequence.  As switching between character types A/B and
 * C carries a cost in the form of a character type switch symbol being introduced
 * into the barcode this preprocessor applies some rules to attempt to produce 
 * as few subsequences (and therefore as few switches) as possible.  
 * 
 * As this class does more preprocessing it may be more expensive to run than the
 * simple or manual preprocessors.  
 *
 * @author Gordon McVey
 */
class Shortest extends PreProcessor
{

	/**
	 * Match strings that can be represented with character type A
	 */
	const REGEX_TYPE_A = '/^[\x00-\x5F]+$/';

	/**
	 * Match strings that can be represented with character type B
	 */
	const REGEX_TYPE_B = '/^[\x20-\x7F]+$/';

	/**
	 * Match strings that can be represented with either character types A or B
	 */
	const REGEX_TYPE_A_OR_B = '/(?:^[\x00-\x5F]+$|^[\x20-\x7F]+$)/';

	/**
	 * Match characters that would be invalid in character type A
	 */
	const REGEX_NOT_TYPE_A = '/[\x60-\x7F]/';

	/**
	 * Match characters that would be invalid in character type B
	 */
	const REGEX_NOT_TYPE_B = '/[\x00-\x19]/';

	/**
	 * Match sequences of strings such that each match is either digits or non-digits.
	 * If the string contains both digits and non-digits then each match will will be
	 * either a sequence of digits or a sequence of non-digits.  
	 */
	const REGEX_DETECT_DIGITS = '/(?:\d+|\D+)/';

	/**
	 * Match sequences of strings such that each match can be encoded with a single
	 * Code 128 character type.  If the string contains both characters that can only
	 * be encoded with type A and characters that can only be encoded with type B then
	 * each match will be capable of being encoded in a single type
	 */
	const REGEX_DETECT_CHARACTER_TYPES = '/(?:[\x00-\x5F]+|[\x20-\x7F]+)/';

	/**
	 * 
	 * @return Sequence[]
	 */
	public function getSequences ()
	{
		if (empty ($this -> subSequences))
		{
			$this -> subSequences = $this -> parse ($this -> rawSequence);
		}
		return $this -> subSequences;
	}

	/**
	 * Parse the given sequence into a set of subsequences with as few members as possible
	 * 
	 * @param string $rawSequence
	 * @return Sequence[]
	 */
	private function parse ($rawSequence)
	{
		// Build a list of candidate splits
		$candidates = $this -> buildCandidateSplits ($rawSequence);
		if (empty ($candidates))
		{
			throw new \RuntimeException ("Unable to determine any candidates for splitting");
		}

		// Find the lowest cost split
		
		// Generate set of subsequences based on the selected split

	}

	/**
	 * 
	 * @param string $rawSequence
	 * @return array
	 */
	private function buildCandidateSplits ($rawSequence)
	{
		// Build a simple set of splits
		$candidateSplits = [];
		$basicSplits = $this -> buildBasicSplits ($rawSequence);

		$this -> logger -> debug ("$rawSequence has undergone basic splitting", $basicSplits);

		// Consolidate any subsequences that can be combined and still be encoded
		// in a single encoding type
		$basicSplits = $this -> consolidateCharacterSequences ($basicSplits);

		$this -> logger -> debug ("$rawSequence has undergone character sequence consolidation", $basicSplits);

		// Now that all the non-digit subsequences are consolidated we can start
		// generating candidate rule sets based on the digit sequences.  We need
		// to check the following factors: 
		//
		// Length: If the digit sequence is too short then encoding it in type C
		// alongside other sequences that require types A or B would not result
		// in a shorter barcode, and could actually make the barcode longer in 
		// some circumstances.  If the digit sequence is too short then it should
		// be consolidated with an adjacent sequence (either the previous or the 
		// following sequence).  This will generate two possible candidates.
		//
		// Evenness: Type C encodes a pair of digits per barcode symbol, which
		// is how it results in shorter barcodes.  However this means that a 
		// digit sequence that isn't even needs to have either its last digit 
		// prepended to the next sequence or have its first digit appended to
		// the previous sequence.  This will generate two possible candidates.

		return $candidateSplits;
	}

	private function generateCandidates(array $candidateSplits)
	{
		foreach ($basicSplits as $sequence)
		{
			if (preg_match ('/^\d+$/', $sequence))
			{
				$this -> logger -> debug ("$sequence is a digit sequence");
				// Check that the digit sequence is long enough to be worth encoding in type C
				// The digit sequence is only worth encoding under the following circumstances:
				//
				// If it's at the start or end of the entire sequence then it 
				// must be at least 4 characters long.  If not then the sequence 
				// should be prepended to the following sequence (if at the start)
				// or appended to the previous sequence (if at the end).
				//
				// If it's in the middle of the entire sequence then it must be
				// at least 6 characters long.  If not then it should either
				// be appended to the previous sequence or prepended to the 
				// following sequence
				//
				// If the digit sequence is the only sequence in the set then 
				// it must be either exactly 2 characters long or at least 4
				// characters long.  If it's a single character or 3 characters
				// long then it should be encoded with type A or C.  
				
				// If the sequence is still long enough to exist in its own right
				// then check whether there is an odd or even number of characters
				// as type C can only encode pairs of digits.  If the length is 
				// odd then one digit needs to be removed.  There are a number of
				// options in this case: 
				// 
				// A character can be removed from the start of the digit sequence
				// and either appended to the start of the previous sequence 
				// (if it exists) or used to create a new previous sequence (if
				// we're already at the beginning).  
				//
				// A character can be removed from the end of the digit sequence
				// and either prepended to the following sequence (if it exists)
				// or a new or used to create a new next sequence (if we're
				// already at the end).  
				// 
				// If at the beginning of a sequence then prepending to the next
				// is preferred to creating a new previous sequence.  If at the 
				// end of a sequence then appending to the previous sequence is
				// preferred to creating a new next sequence.
			}
			else
			{
				$this -> logger -> debug ("$sequence is an alphanumeric sequence, skipping");
			}
		}
	}
	
	/**
	 * 
	 * @param string[] $sequenceStrings
	 * @return Sequence[]
	 */
	private function generateSequenceCollection (array $sequenceStrings)
	{
		$sequences = [];
		foreach ($sequences as $sequence)
		{
			$sequences[] = new Sequence ($sequence);
		}
		return $sequences;
	}
	
	/**
	 * Do a pass on the basic splits to consolidate non-digit sequences.  
	 * 
	 * The basic split process can split can AAAbbb into AAA,bbb or aaaBBB into 
	 * aaa,BBB due to the fact that splits happen wherever a character that can 
	 * only appear in one of A or B appears in the sequence, and the sequence 
	 * continues until another character that can only appear in the opposite 
	 * type appears.  
	 * 
	 * @param string[] $basicSplits
	 * @return string[]
	 */
	private function consolidateCharacterSequences (array $basicSplits)
	{
		$consolidated = [];
		$tempSequence = "";

		foreach ($basicSplits as $sequence)
		{
			// If the sequence is numeric then added it to the consolidated list
			// with no further processing
			if (preg_match ('/^\d+$/', $sequence))
			{
				$this -> logger -> debug ("$sequence is a digit sequence, skipping");
				if (!empty ($tempSequence))
				{
					$consolidated[] = $tempSequence;
					$tempSequence = "";
				}
				$consolidated[] = $sequence;
			}

			// If the sequence can be appended to the previous ones and still be a valid sequence then do so
			else if (preg_match (static::REGEX_TYPE_A_OR_B, $tempSequence . $sequence))
			{
				$this -> logger -> debug ("$sequence can be consolidated with previous sequences");
				$tempSequence .= $sequence;
			}

			// If the sequence cannot be appended then the previous sequences are as far as we can go with the current encoding.  
			else
			{
				$this -> logger -> debug ("$sequence cannot be consolidated with previous sequences");
				if (!empty ($tempSequence))
				{
					$consolidated[] = $tempSequence;
				}
				$tempSequence = $sequence;
			}
		}

		// If there's a non-committed sequence then tag it onto the end
		if (!empty ($tempSequence))
		{
			$consolidated[] = $tempSequence;
		}

		return $consolidated;
	}

	/**
	 * Determine the cost of a given candidate set of sequences
	 * 
	 * This method calculates a cost for a given set of sequences if they 
	 * were used to generate a barcode based on the number of subsequences, the
	 * length of each sequence and the encoding type that could be used.  
	 * 
	 * For a Code 128 barcode the cost can be considered to be a measure of
	 * the number of symbols needed to encode it.  A barcode with more symbols
	 * is more "expensive" than one with fewer symbols.  As there is more than
	 * one valid encoding for a given sequence we want to find the "cheapest"
	 * possible encoding.  
	 * 
	 * @param array $candidate
	 * @return int
	 */
	private function calculateCost (array $candidate)
	{
		// The cost of a given barcode is based on the number of symbols that 
		// would be required to render it.  
		// 
		// As Type C symbols encode 2 characters and all other symbols encode
		// only a single character we will add 2 to the cost for every symbol
		// we require in a barcode unless we're adding type C sequences, for 
		// which we will add 1 per symbol to the cost
		// 
		// Our starting cost is derived from the number of subsequences - 1, as
		// each subsequence must be separated by a type switch symbol at the 
		// boundary between each sequence.  
		$cost = 2 * (count ($candidate) - 1);

		// For each sequence add the length to the cost (with the cost weighted
		// by whether or not the sequence is type C)
		foreach ($candidate as $subSequence)
		{
			
		}

		// We now have a cost
		return $cost;
	}

	/**
	 * Generate a very basic set of splits
	 * 
	 * This method takes a raw character sequence and does a very basic split into
	 * a list of sequences of the following types: 
	 * 
	 * * Digit sequences (of any length)
	 * * Sequences that can be represented by encoding type A (No lower case characters, etc)
	 * * Sequences that can be represented by encoding type B (No ASCII control codes other than DEL, etc)
	 * 
	 * Please note that at this point we don't necessarily have a set of sequences that can
	 * all be efficiently represented by a single encoding type, for example digit sequences 
	 * may be of odd length and therefore not encodable in type C.  Even if all the sequences
	 * are encodable the result would almost certainly be a very inefficient encoding as there
	 * will be many adjacent groups that could potentially be merged into a single group.  
	 * 
	 * @param string $rawSequence
	 * @return string[]
	 */
	private function buildBasicSplits ($rawSequence)
	{
		$splits = [];

		// Outer loop: Split by whether the sequence is a character sequence or a digit sequence
		foreach ($this -> splitBy ($rawSequence, static::REGEX_DETECT_DIGITS) as $outerTemp)
		{
			// Inner loop: Split further if a sequence contains both Type A and Type B characters
			foreach ($this -> splitBy ($outerTemp, static::REGEX_DETECT_CHARACTER_TYPES) as $innerTemp)
			{
				$splits [] = $innerTemp;
			}
		}

		return $splits;
	}

	/**
	 * Split a single character sequence into a set of strings that are distinguished by the provided regex
	 * 
	 * @param string $rawSequence
	 * @return string[]
	 */
	private function splitBy ($rawSequence, $regex)
	{
		$matches = [];
		preg_match_all ($regex, $rawSequence, $matches);
		if (empty ($matches))
		{
			return [];
		}
		return $matches[0];
	}
}
