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

}

