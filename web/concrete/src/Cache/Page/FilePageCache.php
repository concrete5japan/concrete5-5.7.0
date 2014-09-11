<?php

namespace Concrete\Core\Cache\Page;
use \Page as ConcretePage;
use Loader;

class FilePageCache extends PageCache {

	public function getRecord($mixed) {
		$file = $this->getCacheFile($mixed);
		if (file_exists($file)) {
			$contents = file_get_contents($file);
			$record = @unserialize($contents);
			if ($record instanceof PageCacheRecord) {
				return $record;
			}
		}
	}

	protected function getCacheFile($mixed) {
		$key = $this->getCacheKey($mixed);
		$filename = $key;
        
		if(isset($_SERVER['QUERY_STRING']) && strpos($_SERVER['QUERY_STRING'], "ccm_paging_p_b") !== false){
			$filename .= $_SERVER['QUERY_STRING'];
		}
        $filename = sha1($filename);

		$md = new \Mobile_Detect();
		if($md->isMobile()){
			$filename = $filename . '.cachem';
		}else{
			$filename = $filename . '.cachep';
		}
		
		if ($key) {
			if (strlen($key) == 1) {
				$dir = DIR_FILES_PAGE_CACHE . '/' . $key;
			} else if (strlen($key) == 2) {
				$dir = DIR_FILES_PAGE_CACHE . '/' . $key[0] . '/' . $key[1];
			} else {
				$dir = DIR_FILES_PAGE_CACHE . '/' . $key[0] . '/' . $key[1] . '/' . $key[2];
			}
			if ($dir && (!is_dir($dir))) {
				@mkdir($dir, DIRECTORY_PERMISSIONS_MODE, true);
			}
			$path = $dir . '/' . $filename;
			return $path;
		}
	}

	public function purgeByRecord(\Concrete\Core\Cache\Page\PageCacheRecord $rec) {
		$file = $this->getCacheFile($rec);
		if ($file && file_exists($file)) {
			@unlink($file);
		}
	}

	public function flush() {
		$fh = Loader::helper('file');
		$fh->removeAll(DIR_FILES_PAGE_CACHE);
	}

	public function purge(ConcretePage $c) {
		$file = $this->getCacheFile($c);
		if ($file && file_exists($file)) {
			@unlink($file);
		}
	}

	public function set(ConcretePage $c, $content) {
		if (!is_dir(DIR_FILES_PAGE_CACHE)) {
			@mkdir(DIR_FILES_PAGE_CACHE);
			@touch(DIR_FILES_PAGE_CACHE . '/index.html');
		}
		$lifetime = $c->getCollectionFullPageCachingLifetimeValue();
		$file = $this->getCacheFile($c);
		if ($file) {
			$response = new PageCacheRecord($c, $content, $lifetime);
			if ($content) {
				file_put_contents($file, serialize($response));
			}
		}
	}


}
