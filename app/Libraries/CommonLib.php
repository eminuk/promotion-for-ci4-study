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
     * 메시지 발송
     * @param  [type] $to      [description]
     * @param  [type] $title   [description]
     * @param  [type] $content [description]
     * @return [type]          [description]
     */
    private function sendMessage($message_type, $from, $to, $title, $content, $content_type)
    {
        // 결과 배열 생성
        $rtn = array('result' => true, 'message' => '');

        // url 설정
        $url = 'https://api.picnique.co.kr/api/v1/message';

        // headers 설정
        $headers = array (
            'Content-type: application/json',
            'Cache-Control: no-cache'
        );

        // postfields 설정
        $dataField = array(
            'messageType' => $message_type,
            'from' => $from,
            'to' => $to, 
            'title' => $title,
            'content' => $content,
            'contentType' => $content_type
        );
        $postfields = json_encode($dataField);

        // curl 초기화
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);

        // curl 실행
        $output = curl_exec($curl);
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // curl close
        curl_close($curl);

        // 결과 확인
        if ($http_status != '200') {
            $rtn['result'] = false;
            $rtn['message'] = "Request is fail. http status code is {$http_status}.";
        }
        
        return $rtn;
    }

    /**
     * 이메일 발송
     * @param  [type] $to      [description]
     * @param  [type] $title   [description]
     * @param  [type] $content [description]
     * @return [type]          [description]
     */
    public function sendEmail($to, $title, $content)
    {
        // 메시지 발송
        $rtn = $this->sendMessage(
            'EMAIL', 'Support@autocarz.co.kr', $to, $title, $content, 'HTML'
        );

        return $rtn;
    }

    /**
     * LMS 전송
     * @param  [type] $to      [description]
     * @param  [type] $title   [description]
     * @param  [type] $content [description]
     * @return [type]          [description]
     */
    public function sendLms($to, $title, $content)
    {
        // 입력값 보정
        $to = str_replace('-', '', $to);

        // 메시지 발송
        $rtn = $this->sendMessage(
            'SMS', '025550206', $to, $title, $content, 'LMS'
        );

        return $rtn;
    }

}

