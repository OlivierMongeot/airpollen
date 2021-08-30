<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */
// error_reporting(E_ALL);
// ini_set('ignore_repeated_errors', TRUE);
// ini_set('display_errors', TRUE);

require_once __DIR__  . '/../../../../core/php/core.inc.php';
require dirname(__FILE__) . '/../../core/php/airpollen.inc.php';

class airpollen extends eqLogic
{

    public static $_widgetPossibility = ['custom' => true, 'custom::layout' => false];

    public static function cron()
    {
        // Assignation d'une minute de refresh aléatoire suite mail Ambee
        foreach (self::byType('airpollen') as $pollen) {

            if ($pollen->getIsEnable() == 1) {

                if ($pollen->getConfiguration('data_refresh') == 'full' || $pollen->getConfiguration('data_refresh') == 'fake_data') {

                    //  Pollen current toutes heures
                    try {
                        $crontabPollen = "1 * * * *";
                       
                        $c = new Cron\CronExpression($crontabPollen, new Cron\FieldFactory);
                        if ($c->isDue()) {
                             log::add('airpollen', 'debug', 'Cron pollen current : ' . $crontabPollen);
                            $pollen->updatePollen();
                        }
                    } catch (Exception $e) {
                        log::add('airpollen', 'debug', __('Expression cron non valide pour update Pollen full', __FILE__) . $pollen->getHumanName() . ' : ' . json_encode($e));
                    }


                    // Pollen forecast 1x jours si enable
                    if ($pollen->getConfiguration('data_forecast') == 'actived') {
                        try {
                            $cronForecast = "2,42 7 * * *";
                           
                            $c = new Cron\CronExpression($cronForecast, new Cron\FieldFactory);

                            if ($c->isDue()) {
                                log::add('airpollen', 'debug', 'Cron forecast Pollen  : ' . $cronForecast);
                                try {
                                    $refresh = $pollen->getCmd(null, 'refresh_pollen_forecast');
                                    if (is_object($refresh)) {
                                        $refresh->execCmd();
                                    } else {
                                        log::add('airpollen', 'debug', 'Impossible de trouver la commande refresh pour ' . $pollen->getHumanName());
                                    }
                                } catch (Exception $e) {
                                    log::add('airpollen', 'debug', __('Erreur pour ', __FILE__) . $pollen->getHumanName() . ' : ' . $e->getMessage());
                                }
                            }
                        } catch (Exception $e) {
                            log::add('airpollen', 'debug', __('Expression cron non valide pour Pollen refresh forecast', __FILE__) . $pollen->getHumanName() . ' - ' .  $e->getMessage());
                        }
                    }
                }

                switch ($pollen->getConfiguration('data_refresh')) {

                    case 'oneByHour':    //  Pollen current toutes heures 7h à 20h
                        $crontab = "1 8,9,10,11,12,13,14,15,16,17,18,19 * * *";
                        break;
                    case 'oneByTwoHour':    //  Pollen current toutes les 2h
                        $crontab = "1 8,10,12,14,16,18 * * *";
                    case 'twoByDay':     //  Pollen current 2x jours
                        $crontab = "1 8,15 * * *";
                        break;
                    case 'ThreeByDay':       //  Pollen current  3x /jour
                        $crontab = "1 8,12,16 * * *";
                    default:
                        $crontab = false;
                }

                if ($crontab) {
                    //  Pollen current toutes heures 7h à 20h
                    try {
                        $c = new Cron\CronExpression($crontab, new Cron\FieldFactory);
                        if ($c->isDue()) {
                            $pollen->updatePollen();
                        }
                    } catch (Exception $e) {
                        log::add('airpollen', 'debug', __('Expression cron non valide pour update Pollen manual', __FILE__) . $pollen->getHumanName() . ' : ' . json_encode($e));
                    }
                }

                
                // Refresh alert Message Pollen
                try {
                    $specialCronPollen =  $pollen->getConfiguration('alertPollenCronTwoMin');
                    if (empty($specialCronPollen)) {
                        $specialCronPollen = '0 0 1 1 *';
                    }
                    $cManual = new Cron\CronExpression($specialCronPollen, new Cron\FieldFactory);
                    if ($cManual->isDue()) {
                        try {
                            $refresh = $pollen->getCmd(null, 'refresh_alert_pollen');
                            if (is_object($refresh)) {
                                $refresh->execCmd();
                            } else {
                                log::add('airpollen', 'debug', __('Impossible de trouver la commande refresh pour ', __FILE__) . $pollen->getHumanName());
                            }
                        } catch (Exception $e) {
                            log::add('airpollen', 'debug', __('Erreur pour ', __FILE__) . $pollen->getHumanName() . ' : ' . $e->getMessage());
                        }
                    }
                } catch (Exception $e) {
                    log::add('airpollen', 'debug', __('Expression cron non valide pour Refresh alert Pollen', __FILE__) . $pollen->getHumanName() . ' : ' . json_encode($specialCron)  . json_encode($e));
                }

            }
        }
    }


    public function getIntervalLastRefresh($cmdXToTest)
    {
        if (is_object($cmdXToTest)) {

            $collectDate = $cmdXToTest->getCollectDate();
            if ($collectDate == null) {
                return 5000;
            }
            $datetimeCollected = DateTime::createFromFormat('Y-m-d H:i:s', $collectDate);
            $dateNow = new DateTime();
            $dateNow->setTimezone(new DateTimeZone('Europe/Paris'));
            $interval = $datetimeCollected->diff($dateNow);
            log::add('airpollen', 'debug', '----------------------------------------------------------------------');
            log::add('airpollen', 'debug', 'Check Intervale derniere Collecte pour ' . $cmdXToTest->getHumanName() . '  : ' . $interval->i . ' m ' . $interval->h . ' h et ' . $interval->d . ' jours');
            $minuteToAdd = 0;
            if ($interval->d > 0) {
                $minuteToAdd = $interval->d * 24 * 60;
            }
            if ($interval->h > 0) {
                $minuteToAdd .= $interval->h * 60;
            }
            $total = $interval->i + $minuteToAdd;
            return $total;
        } else {
            throw new Exception('Commande non trouvée pour calculer l`\'interval de temps');
        }
    }


    public function preInsert()
    {
        $this->setCategory('heating', 1);
        $this->setIsEnable(1);
        $this->setIsVisible(1);
    }

    public function preUpdate()
    {
        if ($this->getIsEnable()) {
            switch ($this->getConfiguration('searchMode')) {
                case 'city_mode':
                    if ($this->getConfiguration('city') == '' || $this->getConfiguration('country_code') == '') {
                        throw new Exception(__('La ville ou le code pays ne peuvent être vide', __FILE__));
                    }
                    break;
                case 'long_lat_mode':
                    if ($this->getConfiguration('longitude') == '' || $this->getConfiguration('latitude') == '') {
                        throw new Exception(__('La longitude ou la latitude ne peuvent être vide', __FILE__));
                    }
                    break;
                case 'dynamic_mode':
                    if ($this->getConfiguration('geoLongitude') == '' || $this->getConfiguration('geoLatitude') == '') {
                        throw new Exception(__('Probleme de localisation par le navigateur', __FILE__));
                    }
            }
        }
    }

    public function postSave()
    {

        if ($this->getIsEnable()) {

            $cmdXCheckNull =  $this->getCmd(null, 'poaceae');
            if (is_object($cmdXCheckNull) && $cmdXCheckNull->execCmd() == null) {
                $cmd = $this->getCmd(null, 'refresh');
                if (is_object($cmd)) {
                    $cmd->execCmd();
                }
            }

            $cmdXCheckNull =  $this->getCmd(null, 'poaceae_min');
            if (is_object($cmdXCheckNull) && $cmdXCheckNull->execCmd() == null) {
                $cmd = $this->getCmd(null, 'refresh_pollen_forecast');
                if (is_object($cmd)  && $this->getConfiguration('data_forecast') == 'actived' && $this->getConfiguration('data_refresh') == 'full') {
                    $cmd->execCmd();
                }
            }
        }
    }


    public function preSave()
    {
        $this->setDisplay("width", "265px");
        if ($this->getConfiguration('data_forecast') == 'disable') {
            $this->setDisplay("height", "220px");
        } else {
            $this->setDisplay("height", "375px");
        }
    }


    public function postUpdate()
    {

        $refreshForecast = $this->getCmd(null, 'refresh_pollen_forecast');
        if (!is_object($refreshForecast)) {
            $refreshForecast = new airpollenCmd();
            $refreshForecast->setName('Rafraichir Forecast Pollen');
        }
        $refreshForecast->setEqLogic_id($this->getId())
            ->setLogicalId('refresh_pollen_forecast')
            ->setType('action')
            ->setSubType('other')->save();

        $refresh = $this->getCmd(null, 'refresh');
        if (!is_object($refresh)) {
            $refresh = new airpollenCmd();
            $refresh->setName('Rafraichir');
        }
        $refresh->setEqLogic_id($this->getId())
            ->setLogicalId('refresh')
            ->setType('action')
            ->setSubType('other')->save();

        $refresh = $this->getCmd(null, 'refresh_alert_pollen');
        if (!is_object($refresh)) {
            $refresh = new airpollenCmd();
            $refresh->setName('Rafraichir les alertes pollens');
        }
        $refresh->setEqLogic_id($this->getId())
            ->setLogicalId('refresh_alert_pollen')
            ->setType('action')
            ->setSubType('other')->save();

        $setup = SetupPollen::$setupPollen;



        foreach ($setup as $command) {
            $cmdInfo = $this->getCmd(null, $command['name']);
            if (!is_object($cmdInfo)) {
                $cmdInfo = new airpollenCmd();
                $cmdInfo->setName($command['title']);
            }
            $cmdInfo->setEqLogic_id($this->getId())
                ->setLogicalId($command['name'])
                ->setType('info')
                ->setTemplate('dashboard', 'tile')
                ->setSubType($command['subType'])
                ->setUnite($command['unit'])
                ->setDisplay('generic_type', 'GENERIC_INFO')
                ->setConfiguration($command['name'], $command['display']);
            if ($command['subType'] == 'numeric' && $this->getConfiguration('data_history') == 'actived') {

                $cmdInfo->setIsHistorized(1)->save();
            } else {
                $cmdInfo->setIsHistorized(0)->save();
            }
        }
    }


    public function toHtml($_version = 'dashboard')
    {
        $replace = $this->preToHtml($_version);
        if (!is_array($replace)) {
            return $replace;
        }
        $this->emptyCacheWidget(); //vide le cache
        $version = jeedom::versionAlias($_version);
        $activePollenCounter = 0;
        $display = new DisplayInfoPollen;
        $tabUnitReplace = [];

        // Pollen ---------------
        $tabHeader = [];
        $counterMain = 0;
        $elementTemplate = getTemplate('core', $version, 'elementPollen', 'airpollen');
        $headerTemplate = getTemplate('core', $version, 'headerPollen', 'airpollen');

        foreach ($this->getCmd('info') as $cmd) {
            $nameCmd = $cmd->getLogicalId();
            $isObjet = is_object($cmd);
            $iconePollen = new IconesPollen;

            if ($nameCmd == 'tree_pollen' || $nameCmd == 'grass_pollen'  || $nameCmd == 'weed_pollen') {
                switch ($nameCmd) {
                    case 'tree_pollen':
                        $treePollenCmd = $this->getCmd(null, 'tree_risk');
                        $level = $treePollenCmd->execCmd();
                        $headerReplace['#main_risk#'] = $isObjet ? $display->getPollenRisk($level) : '';

                        log::add('airpollen', 'debug', 'tree_risk : ' . $level);
                        break;
                    case 'grass_pollen':
                        $grassPollenCmd = $this->getCmd(null, 'grass_risk');
                        $level = $grassPollenCmd->execCmd();
                        $headerReplace['#main_risk#'] = $isObjet ? $display->getPollenRisk($level) : '';
                        log::add('airpollen', 'debug', 'grass_risk : ' . $level);
                        break;
                    case 'weed_pollen':
                        $weedPollenCmd = $this->getCmd(null, 'weed_risk');
                        $level = $weedPollenCmd->execCmd();
                        $headerReplace['#main_risk#'] = $isObjet ? $display->getPollenRisk($level) : '';
                        log::add('airpollen', 'debug', 'weed_risk : ' . $level);
                }
                $headerReplace['#id#'] =  $isObjet ? $this->getId() : '';
                $headerReplace['#main_cmd_pollen_id#'] = $isObjet ? $cmd->getId() : '';
                $headerReplace['#main_pollen_name#'] = $isObjet ? __($cmd->getName(), __FILE__) : '';
                $headerReplace['#list_main_pollen#'] = $isObjet ? $display->getListPollen($nameCmd) : '';
                $headerReplace['#history#'] =  $isObjet ? 'history cursor' : '';
                $value = $isObjet ? $cmd->execCmd() : '';
                log::add('airpollen', 'debug', 'Value Risk for counter: ' . $value);
                if ($value > 0) {
                    $counterMain++;
                }
                $headerReplace['#main_pollen_value#'] = $value;

                $newIcon = $iconePollen->getIcon($nameCmd, $value, $cmd->getId());
                $headerReplace['#icone__pollen#'] = $isObjet ? $newIcon : '';
                $tabHederOne = template_replace($headerReplace, $headerTemplate);
                $tabHeader[] = [$tabHederOne, $value];
            
            } else  if ($nameCmd == 'updatedAt') {
                $updatedAt = ($isObjet && $cmd->execCmd()) ? $display->parseDate($cmd->getCollectDate()) : '';
            
            } else if ($nameCmd == 'telegramPollen') {
                $message_alert =  $isObjet ? $cmd->execCmd() : '';
                $alert = (!empty($message_alert)) ? true : false;
                if ($alert) {
                    $htmlAlertPollen = '<div style="text-align: center; margin:15px 0px 0px 0px">';
                    $htmlAlertPollen .= '<marquee scrollamount="4" width="85%" class="state" style="font-size: 100%;height:18px;margin: -10px -10px important!">' . $message_alert . '</marquee>';
                    $htmlAlertPollen .= '</div>';
                    $replace['#message_alert#'] =  $htmlAlertPollen;
                }
            
            } else if ($cmd->getConfiguration($nameCmd) == 'slide') {

                $activePollenCounter = ($cmd->execCmd() > 0) ? $activePollenCounter + 1 : $activePollenCounter;
                $valueCurrent = $isObjet ? $cmd->execCmd() : '';
                $maxDisplayLevel = $this->getConfiguration('pollen_alert_level');
                // Value > 0 && Value > Max Display && Cmd visible 
                if ($cmd->getIsVisible() == 1 && $maxDisplayLevel <= $valueCurrent && $valueCurrent > 0) {

                    // Latest display
                    $newIcon = $iconePollen->getIcon($nameCmd, $cmd->execCmd(), $cmd->getId());
                    $unitreplace['#icone#'] =  $isObjet ? $newIcon : '';
                    $unitreplace['#id#'] =  $isObjet ? $this->getId() : '';
                    $value = $isObjet ? $cmd->execCmd() : '';
                    $unitreplace['#value#'] =  $value;
                    $unitreplace['#name#'] = $isObjet ? $cmd->getLogicalId() : '';
                    $unitreplace['#display-name#'] =  $isObjet ? __($cmd->getName(), __FILE__) : '';
                    $unitreplace['#cmdid#'] = $isObjet ?  $cmd->getId() : '';
                    $unitreplace['#history#'] =  $isObjet ? 'history cursor' : '';
                    $unitreplace['#info-modalcmd#'] = $isObjet ?  'info-modal' . $cmd->getLogicalId() . $this->getId() : '';
                    $unitreplace['#unity#'] =  $isObjet ? $cmd->getUnite() : '';

                    // Forecast Display 
                    if ($this->getConfiguration('data_forecast') != 'disable') {
                        $maxCmd = $this->getCmd(null, $nameCmd . '_max');
                        $unitreplace['#max#'] = (is_object($maxCmd) && !empty($maxCmd->execCmd())) ? $maxCmd->execCmd() : "[0,0,0]";
                        $minCmd = $this->getCmd(null, $nameCmd . '_min');
                        $unitreplace['#min#'] = (is_object($minCmd) && !empty($minCmd->execCmd())) ? $minCmd->execCmd() : "[0,0,0]";
                        $unitreplace['#color#'] =  ($isObjet &&  !empty($iconePollen->getColor())) ?  $iconePollen->getColor() : '#222222';
                        $labels = $this->getCmd(null, 'daysPollen');
                        $unitreplace['#labels#'] =  (is_object($labels) && !empty($labels->execCmd())) ? $labels->execCmd() : "['no','-','data']";
                        $unitreplace['#height0#'] = '';
                    } else {
                        $unitreplace['#labels#'] = "['0','0','0']"; // for not js error 
                        $unitreplace['#max#'] = "[0,0,0]";
                        $unitreplace['#min#'] =  "[0,0,0]";
                        $unitreplace['#color#'] = '#333333';
                        $unitreplace['#height0#'] = 'style="height:0"';
                    }


                    $iconePollen->getIcon($nameCmd, $cmd->execCmd(), $cmd->getId());
                    $unitreplace['#risk#'] =  $isObjet ?  $display->getElementRiskPollen($iconePollen->getColor()) : '';

                    $unitreplace['#info-tooltips#'] =   __("Cliquez pour + d'info", __FILE__);
                    $unitreplace['#mini#'] = __("Mini 10 jours", __FILE__);
                    $unitreplace['#maxi#'] = __("Maxi 10 jours", __FILE__);
                    $unitreplace['#tendency#'] = __("Tendance 12h", __FILE__);
                    $unitreplace['#average#'] = __("Moyenne 10 jours", __FILE__);

                    if ($cmd->getIsHistorized() == 1) {
                        $startHist = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' -' . 240 . ' hour'));
                        $historyStatistique = $cmd->getStatistique($startHist, date('Y-m-d H:i:s'));
                        $unitreplace['#minHistoryValue#'] =  $isObjet ?  $display->formatValueForDisplay($historyStatistique['min'], 'short') : '';
                        $unitreplace['#maxHistoryValue#'] =  $isObjet ? $display->formatValueForDisplay($historyStatistique['max'], 'short') : '';
                        $unitreplace['#averageHistoryValue#'] =  $isObjet ?  $display->formatValueForDisplay($historyStatistique['avg'], 'short') : '';
                        $startHist = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' -' . 12 . ' hour'));
                        $tendance = $cmd->getTendance($startHist, date('Y-m-d H:i:s'));
                        if ($tendance > config::byKey('historyCalculTendanceThresholddMax')) {
                            $unitreplace['#tendance#'] = $isObjet ? 'fas fa-arrow-up' : '';
                        } else if ($tendance < config::byKey('historyCalculTendanceThresholddMin')) {
                            $unitreplace['#tendance#'] = $isObjet ? 'fas fa-arrow-down' : '';
                        } else {
                            $unitreplace['#tendance#'] = $isObjet ? 'fas fa-minus' : '';
                        }
                        $unitreplace['#display#'] = '';
                    } else {
                        $unitreplace['#display#'] =  $isObjet ? 'hidden' : '';
                    }
                    $tabUnitReplace[] = [template_replace($unitreplace, $elementTemplate), $value];
                }
                // Cas Pollen à ZERO 
                else if ($this->getConfiguration('pollen_alert_level') == 0 && $cmd->execCmd() == 0) {

                    // log::add('airpollen', 'debug', 'Name Cmd Pollen ZERO : ' . $cmd->getName() . ' - ' .  $cmd->getLogicalId());
                    $newIcon = $iconePollen->getIcon($nameCmd, $cmd->execCmd(), $cmd->getId());
                    $pollenZeroReplace['#icone#'] = $isObjet ? $newIcon : '';
                    $pollenZeroReplace['#id#'] = $isObjet ? $this->getId() : '';
                    $pollenZeroReplace['#value#'] = $isObjet ? 0 : '';
                    $pollenZeroReplace['#name#'] = $isObjet ?  $cmd->getLogicalId() : '';
                    $pollenZeroReplace['#display-name#'] =  $isObjet ? __($cmd->getName(), __FILE__) : '';
                    $pollenZeroReplace['#cmdid#'] = $isObjet ?  $cmd->getId() : '';
                    $pollenZeroReplace['#info-modalcmd#'] =  $isObjet ? 'info-modal' . $cmd->getLogicalId() . $this->getId() : '';
                    $pollenZeroReplace['#message#'] = __('Aucune Détection', __FILE__);
                    $templateZero = getTemplate('core', $version, 'elementPollenZero', 'airpollen');
                    if ($this->getConfiguration('data_forecast') != 'disable') {
                        $pollenZeroReplace['#height#'] =  'min-height:75px;';
                    } else {
                        $pollenZeroReplace['#height#'] = '';
                    }
                    $tabZero[] = template_replace($pollenZeroReplace, $templateZero);
                }

                // Affichage central pour Others à la fin/(double passage boucle) car double affichage
                if ($nameCmd == 'others') {
                    $headerReplace['#main_pollen_value#'] =  $isObjet && $cmd->execCmd() !== null ? $cmd->execCmd() : '';
                    log::add('airpollen', 'debug', 'Value Cmd Pollen Others : ' . $cmd->execCmd() . ' - ' .  $cmd->getLogicalId());
                    $headerReplace['#id#'] =  $isObjet ? $this->getId() : '';
                    $headerReplace['#main_cmd_pollen_id#'] =   $isObjet ? $cmd->getId() : '';
                    $headerReplace['#main_pollen_name#'] =  $isObjet ? __($cmd->getName(), __FILE__) : '';
                    $newIcon = $iconePollen->getIcon($nameCmd, $cmd->execCmd(), $cmd->getId());
                    $headerReplace['#icone__pollen#'] = $isObjet ?  $newIcon : '';
                    $headerReplace['#list_main_pollen#'] =  $isObjet ?  $display->getListPollen($nameCmd) : '';
                    $risk =  $isObjet ? $display->getElementRiskPollen($iconePollen->getColor($cmd->execCmd(), $nameCmd)) : '';
                    log::add('airpollen', 'debug', 'Risk Cmd Pollen Others : ' . $risk . ' - ' .  $cmd->getLogicalId());
                    $headerReplace['#main_risk#'] = $risk;
                    $value = $isObjet ? $cmd->execCmd() : '';
                    $tabHeaderOne = template_replace($headerReplace, $headerTemplate);
                    $tabHeader[] = [$tabHeaderOne, $value];
                }
            }
        }
        $tabUnityValue = array_column($tabUnitReplace, 1);
        $tabUnityHtml = array_column($tabUnitReplace, 0);
        array_multisort($tabUnityValue, SORT_DESC, $tabUnityHtml);

        $counterPollenZero = 0;
        if (isset($tabZero)) {
            if ($this->getConfiguration('data_forecast') != 'disable') {
                $newArray = array_chunk($tabZero, 3);
            } else {
                $newArray = array_chunk($tabZero, 1);
            }
            foreach ($newArray as $arr) {
                $tabUnityHtml[] = implode('', $arr);
                $counterPollenZero++;
            }
        }


        if (!$alert) {
            if ($activePollenCounter == 0) {
                // Vérifier si pollen principaux présent dans la reponse API
                if (isset($counterMain) && $counterMain > 0) {
                    $active_pollen_label = __('Pollens principaux actifs', __FILE__);
                    $htmlActivePollen = '<div title="' . $updatedAt . '" class="cmd noRefresh header-' . $this->getId() . '-mini ';
                    $htmlActivePollen .= 'active-pollen-' . $this->getId() . ' " data-type="info" data-subtype="string" data-cmd_id="' . $cmd->getId() . '">';
                    $htmlActivePollen .= $active_pollen_label . '&nbsp;&nbsp;' . $counterMain . ' / 3 </div>';
                    $replace['#message_alert#'] = $htmlActivePollen;
                } else {
                    $active_pollen_label = __('Aucun pollen actif', __FILE__);
                    $htmlActivePollen = '<div title="' . $updatedAt . '" class="cmd noRefresh header-' . $this->getId() . '-mini ';
                    $htmlActivePollen .= 'active-pollen-' . $this->getId() . ' " data-type="info" data-subtype="string" data-cmd_id="' . $cmd->getId() . '">';
                    $htmlActivePollen .= $active_pollen_label . '</div>';
                    $replace['#message_alert#'] = $htmlActivePollen;
                }
            } else {
                $active_pollen_label = __('Pollens actifs', __FILE__);
                $htmlActivePollen = '<div title="' . $updatedAt . '" class="cmd noRefresh header-' . $this->getId() . '-mini ';
                $htmlActivePollen .= 'active-pollen-' . $this->getId() . ' " data-type="info" data-subtype="string" data-cmd_id="' . $cmd->getId() . '">';
                $htmlActivePollen .= $active_pollen_label . '&nbsp;&nbsp;' . $activePollenCounter . ' / 15 </div>';
                $replace['#message_alert#'] = $htmlActivePollen;
            }
        }
        $tabValue  = array_column($tabHeader, 1);
        $tabHtml = array_column($tabHeader, 0);
        array_multisort($tabValue, SORT_DESC, $tabHtml);

        if (in_array(0, $tabValue) && $this->getConfiguration('pollen_alert_level') > 0) {
            array_pop($tabHtml);
        }
        $replace['#header#'] = implode('', $tabHtml);
        $elementHtml = new CreateHtmlPollen($tabUnityHtml, $this->getId(), 1, $version, $counterPollenZero);

        // Global  --------------------------------------------------------------
        if ($this->getConfiguration('searchMode') == 'follow_me') {
            [$lon, $lat] = $this->getCurrentLonLat();
            $replace['#button#'] = '<span><i class="fas fa-map-marker-alt fa-lg"></i></span> ' . $this->getCurrentCityName();
            $replace['#long_lat#'] = 'Lat ' . $display->formatValueForDisplay($lat, null, 4) . '° - Lon ' . $display->formatValueForDisplay($lon, null, 4) . '°';
            $replace['#height_footer#'] = 'height:50px';
            $replace['#stateRefreshDesktop#'] = 'style="display:none"';
            $replace['#padding#'] = '5px';
        } else {
            $replace['#button#'] = '';
            $replace['#long_lat#'] = '';
            $replace['#height_footer#'] = 'height:0px';
            $replace['#stateRefreshDesktop#'] = '';
            $replace['#padding#'] = '0px';
        }


        $replace['#info-tooltips#'] = __("Cliquez pour + d'info", __FILE__);
        [$miniSlide, $state] = $elementHtml->getLayer();
        $replace['#mini_slide#'] =  $miniSlide;

        if ($state == 'empty') {
            $replace['#hidden#'] = 'hidden';
        } else {
            $replace['#hidden#'] = '';
        }

        $refresh = $this->getCmd(null, 'refresh');
        $replace['#refresh#'] = is_object($refresh) ? $refresh->getId() : '';

        if ($this->getConfiguration('animation_aqi') === 'disable_anim') {
            $replace['#animation#'] = 'disabled';
            $replace['#classCaroussel#'] = 'data-interval="false"';
        } else {
            $replace['#animation#'] = 'active';
            $replace['#classCaroussel#'] = '';
        }
        return $this->postToHtml($_version, template_replace($replace, getTemplate('core', $version, 'airpollen', __CLASS__)));
    }

    private function setMinutedAction($configName, $delay = 2)
    {
        $now = new \DateTime();
        $hour = $now->format('H');
        $minute = $now->format('i');
        $minuteEnd = $minute + $delay;
        if ($minuteEnd > 59) {
            $minuteEnd = str_replace('6', '', $minuteEnd);
            $hour = $hour + 1;
        }
        $cron =  $minuteEnd . ' ' . $hour . ' * * *';
        log::add('airpollen', 'debug', 'Set cron + ' . $delay . ' - ' . $cron . ' to stop message alert for equipement ' . $this->getName());
        $this->setConfiguration($configName, $cron)->save();
    }


    public static function postConfig_apikey()
    {
        if (config::byKey('apikey', 'airpollen') == '' && config::byKey('apikeyAmbee', 'airpollen') == '') {
            throw new Exception('Les clefs API sont requises pour faire marcher le plugin');
        }
    }


    private function getCurrentCityName()
    {
        if ($this->getConfiguration('searchMode') == 'city_mode') {
            $city =  $this->getConfiguration('city');
        } else if ($this->getConfiguration('searchMode') == 'long_lat_mode') {
            $city = $this->getConfiguration('city-llm');
        } else if ($this->getConfiguration('searchMode') == 'dynamic_mode') {
            $city = $this->getConfiguration('geoCity');
        } else if ($this->getConfiguration('searchMode') == 'follow_me') {
            $city = config::byKey('DynCity', 'airpollen');
        } else if ($this->getConfiguration('searchMode') == 'server_mode') {
            $city = config::byKey('info::city');
        }
        log::add('airpollen', 'debug', 'Get Current City : ' . $city);
        return isset($city) ? $city : 'No city';
    }

    public function getCurrentLonLat()
    {
        if ($this->getConfiguration('searchMode') == 'city_mode') {
            log::add('airpollen', 'debug', 'Mode city_mode concerning ' . $this->getHumanName());
            $lon =  $this->getConfiguration('city_longitude');
            $lat =  $this->getConfiguration('city_latitude');
        } elseif ($this->getConfiguration('searchMode') == 'long_lat_mode') {
            log::add('airpollen', 'debug', 'Mode long_lat_mode concerning ' . $this->getHumanName());
            $lon = $this->getConfiguration('longitude');
            $lat = $this->getConfiguration('latitude');
        } elseif ($this->getConfiguration('searchMode') == 'dynamic_mode') {
            log::add('airpollen', 'debug', 'Mode dynamic_mode concerning ' . $this->getHumanName());
            $lon = $this->getConfiguration('geoLongitude');
            $lat = $this->getConfiguration('geoLatitude');
        } else if ($this->getConfiguration('searchMode') == 'follow_me') {
            log::add('airpollen', 'debug', 'Mode follow_me concerning ' . $this->getHumanName());
            $lon = config::byKey('DynLongitude', 'airpollen');
            $lat = config::byKey('DynLatitude', 'airpollen');
        } else if ($this->getConfiguration('searchMode') == 'server_mode') {
            log::add('airpollen', 'debug', 'Mode server_mode concerning ' . $this->getHumanName());
            $lon = config::byKey('info::longitude');
            $lat = config::byKey('info::latitude');
        }
        return [$lon, $lat];
    }


    /**
     * Redirige l'appel API vers la bonne fonction + check des coordonnées 
     */
    private function getApiData(string $apiName)
    {
        $api = new ApiPollen();
        if ($this->getConfiguration('data_refresh') == 'fake_data') {
            return  $api->getFakeData($apiName);
        }

        $city = $this->getCurrentCityName();
        [$lon, $lat] = $this->getCurrentLonLat();
        log::add('airpollen', 'debug', $this->getHumanName() . ' -> Start API ' . $apiName . ' Calling for City : ' . $city . ' - Long :' . $lon . ' Lat :' . $lat);
        return $api->$apiName($lon, $lat);
    }


    /**
     * Pour recevoir appel Ajax. Utilisé dans la configuration mode "Geolocalisation du Navigateur"
     */
    public static function getCityName($longitude, $latitude, $save = false)
    {
        $api = new ApiPollen;
        $city  = $api->callApiReverseGeoLoc($longitude, $latitude);
        if ($save) {
            log::add('airpollen', 'debug', 'Save City : ' . $city . ' en config general');
            config::save('DynCity', $city, 'airpollen');
        }
        return $city;
    }


    /**
     * Pour appel Ajax. Utilisé dans la configuration mode "Par ville" et Follow me 
     */
    public static function getCoordinates($city, $country_code, $state_code = null)
    {
        $api = new ApiPollen;
        log::add('airpollen', 'debug', 'Get new Coordinate Ajax for config -By City- or -Follow Me-');
        return $api->callApiGeoLoc($city, $country_code, $state_code = null);
    }


    /**
     * Utlise en ajax pour mode follow me 
     */
    public static function setNewGeoloc($longitude, $latitude)
    {
        log::add('airpollen', 'debug', 'Save latitude et longitude en config generale pour mode Follow Me');
        config::save('DynLatitude', $latitude, 'airpollen');
        config::save('DynLongitude', $longitude, 'airpollen');
        return [$latitude, $longitude];
    }


    private function getParamAlertPollen()
    {
        $arrayLevel['poaceae_alert_level'] = $this->getConfiguration('poaceae_alert_level');
        $arrayLevel['alder_alert_level'] = $this->getConfiguration('alder_alert_level');
        $arrayLevel['birch_alert_level'] = $this->getConfiguration('birch_alert_level');
        $arrayLevel['cypress_alert_level'] = $this->getConfiguration('cypress_alert_level');
        $arrayLevel['elm_alert_level'] = $this->getConfiguration('elm_alert_level');
        $arrayLevel['hazel_alert_level'] = $this->getConfiguration('hazel_alert_level');
        $arrayLevel['oak_alert_level'] = $this->getConfiguration('oak_alert_level');
        $arrayLevel['pine_alert_level'] = $this->getConfiguration('pine_alert_level');
        $arrayLevel['plane_alert_level'] = $this->getConfiguration('plane_alert_level');
        $arrayLevel['poplar_alert_level'] = $this->getConfiguration('poplar_alert_level');
        $arrayLevel['chenopod_alert_level'] = $this->getConfiguration('chenopod_alert_level');
        $arrayLevel['mugwort_alert_level'] = $this->getConfiguration('mugwort_alert_level');
        $arrayLevel['nettle_alert_level'] = $this->getConfiguration('nettle_alert_level');
        $arrayLevel['ragweed_alert_level'] = $this->getConfiguration('ragweed_alert_level');
        $arrayLevel['others_alert_level'] = $this->getConfiguration('others_alert_level');
        $arrayLevel['alert_notification'] = $this->getConfiguration('alert_notification');
        $arrayLevel['alert_details'] = $this->getConfiguration('alert_details');
        return $arrayLevel;
    }

    /**
     * Création tableau associatif avec data courante pour comparaison / nouvelles valeurs 
     * 
     *  */
    private function getCurrentValues()
    {
        $dataArray = [];
        foreach ($this->getCmd('info') as $cmd) {
            $logicId = is_object($cmd) ?  $cmd->getLogicalId() : '';
            $value = is_object($cmd) ? $cmd->execCmd() : '';
            $dataArray[$logicId] = $value;
        }
        return $dataArray;
    }

    /**
     * Appel API Pollen Live + Update des Commands + reorder by level  
     */
    public function updatePollen()
    {
        $iMinutes = $this->getIntervalLastRefresh($this->getCmd(null, 'grass_pollen'));
        if ($iMinutes > 1) {
        log::add('airpollen', 'debug', 'Interval > 1 : Start Refresh Pollen latest');
        $dataAll = $this->getApiData('getAmbee');
        if (isset($dataAll->data)) {
            $oldData = $this->getCurrentValues();
            $dataPollen = $dataAll->data;
            $this->checkAndUpdateCmd('tree_risk', $dataPollen[0]->Risk->tree_pollen);
            $this->checkAndUpdateCmd('weed_risk', $dataPollen[0]->Risk->weed_pollen);
            $this->checkAndUpdateCmd('grass_risk', $dataPollen[0]->Risk->grass_pollen);
            $this->checkAndUpdateCmd('tree_pollen', $dataPollen[0]->Count->tree_pollen);
            $this->checkAndUpdateCmd('weed_pollen', $dataPollen[0]->Count->weed_pollen);
            $this->checkAndUpdateCmd('grass_pollen', $dataPollen[0]->Count->grass_pollen);
            // Cas API fournit juste 3 principaux Pollen
            $this->checkAndUpdateCmd('poaceae', isset($dataPollen[0]->Species->Grass->{"Grass / Poaceae"}) ? $dataPollen[0]->Species->Grass->{"Grass / Poaceae"} : '');
            $this->checkAndUpdateCmd('alder',  isset($dataPollen[0]->Species->Tree->Alder) ? $dataPollen[0]->Species->Tree->Alder : '');
            $this->checkAndUpdateCmd('birch', isset($dataPollen[0]->Species->Tree->Birch) ? $dataPollen[0]->Species->Tree->Birch : '');
            $this->checkAndUpdateCmd('cypress', isset($dataPollen[0]->Species->Tree->Cypress) ? $dataPollen[0]->Species->Tree->Cypress : '');
            $this->checkAndUpdateCmd('elm',  isset($dataPollen[0]->Species->Tree->Elm) ? $dataPollen[0]->Species->Tree->Elm : '');
            $this->checkAndUpdateCmd('hazel', isset($dataPollen[0]->Species->Tree->Hazel) ? $dataPollen[0]->Species->Tree->Hazel : '');
            $this->checkAndUpdateCmd('oak', isset($dataPollen[0]->Species->Tree->Oak) ? $dataPollen[0]->Species->Tree->Oak : '');
            $this->checkAndUpdateCmd('pine', isset($dataPollen[0]->Species->Tree->Pine) ? $dataPollen[0]->Species->Tree->Pine  : '');
            $this->checkAndUpdateCmd('plane', isset($dataPollen[0]->Species->Tree->Plane) ? $dataPollen[0]->Species->Tree->Plane  : '');
            $this->checkAndUpdateCmd('poplar', isset($dataPollen[0]->Species->Tree->{"Poplar / Cottonwood"}) ? $dataPollen[0]->Species->Tree->{"Poplar / Cottonwood"}  : '');
            $this->checkAndUpdateCmd('chenopod', isset($dataPollen[0]->Species->Weed->Chenopod) ? $dataPollen[0]->Species->Weed->Chenopod : '');
            $this->checkAndUpdateCmd('mugwort', isset($dataPollen[0]->Species->Weed->Mugwort) ? $dataPollen[0]->Species->Weed->Mugwort : '');
            $this->checkAndUpdateCmd('nettle', isset($dataPollen[0]->Species->Weed->Nettle) ? $dataPollen[0]->Species->Weed->Nettle : '');
            $this->checkAndUpdateCmd('ragweed', isset($dataPollen[0]->Species->Weed->Ragweed) ? $dataPollen[0]->Species->Weed->Ragweed : '');
            $this->checkAndUpdateCmd('others', isset($dataPollen[0]->Species->Others) ? $dataPollen[0]->Species->Others : '');
            $this->checkAndUpdateCmd('updatedAt', $dataPollen[0]->updatedAt);
            $paramAlertPollen = $this->getParamAlertPollen();
            $display = new DisplayInfoPollen;
            $city = $this->getCurrentCityName();
            log::add('airpollen', 'debug', 'City For Pollen Message : ' . $city);
            $messagesPollens =  $display->getAllMessagesPollen($oldData, $dataPollen, $paramAlertPollen, $city);
            $this->checkAndUpdateCmd('messagePollen', $messagesPollens[0]);
            $telegramMess = !empty($messagesPollens[0]) ? $messagesPollens[1] : '';
            $this->checkAndUpdateCmd('telegramPollen', $telegramMess);
            $smsMess = !empty($messagesPollens[0]) ? $messagesPollens[2] : '';
            $this->checkAndUpdateCmd('smsPollen',  $smsMess);
            $markdownMessage = !empty($messagesPollens[0]) ? $messagesPollens[3] : '';
            $this->checkAndUpdateCmd('markdownPollen', $markdownMessage);
            if (!empty($messagesPollens[0])) {
                $this->setMinutedAction('alertPollenCronTwoMin', 2);
            }

            if ($this->getConfiguration('data_refresh') == 'fake_data') {
                $this->updateForecastPollen();
            }

            $this->refreshWidget();
        }
        } else {
            log::add('airpollen', 'debug', 'Dernier Pollen latest Update < 1 min, veuillez patienter svp');
        }
    }





    /**
     * Appel API Forecast Pollens + Update des Commands 
     */
    public function updateForecastPollen()
    {
        $cmdXToTest = $this->getCmd(null, 'others_min');
        $interval = $this->getIntervalLastRefresh($cmdXToTest);
        log::add('airpollen', 'debug', 'Forecast Pollen : Test Interval last refresh = ' . $interval . ' min');
        if ($interval >= 720) {
            log::add('airpollen', 'debug', 'Forecast Pollen : Interval refresh > 12h');
            $forecast =  $this->getApiData('getForecastPollen');
            log::add('airpollen', 'debug', 'Forecast Pollen parsed : ' . json_encode($forecast));
            if (is_array($forecast) && !empty($forecast)) {

                $this->checkAndUpdateCmd('daysPollen', json_encode($forecast['Alder']['day']));
                log::add('airpollen', 'debug', 'Alder Pollen Days : ' . json_encode($forecast['Alder']['day']));
                $this->checkAndUpdateCmd('poaceae_min', json_encode($forecast['Poaceae']['min']));
                log::add('airpollen', 'debug', 'Poaceae Pollen Min : ' . json_encode($forecast['Poaceae']['min']));
                $this->checkAndUpdateCmd('poaceae_max', json_encode($forecast['Poaceae']['max']));
                log::add('airpollen', 'debug', 'Poaceae Pollen Max : ' . json_encode($forecast['Poaceae']['max']));
                $this->checkAndUpdateCmd('alder_min', json_encode($forecast['Alder']['min']));
                $this->checkAndUpdateCmd('alder_max', json_encode($forecast['Alder']['max']));
                $this->checkAndUpdateCmd('birch_min', json_encode($forecast['Birch']['min']));
                $this->checkAndUpdateCmd('birch_max', json_encode($forecast['Birch']['max']));
                $this->checkAndUpdateCmd('cypress_min', json_encode($forecast['Cypress']['min']));
                $this->checkAndUpdateCmd('cypress_max', json_encode($forecast['Cypress']['max']));
                $this->checkAndUpdateCmd('elm_min', json_encode($forecast['Elm']['min']));
                $this->checkAndUpdateCmd('elm_max', json_encode($forecast['Elm']['max']));
                $this->checkAndUpdateCmd('hazel_min', json_encode($forecast['Hazel']['min']));
                $this->checkAndUpdateCmd('hazel_max', json_encode($forecast['Hazel']['max']));
                $this->checkAndUpdateCmd('oak_min', json_encode($forecast['Oak']['min']));
                $this->checkAndUpdateCmd('oak_max', json_encode($forecast['Oak']['max']));
                $this->checkAndUpdateCmd('pine_min', json_encode($forecast['Pine']['min']));
                $this->checkAndUpdateCmd('pine_max', json_encode($forecast['Pine']['max']));
                $this->checkAndUpdateCmd('plane_min', json_encode($forecast['Plane']['min']));
                $this->checkAndUpdateCmd('plane_max', json_encode($forecast['Plane']['max']));
                $this->checkAndUpdateCmd('poplar_min', json_encode($forecast['Poplar']['min']));
                $this->checkAndUpdateCmd('poplar_max', json_encode($forecast['Poplar']['max']));
                $this->checkAndUpdateCmd('chenopod_min', json_encode($forecast['Chenopod']['min']));
                $this->checkAndUpdateCmd('chenopod_max', json_encode($forecast['Chenopod']['max']));
                $this->checkAndUpdateCmd('mugwort_min', json_encode($forecast['Mugwort']['min']));
                $this->checkAndUpdateCmd('mugwort_max', json_encode($forecast['Mugwort']['max']));
                $this->checkAndUpdateCmd('nettle_min', json_encode($forecast['Nettle']['min']));
                $this->checkAndUpdateCmd('nettle_max', json_encode($forecast['Nettle']['max']));
                $this->checkAndUpdateCmd('ragweed_min', json_encode($forecast['Ragweed']['min']));
                $this->checkAndUpdateCmd('ragweed_max', json_encode($forecast['Ragweed']['max']));
                $this->checkAndUpdateCmd('others_min', json_encode($forecast['Others']['min']));
                $this->checkAndUpdateCmd('others_max', json_encode($forecast['Others']['max']));
                $this->refreshWidget();
                log::add('airpollen', 'debug', 'Refresh Forecast Pollen finish');
            } else {
                log::add('airpollen', 'debug', 'Cas Forecast != [] ou [] vide : pas de refresh des data');
            }
        } else {
            log::add('airpollen', 'debug', 'Test date de dernière collecte forecast Pollen < 720 min test jour : pas de refresh');
        }
    }

    /**
     * Pour supprimer le message de warning et refresh le widget
     */
    public function deleteAlertPollen()
    {
        $this->checkAndUpdateCmd('messagePollen', '');
        $this->checkAndUpdateCmd('telegramPollen', '');
        $this->checkAndUpdateCmd('smsPollen', '');
        $this->checkAndUpdateCmd('markdownPollen', '');
        $this->refreshWidget();
    }
}

class airpollenCmd extends cmd
{

    public static $_widgetPossibility = array('custom' => false);

    public function execute($_options = [])
    {
        
        if ($this->getLogicalId() == 'refresh') {
            log::add('airpollen', 'debug', '---------------------------------------------------');
            log::add('airpollen', 'debug', 'Refresh equipement ' . $this->getEqLogic()->getHumanName());
            $this->getEqLogic()->updatePollen();
        }

        if ($this->getLogicalId() == 'refresh_pollen_forecast') {
            log::add('airpollen', 'debug', '---------------------------------------------------');
            log::add('airpollen', 'debug', 'Refresh Forecast Pollen equipement ' . $this->getEqLogic()->getHumanName());
            $this->getEqLogic()->updateForecastPollen();
        }

        if ($this->getLogicalId() == 'refresh_alert_pollen') {
            log::add('airpollen', 'debug', 'Cron Action : Delete/Refresh Alert Pollen equipement ' . $this->getEqLogic()->getHumanName());
            log::add('airpollen', 'debug', '---------------------------------------------------');
            $this->getEqLogic()->deleteAlertPollen();
        }
        log::add('airpollen', 'debug', 'Execute commande option : ' . json_encode($_options));
    }
}
