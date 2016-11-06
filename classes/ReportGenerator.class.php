<?php

class ReportGenerator
{
    const SIMULATION_START = "=============================================================\r\n";
    const SIMULATION_END   = "\r\n=============================================================\r\n";
    const FIELD_SEPARATOR  = "\r\n";
    const SCENE_SEPARATOR  = "\r\n\r\n";

    const SIMULATION_NAME   = 'Simulation Name';
    const USER_ID           = 'User Id';
    const USER_NAME         = 'User Name';
    const USER_EMAIL        = 'User Email';
    const PASSING_SCORE     = 'Passing Score';
    const USER_SCORE        = 'User Score';
    const SIMULATION_TIME   = 'Time';
    const SIMULATION_RESULT = 'Result';

    const RESULT_FINISHED = 'Finished';
    const RESULT_PASSED   = 'Passed';
    const RESULT_FAILED   = 'Failed';

    const SCENE_SPEECH  = 'Speech';
    const SCENE_MESSAGE = 'Message';
    const SCENE_REPLY   = 'Reply';

    /**
     * @var TestTakerInfo
     */
    private $testTakerInfo;

    /**
     * @var SimulationResult
     */
    private $result;

    /**
     * @param SimulationResult   $testResults
     * @param TestTakerInfo|null $info
     */
    public function __construct(SimulationResult $testResults, TestTakerInfo $info = null)
    {
        $this->result        = $testResults;
        $this->testTakerInfo = $info;
    }

    /**
     * @return string
     */
    public function createReport()
    {
        return self::SIMULATION_START
               . $this->buildHeader()
               . self::SCENE_SEPARATOR
               . $this->buildScenes()
               . self::SIMULATION_END;
    }

    /**
     * @return string
     */
    private function buildHeader()
    {
        $headerParameters = array(
            self::SIMULATION_NAME   => $this->result->simulationTitle,
            self::USER_ID           => $this->result->studentId,
            self::USER_NAME         => $this->result->getUser(),
            self::USER_EMAIL        => $this->result->studentEmail,
            self::PASSING_SCORE     => $this->getPassingScoreString(),
            self::USER_SCORE        => $this->getStudentScoreString(),
            self::SIMULATION_TIME   => $this->result->studentTime,
            self::SIMULATION_RESULT => $this->getResultString(),
        );

        if ($this->testTakerInfo)
        {
            foreach ($this->testTakerInfo->getFields() as $field)
            {
                $headerParameters[$field->getTitle()] = $field->getValue();
            }
        }

        return $this->formatNonEmptyFields($headerParameters);
    }

    /**
     * @return string
     */
    private function buildScenes()
    {
        $formattedScenes = array_map(
            array($this, 'formatSceneMessages'),
            $this->result->details->scenes
        );

        return implode(self::SCENE_SEPARATOR, $formattedScenes);
    }

    /**
     * @return VariableReplacer
     */
    private function getReplacer()
    {
        $replacer = $this->testTakerInfo ? $this->testTakerInfo->getReplacer() : null;

        return $replacer ?: new VariableReplacer(array());
    }

    /**
     * @return string|null
     */
    private function getPassingScoreString()
    {
        $result = null;
        if ($this->result->isGraded())
        {
            $result = "{$this->result->passingScore} ({$this->result->passingScorePercent}%)";
        }

        return $result;
    }

    /**
     * @return string|null
     */
    private function getStudentScoreString()
    {
        $result = null;
        if ($this->result->isGraded())
        {
            $result = "{$this->result->studentPoints} of {$this->result->totalPoints} ({$this->result->studentPoints}%)";
        }

        return $result;
    }

    /**
     * @return string
     */
    private function getResultString()
    {
        $resultString = self::RESULT_FINISHED;
        if ($this->result->isGraded())
        {
            $resultString = $this->result->hasBeenPassed ? self::RESULT_PASSED : self::RESULT_FAILED;
        }

        return $resultString;
    }

    /**
     * @param string[] $fields
     * @return string
     */
    private function formatNonEmptyFields(array $fields)
    {
        $nonEmptyFields = array_filter($fields);
        $fieldTexts     = array_map(
            array($this, 'buildFieldText'),
            array_keys($nonEmptyFields),
            array_values($nonEmptyFields)
        );

        return implode(self::FIELD_SEPARATOR, $fieldTexts);
    }

    /**
     * @param string $fieldTitle
     * @param string $fieldValue
     * @return string
     */
    private function buildFieldText($fieldTitle, $fieldValue)
    {
        return "{$fieldTitle}: {$fieldValue}";
    }

    /**
     * @param Scene $scene
     * @return array
     */
    private function formatSceneMessages(Scene $scene)
    {
        $replacer    = $this->getReplacer();
        $speechText  = $replacer->replace($scene->speechText);
        $messageText = $replacer->replace($scene->messageText);
        $reply       = $replacer->replace($scene->reply ? $scene->reply->text : '');

        return $this->formatNonEmptyFields(array(
            self::SCENE_SPEECH  => $speechText,
            self::SCENE_MESSAGE => $messageText,
            self::SCENE_REPLY   => $reply
        ));
    }
}