<?php

class Utils
{
    const FILE_DATE_TIME_FORMAT = 'Y-m-d_H-i-s';
    const FULL_DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    const LOG_PATH              = '/../../log/quiz_results.log';

    /**
     * @param string $report
     */
    public static function saveReportToFile($report)
    {
        $dateTime       = date(self::FILE_DATE_TIME_FORMAT);
        $resultFilename = __DIR__ . "/../../result/{$dateTime}.txt";
        file_put_contents($resultFilename, $report, FILE_APPEND);
    }

    /**
     * @param array $requestParameters
     */
    public static function log(array $requestParameters)
    {
        $logFilename = __DIR__ . self::LOG_PATH;
        $event       = array(
            'ts'                 => date(self::FULL_DATE_TIME_FORMAT),
            'request_parameters' => $requestParameters,
            'ts_'                => time()
        );
        $logMessage  = json_encode($event);
        $logMessage .= ',' . PHP_EOL;
        file_put_contents($logFilename, $logMessage, FILE_APPEND);
    }
}