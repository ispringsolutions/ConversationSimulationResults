<?php

class RequestParametersParser
{
    /**
     * @param array   $postParameters
     * @param string  $rawRequest
     * @return array
     */
    public static function getRequestParameters(array $postParameters, $rawRequest)
    {
        $result = $postParameters;
        if (!$result)
        {
            $result = array();
            if ($rawRequest)
            {
                parse_str($rawRequest, $result);
            }
        }

        return $result;
    }
}