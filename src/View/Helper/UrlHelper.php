<?php
/*
 * @copyright (C) 2018 Michiel Keijts
 * 
 */

namespace MKCDN\View\Helper;

use Cake\View\Helper\UrlHelper as BaseUrlHelper;
use Cake\Routing\Router;
use Cake\Core\Configure;

/**
 * Enables CDN content distribution over various servers.
 * Your static (default all content with extension) is distributed over
 * the various servers defined in the config file
 *
 * @author Michiel Keijts
 */
class UrlHelper extends BaseUrlHelper {
	/**
     * Generates URL for given asset file.
     *
     * Depending on options passed provides full URL with domain name. Also calls
     * `Helper::assetTimestamp()` to add timestamp to local files.
	 * 
	 * This function now distributes among CDN files
     *
     * @param string|array $path Path string or URL array
     * @param array $options Options array. Possible keys:
     *   `fullBase` Return full URL with domain name
     *   `pathPrefix` Path prefix for relative URLs
     *   `ext` Asset extension to append
     *   `plugin` False value will prevent parsing path as a plugin
     * @return string Generated URL
     */
	public function assetUrl($path, array $options = array()) 
	{
		if (is_array($path) || !$this->isAsset($path, $options))
			return parent::assetUrl($path, $options);
		
		$default = ['fullBase' => FALSE];
		$options = $options + $default;
		
		$options['fullBase'] = FALSE;
        $path = parent::assetUrl($path, $options);
		
		return $this->getUrl($path, $options);		
	}
	
	/**
	 * Url Generator
	 * @param type $url
	 * @param bool $fullbase
	 * @return string formatted url
	 */
	protected function getUrl($url, array $options = []):string
	{
		$url = Router::url($url, $options['fullBase']);
		if (Configure::read('debug') || !$this->isAsset($url, $options))
			return $url;
		
		return $this->getWithBase(Router::url($url, FALSE));
	}
	
	/**
	 * Determines if the requested url is an asset (not .php, not .html)
	 * @param type $url
	 * @return bool
	 */
	protected function isAsset(string $url, array $options = []):bool
	{
		$ext = strtolower(trim($options['ext'] ?? substr($url, strrpos($url, '.') + 1), '.'));
		
		return $ext !== 'php' && $ext !== 'htm' && $ext !== 'html';
	}
	
	/**
	 * Returns the base for this url
	 * @param string $url
	 * @return string
	 */
	protected function getWithBase(string $url):string
	{
		$index = $this->getIndex($url);
		
		if (!Configure::read('MKCDN.autoConfig.enabled'))
			return Configure::read('MKCDN.servers.' . $index);
		
		return sprintf(Configure::read('MKCDN.autoConfig.serverTemplate'), $index, $url);
	}
	
	/**
	 * Get an index for
	 * @param string $url
	 * @return string
	 */
	protected function getIndex(string $url):string
	{
		return  $this->getHash($url) % $this->getNrOfServers();
	}
	
	/**
	 * Get an hash for
	 * @param string $url
	 * @return string
	 */
	protected function getHash(string $url):string
	{
		return crc32($url);
	}
	
	/**
	 * Returns the number of servers available
	 * @return int
	 */
	protected function getNrOfServers():int
	{
		if (Configure::read('MKCDN.autoConfig.enabled')) {
			return Configure::read('MKCDN.autoConfig.end') - Configure::read('MKCDN.autoConfig.start') + 1;
		}
		
		$list =  Configure::read('MKCDN.servers');
		return count($list);
	}
}
