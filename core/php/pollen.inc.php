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

require_once __DIR__  . '/../../../../core/php/core.inc.php';
/*
 *
 * Fichier d’inclusion si vous avez plusieurs fichiers de class ou 3rdParty à inclure
 * 
 */

if (!class_exists('Setup')){
    require_once dirname(__FILE__) . '/../../3rdparty/class.Setup.php';
}
if (!class_exists('ApiAqi')){
    require_once dirname(__FILE__) . '/../../3rdparty/class.ApiAqi.php';
}
if (!class_exists('CreateHtmlAqi')){
    require_once dirname(__FILE__) . '/../../3rdparty/class.CreateHtmlAqi.php';
}
if (!class_exists('IconesPollen')){
    require_once dirname(__FILE__) . '/../../3rdparty/class.IconesPollen.php';
}
if (!class_exists('DisplayInfo')){
    require_once dirname(__FILE__) . '/../../3rdparty/class.DisplayInfo.php';
}


?>