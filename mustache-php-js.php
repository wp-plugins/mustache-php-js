<?php
/**
 * Plugin Name: Mustache Php Js
 * Version: 1
 * Plugin URI: http://davidajnered.com
 * Description: Easy sharing of mustache templates between php and js for logic-less templates.
 * Author: David Ajnered
 */
require_once('MustachePhpJs.php');

/**
 * Initialize plugin
 */
add_action('init', function () {

    /**
     * Callable function from template.
     *
     * @return MustachePhpJs
     */
    function mustache()
    {
        return MustachePhpJs\MustachePhpJs::getObject();
    }
});
