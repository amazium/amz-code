<?php

namespace Amz\Code\Format;

class Indenter
{
    /**
     * @param mixed $input
     * @param int $level
     * @param int $indentation
     * @param bool $skipFirstLine
     * @return string
     */
    public function __invoke($input, int $level = 0, int $indentation = 4, bool $skipFirstLine = false): string
    {
        return Indenter::indent($input, $level, $indentation, $skipFirstLine);
    }

    /**
     * @param $input
     * @param int $level
     * @param int $indentation
     * @param bool $skipFirstLine
     * @return string
     */
    public static function indent($input, int $level = 0, int $indentation = 4, bool $skipFirstLine = false): string
    {
        $text = (string)$input;
        if ($level <= 0) {
            return $text;
        }
        $indentPerLine = str_repeat(' ', $indentation * $level);
        $prefix = $skipFirstLine ? '' : $indentPerLine;
        $return = $prefix . str_replace(PHP_EOL, PHP_EOL . $indentPerLine, $text);
        return preg_replace('/\n\s*\n/', "\n\n", $return); // remove whitespace only on lines
    }
}
