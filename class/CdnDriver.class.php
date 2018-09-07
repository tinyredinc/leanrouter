<?php
/**
 * CdnDriver - Implement Tencent COS as CDN storage 
 * User: Samuel Zhang
 * Date: 2018-05-12
 */

 
class CdnDriver
{
	
	protected $cos_client;
	protected $cos_bucket;
	protected $cdn_site;
	
	
	public function __construct($cdn_config = false) {
		
		if(!$cdn_config){
			$cdn_config = Config::getConfig('QCLOUD_CONFIG');
		}
		
		$this->cos_client = new Qcloud\Cos\Client(array(
			'region' => $cdn_config['COS_REGION'],
			'credentials' => array(
				'secretId' => $cdn_config['SECRET_ID'],
				'secretKey' => $cdn_config['SECRET_KEY'],
			)
		));
		$this->cos_bucket = $cdn_config['COS_BUCKET'];
		$this->cdn_site = $cdn_config['CDN_SITE'];
	}
	
	
	public function uploadFile($local_src, $remote_dst){
		$remote_dst = preg_replace('%^/%','',$remote_dst);
		try{
			$result = $this->cos_client->putObject(array(
				'Bucket' => $this->cos_bucket,
				'Key' => $remote_dst,
				'Body' => fopen($local_src, 'r'),
			));
		}catch(\Exception $e){
			return false;
		}
		#return $result;
		return $this->cdn_site.$remote_dst;
	}
	
	public function uploadData($str_data, $remote_dst){
		$remote_dst = preg_replace('%^/%','',$remote_dst);
		try{
			$result = $this->cos_client->putObject(array(
				'Bucket' => $this->cos_bucket,
				'Key' => $remote_dst,
				'Body' => $str_data,
			));
		}catch(\Exception $e){
			return false;
		}
		#return $result;
		return $this->cdn_site.$remote_dst;
	}
	
	public function listPath($remote_path){
		$objects = array();
		$remote_path = preg_replace('#^/#','',$remote_path);
		try{
			$result = $this->cos_client->listObjects(array(
				'Bucket' => $this->cos_bucket,
				'Prefix' => $remote_path,
			));
		}catch(\Exception $e){
			return false;
		}
		if(isset($result['Contents']) && !empty($result['Contents'])){
			foreach ($result['Contents'] as $object){
				$objects[] = array (
					'object_name' => basename($object['Key']),
					'object_folder' => '/'.str_replace(basename($object['Key']),'',$object['Key']),
					'object_path' => '/'.$object['Key'],
					'object_size' => $object['Size'],
					'object_modify' => date('Y-m-d H:i:s',strtotime($object['LastModified'])),
					'object_url' => $this->cdn_site.$object['Key'],
				);
			}
		}
		return $objects;
	}
	
	public function deleteFile($remote_dst){
		$remote_dst = preg_replace('#^/#','',$remote_dst);
		try{
			$result = $this->cos_client->deleteObject(array(
				'Bucket' => $this->cos_bucket,
				'Key' => $remote_dst,
			));
		}catch(\Exception $e){
			return false;
		}
		return $result;
	}
	
	public function deletePath($remote_path){
		$result = array();
		$objects = $this->listPath($remote_path);
		if($objects && count($objects)>0){
			foreach($objects as $obj){
				$result[$obj['object_path']] = $this->deleteFile($obj['object_path']);
			}
		}
		return $result;
	}
	
}