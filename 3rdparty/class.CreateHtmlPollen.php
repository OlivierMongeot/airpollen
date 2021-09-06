<?php

class CreateHtmlPollen
{
    private $itemByCell;
    private $countElements;
    private $slides = [];
    private $countCell;
    private $id;
    private $version;

    private $slidesAtZero;

    public function __construct($slides = [], $id, $itemByCell = 1, $version, $slidesAtZero)
    {
        $this->id = $id;
        $this->countElements = count($slides);
        $this->slides = $slides;
        $this->itemByCell = $itemByCell;
        $this->countCell = ceil($this->countElements / $this->itemByCell);
        $this->version = $version;
        $this->slidesAtZero = $slidesAtZero;
    }

    public function getLayer()
    {

        $newTab = [];
        $array = $this->slides;
        $html = [];
        $state = 'ok';
        if ($this->itemByCell == 1) {
            for ($i = 0; $i < ($this->countCell); $i++) {
                $newTab[] = [$array[$i]];
            }
            $total = count($newTab);
            foreach ($newTab as $k => $item) {
                $html[] =  $this->getStartCell($k, $total) . $item[0] . $this->getEndCell($k, $total);
            }
        }

        if (empty($html)) {
            $html[] = '<div disable class="" style="margin-top:20px;color:#00BD01;display:flex;justify-content:center;align-item:center;flex-direction:column;height:auto;font-size:110%">';
            $html[] = '<div style="display:flex;justify-content:center;height:35px;align-items: center">Détails de pollen </div><br>';
            $html[] = '<div style="display:flex;justify-content:center;height:35px;align-items: center"><i class="far fa-times-circle fa-2x"></i></div><br>';
            $html[] = '<div style="display:flex;justify-content:center;height:35px;align-items: center">indisponibles</div><br>';
            $html[] = '</div>';
            $state = 'empty';
        }

        return [implode('', $html), $state];
    }

    private function getEndCell($k, $total)
    {

        if ($this->version == 'mobile') {
            if ($k >= ($total - $this->slidesAtZero)) {
                return '</div></div>';
            } else {
                return '</div>';
            }
        } else {
            return '</div></div>';
        }
    }

    private function getStartCell($k, $total)
    {


        if ($this->version == 'mobile') {

            if ($k >= ($total - $this->slidesAtZero)) {
                return '<div id="slide-' . ($k + 1) . '-' . $this->id . '-aqi row aqi-' . $this->id . '-row" ><div >';
            } else {
                return '<div id="slide-' . ($k + 1) . '-' . $this->id . '-aqi row first-row aqi-' . $this->id . '-row">';
            }
        } else {
            if ($k == 0) {
                $active = 'active';
                $interval = '15000';
            } else {
                $active = '';
                $interval = '12000';
            }

            if ($k >= ($total - $this->slidesAtZero)) {

                return '<div class="item ' . $active . '" data-interval="' . $interval . '" ><div class="aqi-' .
                    // $this->id.'-particule" style="height:200px; display:flex; flex-direction:column;" >';
                    $this->id . '-particule" style="display:flex; flex-direction:column;" >';
            } else {
                return '<div class="item ' . $active . '" data-interval="' . $interval . '"><div class="aqi-' . $this->id . '-row">';
            }
        }
    }
}
