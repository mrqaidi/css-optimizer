<?php
/**
 * CSS prefixer and optimizer.
 *
 * Reads css code, automatically inserts browser-specific prefixes and compresses
 * the code with removing comments, two or more consecutive spaces,
 * newline characters and tabs, spaces, if a curly bracket, colon,
 * semicolon or comma is placed before or after them.
 * There is a possibility of caching the result.
 * It is important to set the correct installation of access rights to the cache directory.
 * This program requires PHP 5.4+
 *
 * @program   CSS prefixer and optimizer.
 * @version   2.0
 * @package   Template
 * @file      css.class.php
 * @author    Victor Nabatov <greenray.spb@gmail.com>
 * @copyright (c) 2016 Victor Nabatov
 * @license   Creative Commons Attribution-ShareAlike 4.0 International Public License
 */

class CSS {

    /** @var boolean TRUE if caching is allowed */
	private $cache = TRUE;

    /** @var string CSS code that is executing */
	private $css = '';

    /**
     * The browser-specific prefixes.
     * This is not a complete list but the most used css properties.
     * So it can easily be extended.
     *
     * @var array
     */
    private $styles = [
        'align-content' => ['-webkit-', ''],
        'align-items'   => ['-webkit-', ''],
        'align-self'    => ['-webkit-', ''],

        'animation'                 => ['-webkit-', '-moz-', '-o-', ''],
        'animation-delay'           => ['-webkit-', '-moz-', '-o-', ''],
        'animation-direction'       => ['-webkit-', '-moz-', '-o-', ''],
        'animation-duration'        => ['-webkit-', '-moz-', '-o-', ''],
        'animation-fill-mode'       => ['-webkit-', '-moz-', '-o-', ''],
        'animation-iteration-count' => ['-webkit-', '-moz-', '-o-', ''],
        'animation-name'            => ['-webkit-', '-moz-', '-o-', ''],
        'animation-play-state'      => ['-webkit-', '-moz-', '-o-', ''],
        'animation-timing-function' => ['-webkit-', '-moz-', '-o-', ''],

        'backface-visibility' => ['-webkit-', '-moz-', '-ms-', ''],

        'background-clip'   => ['-moz-', ''],
        'background-origin' => ['-webkit-', '-moz-', '-o-', ''],
        'background-size'   => ['-webkit-', '-moz-', '-o-', ''],

        'border-image'               => ['-webkit-', '-moz-', '-o-', ''],
        'border-image-outset'        => ['-webkit-', '-moz-', '-o-', ''],
        'border-image-repeat'        => ['-webkit-', '-moz-', '-o-', ''],
        'border-image-source'        => ['-webkit-', '-moz-', '-o-', ''],
        'border-image-width'         => ['-webkit-', '-moz-', '-o-', ''],
        'border-radius'              => ['-webkit-', '-khtml-', '-moz-', ''],
        'border-top-left-radius'     => ['-webkit-', '-khtml-', '-moz-', ''],
        'border-top-right-radius'    => ['-webkit-', '-khtml-', '-moz-', ''],
        'border-bottom-right-radius' => ['-webkit-', '-khtml-', '-moz-', ''],
        'border-bottom-left-radius'  => ['-webkit-', '-khtml-', '-moz-', ''],
        'border-radius-topleft'      => ['-webkit-', '-khtml-', '-moz-', ''],
        'border-radius-topright'     => ['-webkit-', '-khtml-', '-moz-', ''],
        'border-radius-bottomright'  => ['-webkit-', '-khtml-', '-moz-', ''],
        'border-radius-bottomleft'   => ['-webkit-', '-khtml-', '-moz-', ''],

        'box-align'         => ['-webkit-', '-moz-', '-ms-', ''],
        'box-direction'     => ['-webkit-', '-moz-', '-ms-', ''],
        'box-flex'          => ['-webkit-', '-moz-', '-ms-', ''],
        'box-flex-group'    => ['-webkit-', '-moz-', ''],
        'box-lines'         => ['-webkit-', '-moz-', '-ms-', ''],
        'box-ordinal-group' => ['-webkit-', '-moz-', '-ms-', ''],
        'box-orient'        => ['-webkit-', '-moz-', '-ms-', ''],
        'box-pack'          => ['-webkit-', '-moz-', '-ms-', ''],
        'box-shadow'        => ['-webkit-', '-moz-', ''],
        'box-sizing'        => ['-webkit-', '-moz-', ''],

        'calc' => ['-webkit', ''],

        'column-count'        => ['-webkit-', '-moz-', ''],
        'column-fill'         => ['-moz-', ''],
        'column-gap'          => ['-webkit-', '-moz-', ''],
        'column-rule'         => ['-webkit-', '-moz-', ''],
        'column-rule-color'   => ['-webkit-', '-moz-', ''],
        'column-rule-style'   => ['-webkit-', '-moz-', ''],
        'column-rule-width'   => ['-webkit-', '-moz-', ''],
        'column-span'         => ['-webkit-', ''],
        'column-width'        => ['-webkit-', '-moz-', ''],
        'columns'             => ['-webkit-', '-moz-', ''],

        'filter' => ['-webkit-', ''],

        'flex-basis'     => ['-webkit-', ''],
        'flex-direction' => ['-webkit-', ''],

        'fullscreen' => ['-webkit-', '-moz-', '-ms-', ''],

        'hyphens' => ['-webkit-', '-moz-', '-ms-', ''],

        'image-rendering' => ['-webkit-', '-moz-', '-o-', ''],

        'object-fit' => ['-o-', ''],

        'opacity' => ['-khtml-', '-moz-', ''],

        'orient' => ['-moz-', ''],

        'perspective'        => ['-webkit-', '-moz-', ''],
        'perspective-origin' => ['-webkit-', '-moz-', ''],

        'tab-size'  => ['-moz-', '-o-', ''],

        'text-align-last'       => ['-moz-', ''],
        'text-decoration-color' => ['-webkit-', '-moz-', ''],
        'text-decoration-line'  => ['-moz-', ''],
        'text-decoration-style' => ['-moz-', ''],
        'text-overflow'         => ['-ms-', '-o-', ''],

        'transform'        => ['-webkit-', '-moz-', '-ms-', '-o-', ''],
        'transform-origin' => ['-webkit-', '-moz-', '-ms-', '-o-', ''],
        'transform-style'  => ['-webkit-', '-moz-', ''],

        'transition'                 => ['-webkit-', '-moz-', '-o-', ''],
        'transition-delay'           => ['-webkit-', '-moz-', '-o-', ''],
        'transition-duration'        => ['-webkit-', '-moz-', '-o-', ''],
        'transition-property'        => ['-webkit-', '-moz-', '-o-', ''],
        'transition-timing-function' => ['-webkit-', '-moz-', '-o-', ''],

        'linear-gradient' => ['-webkit-', '-moz-', '-o-', ''],
        'radial-gradient' => ['-webkit-', '-moz-', '-o-', ''],

        'repeating-linear-gradient' => ['-webkit-', '-moz-', '-o-', ''],
        'repeating-radial-gradient' => ['-webkit-', '-moz-', '-o-', ''],

        'user-modify' => ['-webkit-', '-moz-', ''],
        'user-select' => ['-webkit-', '-moz-', '-ms-', ''],

        'viewport' => ['-ms-', ''],

        'writing-mode' => ['-webkit-', ''],

        'document'  => ['-moz-', ''],
        'keyframes' => ['-webkit-', '-moz-', '-o-', ''],
        'viewport'  => ['-ms-', ''],

        'placeholder' => ['-webkit-input-', '-moz-', '-ms-input-', ''],
        'selection'   => ['-moz-', '']
    ];

	/**
     * Class constructor.
     *
     * @param boolean $cache Is cache allowed?
     */
	public function __construct($cache = TRUE) {
        $this->css   = '';
        $this->cache = $cache;
    }

    /**
     * Class main method.
     *
     * Handles directive "@import".
     * Generates browser-specific prefixes.
     * Replaces images references with base64_encoded data.
     * Removes unneeded characters, see comments.
     *
     * @param  string $file CSS file
     * @return string       Prefixed and compressed CSS
     */
    public function compress($file) {
        $cached = str_replace('/', '.', $file);
        if ($this->cache === TRUE) {
            $this->css = $this->getFromCache($cached);
        }
        if ($this->css === FALSE) {
            $pathinfo = pathinfo($file);
            $this->css = file_get_contents($file);
            #
            # Processing rule @import
            #
            $this->import($pathinfo['dirname']);
            $this->images();
            #
            # Set the prefixes of browsers
            #
            $this->setPrefixes();
            #
            # Remove two or more consecutive spaces
            #
            $this->css = preg_replace('# {2,}#', '', $this->css);
            #
            # Replace 0[type] values with 0
            #
            $this->css = preg_replace('/([^\\\\]\:|\s)0(?:em|ex|ch|rem|vw|vh|vm|vmin|cm|mm|in|px|pt|pc|%)/iS', '${1}0', $this->css);
            #
            # Replace 0 0; or 0 0 0; or 0 0 0 0; with 0
            #
            $this->css = preg_replace('/\:0(?: 0){1,3}(;|\}| \!)/', ':0$1', $this->css);
            #
            # Remove leading zeros from integer and float numbers preceded by : or a white-space
            # -0.5 to -.5; 1.050 to 1.05
            #
            $this->css = preg_replace('/((?<!\\\\)\:|\s)(\-?)0+(\.?\d+)/S', '$1$2$3', $this->css);
            #
            # Optimize hex colors: #999999 to #999; #ffdd88 to #fd8;
            #
            $this->css = preg_replace('/([^=])#([a-f\\d])\\2([a-f\\d])\\3([a-f\\d])\\4([\\s;\\}])/i', '$1#$2$3$4$5', $this->css);
            #
            # Remove the spaces, if a curly bracket, colon, semicolon or comma is placed before or after them
            #
            $this->css = preg_replace('#\s*([\{:;,]) *#', '$1', $this->css);
                    #
                    # Readable result (remove after testing)
                    #
                    file_put_contents(CACHE.$cached.'.readable.css', $this->css, LOCK_EX);
                    #
                    #
            #
            # Remove newline characters and tabs
            #
            $this->css = str_replace(["\r\n", "\r", "\n", "\t"], '', $this->css);
            #
            # Place the compiled data into cache
            # For clarity, a simple file name is used, but can be applied encoding
            #
            if ($this->cache === TRUE) {
                file_put_contents(CACHE.$cached, $this->css, LOCK_EX);
            }
        }
        return $this->css;
    }

    /**
     * Handles the rule "@import".
     * Recognizes the rules:
     * @import url("dir/style.css");
     * @import url(style.css);
     *
     * @param string $dir CSS file's directory
     */
    private function import($dir) {
        preg_match_all('/\@import url\(([\w\'\"\/.]*)\);/', $this->css, $match);
        if (!empty($match[0])) {
            $match[0] = array_reverse($match[0]);
            $match[1] = array_reverse($match[1]);
            foreach ($match[0] as $key => $import) {
                $this->css = str_replace($match[0][$key], '', $this->css);
                $file      = str_replace(['"', '\''], '', $match[1][$key]);
                $this->css = file_get_contents($dir.DS.$file).PHP_EOL.$this->css;
            }
        }
    }

    /** Replace images references with base64_encoded data. */
    private function images() {
        preg_match_all('/background:(.*?) url\(([\w\'\"\/\.\-]*)\)(.*?);/', $this->css, $match);
        if (!empty($match[2])) {
            foreach ($match[2] as $key => $image) {
                $file      = str_replace(['"', '\'', '../'], '', $match[2][$key]);
                $filetype  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                $file      = file_get_contents($file);
                $encoded   = str_replace($image, 'data:image/'.$filetype.';base64,'.base64_encode($file), $match[2][$key]);
                $this->css = str_replace($image, $encoded, $this->css);
            }
        }
    }

    /** Generates browser-specific prefixes. */
    private function setPrefixes() {
        #
        # Remove comments
        #
        $this->css = preg_replace('#(\/\*).*?(\*\/)#s', '', $this->css);
        $styles = $this->styles;
        $values = [];
        foreach ($styles as $property => $styles) {
            preg_match('/[^-\{]'.$property.'/s', $this->css, $result);
            if (!empty($result)) {
                $values[] = array_unique($result);
            } else {
                #
                # Remove rules unnecessary for further work
                #
                unset($this->styles[$property]);
            }
        }
        $rules  = [];
        $pseudo = [];
        foreach ($values as $value) {
            $pos = strpos($value[0], '@');
            if ($pos === 0) {
                $rules[] = $value[0];
            }
            $pos = strpos($value[0], ':');
            if ($pos === 0) {
                $pseudo[] = $value[0];
                continue;
            }
            $value = trim($value[0]);
            #
            # Search properties from $this->styles list
            #
            preg_match_all('#'.$value.':[a-zA-Z0-9\.\-\#|\d\s][^\}]+?;|[a-zA-Z\-]+: '.$value.'[\S+].+?;#s', $this->css, $keys);
            foreach ($keys[0] as $property) {
                foreach ($this->styles as $style => $prefixes) {
                    if ($style === $value) {
                        $result = '';
                        foreach ($prefixes as $match) {
                            $pos = strpos($property, $value);
                            if ($pos === 0) {
                                $parts = explode(':', $property);
                                $parts[1] = ': '.$parts[1];
                                $parts[0] = $match.$parts[0];
                                $result  .= implode($parts);
                            } else {
                                $parts = explode(':', $property);
                                $parts[0] = $parts[0].':';
                                $parts[1] = trim($parts[1]);
                                $parts[1] = $match.$parts[1];
                                $result  .= implode($parts);
                            }
                        }
                        $this->css = str_replace($property, $result, $this->css);
                    }
                }
            }
        }
        $this->setPrefixesForRules($rules);
        $this->setPrefixesForPseudo($pseudo);
    }

    /**
     * Generates browser-specific prefixes for rules.
     *
     * @param array $rules Array of founded rules in css file
     */
    private function setPrefixesForRules($rules) {
        foreach ($rules as $key => $rule) {
            $rule = str_replace('@', '', $rule);
            preg_match_all('#'.$rule.'[a-zA-Z0-9_\s\{\}\-\;\:\.\"\%\(\)\*\#]+#s', $this->css, $keys);
            foreach ($keys[0] as $property) {
                foreach ($this->styles as $style => $prefixes) {
                    if ($style === $rule) {
                        $result = '';
                        foreach ($prefixes as $match) {
                            $pos = strpos($property, $rule);
                            if ($pos === 0) {
                                $parts    = explode(':', $property);
                                $parts[0] = '@'.$match.$parts[0];
                                $result  .= implode(':', $parts);
                            }
                        }
                        $this->css = str_replace('@'.$property, $result, $this->css);
                    }
                }
            }
        }
    }

    /**
     * Generates browser-specific prefixes for pseudoelements.
     *
     * @param array $pseudo Array of founded rules in css file
     */
    private function setPrefixesForPseudo($pseudo) {
        foreach ($pseudo as $key => $rule) {
            $rule = str_replace(':', '', $rule);
            preg_match_all('#[a-z0-9_\[\]\"\=\:]+'.$rule.'[a-zA-Z0-9_\s\{\-\;\:\.\"\%\(\)\*\#]+\}#s', $this->css, $keys);
            foreach ($keys[0] as $property) {
                foreach ($this->styles as $style => $prefixes) {
                    if ($style === $rule) {
                        $result = '';
                        foreach ($prefixes as $match) {
                            $pos = strpos($property, $rule);
                            if ($pos !== FALSE) {
                                $result .= str_replace($rule, $match.$rule, $property);
                            }
                        }
                        $this->css = str_replace($property, $result, $this->css);
                    }
                }
            }
        }
    }

    /**
	 * Gets a compiled file from the cache.
     *
     * @param  string $file CSS file
     * @return mixed        Data from cache or FALSE
	 */
	private function getFromCache($file) {
        return (file_exists(CACHE.$file)) ? file_get_contents(CACHE.$file) : FALSE;
	}
}
