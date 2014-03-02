<?php
/**
 * Metin2CMS - Easy for Metin2
 * Copyright (C) 2014  ChuckNorris
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, see <http://www.gnu.org/licenses/>.
 */

namespace system;

class ExceptionHandler {

    /**
     * @var \Exception
     */
    private $exception;

    /**
     * Create an exception handler
     */
    public function __construct() {
        if(!is_dir(ROOT_DIR . "exceptions")) {
            mkdir(ROOT_DIR . "exceptions" . DS, 0777);
        }
        set_exception_handler(array($this, 'handleException'));
    }

    /**
     *
     * @param $exception \Exception The occured exception
     * @return string Html string of the exception
     */
    public function handleException(\Exception $exception) {

        $this->exception = $exception;

        // Get Exception number
        $exceptions = glob(ROOT_DIR . "exceptions" . DS . "*.html");
        $counter = ((count($exceptions)) + 1);
        $salt = Utils::generateRandomString(25);

        $file = fopen(ROOT_DIR . "exceptions" . DS . $counter . "_" . $salt . ".html", "a+");

        Logger::error("(" . $counter . ") Exception " . $this->exception->getMessage() . " in file " . $this->exception->getFile() . " on line " . $this->exception->getLine());

        $code = '
                <html>
                        <head>
                                <style>
                                        pre {margin: 0;font-size: 11px;color: #515151;background-color: #D0D0D0;padding-left: 30px;}
                                        pre b { display: block; padding-top: 10px; padding-bottom: 10px; }
                                        .backtrace {border: 1px solid black; margin: 1px;}
                                        .file { font-size: 11px; color: green; background-color: white;}
                                        .header { color: rgb(105, 165, 80); background-color: rgb(65, 65, 65); padding: 4px 2px; }
                                        .step { color: white; }
                                        .tracecode  { color: rgb(105, 165, 80); }
                                </style>
                        </head>
                        <body>' .
            $this->createBackTraceCode($exception->getTrace()) . '
                        </body>
                </html>
                ';

        echo $code;

        fwrite($file, $code);
        fclose($file);
    }

    /**
     * Create a user friendly trace
     * @param $trace array the trace
     * @return string a user friendly trace
     */
    public function createBackTraceCode(array $trace) {

        $backtracecode = '';

        $backtracecontainer = '<div class="backtrace">%s</div>';
        $stepheadercode = '<pre class="header"><span class="step">%s</span> <span class="class">%s</span></pre>';
        $mainheadercode = '<pre class="header"><span class="code">Code: %s</span> <span class="error">Message: %s</span></pre>';

        $current = sprintf($mainheadercode, $this->exception->getCode(), $this->exception->getMessage());
        $current .= $this->getCodeSnippet($this->exception->getFile(), $this->exception->getLine());
        $backtracecode .= sprintf($backtracecontainer, $current);

        if (count($trace)) {
            //Verschiedene Trace stufen durchloopen
            foreach ($trace as $index => $step) {

                ($step['class']) ? $class = $step['class'] . '::' . $step['function'] : $class = $step['function'];

                if(!Core::$instance->isDebug()) {
                    $class .= '(***censored***)';
                } else {
                    $class .= '(';

                    $arguments = '';

                    //Übergebende Argumente ausgeben
                    foreach ($step['args'] as $argument) {
                        $arguments .= ((strlen($arguments)) === 0) ? '' : ', ';
                        if (is_object($argument)) {
                            $arguments .= get_class($argument);
                        } elseif (is_string($argument)) {
                            $arguments .= $argument;
                        } elseif (is_numeric($argument)) {
                            $arguments .= (string) $argument;
                        } else {
                            $arguments .= gettype($argument);
                        }
                    }
                    $class .= $arguments;
                    $class .= ')';
                }

                $stepcode = sprintf($stepheadercode, count($trace) - $index, $class);
                $stepcode .= $this->getCodeSnippet($step['file'], $step['line']);
                $backtracecode .= sprintf($backtracecontainer, $stepcode);
            }
        }

        return $backtracecode;
    }

    /**
     * Returns a code snippet from the specified file.
     *
     * @param $filePathAndName string Absolute path and file name of the PHP file
     * @param $lineNumber int Line number defining the center of the code snippet
     * @return string The code snippet
     */
    protected function getCodeSnippet($filePathAndName, $lineNumber) {
        if(!Core::$instance->isDebug()) {
            return "<span class='file'><b>File:</b> " . $filePathAndName . " <b>Line:</b> " . $lineNumber . "</span><pre><b>Sourcecode only available in debug mode.</b></pre>";
        }
        $pathPosition = strpos($filePathAndName, 'Packages/');
        if (@file_exists($filePathAndName)) {
            $phpFile = @file($filePathAndName);
            if (is_array($phpFile)) {
                $startLine = ($lineNumber > 2) ? ($lineNumber - 2) : 1;
                $endLine = ($lineNumber < (count($phpFile) - 2)) ? ($lineNumber + 3) : count($phpFile) + 1;
                if ($endLine > $startLine) {
                    if ($pathPosition !== FALSE) {
                        $codeSnippet = '<span class="file">' . substr($filePathAndName, $pathPosition) . ':</span><br /><pre>';
                    } else {
                        $codeSnippet = '<span class="file">' . $filePathAndName . ':</span><br /><pre>';
                    }
                    for ($line = $startLine; $line < $endLine; $line++) {
                        $codeLine = str_replace("\t", ' ', $phpFile[$line - 1]);

                        if ($line === $lineNumber) {
                            $codeSnippet .= '</pre><pre class="tracecode">';
                        }
                        $codeSnippet .= sprintf('%05d', $line) . ': ' . $codeLine;
                        if ($line === $lineNumber) {
                            $codeSnippet .= '</pre><pre>';
                        }
                    }
                    $codeSnippet .= '</pre>';
                }
            }
        }
        return $codeSnippet;
    }

}