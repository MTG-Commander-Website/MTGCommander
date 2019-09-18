<?php

namespace com\cminds\rssaggregator\plugin\misc;

class SimplePieXMLNamespaces {

    public static function GetSupported() {
        return array(
            'SIMPLEPIE_NAMESPACE_RSS_20',
            'SIMPLEPIE_NAMESPACE_XML',
            'SIMPLEPIE_NAMESPACE_ATOM_10',
            'SIMPLEPIE_NAMESPACE_ATOM_03',
            'SIMPLEPIE_NAMESPACE_RDF',
            'SIMPLEPIE_NAMESPACE_RSS_090',
            'SIMPLEPIE_NAMESPACE_RSS_10',
            'SIMPLEPIE_NAMESPACE_RSS_10_MODULES_CONTENT',
            'SIMPLEPIE_NAMESPACE_DC_10',
            'SIMPLEPIE_NAMESPACE_DC_11',
            'SIMPLEPIE_NAMESPACE_W3C_BASIC_GEO',
            'SIMPLEPIE_NAMESPACE_GEORSS',
            'SIMPLEPIE_NAMESPACE_MEDIARSS',
            'SIMPLEPIE_NAMESPACE_MEDIARSS_WRONG',
            'SIMPLEPIE_NAMESPACE_ITUNES',
            'SIMPLEPIE_NAMESPACE_XHTML'
        );
    }

}
