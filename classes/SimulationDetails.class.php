<?php

class SimulationDetails
{
    const LINE_BREAK_ENTITY = "&#13;&#10;";

    /**
     * @var SimulationSettings
     */
    public $settings;

    /**
     * @var Summary
     */
    public $summary;

    /**
     * @var Scene[]
     */
    public $scenes = array();

    public function __construct()
    {
        $this->reset();
    }

    /**
     * @param string $xml
     * @param string $xsdSchemeFileName
     * @return bool if xml is valid according to the xsd scheme
     */
    public function loadFromXml($xml, $xsdSchemeFileName)
    {
        $this->reset();

        $doc = new DOMDocument('1.0', 'utf8');

        $xml = $this->preserveLineBreaks($xml);
        $doc->loadXML($xml);

        // validate xml
        if (!$doc->schemaValidate($xsdSchemeFileName))
        {
            return false;
        }

        //export quiz settings
        $quizSettingsNode = $doc->getElementsByTagName('scenarioSettings')->item(0);
        $this->settings->initFromXmlNode($quizSettingsNode);

        //export summary
        $summaryNode = $doc->getElementsByTagName('summary')->item(0);
        $this->summary->initFromXmlNode($summaryNode);

        $questionsNode = $doc->getElementsByTagName('scenes')->item(0);
        $this->initScenesFromXmlNode($questionsNode);

        return true;
    }

    /**
     * @param DOMElement $scenesNode
     */
    private function initScenesFromXmlNode(DOMElement $scenesNode)
    {
        $scenesList = $scenesNode->childNodes;
        for ($i = 0; $i < $scenesList->length; ++$i)
        {
            $scene = null;

            $sceneNode = $scenesList->item($i);
            if ($sceneNode->nodeType !== XML_ELEMENT_NODE)
            {
                continue;
            }

            if ($sceneNode != null)
            {
                $scene = new Scene();
                $scene->initFromXmlNode($sceneNode);
                $this->scenes[] = $scene;
            }
        }
    }

    private function reset()
    {
        $this->settings = new SimulationSettings();
        $this->summary  = new Summary();
        $this->scenes   = [];
    }

    /**
     * @param string $xml
     * @return string
     */
    private function preserveLineBreaks($xml)
    {
        return str_replace(
            array("\r\n", "\n", "\r"),
            html_entity_decode(self::LINE_BREAK_ENTITY),
            $xml
        );
    }
}