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
 * Handles a duplicate entry in a restricted table column
 */
class Mysql1062
{
    /**
     * Returns 409 conflict
     *
     * This is based on the return value provided by Apigility for a Validator
     * error on DbRecordExists.
     *
     * @return int
     */
    public function getStatus(): int
    {
        return 409;
    }

    /**
     * Returns the column name and value that is duplicated
     *
     * @param string $message
     * @return array
     */
    public function getDetail(string $message): array
    {
        $detail['column'] = $this->affectedColumn($message);
        $detail['value'] = $this->getDuplicateValue($message);
        return $detail;
    }

    /**
     * Extracts the affected column from the error message
     *
     * @param string $message
     * @return string
     */
    private function affectedColumn(string $message): string
    {
        $lastSpace = strrpos($message, ' ');
        return substr($message, $lastSpace + 2, -1);
    }

    /**
     * Extracts the duplicated value from the error message
     *
     * @param string $message
     * @return string
     */
    private function getDuplicateValue(string $message): string
    {
        $entry = substr($message, 17);
        $lastQuote = strpos($entry, "'");
        return substr($entry, 0, $lastQuote);
    }
}
