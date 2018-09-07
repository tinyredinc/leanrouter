<?php

if (PHP_SAPI !== 'cli') {
    exit('hello world!');
}

require_once '../autoload.php';

$start_time = microtime(true);
echo date('Y-m-d H:i:s')." Job Start... \n";

$cdn_path = Config::getConfig('QCLOUD_CONFIG')['CDN_PATH'].'/assets';
$app_path = SYSTEM_DIR;
$assets_path = $app_path.'/www/assets';

$assets_files = getDirFileRecursive($assets_path);

$cdn_driver = new CdnDriver();

foreach($assets_files as $row){
	$local_src = $row;
	$remote_dst = $cdn_path.substr($local_src, strlen($assets_path));
	echo $local_src.' >> '.$remote_dst."\n";
	echo $cdn_driver->uploadFile($local_src, $remote_dst)."\n";
}

function getDirFileRecursive($dir,&$results = array()){
    $files = scandir($dir);
    foreach($files as $key => $value){
        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
        if(!is_dir($path)) {
            $results[] = $path;
        } else if($value != "." && $value != "..") {
            getDirFileRecursive($path, $results);
            if(!is_dir($path)){
				$results[] = $path;
			}
        }
    }
    return $results;
}

echo date('Y-m-d H:i:s')." Job End... \n";
$end_time = microtime(true);
$runtime = $end_time - $start_time;
$mem_usage = memory_get_peak_usage()/1024/1024;
echo "Cron Runtime: [$runtime], Peak Mem Usage: [$mem_usage]Mb\n";