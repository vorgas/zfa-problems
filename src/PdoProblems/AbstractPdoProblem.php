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
 * Interface for turning a PDO error message into an ApiProblem
 *
 * The actual translation happens in the PdoProblemFactory
 */
abstract class AbstractPdoProblem
{
    /**
     * Returns the HTTP status code for this error
     *
     * This will most commonly be a 400, but may be a 409, etc.
     *
     * @return int
     */
    abstract public function getStatus(): int;

    /**
     * Converts the error message into an associative array of valid information
     *
     * Common information, such as the mysql error code and sql server state
     * is handled automatically. However, extra information, such as a column
     * name or field value will need to be extracted here.
     *
     * @param string $message
     * @return array
     */
    abstract public function getDetail(string $message): array;
}

