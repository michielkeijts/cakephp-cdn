<?php
/*
 * @copyright (C) 2018 Michiel Keijts
 * 
 */

namespace MKCDN\View\Helper;

use Cake\View\Helper\UrlHelper as BaseUrlHelper;
use Cake\Routing\Router;
/**
 * Enables CDN content distribution over various servers.
 * Your static (default all content with extension) is distributed over
 * the various servers defined in the config file
 *
 * @author Michiel Keijts
 */
class UrlHelper extends BaseUrlHelper {
    /**
     * Returns a URL based on provided parameters.
     *
     * ### Options:
     *
     * - `escape`: If false, the URL will be returned unescaped, do only use if it is manually
     *    escaped afterwards before being displayed.
     * - `fullBase`: If true, the full base URL will be prepended to the result
     *
     * @param string|array|null $url Either a relative string URL like `/products/view/23` or
     *    an array of URL parameters. Using an array for URLs will allow you to leverage
     *    the reverse routing features of CakePHP.
     * @param array|bool $options Array of options; bool `full` for BC reasons.
     * @return string Full translated URL with base path.
     */
    public function build($url = null, $options = false)
    {
        $defaults = [
            'fullBase' => false,
            'escape' => true,
        ];
        if (!is_array($options)) {
            $options = ['fullBase' => $options];
        }
        $options += $defaults;

        $url = $this->getUrl($url, $options['fullBase']);
        if ($options['escape']) {
            $url = h($url);
        }

        return $url;
    }
	
	/**
	 * Url Generator
	 * @param type $url
	 * @param bool $fullbase
	 * @return string formatted url
	 */
	protected function getUrl($url, bool $fullbase):string
	{
		$url = Router::url($url, $fullbase);
		if (Configure::read('debug') || !$this->isAsset($url))
			return $url;
		
		return $this->getWithBase(Router::url($url, FALSE));
	}
	
	/**
	 * Determines if the requested url is an asset
	 * @param type $url
	 * @return bool
	 */
	protected function isAsset(string $url, $options):bool
	{
		$ext = $options['ext'] ?? substr($url, 0, strrpos($url, '.'));
		
		return preg_match("/jpe?g|mpe?g|gif|png|ico|cur|gz|svg|svgz|mp4|ogg|ogv|webm|htc|css|js|eot|woff|woff2|ttf|css|scss/i", $url) >= 1;
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
