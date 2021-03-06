<?php

// Progress bar for curl download
function progress_bar($download_size, $downloaded_size, $upload_size = null, $uploaded_size = null)
{
	static $previousProgress = 0;
 
	if ($download_size == 0)
		$progress = 0;
	else
		$progress = round($downloaded_size * 100 / $download_size);
    
	if ( $progress > $previousProgress){
		$previousProgress = $progress;
		$pct=(double)($progress/100);
		$bar=round($pct * 30);
		$pct_disp=number_format($pct * 100, 0);
		$status_bar="\r[";
		$status_bar.=str_repeat("|", $bar);

		if($bar < 30){
			$status_bar.=">";
			$status_bar.=str_repeat("-", 30 - $bar);
		} else {
			$status_bar.="|";
		}

		$status_bar.="]  $pct_disp%";

		echo $status_bar;
		ob_flush();
		flush();

		if($progress == 100) {
			echo "\n";
		}
	}
}

// Download a file from given url
function get_file_from_url($file, $url) {
	$chfile = fopen($file, 'w');
	$ch = curl_init();
	$ch_vers = curl_version();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	//curl_setopt($ch, CURLOPT_TIMEOUT, 3600);
	curl_setopt($ch, CURLOPT_NOPROGRESS, false);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, 'curl/' . $ch_vers['version'] );
	curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, 'progress_bar' );
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FILE, $chfile );
	curl_exec($ch);
	curl_close($ch);
	fclose($chfile);
}

// get a json array of the contents of gihub repo
function get_content_from_github($repo, $file){
	$ch = curl_init();
	$ch_vers = curl_version();
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_TIMEOUT, 30);
   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_USERAGENT, 'curl/' . $ch_vers['version'] );
   curl_setopt($ch, CURLOPT_URL, $repo);
   $content = curl_exec($ch);
   curl_close($ch);
   if (!empty($content))
   	file_put_contents($file, $content);
}

// Compare the github sha value of a file
function file_check_sha($file, $sha){
	$size = filesize($file);
	$handle = fopen($file, "rb");
	$contents = fread($handle, $size);
	fclose($handle);
	$str = "blob ".$size."\0".$contents;
	$sha_file = sha1($str);
	$return = ($sha_file == $sha) ? true : false;
	return $return;
}
?>