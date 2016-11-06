<?php

class Summary
{
    /**
     * @var string
     */
    public $time;

    /**
     * @var string
     */
    public $score;

    /**
     * @var string
     */
    public $percent;

    /**
     * @param DOMElement $node
     */
    public function initFromXmlNode(DOMElement $node)
    {
        $this->reset();

        $this->time    = $node->getAttribute('time');
        $this->score   = $node->getAttribute('score');
        $this->percent = $node->getAttribute('percent');
    }

    private function reset()
    {
        $this->time    = null;
        $this->score   = null;
        $this->percent = null;
    }
}