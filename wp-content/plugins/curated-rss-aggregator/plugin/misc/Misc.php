<?php

namespace com\cminds\rssaggregator\plugin\misc;

class Misc {

    public static function update_term_meta_array($term_id, $key, $value) {
        $meta = get_term_meta($term_id, $key);
        foreach ($value as $item) {
            if (!in_array($item, $meta)) {
                add_term_meta($term_id, $key, $item);
            }
        }
        foreach ($meta as $item) {
            if (!in_array($item, $value)) {
                delete_term_meta($term_id, $key, $item);
            }
        }
    }

    public static function getImgSrc($s) {
        if (preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $s, $matches)) {
            return $matches[1];
        }
        return NULL;
    }

    public static function invisibleChunkSplit($s, $i = 20) {
        return chunk_split($s, $i, '​'); // U+200B - ZERO WIDTH SPACE
    }

    public static function anyToLowercase($x) {
        if (is_array($x)) {
            foreach ($x as $k => $v) {
                $x[$k] = static::anyToLowercase($v);
            }
            return $x;
        }
        return mb_strtolower($x);
    }

}
