<?php

header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] != 'POST')
{
    http_response_code(400);
    echo 'POST request expected';
    return;
}

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once 'includes/common.inc.php';

$requestParameters = RequestParametersParser::getRequestParameters($_POST, !empty($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : null);
Utils::log($requestParameters);

try
{
    $result = new SimulationResult();
    $result->initFromRequest($requestParameters);

    $generator = ReportGeneratorFactory::createGenerator($result, $requestParameters);
    $report    = $generator->createReport();

    Utils::saveReportToFile($report);
    echo 'OK';
}
catch (Exception $e)
{
    error_log($e);
    echo 'Error: ' . $e->getMessage();
}