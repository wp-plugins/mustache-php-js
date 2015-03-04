<?php
/**
 * MustachePhpJs - for easy sharing of mustache templates between php and js for logic-less templates.
 * Written by David Ajnered
 */
namespace MustachePhpJs;

class MustachePhpJs
{
    /**
     * @var Mustache_Engine
     */
    private $engine;

    /**
     * @var bool
     */
    private $capturing;

    /**
     * @var string
     */
    private $templateName;

    /**
     * @var string
     */
    private $template;

    /**
     * @var array
     */
    private $partials = array();

    /**
     * Singleton.
     *
     * @return PerfectExcerpt
     */
    public static function getObject()
    {
        static $instance;

        if (!$instance) {
            $instance = new MustachePhpJs();
        }

        return $instance;
    }

    /**
     * Constructor.
     */
    private function __construct()
    {
        $this->loadPhpEngine();
        $this->loadJsEngine();
    }

    /**
     * Load mustache PHP engine.
     */
    private function loadPhpEngine()
    {
        require_once(plugin_dir_path(__FILE__) . 'lib/mustache-php/Mustache/Autoloader.php');
        \Mustache_Autoloader::register();
        $this->engine = new \Mustache_Engine();
    }

    /**
     * Enqueue mustache.js.
     */
    private function loadJsEngine()
    {
        wp_enqueue_script('mustache-php-js', plugin_dir_url(__FILE__) . 'lib/mustache-js/mustache.js');
    }

    /**
     * Start buffering template.
     */
    public function capture()
    {
        if ($this->capturing) {
            trigger_error('mustache output buffer already initialized');
            return false;
        }

        $this->capturing = true;

        ob_start();
    }

    /**
     * Set the template directly.
     *
     * @param string $tmplPath
     */
    public function setTmpl($tmplPath)
    {
        if (file_exists($tmplPath)) {
            $this->template = file_get_contents($tmplPath);
        }
    }

    /**
     * Get template wrapped in script tags for mustache.js.
     */
    public function getScript($templateName = '', $template = '', $class = 'mustacheTmpl')
    {
        // If not passed as parameters
        if (!$templateName || !$template) {
            $templateName = $this->templateName;
            $template = $this->template;
        }
        echo '<script id="' . $templateName . '" class="' . $class . '" type="x-tmpl-mustache">';
        echo $template;
        echo '</script>';
    }

    /**
     * Get scripts for both main template and partials.
     */
    public function getScripts()
    {
        // Main template script
        $this->getScript();

        // Add partial scripts
        foreach ($this->partials as $partialName => $partial) {
            $this->getScript($partialName, $partial, 'partialTmpl');
        }
    }

    /**
     * Stop output buffer and render template.
     *
     * @param string templateName
     * @param array $templateData
     */
    public function render($templateName, $templateData)
    {
        $this->templateName = $templateName;

        // Capture partial
        // Template might have been set directly making this step unnecessary
        if ($this->capturing == true) {
            // Get template and clean output buffer
            $this->template = ob_get_clean();

            // Disable lock and enable rendering of another template
            $this->capturing = false;
        }

        echo $this->engine->render($this->template, $templateData);
    }

    /**
     * Render template from file.
     *
     * @param string $templateFile
     * @param array $templateData
     */
    public function renderFile($templateFile, $templateData)
    {
        if (file_exists($templateFile)) {
            // Load template from file
            $this->template = file_get_contents($templateFile);

            echo $this->engine->render($this->template, $templateData);
        }
    }

    /**
     * Stop output buffer and save partial
     *
     * @param string $templateName
     */
    public function setPartial($templateName, $template = null)
    {
        $this->templateName = $templateName;

        // Capture partial
        if ($this->capturing == true) {
            $this->template = ob_get_clean();
            $this->partials[$templateName] = $this->template;

            // Disable lock and enable rendering of another template
            $this->capturing = false;
        }

        // Set partial directly
        else if ($template != null) {
            $this->partials[$templateName] = $template;
        }

        if (!empty($this->partials)) {
            // Set partials in mustache engine
            $this->engine->setPartials($this->partials);
        }

        // For chaining
        return $this;
    }
}
