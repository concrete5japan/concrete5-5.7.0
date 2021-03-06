<?php

namespace Concrete\Core\Config;

class Renderer {

    protected $config = null;

    public function __construct(array $config) {
        $this->config = $config;
    }

    public function render($eol = PHP_EOL, $spacer = '    ') {
        return $this->renderString($this->config, $eol, $spacer);
    }

    protected function renderString($array, $eol = PHP_EOL, $spacer = '    ') {
        $rendered = $this->renderRecursive($array, $eol, $spacer);

        return "<?php\n\nreturn " . $rendered . ";\n";
    }

    protected function renderRecursive(array $array, $eol = PHP_EOL, $spacer = '    ', $depth = 1) {
        $results = array();

        $scalars = array();
        $arrays = array();

        $associative = false;
        $expect = 0;

        foreach ($array as $key => $value) {
            if ($key !== $expect++) {
                $associative = true;
            }
            if (is_array($value)) {
                $arrays[] = array($key, $value);
            } elseif (is_scalar($value)) {
                $scalars[] = array($key, $value);
            } elseif (is_null($value)) {
                $scalars[] = array($key, $value);
            } else {
                $type = gettype($value);
                throw new \Exception(
                    'Invalid configuration type "' . $type . '", configuration supports array and scalar values only.');
            }
        }

        $space = str_repeat($spacer, $depth);
        foreach ($scalars as $scalar) {
            list($key, $value) = $scalar;

            if (is_string($value)) {
                $clean_value = '"' . addcslashes($value, '"\\') . '"';
            } elseif (is_bool($value)) {
                $clean_value = $value ? 'true' : 'false';
            } elseif (is_null($value)) {
                $clean_value = 'null';
            } else {
                $clean_value = $value;
            }

            if ($associative) {
                $results[] = $space . '"' . addcslashes($key, '"\\') . '" => ' . $clean_value;
            } else {
                $results[] = $space . $clean_value;
            }
        }

        foreach ($arrays as $array) {
            list($key, $value) = $array;
            $rendered = $this->renderRecursive($value, $eol, $spacer, $depth + 1);

            if ($associative) {
                $results[] = $space . '"' . addcslashes($key, '"\\') . '" => ' . $rendered;
            } else {
                $results[] = $space . $rendered;
            }
        }

        return 'array(' . $eol .
                    implode(',' . $eol, $results) . $eol .
                ($depth ? str_repeat($spacer, $depth - 1) : '') . ')';
    }

}
