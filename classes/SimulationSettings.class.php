<?php

class SimulationSettings
{
    /**
     * @var string|null
     */
    public $timeLimit;

    /**
     * @var string|null
     */
    public $maxScore;

    /**
     * @var string|null
     */
    public $passingScore;

    /**
     * @var string|null
     */
    public $passingPercent;

    /**
     * @var string|null
     */
    public $simulationType;

    /**
     * @param DOMElement $node
     */
    public function initFromXmlNode(DOMElement $node)
    {
        $this->reset();

        $this->simulationType = $node->hasAttribute('scenarioType') ? $node->getAttribute('scenarioType') : SimulationType::GRADED;
        $this->timeLimit      = $node->hasAttribute('timeLimit') ? $node->getAttribute('timeLimit') : null;
        $this->maxScore       = $node->getAttribute('maxScore');

        if ($node->getElementsByTagName('passingScore')->length != 0)
        {
            $passingScoreNode   = $node->getElementsByTagName('passingScore')->item(0);
            $this->passingScore = $passingScoreNode->textContent;
        }
        else
        {
            $passingPercentNode = $node->getElementsByTagName('passingPercent')->item(0);
            if ($passingPercentNode != null)
            {
                $this->passingPercent = $passingPercentNode->textContent;
            }
        }
    }

    private function reset()
    {
        $this->simulationType = null;
        $this->maxScore       = null;
        $this->timeLimit      = null;
        $this->passingPercent = null;
        $this->passingScore   = null;
    }
}