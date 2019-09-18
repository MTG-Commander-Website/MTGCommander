<?php

namespace com\cminds\rssaggregator\plugin\helpers;

use com\cminds\rssaggregator\App;
use com\cminds\rssaggregator\plugin\misc\Misc;

class SimplePieHelper {

    public static function loadLibrary() {
        if (!class_exists('SimplePie', false)) {
            require_once( ABSPATH . WPINC . '/class-simplepie.php' );
        }
        require_once( ABSPATH . WPINC . '/class-wp-feed-cache.php' );
        require_once( ABSPATH . WPINC . '/class-wp-feed-cache-transient.php' );
        require_once( ABSPATH . WPINC . '/class-wp-simplepie-file.php' );
        require_once( ABSPATH . WPINC . '/class-wp-simplepie-sanitize-kses.php' );
        require_once plugin_dir_path(App::PLUGIN_FILE) . 'plugin/ksesmess/cmri-class-wp-simplepie-sanitize-kses.php';
    }

    public static function getNamespaces() {
        return array_keys(static::getNamespacesAssoc());
    }

    public static function getNamespacesAssoc() {
        return [
            'SIMPLEPIE_NAMESPACE_RSS_20' => 'RSS 2.0 (Blank. RSS 2.0 doesn\'t have a namespace.)',
            'SIMPLEPIE_NAMESPACE_XML' => 'XML (http://www.w3.org/XML/1998/namespace)',
            'SIMPLEPIE_NAMESPACE_ATOM_10' => 'ATOM 1.0 (http://www.w3.org/2005/Atom)',
            'SIMPLEPIE_NAMESPACE_ATOM_03' => 'ATOM 0.3 (http://purl.org/atom/ns#)',
            'SIMPLEPIE_NAMESPACE_RDF' => 'RDF (http://www.w3.org/1999/02/22-rdf-syntax-ns#)',
            'SIMPLEPIE_NAMESPACE_RSS_090' => 'RSS 0.90 (http://my.netscape.com/rdf/simple/0.9/)',
            'SIMPLEPIE_NAMESPACE_RSS_10' => 'RSS 1.0 (http://purl.org/rss/1.0/)',
            'SIMPLEPIE_NAMESPACE_RSS_10_MODULES_CONTENT' => 'RSS 1.0 MODULES CONTENT (http://purl.org/rss/1.0/modules/content/)',
            'SIMPLEPIE_NAMESPACE_DC_10' => 'DC 1.0 (http://purl.org/dc/elements/1.0/)',
            'SIMPLEPIE_NAMESPACE_DC_11' => 'DC 1.1 (http://purl.org/dc/elements/1.1/)',
            'SIMPLEPIE_NAMESPACE_W3C_BASIC_GEO' => 'W3C BASIC GEO (http://www.w3.org/2003/01/geo/wgs84_pos#)',
            'SIMPLEPIE_NAMESPACE_GEORSS' => 'GEORSS (http://www.georss.org/georss)',
            'SIMPLEPIE_NAMESPACE_MEDIARSS' => 'MEDIARSS (http://search.yahoo.com/mrss/)',
            'SIMPLEPIE_NAMESPACE_MEDIARSS_WRONG' => 'MEDIARSS WRONG (http://search.yahoo.com/mrss)',
            'SIMPLEPIE_NAMESPACE_ITUNES' => 'ITUNES (http://www.itunes.com/dtds/podcast-1.0.dtd)',
            'SIMPLEPIE_NAMESPACE_XHT' => 'XHTML (http://www.w3.org/1999/xhtml)',
	        'http://base.google.com/ns/1.0' => 'BASE GOOGLE 1.0 (http://base.google.com/ns/1.0)'
        ];
    }

    public static function resolveNamespace($namespace) {
        return defined($namespace) ? constant($namespace) : $namespace;
    }

    public static function getFirstImgSrc($item, $feedUrl) {
	    $src = '';
        if ( ! ( $item instanceof \SimplePie_Item ) ) {
        	return $src;
        }
	    $data = $item->get_item_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'thumbnail');
        if (is_array($data) && isset($data[0]['attribs'])) {
            $data = array_shift($data[0]['attribs']);
            if (is_array($data) && isset($data['url'])) {
                $src = $data['url'];
            }
        } else {
	        $enclosure = $item->get_enclosure(0);
	        if( !empty( $enclosure ) && !empty( $enclosure->link ) ){
		        $src = $enclosure->link;
	        }
        }
        if (!preg_match('/^http|^\/\//', $src)) {
            $src = Misc::getImgSrc($item->get_description());
        }
        // last chance (e.g. google news)
        if (!preg_match('/^http|^\/\//', $src)) {
            $src = Misc::getImgSrc(var_export($item->data, 1));
        }
        if (!empty($src) && !preg_match('/^http|^\/\//', $src)) {
            $res = parse_url($feedUrl);
            if ($res === FALSE) {
                return;
            }
            if (!$res['scheme']) {
                $res['scheme'] = 'http';
            }
            if (strpos($src, '/') !== 0) {
                $src = '/' . $src;
            }
            $src = sprintf('%s://%s%s', $res['scheme'], $res['host'], $src);
        }

        return $src;
    }

}
