<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('tab') ) {
    function tab($nums = 1, $input = "") {
        return str_pad($input, $nums, "\t");
    }
}

if ( ! function_exists('is_active') ) {
    function is_active( $method = '', $class = "active" ) {
        return get_instance()->router->method == $method ? $class : '';
    }
}

if ( ! function_exists('metadata'))
{
    function metadata( $name, $content ) {
		$tag = tab() . '<meta name="' . $name . '" content="'. (is_array($content) ? implode(", ", $content) : $content) .'">' . PHP_EOL;
		return $tag;
	}
}

if ( ! function_exists('style'))
{
    function style( $file, $props = [], $inline = false ) {

		$file = ( strpos( $file, '.css' ) !== false ) ? $file : $file . '.css';

		if ( ! $inline ) {
			$tag = tab() . '<link rel="stylesheet" href="' . $file . '" type="text/css" />' . PHP_EOL;
		} else {
			$tag = '<style>' . PHP_EOL;
			$tag .= file_get_contents( $file );
			$tag .= '</style>' . PHP_EOL;
		}

		return $tag;
	}
}

if ( ! function_exists('script'))
{
    function script( $file, $props = [] ) {

		$file = ( strpos( $file, '.js' ) !== false ) ? $file : $file . '.js';

		$tag = '<script src="' . $file . '"';
		
		if ( count( $props ) ) {
			$tag .= ' ' . str_replace("=", '="', http_build_query($props, null, '" ', PHP_QUERY_RFC3986)).'"';
		}

		$tag .= '></script>' . PHP_EOL;

		return $tag;
	}
}

if ( ! function_exists('model'))
{
    function model( $model = [], $model_name = 'Zooalist' ) {
		$tag = '<!-- Model -->' . PHP_EOL;
        $tag .= '<script>' . PHP_EOL;
		$tag .= tab() . 'var ' . $model_name . ' = ' . $model_name . ' || {};' . PHP_EOL;
		$tag .= tab() . $model_name . '.base_url = "' . base_url() . '";' . PHP_EOL;
		$tag .= tab() . $model_name . '.model = ' . json_encode( (object) $model ) . ';' . PHP_EOL;
        $tag .= '</script>' . PHP_EOL;
        return $tag;
    }
}