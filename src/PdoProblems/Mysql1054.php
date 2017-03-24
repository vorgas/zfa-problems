<?php
/**
 * Zend Framework 3 interaction library
 *
 * This file is part of a suite of software to ease interaction with ZF3,
 * particularly Apigility.
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2017 Mike Hill
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace vorgas\ZfaProblems\PdoProblems;

/**
 * Handles an unknown table name
 */
class Mysql1054
{
    /**
     * Returns 400 conflict
     *
     * @return int
     */
    public function getStatus(): int
    {
        return 400;
    }

    /**
     * Returns the column name and value that is duplicated
     *
     * @param string $message
     * @return array
     */
    public function getDetail(string $message): array
    {
        $parts = $this->parseMessage($message);
        $detail['issue'] = 'Invalid option';
        $detail['message'] = "Unknown column name in " . $parts['where'];
        $detail['column'] = $parts['column'];
        return $detail;
    }


    /**
     * Extracts the affected column and the location from the message
     * 
     * @param string $message
     * @return array
     */
    private function parseMessage(string $message): array
    {
        $message = str_replace("Unknown column '", '', $message);
        $quotePos = strpos($message, "'");
        $column = substr($message, 0, $quotePos);
        $quotePos = $quotePos + 6;
        $where = substr($message, $quotePos, -1);
        return ['column' => $column, 'where' => $where];
    }
}
