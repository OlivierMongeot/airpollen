<?php

class  SetupPollen
{

    public static $setupPollen = [
        ['name' => 'grass_pollen', 'title' => 'Herbes', 'unit' => 'part/m3', 'subType' => 'numeric', 'order' => 58, 'display' => 'main'],
        ['name' => 'tree_pollen', 'title' => 'Arbres', 'unit' => 'part/m3', 'subType' => 'numeric', 'order' => 59, 'display' => 'main'],
        ['name' => 'weed_pollen', 'title' => 'Mauvaises Herbes', 'unit' => 'part/m3', 'subType' => 'numeric', 'order' => 54, 'display' => 'main'],
        ['name' => 'grass_risk', 'title' => 'Risque herbe', 'unit' => '', 'subType' => 'string', 'order' => 55, 'display' => 'main'],
        ['name' => 'weed_risk', 'title' => 'Risque mauvaise herbe', 'unit' => '', 'subType' => 'string', 'order' => 56, 'display' => 'main'],
        ['name' => 'tree_risk', 'title' => 'Risque arbres', 'unit' => '', 'subType' => 'string', 'order' => 57, 'display' => 'main'],
        ['name' => 'poaceae', 'title' => 'Graminées', 'unit' => 'part/m3', 'subType' => 'numeric', 'order' => 6, 'display' => 'slide'],
        ['name' => 'alder', 'title' => 'Aulne', 'unit' => 'part/m3', 'subType' => 'numeric', 'order' => 19, 'display' => 'slide'],
        ['name' => 'birch', 'title' => 'Bouleau', 'unit' => 'part/m3', 'subType' => 'numeric', 'order' => 18, 'display' => 'slide'],
        ['name' => 'cypress', 'title' => 'Cyprès', 'unit' => 'part/m3', 'subType' => 'numeric', 'order' => 8, 'display' => 'slide'],
        ['name' => 'elm', 'title' => 'Orme', 'unit' => 'part/m3', 'subType' => 'numeric', 'order' => 16, 'display' => 'slide'],
        ['name' => 'hazel', 'title' => 'Noisetier', 'unit' => 'part/m3', 'subType' => 'numeric', 'order' => 17, 'display' => 'slide'],
        ['name' => 'oak', 'title' => 'Chêne', 'unit' => 'part/m3', 'subType' => 'numeric', 'order' => 11, 'display' => 'slide'],
        ['name' => 'pine', 'title' => 'Pin', 'unit' => 'part/m3', 'subType' => 'numeric', 'order' => 12, 'display' => 'slide'],
        ['name' => 'plane', 'title' => 'Platane', 'unit' => 'part/m3', 'subType' => 'numeric', 'order' => 13, 'display' => 'slide'],
        ['name' => 'poplar', 'title' => 'Peuplier', 'unit' => 'part/m3', 'subType' => 'numeric', 'order' => 14, 'display' => 'slide'],
        ['name' => 'chenopod', 'title' => 'Chenopod', 'unit' => 'part/m3', 'subType' => 'numeric', 'order' => 15, 'display' => 'slide'],
        ['name' => 'mugwort', 'title' => 'Armoise', 'unit' => 'part/m3', 'subType' => 'numeric', 'order' => 9, 'display' => 'slide'],
        ['name' => 'nettle', 'title' => 'Ortie', 'unit' => 'part/m3', 'subType' => 'numeric', 'order' => 10, 'display' => 'slide'],
        ['name' => 'ragweed', 'title' => 'Ambroisie', 'unit' => 'part/m3', 'subType' => 'numeric', 'order' => 7, 'display' => 'slide'],
        ['name' => 'others', 'title' => 'Autres', 'unit' => 'part/m3', 'subType' => 'numeric', 'order' => 22, 'display' => 'slide'],
        ['name' => 'updatedAt', 'title' => 'Update at', 'unit' => '', 'subType' => 'string', 'order' => 60, 'display' => 'main'],
        ['name' => 'daysPollen', 'title' => 'Forecast days Pollen', 'unit' => '', 'subType' => 'string', 'order' => 23, 'display' => 'chart'],
        ['name' => 'poaceae_min', 'title' => "Grass-Poaceae Mini prévision", 'unit' => 'part/m³', 'subType' => 'string', 'order' => 24, 'display' => 'chart'],
        ['name' => 'poaceae_max', 'title' => 'Grass-Poaceae Maxi prévision', 'unit' => 'part/m³', 'subType' => 'string', 'order' => 25, 'display' => 'chart'],
        ['name' => 'alder_min', 'title' => "Alder Mini prévision", 'unit' => 'part/m³', 'subType' => 'string', 'order' => 26, 'display' => 'chart'],
        ['name' => 'alder_max', 'title' => 'Alder Maxi prévision', 'unit' => 'part/m³', 'subType' => 'string', 'order' => 27, 'display' => 'chart'],
        ['name' => 'birch_min', 'title' => "Birch Mini prévision", 'unit' => 'part/m³', 'subType' => 'string', 'order' => 28, 'display' => 'chart'],
        ['name' => 'birch_max', 'title' => "Birch Maxi prévision", 'unit' => 'part/m³', 'subType' => 'string', 'order' => 29, 'display' => 'chart'],
        ['name' => 'cypress_min', 'title' => "Cypress Mini prévision", 'unit' => 'part/m³', 'subType' => 'string', 'order' => 30, 'display' => 'chart'],
        ['name' => 'cypress_max', 'title' => 'Cypress Maxi prévision', 'unit' => 'part/m³', 'subType' => 'string', 'order' => 31, 'display' => 'chart'],
        ['name' => 'elm_min', 'title' => "Elm Mini prévision", 'unit' => 'part/m³', 'subType' => 'string', 'order' => 32, 'display' => 'chart'],
        ['name' => 'elm_max', 'title' => 'Elm Maxi prévision', 'unit' => 'part/m³', 'subType' => 'string', 'order' => 33, 'display' => 'chart'],
        ['name' => 'hazel_min', 'title' => "Hazel Mini prévision", 'unit' => 'part/m³', 'subType' => 'string', 'order' => 34, 'display' => 'chart'],
        ['name' => 'hazel_max', 'title' => 'Hazel Maxi prévision', 'unit' => 'part/m³', 'subType' => 'string', 'order' => 35, 'display' => 'chart'],
        ['name' => 'oak_min', 'title' => "Oak Mini prévision", 'unit' => 'part/m³', 'subType' => 'string', 'order' => 36, 'display' => 'chart'],
        ['name' => 'oak_max', 'title' => 'Oak Maxi prévision', 'unit' => 'part/m³', 'subType' => 'string', 'order' => 37, 'display' => 'chart'],
        ['name' => 'pine_min', 'title' => "Pine Mini prévision", 'unit' => 'part/m³', 'subType' => 'string', 'order' => 38, 'display' => 'chart'],
        ['name' => 'pine_max', 'title' => 'Pine Maxi prévision', 'unit' => 'part/m³', 'subType' => 'string', 'order' => 39, 'display' => 'chart'],
        ['name' => 'plane_min', 'title' => "Plane Mini prévision", 'unit' => 'part/m³', 'subType' => 'string', 'order' => 40, 'display' => 'chart'],
        ['name' => 'plane_max', 'title' => 'Plane Maxi prévision', 'unit' => 'part/m³', 'subType' => 'string', 'order' => 41, 'display' => 'chart'],
        ['name' => 'poplar_min', 'title' => "Poplar Cottonwood Mini prévision", 'unit' => 'part/m³', 'subType' => 'string', 'order' => 42, 'display' => 'chart'],
        ['name' => 'poplar_max', 'title' => 'Poplar Cottonwood Maxi prévision', 'unit' => 'part/m³', 'subType' => 'string', 'order' => 43, 'display' => 'chart'],
        ['name' => 'chenopod_min', 'title' => "Chenopod Mini prévision", 'unit' => 'part/m³', 'subType' => 'string', 'order' => 44, 'display' => 'chart'],
        ['name' => 'chenopod_max', 'title' => 'Chenopod Maxi prévision', 'unit' => 'part/m³', 'subType' => 'string', 'order' => 45, 'display' => 'chart'],
        ['name' => 'mugwort_min', 'title' => "Mugwort Mini prévision", 'unit' => 'part/m³', 'subType' => 'string', 'order' => 46, 'display' => 'chart'],
        ['name' => 'mugwort_max', 'title' => 'Mugwort Maxi prévision', 'unit' => 'part/m³', 'subType' => 'string', 'order' => 47, 'display' => 'chart'],
        ['name' => 'nettle_min', 'title' => "Nettle Mini prévision", 'unit' => 'part/m³', 'subType' => 'string', 'order' => 48, 'display' => 'chart'],
        ['name' => 'nettle_max', 'title' => 'Nettle Maxi prévision', 'unit' => 'part/m³', 'subType' => 'string', 'order' => 49, 'display' => 'chart'],
        ['name' => 'ragweed_min', 'title' => "Ragweed Mini prévision", 'unit' => 'part/m³', 'subType' => 'string', 'order' => 50, 'display' => 'chart'],
        ['name' => 'ragweed_max', 'title' => 'Ragweed Maxi prévision', 'unit' => 'part/m³', 'subType' => 'string', 'order' => 51, 'display' => 'chart'],
        ['name' => 'others_min', 'title' => "Others Mini prévision", 'unit' => 'part/m³', 'subType' => 'string', 'order' => 52, 'display' => 'chart'],
        ['name' => 'others_max', 'title' => 'Others Maxi prévision', 'unit' => 'part/m³', 'subType' => 'string', 'order' => 53, 'display' => 'chart'],
        ['name' => 'messagePollen', 'title' => 'Alerte Pollen', 'unit' => '', 'subType' => 'string', 'order' => 54, 'display' => 'none'],
        ['name' => 'telegramPollen', 'title' => 'Telegram Pollen', 'unit' => '', 'subType' => 'string', 'order' => 55, 'display' => 'none'],
        ['name' => 'smsPollen', 'title' => 'SMS Pollen', 'unit' => '', 'subType' => 'string', 'order' => 56, 'display' => 'none'],
        ['name' => 'markdownPollen', 'title' => 'Markdown Pollen', 'unit' => '', 'subType' => 'string', 'order' => 57, 'display' => 'none']
    ];


    /**
     * Les niveaux sont basés sur un recoupement d'informations que j'ai pu obtenir et ne sont pas officiel. 
     */
    public static $pollenRange =
    [
        'poaceae' => [
            '#00BD01' => [0, 30],
            '#EFE800' => [30, 60],
            '#E79C00' => [60, 130],
            '#B00000' => [130, 10000] // 130 le 20/07

        ],
        'elm' => [
            '#00BD01' => [0, 5],
            '#EFE800' => [5, 40],
            '#E79C00' => [40, 100],
            '#B00000' => [100, 10000]
        ],
        'alder' => [
            '#00BD01' => [0, 5],
            '#EFE800' => [5, 40],
            '#E79C00' => [40, 100],
            '#B00000' => [100, 10000]
        ],
        'birch' => [
            '#00BD01' => [0, 5],
            '#EFE800' => [5, 40],
            '#E79C00' => [40, 100],
            '#B00000' => [100, 10000]
        ],
        'grass_pollen' => [
            '#00BD01' => [0, 30],
            '#EFE800' => [30, 60],
            '#E79C00' => [60, 130], //130 le 20/07
            '#B00000' => [130, 10000]
        ],
        'tree_pollen' => [
            '#00BD01' => [0, 40],
            '#EFE800' => [40, 100],
            '#E79C00' => [100, 300],
            '#B00000' => [300, 10000]
        ],
        'weed_pollen' => [
            '#00BD01' => [0, 25],
            '#EFE800' => [25, 75],
            '#E79C00' => [75, 270],
            '#B00000' => [270, 10000]
        ],
        'cypress' => [
            '#00BD01' => [0, 5],
            '#EFE800' => [5, 40],
            '#E79C00' => [40, 100],
            '#B00000' => [100, 10000]
        ],
        'oak' => [
            '#00BD01' => [0, 5],
            '#EFE800' => [5, 40],
            '#E79C00' => [40, 100],
            '#B00000' => [100, 10000]
        ],
        'hazel' => [
            '#00BD01' => [0, 5],
            '#EFE800' => [5, 40],
            '#E79C00' => [40, 100],
            '#B00000' => [100, 10000]
        ],
        'pine' => [
            '#00BD01' => [0, 5],
            '#EFE800' => [5, 40],
            '#E79C00' => [40, 100],
            '#B00000' => [100, 10000]
        ],
        'plane' => [
            '#00BD01' => [0, 5],
            '#EFE800' => [5, 40],
            '#E79C00' => [40, 100],
            '#B00000' => [100, 10000]
        ],
        'poplar' => [
            '#00BD01' => [0, 5],
            '#EFE800' => [5, 40],
            '#E79C00' => [40, 100],
            '#B00000' => [100, 10000]
        ],
        'chenopod' => [
            '#00BD01' => [0, 5],
            '#EFE800' => [5, 40],
            '#E79C00' => [40, 100],
            '#B00000' => [100, 10000]
        ],
        'mugwort' => [
            '#00BD01' => [0, 5],
            '#EFE800' => [5, 40],
            '#E79C00' => [40, 100],
            '#B00000' => [100, 10000]
        ],
        'nettle' => [
            '#00BD01' => [0, 5],
            '#EFE800' => [5, 75],
            '#E79C00' => [75, 210],
            '#B00000' => [210, 10000]
        ],
        'ragweed' => [
            '#00BD01' => [0, 5],
            '#EFE800' => [5, 40],
            '#E79C00' => [40, 100],
            '#B00000' => [100, 10000]
        ],
        'others' => [
            '#00BD01' => [0, 25],
            '#EFE800' => [25, 45],
            '#E79C00' => [45, 100],
            '#B00000' => [100, 10000]
        ]
    ];
}
