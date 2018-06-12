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

        $url = Router::url($url, $options['fullBase']);
        if ($options['escape']) {
            $url = h($url);
        }

        return $url;
    }
	
	protected function get() {}
}
