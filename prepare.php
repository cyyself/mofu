
<?php
  /*
  Use this to generate index.
  Go to your database and execute this sql:
  
  "ALTER TABLE `your table name` ADD COLUMN `mofuindex` TEXT NULL;"
  
  Then excute this to speed up.
  
  "CREATE FULLTEXT INDEX `idx_mofuindex`  ON `your table name` (mofuindex);"
  
  Now, you can generate index text by this php.
  
  */
	function mofu_prepare($str) {
		$str = mb_strtolower($str);
		for ($i=33;$i<= 47;$i++) $str = str_replace(chr($i),' ',$str);
		for ($i=123;$i<=126;$i++) $str = str_replace(chr($i),' ',$str);
		$retstr = ' ';
		$len = strlen($str);
		for ($i=0;$i<$len-1;$i++) {
			$retstr .= $str[$i];
			if ( (ord($str[$i+1]) & (1 << 7)) != (ord($str[$i]) & (1 << 7)) ) $retstr .= " ";
			else {
				if ((ord($str[$i]) & (1 << 7)) == 0) {
					//ascii, and the next is ascii
					$curr = (ord($str[$i]) >= ord('0') && ord($str[$i]) <= ord('9'));
					$next = (ord($str[$i+1]) >= ord('0') && ord($str[$i+1]) <= ord('9'));
					if ($curr != $next) $retstr .= " ";
				}
			}
		}
		$retstr .= $str[$len-1];
		$retstr .= ' ';
		return $retstr;
	}
?>
