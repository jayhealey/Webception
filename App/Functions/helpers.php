<?php

/*
 * This file is part of the Webception package.
 *
 * (c) James Healey <jayhealey@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (! function_exists('run_terminal_command')) {

    /**
     * Run a terminal command.
     *
     * @param  string $command
     * @return array  Each array entry is a line of output from running the command.
     */
    function run_terminal_command($command)
    {
        $output = array();

        $spec = array(
            0 => array("pipe", "r"),   // stdin is a pipe that the child will read from
            1 => array("pipe", "w"),   // stdout is a pipe that the child will write to
            2 => array("pipe", "w")    // stderr is a pipe that the child will write to
        );

        flush();

        $process = proc_open($command, $spec, $pipes, realpath('./'), $_ENV);

        if (is_resource($process)) {

            while ($line = fgets($pipes[1])) {

                // Trim any line breaks and white space
                $line = trim(preg_replace("/\r|\n/", "", $line));

                // If the line has content, add to the output log.
                if (! empty($line))
                    $output[] = $line;

                flush();
            }
        }

        return $output;
    }

}

if (! function_exists('get_webception_config')) {

    /**
     * Decide on which Webception configuration file to load
     * based on the 'test' query string parameter.
     *
     * If the test config is not found, it falls back to the default file.
     *
     * @param  object $app Slim's App object.
     * @return array  Array of the application config.
     */
    function get_webception_config($app)
    {
        $config            = FALSE;
        $test_type         = $app->request()->params('test');
        $webception_config = $app->config('webception');

        // If the test query string parameter is set,
        // a test config will be loaded.
        if ($test_type !== NULL) {

            // Sanitize the test type.
            $test_type = trim(strtolower(remove_file_extension($test_type)));

            // Filter the test type into the test string.
            $test_config = sprintf($webception_config['test'], $test_type);

            // Load the config if it can be found
            if (file_exists($test_config))
                $config = require_once($test_config);
        }

        if ($config == FALSE)
            $config = require_once($webception_config['config']);

        return $config;
    }
}

if (! function_exists('get_route_param')) {

    /**
     * Given the Application, and a given name of a parameter
     * return the value of the parameter.
     *
     * @param  object $app
     * @param  string $param Name of route
     * @return value  if found or false.
     */
    function get_route_param($app, $param)
    {
        $route  = $app->router()->getCurrentRoute();

        $params = $route->getParams();

        if (isset($params[$param]))
            return $params[$param];

        return FALSE;
    }

}

if (! function_exists('camel_to_sentance')) {

    /**
     * Take a camel cased string and turn it into a word seperated sentance.
     * e.g. 'ThisIsASentance' would turn into 'This Is A Sentance'
     *
     * @param  string $string
     * @return string
     */
    function camel_to_sentance($string)
    {
        return trim(preg_replace('/(?!^)[A-Z]{2,}(?=[A-Z][a-z])|[A-Z][a-z]/', ' $0', $string));
    }

}

if (! function_exists('remove_file_extension')) {

    /**
     * Given a file name, remove any file extension from the string.
     *
     * @param  string $string
     * @return string
     */
    function remove_file_extension($string)
    {
        return preg_replace("/\\.[^.\\s]{3,4}$/", "", $string);
    }

}
