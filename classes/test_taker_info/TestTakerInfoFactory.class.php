<?php

class TestTakerInfoFactory
{
    const PARAM_FIELDS      = 'vt';
    const FIELD_PARAM_ID    = 'id';
    const FIELD_PARAM_TITLE = 'title';

    /**
     * @param array $requestParameters
     * @return null|TestTakerInfo
     */
    public static function createFromRequest(array $requestParameters)
    {
        $fieldTitles = self::getFieldTitlesFromParameters($requestParameters);
        if (!$fieldTitles)
        {
            return null;
        }
        unset($requestParameters[self::PARAM_FIELDS]);

        return new TestTakerInfo($fieldTitles, $requestParameters);
    }

    /**
     * @param array $requestParameters
     * @return array
     */
    private static function getFieldTitlesFromParameters(array $requestParameters)
    {
        if (!self::hasValidFieldTitlesParameter($requestParameters))
        {
            return null;
        }

        $result = array();
        $fields = $requestParameters[self::PARAM_FIELDS];
        ksort($fields);

        foreach ($fields as $fieldInfo)
        {
            if (!self::isValidFieldInfo($fieldInfo))
            {
                continue;
            }

            $id          = $fieldInfo[self::FIELD_PARAM_ID];
            $title       = isset($fieldInfo[self::FIELD_PARAM_TITLE]) ? $fieldInfo[self::FIELD_PARAM_TITLE] : '';
            $result[$id] = $title;
        }

        return $result;
    }

    /**
     * @param array $requestParameters
     * @return bool
     */
    private static function hasValidFieldTitlesParameter(array $requestParameters)
    {
        return !empty($requestParameters[self::PARAM_FIELDS]) && is_array($requestParameters[self::PARAM_FIELDS]);
    }

    /**
     * @param array $fieldInfo
     * @return bool
     */
    private static function isValidFieldInfo(array $fieldInfo)
    {
        return isset($fieldInfo[self::FIELD_PARAM_ID]);
    }
}