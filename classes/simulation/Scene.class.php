<?php

class Scene
{
    /**
     * @var string
     */
    public $speechText;

    /**
     * @var string
     */
    public $messageText;

    /**
     * @var Reply
     */
    public $reply;

    /**
     * @param DOMElement $node
     */
    public function initFromXmlNode(DOMElement $node)
    {
        $this->reset();

        $this->speechText  = $this->getElementText($node, 'speechText');
        $this->messageText = $this->getElementText($node, 'messageText');

        $replyNode = $node->getElementsByTagName('reply');
        if ($replyNode->length > 0)
        {
            $this->reply = $this->createReply($replyNode[0]);
        }
    }

    /**
     * @param DOMElement $replyNode
     * @return Reply
     */
    private function createReply(DOMElement $replyNode)
    {
        $reply = new Reply();
        $reply->initFromXmlNode($replyNode);

        return $reply;
    }

    /**
     * @param DOMElement $node
     * @param string     $tagName
     * @return string
     */
    private function getElementText(DOMElement $node, $tagName)
    {
        $result   = '';
        $elements = $node->getElementsByTagName($tagName);
        if ($elements->length > 0)
        {
            $result = $elements->item(0)->textContent;
        }

        return $result;
    }

    private function reset()
    {
        $this->speechText  = null;
        $this->messageText = null;
        $this->reply       = null;
    }
}