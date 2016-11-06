<?php

class VariableReplacer
{
    /**
     * @var string[]
     */
    private $values;

    /**
     * @var string
     */
    private $pattern;

    /**
     * @param string[] $replacements
     */
    public function __construct(array $replacements)
    {
        $this->values  = $this->buildValueByVariableMap($replacements);
        $this->pattern = $this->values ? $this->buildPattern($replacements) : null;
    }

    /**
     * @param string $subject
     * @return string
     */
    public function replace($subject)
    {
        $result = $subject;
        if ($this->pattern)
        {
            $result = preg_replace_callback($this->pattern, array($this, 'replaceSingleMatch'), $subject);
        }

        return $result;
    }

    /**
     * @param string[] $replacements
     * @return string[]
     */
    private function buildValueByVariableMap(array $replacements)
    {
        $result = array();
        foreach ($replacements as $variable => $value)
        {
            $hash          = $this->normalizeVariable($variable);
            $result[$hash] = $value;
        }

        return $result;
    }

    /**
     * @param string $variable
     * @return string
     */
    private function normalizeVariable($variable)
    {
        return strtoupper($variable);
    }

    /**
     * @param string[] $replacements
     * @return string
     */
    private function buildPattern(array $replacements)
    {
        $variables = array_keys($replacements);
        $variables = $this->escapeForPattern($variables);

        return '~%(' . implode('|', $variables) . ')%~i';
    }

    /**
     * @param string $variable
     * @return string
     */
    private function escapeSingleVariable($variable)
    {
        return preg_quote($variable, '~');
    }

    /**
     * @param string[] $variables
     * @return array
     */
    private function escapeForPattern(array $variables)
    {
        return array_map(array($this, 'escapeSingleVariable'), $variables);
    }

    /**
     * @param string[] $matches
     * @return string
     */
    private function replaceSingleMatch($matches)
    {
        $variable = $this->normalizeVariable($matches[1]);

        return !empty($this->values[$variable]) ? $this->values[$variable] : '';
    }
}