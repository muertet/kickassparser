<?php

class KickAss {
	
	private $downloadsFolder = '/tmp/';
	private $dumpTypes = array(
		'hourly', 'daily'
	);
	
	public function __construct ($downloadFolder = null) {
		if (!empty($downloadFolder)) {
			$this->downloadFolder = $downloadFolder;
		}
	}
	
	/**
	* Gets backup dump from kickass.to
	*
	* @param string $type
	* @param boolean $decompress
	* @param string $content
	*/
	public function getDump ($type = 'hourly', $decompress = false) {
		$this->checkType($type);
		
		$file = $this->getFileRoute($type);
		$content = self::curl('http://kickass.to/'.$type.'dump.txt.gz');
		file_put_contents($file, $content);
		if ($decompress) {
			self::decompress($file);
		}
		return $content;
	}
	
	/**
	* Parses a dump file 
	* 
	* @param string $type
	* @param function $callback function to call on each parsed torrent
	* 
	* @return void
	*/
	public function parse ($type, $callback) {
		$this->checkType($type);

		$file = file_get_contents($this->getFileRoute($type, false));

		preg_match_all("/(.*)\|(.*)\|(.*)\|(.*)\|/", $file, $matches);

		foreach ($matches[0] as $k => $match) {
			$torrent = array(
				'name' => $matches[2][$k],
				'category' => $matches[3][$k],
				'link' => $matches[4][$k],
				'hash' => $matches[1][$k],
			);
			$callback($torrent);
		}
	}
	
	/**
	* Gets file route 
	*
	* @param string $type
	* 
	* @return string
	*/
	public function getFileRoute ($type, $compressed = true) {
		$file = $this->downloadsFolder.$type.'dump.txt';
		if ($compressed) {
			$file .= '.gz';
		}
		return $file;
	}
	
	/**
	* Gets file route 
	*
	* @param string $type
	* 
	* @return void
	* @throws Exception if dump type is invalid
	*/
	private function checkType ($type) {
		if (!in_array($type, $this->dumpTypes)) {
			throw new Exeption ('Invalid type'); 
		}
	}
	
	/**
	 * Decompresses a file 
	 *
	 * @param string $file
	 *
	 * @return void
	*/
	private static function decompress ($file_name) {
		$buffer_size = 4096; // read 4kb at a time
		$out_file_name = str_replace('.gz', '', $file_name);

		// Open our files (in binary mode)
		$file = gzopen($file_name, 'rb');
		$out_file = fopen($out_file_name, 'wb');

		// Keep repeating until the end of the input file
		while(!gzeof($file)) {
			// Read buffer-size bytes
			// Both fwrite and gzread and binary-safe
			fwrite($out_file, gzread($file, $buffer_size));
		}

		// Files are done, close files
		fclose($out_file);
		gzclose($file);
	}
	
	/**
	* Makes curl requests
	* @param string $url
	* @param array $data
	* 
	* @return string
	*/
	private static function curl($url, $data = array()) {
        $ch = curl_init($url);
        $header = array();
        $header[0]  = "Accept: text/xml,application/xml,application/xhtml+xml,";
        $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
        $header[]   = "Cache-Control: max-age=0";
        $header[]   = "Connection: keep-alive";
        $header[]   = "Keep-Alive: 300";
        $header[]   = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $header[]   = "Accept-Language: en-us,en;q=0.5";
        $header[]   = "Pragma: "; // browsers keep this blank.

        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.2; en-US; rv:1.8.1.7) Gecko/20070914 Firefox/2.0.0.7');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        if(sizeof($data) > 0) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $url);

        return curl_exec($ch);
    }
}
