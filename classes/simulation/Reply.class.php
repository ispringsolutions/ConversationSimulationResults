<?php

class Reply
{
    /**
     * @var string
     */
    public $text;

    /**
     * @param DOMElement $node
     */
    public function initFromXmlNode(DOMElement $node)
    {
        $this->reset();
        $textElements = $node->getElementsByTagName('text');
        $this->text   = $textElements[0]->textContent;
    }

    private function reset()
    {
        $this->text = '';
    }
}