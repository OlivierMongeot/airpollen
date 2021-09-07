<?php

class DisplayInfoPollen
{

    public function formatValueForDisplay($value, $style = 'normal', $decimal = null)
    {
        if(!empty($decimal)){
            return number_format((float)($value), $decimal, '.', '');
        }
        if ($style === 'normal') {
            switch ($value) {
                case 1:
                case 2:
                case 3:
                case 4:
                case 5:
                case 6:
                    return $value;
                case $value > 0  && $value <= 10:
                    return number_format((float)($value), 2, '.', '');
                case $value > 10  && $value <= 100:
                    return number_format((float)($value), 1, '.', '');
                case $value > 100:
                    return number_format((float)($value), 0, '.', '');
            }
        } else {
            switch ($value) {
                case $value > 10:
                    return number_format((float)($value), 0, '.', '');
                default:
                    return number_format((float)($value), 1, '.', '');
            }
        }
    }


    public function getElementRiskPollen($color)
    {
        // log::add('pollen', 'debug', 'getElementRiskPollen for color '. $color);
        switch ($color) {
            case '#00BD01':
                return __("Risque bas", __FILE__);
                break;
            case '#EFE800':
                return  __("Risque modéré", __FILE__);
                break;
            case '#E79C00':
                return __("Risque haut", __FILE__);
                break;
            default:
                return  __("Risque très haut", __FILE__);
        }
    }


    public function getPollenRisk(string $level)
    {
        log::add('airpollen', 'debug', 'Function getPollenRisk for level : ' . $level);
        switch ($level) {
            case  'risque haut':
            case  'high risk':
            case  'High':
                return __("Risque haut", __FILE__);
            case 'risque modéré':
            case 'moderate risk':
            case 'Moderate':
                return __("Risque modéré", __FILE__);
            case 'risque bas':
            case 'low risk':
            case 'Low':
                return __("Risque bas", __FILE__);
            case 'risque très haut':
            case 'very high risk':
            case 'Very high':
                return __("Risque très haut", __FILE__);
            default:
                return __("Risque inconnu", __FILE__);
        }
    }


    public function getListPollen($category)
    {
        switch ($category) {
            case 'tree_pollen':
                return __('Aulne', __FILE__) . ' - ' . __('Bouleau', __FILE__) . ' - ' . __('Cyprès', __FILE__) . ' - ' . __('Chêne', __FILE__)
                    . ' - ' . __('Platane', __FILE__) . ' - ' . __('Noisetier', __FILE__) . ' - ' . __('Orme', __FILE__) . ' - ' . __('Pin', __FILE__);
                break;
            case 'grass_pollen':
                return __('Herbes', __FILE__) . ' - ' . __('Poacées', __FILE__) . ' - ' . __('Graminées', __FILE__);
                break;
            case 'weed_pollen':
                return __('Chenopod', __FILE__) . ' - ' . __('Armoise', __FILE__) . ' - ' . __('Ortie', __FILE__) . ' - ' . __('Ambroisie', __FILE__);
                break;
            default:
                return __("Autres pollens d'origine inconnue", __FILE__);
        }
    }

    public function parseDate($date = null)
    {
        if (empty($date)) {
            $datetime = new DateTime;
            $time =   $datetime->format('H:i');
            $date = $datetime->format('d-m-Y');
            return __('Mise à jour le ', __FILE__) . $date . __(' à ', __FILE__) . $time;
        } else {
            $dt = DateTime::createFromFormat('Y-m-d H:i:s', $date);
            return __('Mise à jour le ', __FILE__) . $dt->format('d') . '-' . $dt->format('m') . '-' . $dt->format('Y') . __(' à ', __FILE__) . $dt->format('H') . 'h' . $dt->format('i');
        }
    }

    public function getNameDay($numDay)
    {
        switch ($numDay) {
            case 1:
                return __('Lundi', __FILE__);
            case 2:
                return __('Mardi', __FILE__);
            case 3:
                return __('Mercredi', __FILE__);
            case 4:
                return __('Jeudi', __FILE__);
            case 5:
                return  __('Vendredi', __FILE__);
            case 6:
                return  __('Samedi', __FILE__);
            case 7:
                return __('Dimanche', __FILE__);
        }
    }



  
    private function getSynonyme($name)
    {
        $synonymes = [
            'une concentration de' => ['une quantité de', 'une mesure de', 'une concentration de', 'une mesure de', 'une quantité de','','','','',''],
            'amélioration' => ['amélioration', 'embellie', 'amélioration'],
            'dégradation' => ['dégradation', 'altération', 'détérioration'],
            'hausse' => ['hausse', 'augmentation', 'élévation', 'hausse'],
            'stable' => ['stable', 'constant', 'stabilisé','se stabilise', 'stable','reste'],
            ' avec ' => [' avec ', ' avec ', ' avec ', ' grâce à '],
            'baisse' => ['baisse', 'diminution'],
            'petite' => ['petite', 'légère', 'légère', 'petite'],
            'en hausse' => ['en hausse', 'en augmentation'],
            'en baisse' => ['en baisse', 'en diminution'],
            'au niveau' => ['au niveau', 'au palier', 'à l\'échelon', 'au niveau'],
            'en légère hausse' =>  ['en légère hausse', 'en légère augmentation', 'en légère élévation',  'en légère hausse'],
            'reste au niveau' => ['reste au niveau', 'reste au palier', 'reste à l\'échelon', 'reste au niveau'],
            'légère baisse' => ['légère baisse', 'petite diminution', 'petite baisse'],
            'reste au meilleur niveau' => ['reste au meilleur niveau', 'reste au meilleur palier', 'reste au meilleur échelon', 'reste au meilleur niveau'],
        ];
        if (isset($synonymes[$name])) {
            $tab = $synonymes[$name];
            $rand_keys = array_rand($tab, 1);
            return $tab[$rand_keys];
        } else {
            return $name;
        }
    }


    /**
     * Création messages : analyse si bascule de tranche 
     */
    public function getAllMessagesPollen($oldData, $dataPollen, $paramAlertPollen, $city )
    {
        $message = [];
        $messageInMore = [];
        // Poaceae
        $newPoaceae = isset($dataPollen[0]->Species->Grass->{"Grass / Poaceae"}) ? $dataPollen[0]->Species->Grass->{"Grass / Poaceae"} : -1;
        $oldPoaceae = $oldData['poaceae'];
        if ($paramAlertPollen['poaceae_alert_level'] <= $newPoaceae) {
            $mess = $this->makeMessagePollen($newPoaceae, $oldPoaceae, 'poaceae', 'Graminées');

            if (!empty($mess[0])) {
                $message[] = $mess[0];
                if (!empty($mess[2])) {
                    // log::add('airpollen', 'debug', 'Message High' . $mess[2]);
                    $message[] = $mess[2];
                }
            } else if (!empty($mess[1])) {
                $messageInMore[] = $mess[1];
            }
        }

        //Elm
        $newElm = isset($dataPollen[0]->Species->Tree->Elm) ? $dataPollen[0]->Species->Tree->Elm : -1;
        $oldElm = $oldData['elm'];
        if ($paramAlertPollen['elm_alert_level'] <= $newElm) {
            $mess = $this->makeMessagePollen($newElm, $oldElm, 'elm', 'Orme');
            if (!empty($mess[0])) {
                $message[] = $mess[0];
            } else if (!empty($mess[1])) {
                $messageInMore[] = $mess[1];
            }
        }

        //Alder
        $newAlder = isset($dataPollen[0]->Species->Tree->Alder) ? $dataPollen[0]->Species->Tree->Alder : -1;
        $oldAlder = $oldData['alder'];
        if ($paramAlertPollen['alder_alert_level'] <= $newAlder) {
            $mess = $this->makeMessagePollen($newAlder, $oldAlder, 'alder', 'Aulne');
            if (!empty($mess[0])) {
                $message[] = $mess[0];
            } else if (!empty($mess[1])) {
                $messageInMore[] = $mess[1];
            }
        }
        // Birch
        $newBirch = isset($dataPollen[0]->Species->Tree->Birch) ? $dataPollen[0]->Species->Tree->Birch : -1;
        $oldBirch = $oldData['birch'];
        if ($paramAlertPollen['birch_alert_level'] <= $newBirch) {
            $mess = $this->makeMessagePollen($newBirch, $oldBirch, 'birch', 'Bouleau');
            if (!empty($mess[0])) {
                $message[] = $mess[0];
            } else if (!empty($mess[1])) {
                $messageInMore[] = $mess[1];
            }
        }
        // Cypress
        $newCypress = isset($dataPollen[0]->Species->Tree->Cypress) ? $dataPollen[0]->Species->Tree->Cypress : -1;
        $oldCypress = $oldData['cypress'];
        if ($paramAlertPollen['cypress_alert_level'] <= $newCypress) {
            $mess = $this->makeMessagePollen($newCypress, $oldCypress, 'cypress', 'Cyprès');
            if (!empty($mess[0])) {
                $message[] = $mess[0];
            } else if (!empty($mess[1])) {
                $messageInMore[] = $mess[1];
            }
        }

        // Hazel    
        $newHazel = isset($dataPollen[0]->Species->Tree->Hazel) ? $dataPollen[0]->Species->Tree->Hazel : -1;
        $oldHazel = $oldData['hazel'];
        if ($paramAlertPollen['hazel_alert_level'] <= $newHazel) {
            $mess = $this->makeMessagePollen($newHazel, $oldHazel, 'hazel', 'Noisetier');
            if (!empty($mess[0])) {
                $message[] = $mess[0];
            } else if (!empty($mess[1])) {
                $messageInMore[] = $mess[1];
            }
        }

        // Oak 
        $newOak = isset($dataPollen[0]->Species->Tree->Oak) ? $dataPollen[0]->Species->Tree->Oak : -1;
        $oldOak = $oldData['oak'];
        if ($paramAlertPollen['oak_alert_level'] <= $newOak) {
            $mess = $this->makeMessagePollen($newOak, $oldOak, 'oak', 'Chêne');
            if (!empty($mess[0])) {
                $message[] = $mess[0];
            } else if (!empty($mess[1])) {
                $messageInMore[] = $mess[1];
            }
        }

        // Pine 
        $newPine = isset($dataPollen[0]->Species->Tree->Pine) ? $dataPollen[0]->Species->Tree->Pine : -1;
        $oldPine = $oldData['pine'];
        if ($paramAlertPollen['pine_alert_level'] <= $newPine) {
            $mess = $this->makeMessagePollen($newPine, $oldPine, 'pine', 'Pin');
            if (!empty($mess[0])) {
                $message[] = $mess[0];
            } else if (!empty($mess[1])) {
                $messageInMore[] = $mess[1];
            }
        }

        // Plane
        $newPlane = isset($dataPollen[0]->Species->Tree->Plane) ? $dataPollen[0]->Species->Tree->Plane : -1;
        $oldPlane = $oldData['plane'];
        if ($paramAlertPollen['plane_alert_level'] <= $newPlane) {
            $mess = $this->makeMessagePollen($newPlane, $oldPlane, 'plane', 'Platane');
            if (!empty($mess[0])) {
                $message[] = $mess[0];
            } else if (!empty($mess[1])) {
                $messageInMore[] = $mess[1];
            }
        }

        // Poplar
        $newPoplar = isset($dataPollen[0]->Species->Tree->Poplar) ? $dataPollen[0]->Species->Tree->Poplar : -1;
        $oldPoplar = $oldData['poplar'];
        if ($paramAlertPollen['poplar_alert_level'] <= $newPoplar) {
            $mess = $this->makeMessagePollen($newPoplar, $oldPoplar, 'poplar', 'Peuplier');
            if (!empty($mess[0])) {
                $message[] = $mess[0];
            } else if (!empty($mess[1])) {
                $messageInMore[] = $mess[1];
            }
        }

        // Chenopod
        $newChenopod = isset($dataPollen[0]->Species->Weed->Chenopod) ? $dataPollen[0]->Species->Weed->Chenopod : -1;
        $oldChenopod = $oldData['chenopod'];
        if ($paramAlertPollen['chenopod_alert_level'] <= $newChenopod) {
            $mess = $this->makeMessagePollen($newChenopod, $oldChenopod, 'chenopod', 'Chénopodes');
            if (!empty($mess[0])) {
                $message[] = $mess[0];
            } else if (!empty($mess[1])) {
                $messageInMore[] = $mess[1];
            }
        }

        // Mugwort
        $newMugwort = isset($dataPollen[0]->Species->Weed->Mugwort) ? $dataPollen[0]->Species->Weed->Mugwort : -1;
        $oldMugwort = $oldData['mugwort'];
        if ($paramAlertPollen['mugwort_alert_level'] <= $newMugwort) {
            $mess = $this->makeMessagePollen($newMugwort, $oldMugwort, 'mugwort', 'Armoises');
            if (!empty($mess[0])) {
                $message[] = $mess[0];
                if (!empty($mess[2])) {
                    // log::add('airpollen', 'debug', 'Message High' . $mess[2]);
                    $message[] = $mess[2];
                }
            } else if (!empty($mess[1])) {
                $messageInMore[] = $mess[1];
            }
        }

        // Nettle
        $newNettle = isset($dataPollen[0]->Species->Weed->Nettle) ? $dataPollen[0]->Species->Weed->Nettle : -1;
        $oldNettle = $oldData['nettle'];
        if ($paramAlertPollen['nettle_alert_level'] <= $newNettle) {
            $mess = $this->makeMessagePollen($newNettle, $oldNettle, 'nettle', 'Ortie');
            if (!empty($mess[0])) {
                $message[] = $mess[0];
                if (!empty($mess[2])) {
                    // log::add('airpollen', 'debug', 'Message High' . $mess[2]);
                    $message[] = $mess[2];
                }
            } else if (!empty($mess[1])) {
                $messageInMore[] = $mess[1];
            }
        }
        // Ragweed 
        $newRagweed = isset($dataPollen[0]->Species->Weed->Ragweed) ? $dataPollen[0]->Species->Weed->Ragweed : -1;
        $oldRagweed = $oldData['ragweed'];
        if ($paramAlertPollen['ragweed_alert_level'] <= $newRagweed) {
            $mess = $this->makeMessagePollen($newRagweed, $oldRagweed, 'ragweed', 'Ambroisie');
            if (!empty($mess[0])) {
                $message[] = $mess[0];
                if (!empty($mess[2])) {
                    log::add('airpollen', 'debug', 'Message High' . $mess[2]);
                    $message[] = $mess[2];
                }
            } else if (!empty($mess[1])) {
                $messageInMore[] = $mess[1];
            }
        }

        // Others
        $newOthers = isset($dataPollen[0]->Species->Others) ? $dataPollen[0]->Species->Others : -1;
        $oldOthers = $oldData['others'];
        if ($paramAlertPollen['others_alert_level'] <= $newOthers) {
            $mess = $this->makeMessagePollen($newOthers, $oldOthers, 'others', 'Autres pollens');
            if (!empty($mess[0])) {
                $message[] = $mess[0];
                if (!empty($mess[2])) {
                    // log::add('airpollen', 'debug', 'Message High' . $mess[2]);
                    $message[] = $mess[2];
                }
            } else if (!empty($mess[1])) {   
                $messageInmore[] = $mess[1];
            }
        }

        if ($paramAlertPollen['alert_details'] == 1) {
            $message = array_merge($message, $messageInMore);
        }

        $stringMess = implode(' - ', $message);
        if ($paramAlertPollen['alert_notification'] == 1) {
            message::add('Message Pollen', $stringMess);
        }

        $telegramMessage = $this->formatPollensForTelegram($message, $city);
        $markdownMessage = $this->formatPollenMarkDown($message);
        $smsMessage = $this->formatPollensForSms($message);
        log::add('airpollen', 'debug', 'Markdown Message Pollen' . json_encode( $message));
        return [$stringMess, $telegramMessage, $smsMessage, $markdownMessage];
    }

    private function makeMessagePollen($newData, $oldData, $type, $typeName)
    {
        $message = '';
        $messageMore = '';
        $messageHigh = '';
        //Hausse
        // log::add('airpollen', 'debug', '----------Make Message Pollen ----------------------');
        // log::add('airpollen', 'debug', 'New Data : ' . $newData . ' Old Data : ' . $oldData . ' For: ' . $typeName);
        if ($newData > $oldData) {
            $newCategory = $this->getLevelPollen($newData, $type);
            $oldCategory = $this->getLevelPollen($oldData, $type);
            // log::add('airpollen', 'debug', 'Get Level Message Hausse Pollen for type: ' . $type . ' New Cat: ' . $newCategory . ' > OldCat: ' . $oldCategory);
            if ($newCategory !== $oldCategory) {
                $message = '- <b>' . __($typeName , __FILE__) . "</b> " . __($this->getSynonyme('en hausse'),__FILE__) . " " .__($this->getSynonyme('au niveau'),__FILE__) ." " . $newCategory .
                    " " . __('avec', __FILE__) . " " . $newData . " part/m³ ";
            }
            // Pas de changement de level mais hausse 
            else if ($oldCategory != 'risque très haut' && $oldCategory != 'risque haut') {
                $messageMore = ' - <b>' .  __($typeName , __FILE__) . "</b> " . __($this->getSynonyme('en légère hausse'),__FILE__) . ", " . __($this->getSynonyme('reste au niveau'),__FILE__) . " " . $newCategory .  " ".__('avec',__FILE__)." " . $newData . " part/m³ ";
            }
            // Message pour les hauts niveaus 
            else {
                $messageHigh = '- <b>' . __($typeName, __FILE__) . "</b> " . __($this->getSynonyme('en hausse'), __FILE__) . __($this->getSynonyme('reste au niveau'), __FILE__) . " " . $newCategory .
                    " " . __('avec', __FILE__) . " " . $newData . " part/m³ ";
            }
            //Baisse
        } else if ($newData < $oldData) {
            $newCategory = $this->getLevelPollen($newData, $type);
            $oldCategory = $this->getLevelPollen($oldData, $type);
            // log::add('airpollen', 'debug', 'Make Message Baisse Pollen type: ' . $type . ' New Cat: ' . $newCategory . ' < OldCat: ' . $oldCategory);
            if ($newCategory !== $oldCategory) {
                $message = "<b>" . __($typeName, __FILE__) . "</b> " . __($this->getSynonyme('en baisse'), __FILE__) . " " . __($this->getSynonyme('au niveau'), __FILE__) . " " . $newCategory . " " . __('avec', __FILE__) . " " . $newData . " part/m³ ";
            } // Pas de changement de level 
            else if ($newCategory != 'risque bas')
            {
                $messageMore = "- <b>" .  __($typeName, __FILE__) . "</b> " . __($this->getSynonyme('légère baisse'),__FILE__) . ", " . __($this->getSynonyme('reste au niveau'), __FILE__) . " " . $newCategory .  " " . __('avec', __FILE__) . " " . $newData . " part/m³ ";
            }
            else if ($newCategory == 'risque bas')
            {
                $messageMore = "- <b>" .  __($typeName, __FILE__) . "</b> " . __($this->getSynonyme('légère baisse'),__FILE__) . ", " . __($this->getSynonyme('reste au meilleur niveau'), __FILE__) . " " . $newCategory .  " " . __('avec', __FILE__) . " " . $newData . " part/m³ ";
            }
            else if ($newCategory == 'risque très haut' || $newCategory == 'risque haut' )
            {
                $messageHigh = "<b>" . __($typeName, __FILE__) . "</b> " . __($this->getSynonyme('en baisse'), __FILE__) . ", ".__("mais reste",__FILE__) ." " .
                 __($this->getSynonyme('au niveau'), __FILE__) . " " . $newCategory . " " . __('avec',__FILE__) . " " . $newData . " part/m³ ";
            }
            // Stable
        } else {
           
            $newCategory = $this->getLevelPollen($newData, $type);
            // log::add('airpollen', 'debug', 'Make Message Stable Pollen type: ' . $type . ' New Cat: ' . $newCategory . ' is stable ');

            if ($newCategory == 'risque haut' || $newCategory == 'risque très haut') {
                $messageHigh = ' - <b>' .  __($typeName, __FILE__) . "</b> " . __($this->getSynonyme('stable'), __FILE__)
                . ", et reste " . __($this->getSynonyme('au niveau'), __FILE__) . " "
                . $newCategory . " " . __('avec', __FILE__) . " "
                . $newData . " part/m³ ";
            } else {        
            $messageMore = ' - <b>' .  __($typeName , __FILE__) . "</b> " . __($this->getSynonyme('stable'),__FILE__) 
            . " " .__($this->getSynonyme('au niveau'),__FILE__) ." "
                . $newCategory . " " . __('avec', __FILE__) . " "
                . $newData . " part/m³ ";
            }
        }

        return [$message,  $messageMore, $messageHigh];
    }


    public function getLevelPollen($value, $type)
    {
        $allranges = SetupPollen::$pollenRange;
        $ranges = $allranges[$type];
        foreach ($ranges as $color => $range) {
            if ($range[0] <= $value && $range[1] > $value) {
                return strtolower($this->getElementRiskPollen($color));
            }
        }
    }



    private function formatPollensForTelegram($messages, $city)
    {
        $arrayMessage[] = "&#127804; <b>".__('Alerte',__FILE__)." Pollen - ". $city . "</b> " ." \n" . " ";
        $findLetters = [
            '&#127808;' => 'bas', '&#128545;' => 'haut', '&#128520;' => 'très', '&#127803;' => 'modéré', '&#128545;' => 'very',  '&#127752;' => 'low', '&#128551;' => 'moderate'
            , '&#128545;' => 'high', '&#128520;' => 'very'
        ];
        foreach ($messages as $message) {
            $icon = '';
            foreach ($findLetters as $key => $value) {
                $match = (str_replace($value, '', $message) != $message);
                if ($match) {
                    $icon = $key;
                }
            }
            $arrayMessage[] = "<em>" . $message . "</em> " . $icon . " \n";
        }
        return implode(' ', $arrayMessage);
    }

    private function formatPollensForSms($messages)
    {
        $arrayMessage[] = "-- ".__('Alerte',__FILE__)." Pollen -- \n";
        foreach ($messages as $message) {
            $arrayMessage[] = strip_tags($message) . " \n";
        }
        return implode(' ', $arrayMessage);
    }

    /**
     *  Format Pollen for discord
     */
    private function formatPollenMarkDown($messages)
    {
        $arrayMessage[] = ":blossom: **".__('Alerte',__FILE__)." Pollen** :herb:" . " ";
        $findLetters = [
            ':four_leaf_clover:' => 'bas', ':maple_leaf:' => 'haut', ':rage:' => 'très', ':sunflower:' => 'modéré',
            // ':four_leaf_clover:' => 'low', ':maple_leaf:' => 'high', ':rage:' => 'very', ':sunflower:' => 'moderate'
        ];
        foreach ($messages as $message) {
            $icon = '';
            $message = str_replace('<b>', '**', $message);
            $message = str_replace('</b>', '**', $message);
            $message = strip_tags($message);

            foreach ($findLetters as $key => $value) {
                $match = (str_replace($value, '', $message) != $message);
                if ($match) {
                    $icon = $key;
                }
            }
            $arrayMessage[] = $message . "  " . $icon;
        }
        // log::add('airquality', 'debug', 'Markdown Pollen : '. (implode(' ', $arrayMessage)));
        return implode(' ', $arrayMessage);
    }
}

