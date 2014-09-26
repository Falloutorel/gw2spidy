<?php

namespace GW2Spidy\GW2API;

class ConsumableV2 extends APIItemV2 {
    private $duration_ms;
    private $sub_description;
    
    public function __construct($APIItem) {
        parent::__construct($APIItem);
        
        $this->sub_type = $APIItem['details']['type'];
        $this->duration_ms = (isset($APIItem['details']['duration_ms'])) ? $APIItem['details']['duration_ms'] : null;
        $this->sub_description = (isset($APIItem['details']['description'])) ? $APIItem['details']['description'] : null;
    }
    
    public function getFormattedSubDescription() {
        return nl2br($this->sub_description);
    }
    
    private function getNourishment() {
        if ($this->duration_ms !== null) {
            $input = $this->duration_ms;

            $uSec = $input % 1000;
            $input = floor($input / 1000);

            $seconds = $input % 60;
            $input = floor($input / 60);

            $minutes = $input % 60;
            $input = floor($input / 60);
            
            $hours = $input % 60;
            $input = floor($input / 60);
            
            $time = array();
            
            if ($hours > 0) {
                $time[] = "$hours h";
            }
            
            if ($minutes > 0) {
                $time[] = "$minutes m";
            }
            
            if ($seconds > 0) {
                $time[] = "$seconds s";
            }
            
            $time_string = implode(',', $time);
            
            $nourishment = '<span class="db-consumableType">Double-click to consume</span><br>'.
                    "Nourishment({$time_string}): {$this->getFormattedSubDescription()}";
            
            return $nourishment;
        }
        
        return null;
    }
    
    public function getTooltipDescription() {
        $tooltip = <<<HTML
            <dt class="db-title gwitem-{$this->getRarityLower()}">{$this->getHTMLName()}</dt>
            <dd class="db-consumableDescription">{$this->getNourishment()}</dd>
            <dd class="db-itemDescription">{$this->getHTMLDescription()}</dd>
            <dd class="db-consumableType">{$this->getType()}</dd>
            {$this->getFormattedLevel()}
HTML;
        return $tooltip;
    }
}