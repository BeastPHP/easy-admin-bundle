<?php

namespace Beast\EasyAdminBundle\Helper;

class Log extends HelperAbstract
{
    public static $avalidModuleTypes = array('api', 'backend');
    protected $rootDir = null;
    protected $logDir = null;

    public function __construct()
    {
        parent::__construct();
        $this->rootDir = $this->getLogDir();
        $this->logDir = $this->getLogDir();
    }

    /**
     * @param $content
     * @param $moduleType
     * @param string $requestType
     *
     * @throws \Exception
     */
    public function logData($content, $moduleType, $requestType = "request")
    {
        try {
            if (!in_array($moduleType, Log::$avalidModuleTypes)) {
                throw new \Exception('Invalid module type: ' . $moduleType);
            }
            $modulePath = strtolower($moduleType);
            $logFullDir = $this->logDir . "/" . $modulePath . "/" . date("Ym") . "/";
            if (!is_dir($logFullDir)) {
                @mkdir($logFullDir, 0777, true);
            }
            $logFullPath = $logFullDir . "/" . date("d") . ".log";
            $handle = fopen($logFullPath, 'a');
            $string = "";
            if (is_array($content)) {
                $string = var_export($content, true);
            } else {
                $string = $content;
            }
            if ($requestType == "request") {
                $requestTypeString = "Request: ";
            } else {
                $requestTypeString = "Response: ";
            }
            $string = "[" . date("Y-m-d H:i:s") . " - IP: " . $this->getRealIpAddr() . "] " . $requestTypeString . $string . "\n";
            fwrite($handle, $string);
            @chmod($logFullPath, 0777);
            fclose($handle);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @return mixed
     */
    public function getRealIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * @param $content
     * @param $moduleType
     * @param string $requestType
     *
     * @throws \Exception
     */
    public static function writeLog($content, $moduleType, $requestType = "request")
    {
        $log = new Log();
        $log->logData($content, $moduleType, $requestType);
    }

    /**
     * @param $content
     * @param $moduleType
     * @param string $requestType
     *
     * @throws \Exception
     */
    public static function debugLog($content, $moduleType, $requestType = "request")
    {
        $kernel = (new CoreHelper())->getKernel();
        if ($kernel->getContainer()->getParameter('core.debug_enable')) {
            $log = new Log();
            $log->logData($content, $moduleType, $requestType);
        }
    }

    /**
     * @param $message
     * @param string $logFilename
     *
     * @return bool
     */
    public static function logMessage($message, $logFilename = '')
    {
        $log = new Log();
        if (!$logFilename) {
            $logFilename = $log->getKernel()->getEnvironment() . '.log';
        }

        if (!$log->logDir) {
            @mkdir($log->logDir, 0777, true);
        }

        $logFile = $log->logDir . $logFilename;
        $handle = fopen($logFile, "a");
        fwrite($handle, date("Y-m-d H:i:s") . ": $message\n");
        fclose($handle);
        return true;
    }
}
