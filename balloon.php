<?php

	/*
		PHP-BALLOON-HASHING.
		Based on Python version: https://github.com/nachonavarro/balloon-hashing
	*/

	/*
		*** function bchexdec($hex) ***
		
		Hexdec function for large numbers using BCMath.
		More info here: http://stackoverflow.com/questions/1273484/large-hex-values-with-php-hexdec
		
		Args:
			*args: Hexadecimal value
		
		Returns:
			str: The decimal value
		
	*/
	function bchexdec($hex)
	{
		$dec = 0;
		$len = strlen($hex);
		for ($i = 1; $i <= $len; $i++) {
			$dec = bcadd($dec, bcmul(strval(hexdec($hex[$i - 1])), bcpow('16', strval($len - $i))));
		}
		return $dec;
	}

	/*
		*** function hash_func($params) ***
		
		Concatenate all the arguments and hash the result.

		Args:
			*args: Arguments to concatenate

		Returns:
			str: The hashed string
	*/
	function hash_func($params)
	{
		$t = '';
		foreach($params as $param)
		{
			$t .= $param;
		}
		return hash('sha256', $t, TRUE);
	}

	/*
		*** function hash_func_hex($params) ***
		
		Concatenate all the arguments and hash the result.

		Args:
			*args: Arguments to concatenate

		Returns:
			str: The hashed string
	*/
	function hash_func_hex($params)
	{
		$t = '';
		foreach($params as $param)
		{
			$t .= $param;
		}
		return hash('sha256', $t);

	}

	/*
		*** function expand(&$buf, $cnt, $space_cost) ***
	
		First step of the algorithm. Fill up a buffer with pseudorandom bytes derived from the password and salt
		by computing repeatedly the hash function on a combination of the password and the previous hash.

		Args:
			$buf (str array): Array of hashes as bytes.
			$cnt (int): Used in a security proof (read the paper)
			$space_cost (int): The size of the buffer

		Returns:
			void: Updates the buffer and counter, but does not return anything.
	*/
	function expand(&$buf, $cnt, $space_cost)
	{
		for($s=1;$s<$space_cost;$s++)
		{
			array_push($buf, hash_func(array($cnt, $buf[$s - 1])));
			$cnt += 1;
		}
		return;
	}

	/*
		*** function mix(&$buf, $cnt, $delta, $salt, $space_cost, $time_cost) ***
		
		Second step of the algorithm. Mix time_cost number of times the pseudorandom bytes in the buffer.
		At each step in the for loop, update the nth block to be the hash of the n-1th block, the nth block,
		and delta other blocks chosen at random from the buffer.

		Args:
			buf (str array): Array of hashes as bytes.
			cnt (int): Used in a security proof (read the paper)
			delta (int): Number of random blocks to mix with.
			salt (str): A user defined random value for security
			space_cost (int): The size of the buffer
			time_cost (int): Number of rounds to mix

		Returns:
			void: Updates the buffer and counter, but does not return anything.
	*/
	function mix(&$buf, $cnt, $delta, $salt, $space_cost, $time_cost)
	{		
		for($t=0;$t<$time_cost;$t++)
		{
			for($s=0;$s<$space_cost;$s++)
			{
				if($s == 0)
				{
					 $buf[$s] = hash_func(array($cnt, end($buf), $buf[$s]));
				}
				else
				{
					$buf[$s] = hash_func(array($cnt, $buf[$s-1], $buf[$s]));
				}
				$cnt += 1;
				for($i=0;$i<$delta;$i++)
				{
					$other = bchexdec(hash_func_hex(array($cnt, $salt, $t, $s, $i)));
					$n = bcmod($other, strval($space_cost));
					$cnt += 1;
					$buf[$s] = hash_func(array($cnt, $buf[$s], $buf[$n]));
					$cnt += 1;
				}
			}
		}
		return;
	}

	/*
		*** function balloon_extract($buf) ***
			
		Final step. Return the last value in the buffer.

		Args:
			buf (str array): Array of hashes as bytes.

		Returns:
			str: Last value of the buffer as bytes

	*/
	function balloon_extract($buf)
	{
		return end($buf);
	}

	/*
		*** function balloon($password, $salt, $space_cost, $time_cost, $delta) ***
		
		Main function that collects all the substeps. As previously mentioned, first expand, then mix, and finally extract.

		Args:
			password (str): The main string to hash
			salt (str): A user defined random value for security
			space_cost (int): The size of the buffer
			time_cost (int): Number of rounds to mix
			delta (int): Number of random blocks to mix with.

		Returns:
			str: A series of bytes, the hash.
	*/
	function balloon($password, $salt, $space_cost, $time_cost, $delta)
	{
		$buf = array();
		$buf[0] = hash_func(array(0, $password, $salt));
		$cnt = 1;
		expand($buf, $cnt, $space_cost);
		mix($buf, $cnt, $delta, $salt, $space_cost, $time_cost);
		return balloon_extract($buf);
	}

	/*
		*** function balloon_hash($password, $salt) ***
		
		A more friendly client function that just takes a password and a salt and computes outputs the hash in hex.

		Args:
			password (str): The main string to hash
			salt (str): A user defined random value for security

		Returns:
			str: The hash as hex.
	*/
	function balloon_hash($password, $salt)
	{
		$delta = 5;
		$time_cost = 18;
		$space_cost = 24;
		return bin2hex(balloon($password, $salt, $space_cost, $time_cost, $delta));
	}
?>