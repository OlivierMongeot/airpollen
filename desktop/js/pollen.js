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


    var getCity = (longitude, latitude, displayFor) => {   
    console.log("Requete ajax : getcity" +  ' Longitude : ' + longitude + ' latitude ' + latitude  )
    $.ajax({
        type: "POST",
        url: "plugins/airquality/core/ajax/airquality.ajax.php",
        data: { action: "getcity", longitude: longitude, latitude: latitude, save: 'false' },
        dataType: 'json',
        error: function (request, status, error) { handleAjaxError(request, status, error); },
        success: function (data) {
            console.log("Requete ajax succes getCity(): " + data.result)
            if (displayFor == 'geoCity') {
                // Geo city
                document.getElementById("geoCity").value = data.result;
            } else {
                //  LONG LAT MODE 
                document.getElementById("geo-loc-llm").value = data.result;
            }
            if (data.state != 'ok') {
                console.log('ereur AJAX : ' + data.result);
            }
        }
    });
}


$('#validate_dyn_mode').on('click', () => {

    if (navigator.geolocation) {
        console.log('Check New Location')
        navigator.geolocation.getCurrentPosition(maPosition, noLocation, { timeout: 10000, enableHighAccuracy: true });
    }

    function maPosition(position) {
        document.getElementById("latitude").value = position.coords.latitude;
        document.getElementById("longitude").value = position.coords.longitude;
        getCity(position.coords.longitude, position.coords.latitude, 'geoCity')
    }
    function noLocation() {
        console.log("Could not find location");
    }
})




$('#validate-llm').on('click', () => {

    let longi = $('.eqLogicAttr[data-l1key=configuration][data-l2key=longitude]').value()
    let lati = $('.eqLogicAttr[data-l1key=configuration][data-l2key=latitude]').value()
    getCity(longi, lati, 'longLatMode')
});


var getCoordinates = (cityName, cityCode) => {
    $.ajax({
        type: "POST",
        url: "plugins/airquality/core/ajax/airquality.ajax.php",
        data: {
            action: "getCoordinates",
            cityName: cityName,
            cityCode: cityCode
        },
        dataType: 'json',
        beforeSend: () => {
        },
        error: (request, status, error) => {
            handleAjaxError(request, status, error);
        },
        success: (data) => {
            if (data.state != 'ok') {
                console.log('Erreur AJAX : ' + data.result);
            } else {
                console.log("Ajax succes : Longitude et Latitude = " + data.result)
                let html = '<div class="form-group searchMode city_mode"><label class="col-sm-3 control-label">{{Longitude}}</label><div class="col-sm-4">'
                html += '<input type="number" value="' + data.result[0] + '" disabled="disabled" id="city-longitude" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="city_longitude" />'
                html += '</div>'
                if (data.result[1] != 0) {
                    html += '<i class="fas fa-check"></i>'
                } else {
                    html += '<i class="fas fa-times"></i>'
                }
                html += '</div><div class="form-group searchMode city_mode">	<label class="col-sm-3 control-label">{{Latitude}}</label><div class="col-sm-4">'
                html += '<input type="number" value="' + data.result[1] + '" id="city-latitude" disabled="disabled" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="city_latitude" />'
                html += '</div>'
                if (data.result[0] != 0) {
                    html += '<i class="fas fa-check"></i>'
                } else {
                    html += '<i class="fas fa-times"></i>'
                }
                html += '</div>'
                setTimeout(() => {
                    $('#geoloc-city-mode').hide().html(html).fadeIn('slow')
                }, 200);
            }
        }
    });
}


$('#validate-city').on('click', () => {

    let cityName = $('.eqLogicAttr[data-l1key=configuration][data-l2key=city]').value()
    let cityCode = $('.eqLogicAttr[data-l1key=configuration][data-l2key=country_code]').value()
    if (cityCode.length >= 2 && cityName.length >= 2) {
        getCoordinates(cityName, cityCode)
    }
});


$('.eqLogicAttr[data-l1key=configuration][data-l2key=searchMode]').on('change', function () {
    $('.searchMode').hide();
    $('.searchMode.' + $(this).value()).show();
});

$('.eqLogicAttr[data-l1key=configuration][data-l2key=elements]').on('change', function () {
  
    $('.elements').hide();
    $('.elements.' + $(this).value()).show();
});

/*
 * Permet la réorganisation des commandes dans l'équipement
 */
$("#table_cmd").sortable({
    axis: "y",
    cursor: "move",
    items: ".cmd",
    placeholder: "ui-state-highlight",
    tolerance: "intersect",
    forcePlaceholderSize: true
});

/*
 * Fonction permettant l'affichage des commandes dans l'équipement
 */
function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {
            configuration: {}
        };
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }
    //   console.log(_cmd);
    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '" data-cmd_logical_id="' + init(_cmd.logicalId) + '">';
    //  ID Commande
    tr += '<td style="min-width:50px;width:70px;">';
    tr += '<span class="cmdAttr" data-l1key="id"></span>';
    tr += '</td>';

    // Nom
    tr += '<td style="min-width:300px;width:350px;">';
    tr += '<div class="row">';
    tr += '<div class="col-xs-7">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" placeholder="{{Nom de la commande}}">';
    tr += '<select class="cmdAttr form-control input-sm" data-l1key="value" style="display : none;margin-top : 5px;" title="{{Commande information liée}}">';
    tr += '<option value="">{{Aucune}}</option>';
    tr += '</select>';
    tr += '</div>';
    // Icone
    tr += '<div class="col-xs-5">';
    tr += '<a class="cmdAction btn btn-default btn-sm" data-l1key="chooseIcon"><i class="fas fa-flag"></i> {{Icône}}</a>';
    tr += '<span class="cmdAttr" data-l1key="display" data-l2key="icon" style="margin-left : 10px;"></span>';
    tr += '</div>';
    tr += '</div>';
    tr += '</td>';
    // Type: Info/Action  +  Sous-Type: Binaire/Numerique/Autre 
    tr += '<td>';
    tr += '<span class="type" type="' + init(_cmd.type) + '">' + jeedom.cmd.availableType() + '</span>';
    tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
    tr += '</td>';
    // Afficher  + Historiser 
    tr += '<td style="min-width:120px;width:140px;">';
    tr += '<div><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" checked/>{{Afficher}}</label></div> ';
    if (init(_cmd.subType) == 'numeric') {
        tr += '<div><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline isHistorized cmd_' + init(_cmd.logicalId) + '" data-l1key="isHistorized" checked/>{{Historiser}}</label></div> ';
    }
    tr += '</td>';
    //  MIN  + MAX + UNITE
    tr += '<td style="min-width:180px;">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="unite" placeholder="{{Unité}}" title="{{Unité}}" style="width:30%;display:inline-block;"/>';
    tr += '</td>';
    tr += '<td>';
    if (is_numeric(_cmd.id)) {
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fas fa-cogs"></i></a> ';
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fas fa-rss"></i>{{Tester}}</a>';
    }
    tr += '<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';
    tr += '</tr>';


    $('#table_cmd tbody').append(tr);

    var tr = $('#table_cmd tbody tr').last();
    jeedom.eqLogic.builSelectCmd({
        id: $('.eqLogicAttr[data-l1key=id]').value(),
        filter: {
            type: 'info'
        },
        error: function (error) {
            $('#div_alert').showAlert({
                message: error.message,
                level: 'danger'
            });
        },
        success: function (result) {
            tr.find('.cmdAttr[data-l1key=value]').append(result);
            tr.setValues(_cmd, '.cmdAttr');
            jeedom.cmd.changeType(tr, init(_cmd.subType));
        }
    });
}




