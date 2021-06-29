<?php
/*
 *  This file is part of the MyChiaFarm project.
 *
 *    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Classes\Plotters;

class Processor
{

    /**
     * Get part of log from one line containing text to another.
     *
     * @param string $subject
     * @param string|null $from
     * @param string|null $to
     * @param bool $includeFrom
     * @param bool $includeTo
     *
     * @return  string|null
     */
    public static function part(string $subject, ?string $from = null, ?string $to = null, bool $includeFrom = true, bool $includeTo = false): ?string
    {
        if (empty($subject)) {
            return null;
        }

        // check if need to find specified starting line or use subject from beginning
        if ($from !== null) {
            // if no beginning phrase not found return null
            if (!str_contains($subject, $from)) {
                return null;
            }
            // search from specified line
            $parts = explode($from, $subject, 2);
            if ($includeFrom) {
                $content = $from . $parts[1];
            } else {
                $parts = explode("\n", $parts[1], 2);
                $content = $parts[1] ?? '';
            }
        } else {
            $content = $subject;
        }

        // check if need to find specified ending line or use subject to ending
        if (($to !== null) && str_contains($content, $to)) {
            $parts = explode($to, $content, 2);
            $parts[1] = $to . $parts[1] ?? '';

            if ($includeTo) {
                $content = $parts[0];
                $parts = explode("\n", $parts[1], 2);
                $content .= $parts[0];
            } else if (str_ends_with($parts[0], "\n")) {
                $content = rtrim($parts[0], "\n");
            } else {
                $parts = preg_split('/\n(.*$)/', $parts[0]);
                $content = $parts[0];
            }
        }

        return $content;
    }

    /**
     * Count pattern occurrences in subject.
     *
     * @param string $pattern
     * @param string|null $subject
     * @param bool $unique
     *
     * @return  int
     */
    public static function count(string $pattern, ?string $subject, bool $unique = false): int
    {
        if (empty($subject)) {
            return 0;
        }

        $matches = [];

        $count = preg_match_all($pattern, $subject, $matches);

        if ($count === 0 || $count === false) {
            return 0;
        }

        return $unique ? count(array_unique($matches[0])) : $count;
    }
}
