<?php

class SimulationResult
{
    const XSD_8_5_0 = "ScenarioReport_8_5_0.xsd";

    /**
     * @var string - one of SimulationType::* values
     */
    public $type;

    /**
     * @var string
     */
    public $simulationTitle;

    /**
     * @var float
     */
    public $totalPoints;

    /**
     * @var float
     */
    public $passingScore;

    /**
     * @var string
     */
    public $playerVersion;

    /**
     * @var string
     */
    public $studentName;

    /**
     * @var string
     */
    public $studentEmail;

    /**
     * @var string
     */
    public $studentId;

    /**
     * @var float
     */
    public $studentPoints;

    /**
     * @var float
     */
    public $studentPercent;

    /**
     * @var bool
     */
    public $hasBeenPassed;

    /**
     * @var string
     */
    public $studentTime;

    /**
     * @var float
     */
    public $passingScorePercent;

    /**
     * @var string
     */
    public $timeLimit;

    /**
     * @var SimulationDetails
     */
    public $details;

    /**
     * @param array $requestParams
     */
    public function initFromRequest(array $requestParams)
    {
        $this->readFromRequestParams($requestParams);
        $this->checkInvalidVariables();
        $this->initUserAttemptData();
        $this->initDetailedResult($requestParams);
    }

    /**
     * Returns the taker's name/email pair.
     *
     * @return string
     */
    public function getUser()
    {
        $studentName  = trim($this->studentName);
        $studentEmail = trim($this->studentEmail);

        $parts = array(
            $studentName,
            $studentEmail ? "<$studentEmail>" : ''
        );
        $parts = array_filter($parts);

        return implode(' ', $parts);
    }

    /**
     * @return bool
     */
    public function isGraded()
    {
        return $this->type === SimulationType::GRADED;
    }

    /**
     * @param array $requestParams
     */
    private function readFromRequestParams(array $requestParams)
    {
        $requestParams = new RequestParameters($requestParams);

        $this->type = $requestParams["t"];
        if (!$this->type)
        {
            $this->type = SimulationType::GRADED;
        }

        $this->simulationTitle = $requestParams['st'];
        $this->totalPoints     = $requestParams['tp'];
        $this->passingScore    = $requestParams['ps'];
        $this->playerVersion   = $requestParams['v'];

        $this->studentName   = $requestParams['sn'];
        $this->studentEmail  = $requestParams['se'];
        $this->studentId     = $requestParams['sid'];
        $this->studentPoints = $requestParams['sp'];
        $this->studentTime   = $requestParams['ut'];
    }

    /**
     * @throws InvalidArgumentException
     */
    private function checkInvalidVariables()
    {
        $invalidVariables = array();
        if (empty($this->simulationTitle))
        {
            $invalidVariables[] = 'st(Simulation Title)';
        }

        if ($this->type == SimulationType::GRADED)
        {
            if (!is_numeric($this->studentPoints))
            {
                $invalidVariables[] = 'sp(Student Points)';
            }

            if (!is_numeric($this->totalPoints))
            {
                $invalidVariables[] = 'tp(Total Points)';
            }

            if (!is_numeric($this->passingScore))
            {
                $invalidVariables[] = 'ps(Passing Score)';
            }
        }

        if (count($invalidVariables) > 0)
        {
            $valuesString = implode(', ', $invalidVariables);
            throw new InvalidArgumentException("Incorrect or missing variables: {$valuesString}.");
        }
    }

    private function initUserAttemptData()
    {
        $this->studentPoints  = floatval($this->studentPoints);
        $this->totalPoints    = floatval($this->totalPoints);
        $this->studentPercent = $this->totalPoints > 0 ? ($this->studentPoints * 100.0) / $this->totalPoints : 0;
        $this->studentPercent = round($this->studentPercent, 2);

        $this->passingScorePercent = $this->totalPoints > 0 ? ($this->passingScore * 100.0) / $this->totalPoints : 0;
        $this->passingScorePercent = round($this->passingScorePercent, 2);

        $this->hasBeenPassed = $this->studentPoints >= floatval($this->passingScore);

        $this->playerVersion = $this->playerVersion ? $this->playerVersion : '1.0';

        $studentTimeSecs   = intval($this->studentTime);
        $this->studentTime = $this->convertSecsToTime($studentTimeSecs);

        $timeLimitSecs   = intval($this->timeLimit);
        $this->timeLimit = ($timeLimitSecs != 0) ? $this->convertSecsToTime($timeLimitSecs) : html_entity_decode('&infin;');
    }

    /**
     * @param array $requestParams
     */
    private function initDetailedResult(array $requestParams)
    {
        if (!isset($requestParams["dr"]))
        {
            return;
        }

        $detailResultXml = $requestParams["dr"];
        if ($detailResultXml)
        {
            $xsdFileName   = $this->getXsdSchemeFileNameByPlayerVersion($this->playerVersion);
            $this->details = new SimulationDetails();
            $this->details->loadFromXml($detailResultXml, $xsdFileName);
        }
    }

    /**
     * @param string $version
     * @return string
     */
    private function getXsdSchemeFileNameByPlayerVersion($version)
    {
        switch ($version)
        {
            case '8.5':
                return self::XSD_8_5_0;
            default:
                return self::XSD_8_5_0;
        }
    }

    /**
     * @param int $secs
     * @return string
     */
    private function convertSecsToTime($secs)
    {
        return date("H:i:s", mktime(0, 0, $secs));
    }
}