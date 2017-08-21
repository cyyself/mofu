<?php
	error_reporting(0);
	function mofu($query) {
		$db = new PDO('mysql:host=your_db_host;dbname=your_data_base;charset=utf8', 'your_db_username', 'your_db_password');
		$keyarr = explode(' ',trim($query));
		$result = array();
		foreach ($keyarr as $key) {
			$subkey = array();
			$keylen = mb_strlen($key);
			if ($keylen == 0) continue;
			//构造subkey
			if ($keylen == strlen($key)) {
				//纯ASCII字符
				if ($keylen <= 5) {
					$subkey[0] = $key;
					$subkey[1] = ' ' . $key . ' ';
				}
				else for ($i=0;$i<=$keylen-4;$i++) $subkey[$i] = mb_substr($key,$i,4);
			}
			else {
				//非纯ASCII字符
				if ($keylen <= 3) $subkey[0] = $key;//完全匹配
				else for ($i=0;$i<=$keylen-2;$i++) $subkey[$i] = mb_substr($key,$i,2);//2字分词模糊匹配
			}
			//精确
			$stmt = $db->prepare("SELECT `id` FROM `your_table_name` WHERE mofuindex LIKE :l");
			$stmt->execute(array(':l' => '%' . $key . '%'));
			$sqlresult = $stmt->fetchAll();
			foreach($sqlresult as $row) $result[$row['oihid']] ++;
			//准备模糊匹配
			$curweightc = array();
			$curweightt = array();
			foreach ($subkey as $eachsubkey) {
				//模糊
				$stmt = $db->prepare("SELECT `id` FROM your_table_name WHERE mofuindex LIKE :l");
				$stmt->execute(array(':l' => '%' . $eachsubkey . '%'));
				$sqlresult = $stmt->fetchAll();
				foreach($sqlresult as $row) $curweightt[$row['id']] ++;
			}
			foreach ($curweightc as $key => $row) $result[$key] += pow($row/count($subkey),2);	
			foreach ($curweightt as $key => $row) $result[$key] += pow($row/count($subkey),2);
		}
		arsort($result);
		return $result;
	}
  /*
Then you get a weight sorted array from function "mofu", you can do everything to output by the id you get.
Hope you enjoy this new way to build search on your website.
*/
?>
