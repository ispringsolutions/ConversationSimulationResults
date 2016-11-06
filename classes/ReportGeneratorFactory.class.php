<?php

class ReportGeneratorFactory
{
    /**
     * @param SimulationResult $result
     * @param array            $requestParams
     *
     * @return ReportGenerator
     */
    public static function createGenerator(SimulationResult $result, $requestParams)
    {
        $takerInfo = TestTakerInfoFactory::createFromRequest($requestParams);
        if ($takerInfo)
        {
            $takerInfo->initUserInResults($result);
        }

        return new ReportGenerator($result, $takerInfo);
    }
}