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

namespace vorgas\ZfaProblems;

use ZF\ApiProblem\ApiProblem;

/**
 * Constructs an ZF\ApiProblem\ApiProblem with custom information.
 *
 * This extracts particular information into a special array for
 * inclusing in the ApiProblem. This allows for more meaningful error
 * messages in the api.
 * 
 * Currently only handles Mysql PDO problems
 * 
 * @todo Enable handling of problems other than Mysql PDO issues.
 */
class ProblemFactory
{
    /**
     * Returns an ApiProblem with the information tuned to the error
     *
     * The basic workflow here is as follows:
     *  - Split the error message into parts
     *  - Populate the ApiProblem message with key information from this message
     *  - Get the problem model for this specific error
     *  - Add any extra information into the ApiProblem message
     *  - Return the ApiProblem with the status and detail set
     *
     * @param \Exception $exception
     * @return \ZF\ApiProblem\ApiProblem
     */
    public static function build(\Exception $exception, string $class): ApiProblem
    {
        $detail = self::parseMessage($exception->getMessage());
        $className = self::determineClassName($detail['errno'], $class);
        $pdoProblem = new $className();
        $status = $pdoProblem->getStatus();
        $extras = $pdoProblem->getDetail($detail['message']);
        $detail = array_merge($detail, $extras);
        return new ApiProblem($status, $detail);
    }

    /**
     * Returns the name of the appropriate PdoProblem class
     *
     * This looks for 3 files within the ApigilityHelpers/PdoProblems directory. In order
     * of specificity, there is
     *  - {ClassName}{Errno}
     *      If the class MyCustomClass returns a Sql error number of 1234 then this
     *      would look for MyCustomClass1234.php and return that if found. In this way,
     *      you can customize your error messages for a specific api service.
     *      
     *  - Mysql{Errno}
     *      If a file matching the Sql error number exists, then use that.
     *  
     *  - MysqlDefault
     *      The fallback file if no others are found
     *  
     * @param string $errno
     * @param string $class
     * @return string
     */
    private static function determineClassName(string $errno, string $class): string
    {
        // Extract the actual class name from $class
        $array = explode('\\', $class);
        $class = array_pop($array);

        // Set up the basic vars
        $root = sprintf('%s/PdoProblems', dirname(__FILE__));
        $namespace = sprintf('%s\PdoProblems', __NAMESPACE__);        
        $classMatch = sprintf('%s%s', $class, $errno);
        $errnoMatch = sprintf('Mysql%s', $errno);
        $genericMatch = 'MysqlDefault';
        
        // Find the best match
        foreach ([$classMatch, $errnoMatch] as $lookup) {
            $fileName = sprintf('%s/%s.php', $root, $lookup);
            if (file_exists($fileName)) return sprintf('%s\%s', $namespace, $lookup);
        }
        return sprintf('%s\%s', $namespace, $genericMatch);
    }

    
    /**
     * Returns an array of info based on the original sql error message
     *
     * This is based on the standard messages returned by the Zend\Db\Sql
     * from version 3. Currently this message looks like:
     *  primary issue (sqlstate - errno - specific detail)
     *
     * @param string $message
     * @return array
     */
    private static function parseMessage(string $message): array
    {
        $message = substr($message, 0, -1);
        $detail = [];
        $parts = explode('(', $message);
        $detail['issue'] = trim($parts[0]);
        $parts = explode(' - ', $parts[1]);
        $detail['message'] = $parts[2];
        $detail['sqlstate'] = $parts[0];
        $detail['errno'] = $parts[1];
        return $detail;
    }
}

