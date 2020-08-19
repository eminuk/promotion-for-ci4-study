<?php namespace App\Libraries;

/**
 * Common method library used by the application
 */
class CommonLib
{
    private $request;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->request = \Config\Services::request();
    }
    /**
     * Get hashed string to use password
     *
     * @param string $password
     * @return string
     */
    public function hashPassword(string $password): string
    {
        return hash('SHA256', "salt{$password}tlsa");
    }


    /**
     * Read parameter with default value, trim(string only) - POST & GET
     *
     * @param string $name
     * @param string|int|array|null $default_value
     * @return string|int|array|null
     */
    public function readPostGet(string $name, $default_value = null)
    {
        // Read parameter
        $rtn = $this->request->getPostGet($name);

        // Check if parameter are set
        if (!isset($rtn) || $rtn === []) {
            return $default_value;
        }

        // Trim
        if (gettype($default_value) == 'string') {
            $rtn = trim($rtn);
        }
        
        // Type casting
        if (gettype($default_value) == 'integer') {
            $rtn = (int)$rtn;
        }

        return $rtn;
    }

    /**
     * Read row data(PUT ,PATCH, DELETE) with default value, trim(string only) - POST & GET
     *
     * @param string $name
     * @param string|int|array|null $default_value
     * @return string|int|array|null
     */
    public function readRawInput(string $name = '', $default_value = null)
    {
        // read row data
        $row_input = $this->request->getRawInput();

        // return all row data
        if (empty($name)) {
            return $row_input;
        }

        // Check if parameter are set
        if (!isset($row_input[$name]) || $row_input[$name] === []) {
            return $default_value;
        }

        // Read parameter
        $rtn = $row_input[$name];

        // Trim
        if (gettype($default_value) == 'string') {
            $rtn = trim($rtn);
        }
        
        // Type casting
        if (gettype($default_value) == 'integer') {
            $rtn = (int)$rtn;
        }

        return $rtn;
    }

    /**
     * Read file with default value
     *
     * @param string $name
     * @return mixed|null
     */
    public function readFile(string $name)
    {
        // Read file
        $rtn = $this->request->getFile($name);

        return $rtn;
    }

    /**
     * Read Cookie with default value, trim(string only)
     *
     * @param string $name
     * @param string|int|array|null $default_value
     * @return string|int|array|null
     */
    public function readCookie(string $name, $default_value = null)
    {
        // Read parameter
        $rtn = $this->request->getCookie($name);

        // Check if parameter are set
        if (!isset($rtn) || $rtn === []) {
            return $default_value;
        }

        // Trim
        if (gettype($default_value) == 'string') {
            $rtn = trim($rtn);
        }
        
        // Type casting
        if (gettype($default_value) == 'integer') {
            $rtn = (int)$rtn;
        }

        return $rtn;
    }



    /**
     * Check mobile nunber pattern
     *
     * @param string $input
     * @param boolean $no_dash
     * @return boolean
     */
    public function isMobileNum(string $input, bool $no_dash = true): bool
    {
        $regexp_dash = '/^01\d-\d{3,4}-\d{4}$/';
        $regexp_no_dash = '/^01\d{8,9}$/';

        $pattern = ($no_dash)?$regexp_no_dash: $regexp_dash;
        $res = preg_match($pattern, $input);

        return ($res === 1);
    }



    /**
     * Add javascript to response view
     *
     * @param string $javascript
     * @param boolean $rendering_view
     * @return void
     */
    public function addJavascript(string $javascript, bool $rendering_view = true): void
    {
        $script = '<script type="text/javascript" charset="UTF-8">';
        $script .= $javascript;
        $script .= '</script>';

        echo $script;

        if (!$rendering_view) {
            exit();
        }
    }

    /**
     * Add javascript location replace
     *
     * @param string $redirect_url
     * @return void
     */
    public function jsRedirect(string $redirect_url): void
    {
        $this->addJavascript("location.replace('{$redirect_url}');", false);
    }

    /**
     * Add javascript alert to response view
     *
     * @param string $message
     * @param boolean $rendering_view
     * @return void
     */
    public function jsAlert(string $message, bool $rendering_view = true): void
    {
        $this->addJavascript("alert('{$message}');", $rendering_view);
    }

    /**
     * Javascrpt alert and history back
     *
     * @param string $message
     * @return void
     */
    public function jsAlertBack(string $message): void
    {
        $this->addJavascript("alert('{$message}');history.back();", false);
    }

    /**
     * Javascript alert and location replace
     *
     * @param string $message
     * @param string $redirect_url
     * @return void
     */
    public function jsAlertRedirect(string $message, string $redirect_url): void
    {
        $this->addJavascript("alert('{$message}');location.replace('{$redirect_url}');", false);
    }


}

