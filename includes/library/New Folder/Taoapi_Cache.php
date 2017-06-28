<?php
/**
 * Api Cache System
 * 
 * @author Taoapi.com
 *
 */
class Taoapi_Cache
{
    //缓存路径
    private $_CachePath;

    //缓存时间
    private $_cachetime = 0;

    //API名称
    private $_method = "";

    //是否自动清除缓存
    private $_ClearCache = 0;
    
    public function __construct()
    {
        $Taoapi_Config = Taoapi_Config::Init();
        $this->setCacheTime($Taoapi_Config->getConfig()->Cache);
        $this->setCachePath($Taoapi_Config->getConfig()->CachePath);
    }

    public function setMethod ($method)
    {
        $this->_method = $method;
    }

    /**
     * @return Taoapi_Cache
     */
    public function setCacheTime ($time)
    {
        $this->_cachetime = intval($time);
        return $this;
    }

    /**
     * @return Taoapi_Cache
     */
    public function setClearCache ($num)
    {
        $this->_ClearCache = intval($num);

        return $this;
    }

    /**
     * @return Taoapi_Cache
     */
    public function setCachePath ($CachePath)
    {
        $this->_CachePath = substr($CachePath, - 1, 1) == '/' ? $CachePath : $CachePath . '/';
        return $this;
    }

    public function saveCacheData ($id, $result)
    {
        $idkey = substr($id,0,2);
        
        if ($this->_cachetime) {
            if (! is_dir($this->_CachePath)) {
                mkdir($this->_CachePath);
            }
            if (! is_dir($this->_CachePath . $this->_method)) {
                mkdir($this->_CachePath . $this->_method);
            }
            if (! is_dir($this->_CachePath . $this->_method.'/'.$idkey)) {
                mkdir($this->_CachePath . $this->_method.'/'.$idkey);
            }
            $filepath = $this->_CachePath . $this->_method.'/'.$idkey;
            if (is_dir($filepath)) {
                $filename = $filepath . '/' . $id . '.cache';
                @file_put_contents($filename, $result);
            }
        }
    }

	public function autoClearCache($path ='')
	{
		$path = $path ? $path : $this->_CachePath;

		if($this->_cachetime)
		{
			if(!is_dir($path))
			{
				return false;
			}
			
			if($fdir = opendir($path))
			{
				$old_cwd = getcwd();
				chdir($path);
				$path = getcwd().'/';
				while(($file = readdir($fdir)) !== false)
				{
					if(in_array($file,array('.','..')))
					{
						continue;
					}

					if(is_dir($path.$file))
					{
						$this->autoClearCache($path.'/'.$file.'/'); 
					}else{
						$filetime = date('U', filemtime($path.$file));
						$cachetime = $this->_cachetime * 60 * 60;
						if ($this->_cachetime != 0 && (time() - $filetime) > $cachetime) {
								@unlink($path.$file);
						}
					}
				}				
				closedir($fdir);
				chdir($old_cwd);
			}
		}

	}

    public function clearCache ($id = null)
    {
        if ($id) {
            $filename = $this->_CachePath . $this->_method . '/' . $id . '.cache';
            unlink($filename);
        } else {
            $dir = $this->_CachePath . $this->_method . '/';
            if (is_dir($dir)) {
                if ($dh = opendir($dir)) {
                    while (($file = readdir($dh)) !== false) {
                        if (is_dir($dir . $file)) {
                            continue;
                        }
                        unlink($dir . $file);
                    }
                    closedir($dh);
                }
            }
        }
    }
    public function getCacheData ($id)
    {
		if(rand(1,$this->_ClearCache) == 1)
		{
			$this->autoClearCache();
		}

        $idkey = substr($id,0,2);
        $filename = $this->_CachePath . $this->_method . '/' . $idkey .'/'. $id . '.cache';
        if ($this->_cachetime) {
            if (file_exists($filename)) {
                $filetime = date('U', filemtime($filename));
                $cachetime = $this->_cachetime * 60 * 60;
                if ($this->_cachetime != 0 && (time() - $filetime) > $cachetime) {
                    return false;
                }
                return @file_get_contents($filename);
            }
        }
        return false;
    }
}