<?php

class TestTakerInfo
{
    const FIELD_USER_NAME  = 'USER_NAME';
    const FIELD_USER_EMAIL = 'USER_EMAIL';

    /**
     * @var TestTakerInfoField[]
     */
    private $fields;

    /**
     * @var string[]
     */
    private $fieldTitles;

    /**
     * @var string[]
     */
    private $fieldValues;
    /**
     * @var VariableReplacer
     */
    private $replacer;

    /**
     * @param string $fieldTitles
     * @param string $fieldValues
     */
    public function __construct($fieldTitles, $fieldValues)
    {
        $this->fieldTitles = $fieldTitles;
        $this->fieldValues = $this->collectKnownFieldValues($fieldValues);
    }

    /**
     * @return string[]
     */
    public function getFieldValues()
    {
        return $this->fieldValues;
    }

    /**
     * @param SimulationResult $result
     */
    public function initUserInResults(SimulationResult $result)
    {
        if (!$this->doesContainUserInfo())
        {
            return;
        }

        $result->studentName  = !empty($this->fieldValues[self::FIELD_USER_NAME]) ? $this->fieldValues[self::FIELD_USER_NAME] : null;
        $result->studentEmail = !empty($this->fieldValues[self::FIELD_USER_EMAIL]) ? $this->fieldValues[self::FIELD_USER_EMAIL] : null;
    }

    /**
     * @return VariableReplacer
     */
    public function getReplacer()
    {
        if (!isset($this->replacer))
        {
            $this->replacer = new VariableReplacer($this->fieldValues);
        }

        return $this->replacer;
    }

    /**
     * @return TestTakerInfoField[]
     */
    public function getFields()
    {
        if (!isset($this->fields))
        {
            $this->fields = $this->createFields();
        }

        return $this->fields;
    }

    /**
     * @return TestTakerInfoField[]
     */
    private function createFields()
    {
        $result = array();
        foreach ($this->fieldTitles as $fieldId => $fieldTitle)
        {
            if ($this->isUserInfoField($fieldId))
            {
                continue;
            }

            $result[$fieldId] = new TestTakerInfoField($fieldTitle, $this->fieldValues[$fieldId]);
        }

        return $result;
    }

    /**
     * @return bool
     */
    private function doesContainUserInfo()
    {
        return !empty($this->fieldValues[self::FIELD_USER_NAME])
               || !empty($this->fieldValues[self::FIELD_USER_EMAIL]);
    }

    /**
     * @param array $arrayContainingFieldValues
     * @return string
     */
    private function collectKnownFieldValues(array $arrayContainingFieldValues)
    {
        $result = array();
        foreach ($this->fieldTitles as $fieldId => $fieldTitle)
        {
            $result[$fieldId] = !empty($arrayContainingFieldValues[$fieldId]) ? $arrayContainingFieldValues[$fieldId] : '';
        }

        return $result;
    }

    /**
     * @param string $fieldId
     * @return bool
     */
    private function isUserInfoField($fieldId)
    {
        $fieldId = strtoupper($fieldId);

        return $fieldId === self::FIELD_USER_NAME
               || $fieldId === self::FIELD_USER_EMAIL;
    }
}