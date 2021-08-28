<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
// Déclaration des variables obligatoires
$plugin = plugin::byId('airpollen');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());

?>
<div id='aqi-alert'></div>
<div class="row row-overflow">
    <!-- Page d'accueil du plugin -->
    <div class="col-xs-12 eqLogicThumbnailDisplay">
        <legend><i class="fas fa-cog"></i> {{Gestion}}</legend>
        <!-- Boutons de gestion du plugin -->
        <div class="eqLogicThumbnailContainer">
            <div class="cursor eqLogicAction logoPrimary" data-action="add">
                <i class="fas fa-plus-circle"></i>
                <br>
                <span>{{Ajouter}}</span>
            </div>
            <div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
                <i class="fas fa-wrench"></i>
                <br>
                <span>{{Configuration}}</span>
            </div>
        </div>
        <legend><i class="fas fa-table"></i> {{Mes Pollens}}</legend>
        <?php
        if (count($eqLogics) == 0) {
            echo '<br/><div class="text-center" style="font-size:1.2em;font-weight:bold;">{{Aucun équipement Template n\'est paramétré, cliquer sur "Ajouter" pour commencer}}</div>';
        } else {
            // Champ de recherche
            echo '<div class="input-group" style="margin:5px;">';
            echo '<input class="form-control roundedLeft" placeholder="{{Rechercher}}" id="in_searchEqlogic"/>';
            echo '<div class="input-group-btn">';
            echo '<a id="bt_resetSearch" class="btn roundedRight" style="width:30px"><i class="fas fa-times"></i></a>';
            echo '</div>';
            echo '</div>';
            // Liste des équipements du plugin
            echo '<div class="eqLogicThumbnailContainer">';
            foreach ($eqLogics as $eqLogic) {
                $opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
                echo '<div class="eqLogicDisplayCard cursor ' . $opacity . '" data-eqLogic_id="' . $eqLogic->getId() . '">';
                echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
                echo '<br>';
                echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
                echo '</div>';
            }
            echo '</div>';
        }
        ?>

    </div> <!-- /.eqLogicThumbnailDisplay -->

    <!-- Page de présentation de l'équipement -->
    <div class="col-xs-12 eqLogic" style="display: none;">
        <!-- barre de gestion de l'équipement -->
        <div class="input-group pull-right" style="display:inline-flex;">
            <span class="input-group-btn">
                <!-- Les balises <a></a> sont volontairement fermées à la ligne suivante pour éviter les espaces entre les boutons. Ne pas modifier -->
                <a class="btn btn-sm btn-default eqLogicAction roundedLeft" data-action="configure">
                    <i class="fas fa-cogs"></i><span class="hidden-xs"> {{Configuration avancée}}</span>

                </a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}
                </a><a class="btn btn-sm btn-danger eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}
                </a>
            </span>
        </div>
        <!-- Onglets -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fas fa-arrow-circle-left"></i></a></li>
            <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i><span class="hidden-xs"> {{Équipement}}</span></a></li>
            <li role="presentation"><a href="#commandtab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-list"></i><span class="hidden-xs"> {{Commandes}}</span></a></li>
            <li role="presentation"><a href="#alerttab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-exclamation-circle"></i><span class="hidden-xs"> {{Alertes}}</span></a></li>
        </ul>
        <div class="tab-content">
            <!-- Onglet de configuration de l'équipement -->
            <div role="tabpanel" class="tab-pane active" id="eqlogictab">

                <form class="form-horizontal">
                    <fieldset>
                        <div class="col-lg-6">
                            <legend><i class="fas fa-wrench"></i> {{Général}}</legend>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Nom de l'équipement}}</label>
                                <div class="col-sm-7">
                                    <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                                    <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement}}" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Objet parent}}</label>
                                <div class="col-sm-7">
                                    <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
                                        <option value="">{{Aucun}}</option>
                                        <?php
                                        $options = '';
                                        foreach ((jeeObject::buildTree(null, false)) as $object) {
                                            $options .= '<option value="' . $object->getId() . '">' . str_repeat('&nbsp;&nbsp;', $object->getConfiguration('parentNumber')) . $object->getName() . '</option>';
                                        }
                                        echo $options;
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Catégorie}}</label>
                                <div class="col-sm-7">
                                    <?php
                                    foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                                        echo '<label class="checkbox-inline">';
                                        echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                                        echo '</label>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Options}}</label>
                                <div class="col-sm-7">
                                    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked />{{Activer}}</label>
                                    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked />{{Visible}}</label>
                                </div>
                            </div>
                            <br>

                            <legend><i class="fas fa-cogs"></i> {{Paramètres}}</legend>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Méthode de localisation}}</label>
                                <div class="col-sm-6">
                                    <select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="searchMode">
                                        <option value="city_mode">{{Par ville}}</option>
                                        <option value="long_lat_mode">{{Par longitude & latitude}}</option>
                                        <option value="dynamic_mode">{{Géolocalisation du navigateur (https requis)}}</option>
                                        <?php
                                        if ((config::byKey('info::latitude') != '') && (config::byKey('info::longitude') != '')) {
                                            echo '<option value="server_mode">{{Géolocalisation du serveur Jeedom}}</option>';
                                        } else {
                                            echo '<option disabled="disabled" value="server_mode">{{Localisation Jeedom non renseigné dans la configuration générale}}</option>';
                                        }
                                        ?>
                                        <option value="follow_me">{{Mobile Follow Me (https requis)}}</option>
                                    </select>
                                </div>
                            </div>
                            <!-- city_mode Par Ville  -->
                            <div class="form-group searchMode city_mode">
                                <label class="col-sm-3 control-label">{{Ville}}</label>
                                <div class="col-sm-6">
                                    <input type="string" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="city" required />
                                </div>

                            </div>
                            <div class="form-group searchMode city_mode">
                                <label class="col-sm-3 control-label">{{Code pays}}
                                </label>
                                <div class="col-sm-6">
                                    <input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="country_code" placeholder="ex: FR (iso 3166-1) " required />
                                </div>
                            </div>

                            <div class="form-group searchMode city_mode">
                                <label class="col-sm-3 control-label"></label>
                                <div class="col-sm-6">
                                    <a id="validate-city" class="btn btn-sm btn-success"><i class="fas fa-check-circle"></i> {{Vérifier}}</a>
                                </div>
                            </div>

                            <div id="geoloc-city-mode">
                                <!-- Js -->
                            </div>

                            <!-- longitude latitude mode long_lat_mode-->
                            <div class="form-group searchMode long_lat_mode">
                                <label class="col-sm-3 control-label">{{Longitude}}</label>
                                <div class="col-sm-6">
                                    <input type="number" id="longitude-llm" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="longitude" />
                                </div>
                            </div>
                            <div class="form-group searchMode long_lat_mode">
                                <label class="col-sm-3 control-label">{{Latitude}}</label>
                                <div class="col-sm-6">
                                    <input type="number" id="latitude-llm" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="latitude" />
                                </div>
                            </div>

                            <div class="form-group searchMode long_lat_mode">
                                <label class="col-sm-3 control-label"></label>
                                <div class="col-sm-6">
                                    <a id="validate-llm" class="btn btn-sm btn-success"><i class="fas fa-check-circle"></i> {{Vérifier les coordonées}}</a>
                                </div>
                            </div>

                            <div style="display:none" class="form-group searchMode long_lat_mode">
                                <label class="col-sm-3 control-label">{{Lieu correspondant}}</label>
                                <div class="col-sm-6">
                                    <input id="geo-loc-llm" type="text" disabled="disabled" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="city-llm" />
                                </div>
                            </div>
                            <!-- dynamic_mode Geoloc du navigateur -->
                            <div class="form-group searchMode dynamic_mode">
                                <label class="col-sm-3 control-label">{{Longitude }}</label>
                                <div class="col-sm-6">
                                    <input id="longitude" type="number" disabled="disabled" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="geoLongitude" />
                                </div>
                            </div>
                            <div class="form-group searchMode dynamic_mode">
                                <label class="col-sm-3 control-label">{{Latitude}}</label>
                                <div class="col-sm-6">
                                    <input id="latitude" type="number" disabled="disabled" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="geoLatitude" />
                                </div>
                            </div>
                            <div class="form-group searchMode dynamic_mode">
                                <label class="col-sm-3 control-label">{{Ville}}</label>
                                <div class="col-sm-6">
                                    <input id="geoCity" type="text" disabled="disabled" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="geoCity" />
                                </div>
                            </div>
                            <div class="form-group searchMode dynamic_mode">
                                <label class="col-sm-3 control-label"></label>
                                <div class="col-sm-6">
                                    <a id="validate_dyn_mode" class="btn btn-sm btn-success"><i class="fas fa-map-marked-alt"></i> {{Géolocaliser}}</a>
                                </div>
                            </div>

                            <br />
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Animation du carrousel}}</label>
                                <div class="col-sm-6">
                                    <select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="animation_aqi">
                                        <option value="disable_anim">{{Désactiver}}</option>
                                        <option value="slow_anim">{{Activer}}</option>
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Mini, Maxi, Moyenne, Tendance, Statistiques}}</label>
                                <div class="col-sm-6">
                                    <select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="data_history">
                                        <option value="actived">{{Activer}}</option>
                                        <option value="disable">{{Désactiver}}</option>
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Affichage des prévivsions}}
                                    <sup><i class="fa fa-question-circle tooltips" title="{{ Consomme 48 appels d'API par jours }}"></i></sup></label>
                               
                                <div class="col-sm-6">
                                    <select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="data_forecast">
                                        <option value="actived">{{Activer}}</option>
                                        <option value="disable">{{Désactiver}}</option>
                                    </select>
                                </div>
                            </div>
                            <br>
                            <br/>
                            <div class="form-group elements pollen">
                                <label style="margin-top:7px;" class="col-sm-3 control-label">{{Afficher à partir de}}</label>
                                <div class="col-sm-6">
                                    <div class="range-slider aqi">
                                        <style>
                                            .output-pollen::after {
                                                content: " part/m³";
                                            }
                                        </style><output class='output-pollen' style="color: var(--linkHover-color) !important;margin-bottom:10px;"></output>
                                        <input type="range" min="0" max="40" oninput="this.previousElementSibling.value = this.value" onchange="this.previousElementSibling.value = this.value" class="input-range aqi eqLogicAttr" orient="horizontal" name="pollen" id="pollen" data-l1key="configuration" data-l2key="pollen_alert_level"></input>
                                    </div>
                                </div>
                            </div>





                        </div>
                        <!-- Affiche l'icône du plugin par défaut mais vous pouvez y afficher les informations de votre choix -->
                        <div class="col-lg-6">
                            <br>


                            <legend><i class="fas fa-info"></i> {{Informations}}</legend>

                            <!--  -->
                            <div class="form-group">


                                <div class="container">
                                    <h6>{{Code couleur pollen utilisé dans le plugin}}</h6>
                                </div>
                                <br>
                                <div class="container">
                                    <div>
                                        <input type="color" name="body" disabled value="#B00000">
                                        <label for="body">{{Risque très haut}}</label>
                                        <br><br>
                                        <input type="color" name="body" disabled value="#E79C00">
                                        <label for="body">{{Risque haut}}</label>
                                        <br><br>
                                        <input type="color" name="body" disabled value="#EFE800">
                                        <label for="body">{{Risque modéré}}</label>
                                        <br><br>
                                        <input type="color" name="head" disabled value="#00BD01">
                                        <label for="head">{{Risque bas}}</label>
                                    </div>
                                    <br>
                                    <br>
                                </div>
                            </div>
                         

                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{Rafraichissement des données}} <sup><i class="fa fa-question-circle tooltips" title="{{ Toutes les possibilitées sont gratuites pendant 15 jours dans la limite de 100 appels/jours}}"></i></sup></label>
                                    <div class="col-sm-6">
                                        <select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="data_refresh">
                                            <option value="fake_data">{{Données aléatoires pour démo }}</option>
                                            <option value="disable">{{Manuel : 0.017€/appel}}</option>
                                            <option value="oneByHour">{{Auto : 12 X par jour (de 8h à 19h) environ 6.20€/mois}}</option>
                                            <option value="oneByTwoHour">{{Auto : 6 X par jour (de 8h à 18h) environ 3.10€/mois}}</option>
                                            <option value="ThreeByDay">{{Auto : 3 X par jour (8h, 12h et 16h) environ 1.55€/mois}}</option>
                                            <option value="twoByDay">{{Auto : 2 X par jour  (8h et 15h) environ 1€/mois}}</option>
                                            <option value="full">{{Auto : 1 X par heure + forecast 1X par jour, environs 1.20€/jour}}</option>
                                        </select>
                                    </div>
                                </div>
                                <br>
                                <br><br>
                            

                            <!--  -->
                            <div class="form-group">
                                <div class="text-center">
                                    <img name="icon_visu" src="<?= $plugin->getPathImgIcon(); ?>" style="max-width:160px;" />
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </form>
                <hr>
            </div><!-- /.tabpanel #eqlogictab-->

            <!-- Onglet des commandes de l'équipement -->
            <div role="tabpanel" class="tab-pane" id="commandtab">
                <a class="btn btn-default btn-sm pull-left cmdAction" data-action="add" style="margin-top:5px;"><i class="fas fa-plus-circle"></i> {{Ajouter une commande}}</a>
                <br /><br />
                <div class="table-responsive">
                    <table id="table_cmd" class="table table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th>{{Id}}</th>
                                <th>{{Nom}}</th>
                                <th>{{Type}}</th>
                                <th>{{Options}}</th>
                                <th>{{Paramètres}}</th>
                                <th>{{Action}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div><!-- /.tabpanel #commandtab-->

            <!-- Onglet Niveaux des Alertes -->
            <div role="tabpanel" class="tab-pane" id="alerttab">

                <div class="col-lg-7" style="padding:10px 35px">
                    <legend>
                        <span> Plancher de déclenchement des alertes</span>
                    </legend>
                    <br />
                    <div class="container range-container aqi" style="width: 90%;">

                        <style>
                            .range-slider.aqi .input-range.aqi {
                                -webkit-appearance: none;
                                height: 6px;
                                border-radius: 5px;
                                background: #ccc;
                                outline: none;
                                writing-mode: bt-lr;
                            }
                        </style>

                        <div class="container range-container pollen" style=" width: 90%;">

                            <div class="form-group">
                                <div class="range-slider aqi">
                                    <label for="poaceae_alert_level">{{Graminées / Poacées}}</label>
                                    <style>
                                        .output-poaceae::after {
                                            content: " part/m³";
                                        }
                                    </style>
                                    <output class='output-poaceae pull-right' style="color: var(--linkHover-color) !important;margin-bottom:10px;"></output>
                                    <input type="range" min="1" max="250" oninput="this.previousElementSibling.value = this.value" onchange="this.previousElementSibling.value = this.value" id="poaceae" class="input-range aqi eqLogicAttr" orient="horizontal" name="poaceae" id="poaceae_alert_level" data-l1key="configuration" data-l2key="poaceae_alert_level"></input>
                                    <br>
                                    <label for="elm_alert_level">{{Orme}}</label>
                                    <style>
                                        .output-elm::after {
                                            content: " part/m³";
                                        }
                                    </style>
                                    <output class='output-elm pull-right' style="color: var(--linkHover-color) !important;margin-bottom:10px;"></output>
                                    <input type="range" min="1" max="250" oninput="this.previousElementSibling.value = this.value" onchange="this.previousElementSibling.value = this.value" id="elm" class="input-range aqi eqLogicAttr" orient="horizontal" name="elm" id="elm_alert_level" data-l1key="configuration" data-l2key="elm_alert_level"></input>
                                    <br>
                                    <label for="alder_alert_level">{{Auln}}</label>
                                    <style>
                                        .output-alder::after {
                                            content: " part/m³";
                                        }
                                    </style>
                                    <output class='output-alder pull-right' style="color: var(--linkHover-color) !important;margin-bottom:10px;"></output>
                                    <input type="range" min="1" max="250" oninput="this.previousElementSibling.value = this.value" onchange="this.previousElementSibling.value = this.value" id="alder" class="input-range aqi eqLogicAttr" orient="horizontal" name="alder" id="alder_alert_level" data-l1key="configuration" data-l2key="alder_alert_level"></input>
                                    <br>
                                    <label for="birch_alert_level">{{Bouleau}}</label>
                                    <style>
                                        .output-birch::after {
                                            content: " part/m³";
                                        }
                                    </style>
                                    <output class='output-birch pull-right' style="color: var(--linkHover-color) !important;margin-bottom:10px;"></output>
                                    <input type="range" min="1" max="250" oninput="this.previousElementSibling.value = this.value" onchange="this.previousElementSibling.value = this.value" id="birch" class="input-range aqi eqLogicAttr" orient="horizontal" name="birch" id="birch_alert_level" data-l1key="configuration" data-l2key="birch_alert_level"></input>
                                    <br>
                                    <label for="cypress_alert_level">{{Cyprès}}</label>
                                    <style>
                                        .output-cypress::after {
                                            content: " part/m³";
                                        }
                                    </style>
                                    <output class='output-cypress pull-right' style="color: var(--linkHover-color) !important;margin-bottom:10px;"></output>
                                    <input type="range" min="1" max="250" oninput="this.previousElementSibling.value = this.value" onchange="this.previousElementSibling.value = this.value" id="cypress" class="input-range aqi eqLogicAttr" orient="horizontal" name="cypress" id="cypress_alert_level" data-l1key="configuration" data-l2key="cypress_alert_level"></input>
                                    <br>
                                    <label for="oak_alert_level">{{Chêne}}</label>
                                    <style>
                                        .output-oak::after {
                                            content: " part/m³";
                                        }
                                    </style>
                                    <output class='output-oak pull-right' style="color: var(--linkHover-color) !important;margin-bottom:10px;"></output>
                                    <input type="range" min="1" max="250" oninput="this.previousElementSibling.value = this.value" onchange="this.previousElementSibling.value = this.value" id="oak" class="input-range aqi eqLogicAttr" orient="horizontal" name="oak" id="oak_alert_level" data-l1key="configuration" data-l2key="oak_alert_level"></input>
                                    <br>
                                    <label for="hazel_alert_level">{{Noisetier}}</label>
                                    <style>
                                        .output-hazel::after {
                                            content: " part/m³";
                                        }
                                    </style>
                                    <output class='output-hazel pull-right' style="color: var(--linkHover-color) !important;margin-bottom:10px;"></output>
                                    <input type="range" min="1" max="250" oninput="this.previousElementSibling.value = this.value" onchange="this.previousElementSibling.value = this.value" id="hazel" class="input-range aqi eqLogicAttr" orient="horizontal" name="hazel" id="hazel_alert_level" data-l1key="configuration" data-l2key="hazel_alert_level"></input>
                                    <br>
                                    <label for="pine_alert_level">{{Pin}}</label>
                                    <style>
                                        .output-pine::after {
                                            content: " part/m³";
                                        }
                                    </style>
                                    <output class='output-pine pull-right' style="color: var(--linkHover-color) !important;margin-bottom:10px;"></output>
                                    <input type="range" min="1" max="250" oninput="this.previousElementSibling.value = this.value" onchange="this.previousElementSibling.value = this.value" id="pine" class="input-range aqi eqLogicAttr" orient="horizontal" name="pollen" id="pine_alert_level" data-l1key="configuration" data-l2key="pine_alert_level"></input>
                                    <br>
                                    <label for="plane_alert_level">{{Platane}}</label>
                                    <style>
                                        .output-plane::after {
                                            content: " part/m³";
                                        }
                                    </style>
                                    <output class='output-plane pull-right' style="color: var(--linkHover-color) !important;margin-bottom:10px;"></output>
                                    <input type="range" min="1" max="250" oninput="this.previousElementSibling.value = this.value" onchange="this.previousElementSibling.value = this.value" id="plane" class="input-range aqi eqLogicAttr" orient="horizontal" name="plane" id="plane_alert_level" data-l1key="configuration" data-l2key="plane_alert_level"></input>
                                    <br>
                                    <label for="poplar_alert_level">{{Peuplier}}</label>
                                    <style>
                                        .output-poplar::after {
                                            content: " part/m³";
                                        }
                                    </style>
                                    <output class='output-poplar pull-right' style="color: var(--linkHover-color) !important;margin-bottom:10px;"></output>
                                    <input type="range" min="1" max="250" oninput="this.previousElementSibling.value = this.value" onchange="this.previousElementSibling.value = this.value" id="poplar" class="input-range aqi eqLogicAttr" orient="horizontal" name="poplar" id="poplar_alert_level" data-l1key="configuration" data-l2key="poplar_alert_level"></input>
                                    <br>
                                    <label for="chenopod_alert_level">{{Chénopodes}}</label>
                                    <style>
                                        .output-chenopod::after {
                                            content: " part/m³";
                                        }
                                    </style>
                                    <output class='output-chenopod pull-right' style="color: var(--linkHover-color) !important;margin-bottom:10px;"></output>
                                    <input type="range" min="1" max="250" oninput="this.previousElementSibling.value = this.value" onchange="this.previousElementSibling.value = this.value" id="chenopod" class="input-range aqi eqLogicAttr" orient="horizontal" name="chenopod" id="chenopod_alert_level" data-l1key="configuration" data-l2key="chenopod_alert_level"></input>
                                    <br>
                                    <label for="mugwort_alert_level">{{Armoise}}</label>
                                    <style>
                                        .output-mugwort::after {
                                            content: " part/m³";
                                        }
                                    </style>
                                    <output class='output-mugwort pull-right' style="color: var(--linkHover-color) !important;margin-bottom:10px;"></output>
                                    <input type="range" min="1" max="250" oninput="this.previousElementSibling.value = this.value" onchange="this.previousElementSibling.value = this.value" id="mugwort" class="input-range aqi eqLogicAttr" orient="horizontal" name="mugwort" id="mugwort_alert_level" data-l1key="configuration" data-l2key="mugwort_alert_level"></input>
                                    <br>
                                    <label for="nettle_alert_level">{{Ortie}}</label>
                                    <style>
                                        .output-nettle::after {
                                            content: " part/m³";
                                        }
                                    </style>
                                    <output class='output-nettle pull-right' style="color: var(--linkHover-color) !important;margin-bottom:10px;"></output>
                                    <input type="range" min="1" max="250" oninput="this.previousElementSibling.value = this.value" onchange="this.previousElementSibling.value = this.value" id="nettle" class="input-range aqi eqLogicAttr" orient="horizontal" name="nettle" id="nettle_alert_level" data-l1key="configuration" data-l2key="nettle_alert_level"></input>
                                    <br>
                                    <label for="ragweed_alert_level">{{Ambroisie}}</label>
                                    <style>
                                        .output-ragweed::after {
                                            content: " part/m³";
                                        }
                                    </style>
                                    <output class='output-ragweed pull-right' style="color: var(--linkHover-color) !important;margin-bottom:10px;"></output>
                                    <input type="range" min="1" max="250" oninput="this.previousElementSibling.value = this.value" onchange="this.previousElementSibling.value = this.value" id="ragweed" class="input-range aqi eqLogicAttr" orient="horizontal" name="ragweed" id="ragweed_alert_level" data-l1key="configuration" data-l2key="ragweed_alert_level"></input>
                                    <br>
                                    <label for="others_alert_level">{{Autres pollens d'origine inconnue}}</label>
                                    <style>
                                        .output-others::after {
                                            content: " part/m³";
                                        }
                                    </style>
                                    <output class='output-others pull-right' style="color: var(--linkHover-color) !important;margin-bottom:10px;"></output>
                                    <input type="range" min="1" max="250" oninput="this.previousElementSibling.value = this.value" onchange="this.previousElementSibling.value = this.value" id="others" class="input-range aqi eqLogicAttr" orient="horizontal" name="others" id="others_alert_level" data-l1key="configuration" data-l2key="others_alert_level"></input>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-lg-5" style="padding:15px 35px">
                    <legend>
                        <span style="text-align:left">{{Options des alertes}}</span>
                    </legend>
                    <br /><br />
                    <div class="container">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{Alertes dans le centre des messages}}</label>
                            <div class="col-sm-1">
                                <label class="checkbox-inline">
                                    <input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="alert_notification" />{{activer}}
                                </label>
                            </div>
                        </div>
                        <br /><br />
                        <br />
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{Alertes détaillées}}</label>
                            <div class="col-sm-1">
                                <label class="checkbox-inline">
                                    <input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="alert_details" />{{activer}}
                                </label>
                            </div>
                        </div>
                        <br>
                    </div>
                </div>

                <!-- </div> -->
            </div>
            <!--/.tabpanel-->



        </div><!-- /.tab-content -->
    </div><!-- /.eqLogic -->
</div><!-- /.row row-overflow -->

<?php include_file('desktop', 'airpollen', 'js', 'airpollen'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>