<?php

if (!function_exists('site_url')) {
    /**
     * Site URL
     *
     * Create a local URL based on your basepath. Segments can be passed via the
     * first parameter either as a string or an array.
     *
     * @param    string $uri
     * @param bool $appCheck
     * @param    string $protocol
     * @return string
     */
    function site_url($uri = '', $appCheck = true, $protocol = NULL)
    {
        if ($appCheck) {
            $CI = get_instance();
            $segment1 = $CI->uri->segment(1);
            $segment2 = $CI->uri->segment(2);
            if ($segment1 == 'p') {
                return get_instance()->config->site_url($segment1 . '/' . $segment2 . '/' . $uri, $protocol);
            }
        }

        return get_instance()->config->site_url($uri, $protocol);
    }
}

if (!function_exists('redirect')) {
    /**
     * Header Redirect
     *
     * Header redirect in two flavors
     * For very fine grained control over headers, you could use the Output
     * Library's set_header() function.
     *
     * @param    string $uri URL
     * @param bool $appCheck
     * @param    string $method Redirect method
     *            'auto', 'location' or 'refresh'
     * @param    int $code HTTP Response status code
     * @return void
     */
    function redirect($uri = '', $appCheck = true, $method = 'auto', $code = NULL)
    {
        if ($appCheck) {
            if (isset($_SERVER['HTTP_REFERER'])) {
                $root = str_replace(base_url(), "", $_SERVER['HTTP_REFERER']);
                if (!empty($root)) {
                    $segments = explode('/', $root);
                    if (key_exists(0, $segments) && key_exists(1, $segments) && $segments[0] == 'p') {
                        $uri = $segments[0] . '/' . $segments[1] . '/' . $uri;
                    }
                }
            } else {
                $CI = get_instance();
                $segment1 = $CI->uri->segment(1);
                $segment2 = $CI->uri->segment(2);
                if ($segment1 == 'p') {
                    $uri = $CI->config->site_url($segment1 . '/' . $segment2 . '/' . $uri);
                }
            }
        }

        if (!preg_match('#^(\w+:)?//#i', $uri)) {
            $uri = site_url($uri, false);
        }

        // IIS environment likely? Use 'refresh' for better compatibility
        if ($method === 'auto' && isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== FALSE) {
            $method = 'refresh';
        } elseif ($method !== 'refresh' && (empty($code) OR !is_numeric($code))) {
            if (isset($_SERVER['SERVER_PROTOCOL'], $_SERVER['REQUEST_METHOD']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1') {
                $code = ($_SERVER['REQUEST_METHOD'] !== 'GET')
                    ? 303    // reference: http://en.wikipedia.org/wiki/Post/Redirect/Get
                    : 307;
            } else {
                $code = 302;
            }
        }

        switch ($method) {
            case 'refresh':
                header('Refresh:0;url=' . $uri);
                break;
            default:
                header('Location: ' . $uri, TRUE, $code);
                break;
        }
        exit;
    }
}

if (!function_exists('urlencode_rfc3986')) {

    /**
     * Manual encode url based RFC-3986 standard.
     *
     * @param $string
     * @return mixed
     */
    function urlencode_rfc3986($string)
    {
        $entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D');
        $replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]");
        return str_replace($replacements, $entities, $string);
    }
}