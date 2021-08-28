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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect()) {
    include_file('desktop', '404', 'php');
    die();
}

?>
<form class="form-horizontal">
    <fieldset>
        <div class="form-group">
            <label class="col-sm-3 control-label">{{Clef API OpenWeather}}</label>
            <div class="col-sm-6">
                <input id="api-aqi-key" class="configKey form-control" data-l1key="apikey" required />
            </div>
            <?php $state = '' ?>
            <?php if (file_exists(plugin::getPathById('weather'))) : ?>
                <?= '<div class="col-sm-1 tooltips" id="import-weather-key" title="Importer la clef du plugin Weather"><a class="btn btn-xs btn-success">Importer</a></div>' ?>
                <?php $state = 'wheatherPlugin' ?>
                <?php endif ?>
            <?php if (file_exists(plugin::getPathById('airquality'))) : ?>
                <?php if ( $state != 'wheatherPlugin'):  ?>
                    <?= '<div class="col-sm-1 tooltips" id="import-airquality-key" title="Importer la clef du plugin Airquality"><a class="btn btn-xs btn-success">Importer</a></div>' ?>
                <?php endif ?>
            <?php endif ?>
        </div>
        <br>
        <div class="form-group">
            <label title="https://api-dashboard.getambee.com" class="col-sm-3 control-label">{{Clef API Ambee}}</label>
            <div class="col-sm-6">
                <input class="configKey form-control" data-l1key="apikeyAmbee" />
            </div>
        </div>
    </fieldset>

</form>
<script>
    $(document).on('click', '#import-weather-key', function() {
        $.ajax({
            type: "POST",
            url: "plugins/airpollen/core/ajax/airpollen.ajax.php",
            data: {
                action: "getApiKeyWeather"
            },
            dataType: 'json',
            error: function(request, status, error) {
                handleAjaxError(request, status, error);
            },
            success: function(data) {
                console.log("Requete ajax succes : " + data.result)
                document.getElementById("api-aqi-key").value = data.result;
                if (data.state != 'ok') {
                    console.log('ereur AJAX : ' + data.result);
                }
            }
        });
    })

    $(document).on('click', '#import-airquality-key', function() {
        $.ajax({
            type: "POST",
            url: "plugins/airpollen/core/ajax/airpollen.ajax.php",
            data: {
                action: "getApiKeyAirquality"
            },
            dataType: 'json',
            error: function(request, status, error) {
                handleAjaxError(request, status, error);
            },
            success: function(data) {
                console.log("Requete ajax succes : " + data.result)
                document.getElementById("api-aqi-key").value = data.result;
                if (data.state != 'ok') {
                    console.log('ereur AJAX : ' + data.result);
                }
            }
        });
    })
</script>