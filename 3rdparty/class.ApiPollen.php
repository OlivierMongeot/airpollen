<?php

class ApiPollen
{

    /**
     * Ambee Api Key
     */
    private $ambeeApiKey;

    /**
     *  OpenWeather Api Key
     */
    private $apiKey;


    public function __construct()
    {
        $this->ambeeApiKey = trim(config::byKey('apikeyAmbee', 'airpollen'));
        $this->apiKey = trim(config::byKey('apikey', 'airpollen'));
    }

    /**
     * Methode générique d'appel API avec curl 
     * @param string $url  The url for connect the API
     * @param string $apiKey  The apikey
     * @param string $apiName  The API Name : 'Openwheather' or 'Ambee'
     * @return array The response with maybe errors and responsecodeHttp 
     */
    private function curlApi(string $url, string $key, string $apiName = 'openwheather')
    {
        $curl = curl_init();
        if ($apiName == 'openwheather') {
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => ["Accept: application/json", "x-api-key:" . $key]
            ]);
        } else {
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => ["Content-type: application/json", "x-api-key:" . $key]
            ]);
        }

        $response = curl_exec($curl);
        $curlInfo = curl_getinfo($curl);
        $http_response_code = $curlInfo['http_code'];

        if ($http_response_code !== 200) {

            log::add('airpollen', 'debug', 'Info Curl httpResponse != 200 : ' . json_encode($curlInfo) . ' - Url : ' . json_encode($url));
        }
        $error = curl_error($curl);
        if ($error != '') {
            log::add('airpollen', 'debug', 'Problem with API : ' . json_encode($error));
        }
        curl_close($curl);
        return [$response, $error, $http_response_code];
    }



    /**
     * Retourne Longitude et latitude avec la ville et le code pays
     **/
    public function callApiGeoLoc($city, $country_code, $state_code = null)
    {
        $url = "http://api.openweathermap.org/geo/1.0/direct?q=" . $city . "," . $country_code . "," . $state_code . "&limit=1";
        $response = $this->curlApi($url, $this->apiKey, 'openwheather');
        $coordinates = json_decode($response[0]);
        if ($response[1]) {
            return (__('Impossible de récupérer les coordonnées de cette ville', __FILE__));
        }
        if (!isset($coordinates[0]->name)) {
            return [0, 0];
        } else {
            if (isset($coordinates[0]->lat) && isset($coordinates[0]->lon)) {
                return  [$coordinates[0]->lon, $coordinates[0]->lat];
            } else {
                return [0, 0];
            }
        }
    }


    /**
     * Recupère nom de la ville avec la latitude et longitude 
     * */
    public function callApiReverseGeoLoc($longitude, $latitude)
    {
        if ($longitude != '' && $latitude != '') {
            $url = "http://api.openweathermap.org/geo/1.0/reverse?lat=" . $latitude . "&lon=" . $longitude;
            $response = $this->curlApi($url, $this->apiKey, 'openwheather');

            if (empty(json_decode($response[0]))) {
                return __("Pas de lieu trouvé par l'API Reverse Geoloc avec ces coordonnées", __FILE__);
            } else {

                $data = json_decode($response[0]);
                log::add('airpollen', 'debug', 'Ville récupéré par l\'API reverse geoloc: ' . $data[0]->name);
                return  $data[0]->name;
            }
        } else {
            return (__('Les coordonnées sont vides', __FILE__));
        }
    }



    /**
     * Retourne Forecast parsé min/max/jour Pollen 
     */
    public function getForecastPollen($longitude = null, $latitude = null)
    {
        
        $pollens = [
            "Poaceae", "Alder", "Birch", "Cypress", "Elm", "Hazel", "Oak", "Pine", "Plane", "Poplar",
            "Chenopod", "Mugwort", "Nettle", "Ragweed", "Others", "Grass", "Tree", "Weed"
        ];
        log::add('airpollen', 'debug', 'getForecastPollen Methode Start');
        $dataList = $this->callApiForecastPollen($longitude, $latitude);

        if (isset($dataList) && $dataList != []) {
            foreach ($pollens as $pollen) {
                $newTabDay = $this->parseDataPollen($dataList, $pollen);
                $minMaxTab[$pollen] = $this->pushMinMaxByDay($newTabDay, $pollen);
            }
            return $minMaxTab;
        } else {
            return [];
        }
    }





    /**
     * Appel Pollen latest GetAmbee
     */
    public function getAmbee($longitude, $latitude)
    {
        $longitude = (float)trim(round($longitude, 3));
        $latitude =  (float)trim(round($latitude, 3));
        log::add('airpollen', 'debug', 'Call Pollen laltest For longitude: ' . $longitude . ' / latitude: ' . $latitude);
        $url = "https://api.ambeedata.com/latest/pollen/by-lat-lng?lat=" . $latitude . "&lng=" . $longitude;
        log::add('airpollen', 'debug', 'URL Pollen Latest : ' . $url);
        $response = $this->curlApi($url, $this->ambeeApiKey, 'ambee');

        if ($response[2] == '429') {
            message::add('Ambee', __('Quota journalier données pollen dépassé', __FILE__));
            log::add('airpollen', 'debug', 'Quota journalier données pollen dépassé');

        } else  if ($response[2] == '401') {
            throw new Exception('Api Key is unauthorized');

        } else if ($response[2] == '200') {
            $data = json_decode($response[0]);
            if (property_exists($data, 'data')) {
                log::add('airpollen', 'debug', 'Pollen latest for Longitude: ' . $longitude . ' & Latitude: ' . $latitude);
                log::add('airpollen', 'debug', 'Data Ambee latest : ' . json_encode($data));
                return $data;
            }

        } else if ($response[2] == '403') {
            message::add('Ambee', __('Votre clef Ambee n\'a plus de permission', __FILE__));
            log::add('airpollen', 'debug', 'Votre clef Ambee n\'a plus de permission, vous pouvez basculer sur une formule payante');

        } else {
            throw new Exception('No data pollen server response - Http code : ' . $response[2]);
        }

        // Test
        // $response = file_get_contents(dirname(__DIR__) . '/docs/dataModel/pollenLatest.json', 1);
        // return json_decode($response);

    }



    /**
     * Appel Forecast Pollen Getambee
     */
    public function callApiForecastPollen($longitude, $latitude)
    {

        $longitude = (float)trim(round($longitude, 4));
        $latitude =  (float)trim(round($latitude, 4));
        log::add('airpollen', 'debug', 'Call API Forecast Pollen for Longitude: ' . $longitude . ' & Latitude: ' . $latitude);
        $url = "https://api.ambeedata.com/forecast/pollen/by-lat-lng?lat=" . $latitude . "&lng=" . $longitude;
        log::add('airpollen', 'debug', 'URL Forecast Pollen  : ' . $url);
        $response = $this->curlApi($url, $this->ambeeApiKey, 'ambee');

        if ($response[2] == '429') {
            message::add('Ambee', __('Quota journalier données pollen dépassé', __FILE__));
        } else if ($response[2] == '401') {
            message::add('Ambee', __('Clef API fournie non valide', __FILE__));
        } else if ($response[2] == '403') {
            message::add('Ambee', __('Clef API n\'a pas les bonnes permission', __FILE__));
        } else if ($response[2] == '404') {
            message::add('Ambee', __('La source demandé n\'existe pas', __FILE__));
        } else if ($response[2] == '200') {
            $data = json_decode($response[0]);

            if (isset($data->message) && $data->data == []) {
                log::add('airpollen', 'debug', 'Data Pollen Forecast not available !!');
            } else if (property_exists($data, 'data')) {
                log::add('airpollen', 'debug', 'Data Pollen Forecast : ' . json_encode($response));
                return $data->data;
            }
        } else {
            throw new Exception('No data pollen response - Http code : ' . $response[2]);
        }
        // Test
        // $response = file_get_contents(dirname(__DIR__) . '/core/dataModel/pollen2f.json', 1);     
        // return json_decode($response);
    }


    /**
     * Return array with min max by day for an element 
     * This is data preparation for highCharts  
     */
    private function pushMinMaxByDay($newTabDay, $element)
    {
        $newTabDayElement = $newTabDay[$element];
        foreach ($newTabDayElement as $k => $value) {
            $forecast['day'][] = $k;
            $forecast['min'][] = min($value);
            $forecast['max'][] = max($value);
        }
        return $forecast;
    }


    /**
     * Combine les données en tableau avec index nommé par jour + recupération du nom du jour de la semaine avec le timestamp
     */
    private function parseDataPollen($response, $element)
    {
        $beginOfDay = strtotime("today", time());
        $day = 86399; // in seconds
        foreach ($response as $hourCast) {
            if ($hourCast->time >= $beginOfDay && $hourCast->time <= ($beginOfDay + 5 * $day)) {
                $weekday = date('N', ($hourCast->time + 100));
                $nameDay = new DisplayInfoPollen();
                $dayName =  $nameDay->getNameDay($weekday);
                switch ($element) {
                    case "Poaceae":
                        $newTabAqiDay[$element][$dayName][] =  $hourCast->Species->Grass->{"Grass / Poaceae"};
                        break;
                    case "Poplar":
                        $newTabAqiDay[$element][$dayName][] = $hourCast->Species->Tree->{"Poplar / Cottonwood"};
                        break;
                    case "Alder":
                    case "Birch":
                    case "Cypress":
                    case "Elm":
                    case "Hazel":
                    case "Oak":
                    case "Pine":
                    case "Plane":
                        $newTabAqiDay[$element][$dayName][] = $hourCast->Species->Tree->$element;
                        break;
                    case "Chenopod":
                    case "Mugwort":
                    case "Nettle":
                    case "Ragweed":
                        $newTabAqiDay[$element][$dayName][] = $hourCast->Species->Weed->$element;
                        break;
                    case "Others":
                        $newTabAqiDay[$element][$dayName][] = $hourCast->Species->$element;
                        break;
                    case "Grass":
                        $newTabAqiDay[$element][$dayName][] = $hourCast->Count->grass_pollen;
                        break;
                    case "Tree":
                        $newTabAqiDay[$element][$dayName][] = $hourCast->Count->tree_pollen;
                        break;
                    case "Weed":
                        $newTabAqiDay[$element][$dayName][] = $hourCast->Count->weed_pollen;
                        break;
                }
            }
        }
        return $newTabAqiDay;
    }

    
    public function getFakeData($apiName){
        if ($apiName == 'getForecastPollen') {
            return $this->fakeForecastPollen();
        } else {
            return json_decode($this->fakeDataPollen());
        }
    }

    
    private function fakeDataPollen()
    {
        $fakeData = [];
        $alder = rand(0, 5);
        $birch = rand(0, 20);
        $cypress = rand(0, 3);
        $elm = rand(0, 3);
        $hazel = rand(0, 3);
        $oak = rand(0, 25);
        $pine = rand(0, 7);
        $plane = rand(0, 5);
        $poplar = rand(0, 10);

        if($birch > 0) {
            $cypress = 0;
        }
        if($elm > 0) {
            $hazel = 0;
        }
        if($oak > 0) {
            $pine = 0;
        }
        $totalTree = $alder + $birch + $cypress + $elm + $hazel + $oak + $pine + $plane + $poplar;
        $others = rand(0, 4);
        $chenopod = rand(0, 50);
        $ragweed = rand(0, 3);
        $mugwort = rand(0, 10);
        if ($ragweed > 0) {
               $mugwort = 0;
        }     
        $nettle = rand(10, 250);
        if ($nettle < 120) {
            $poaceae = rand(130, 180);
        }else{
            $poaceae = rand(0, 90);
        }
        
        $totalWeed = $chenopod + $mugwort + $nettle + $ragweed;

        $infoPollen = new DisplayInfoPollen();
        $grass_risk = $infoPollen->getLevelPollen($poaceae, 'grass_pollen');
        $tree_risk = $infoPollen->getLevelPollen($totalTree, 'tree_pollen');
        $weed_risk = $infoPollen->getLevelPollen($totalWeed, 'weed_pollen');

        $fakeData  = [
            "Count" => [
                "grass_pollen"  => $poaceae,
                "tree_pollen"   => $totalTree,
                "weed_pollen"   => $totalWeed
            ],
            "Risk" => [
                "grass_pollen"  => $grass_risk,
                "tree_pollen"   => $tree_risk,
                "weed_pollen"   => $weed_risk
            ],
            "Species" => [
                "Grass" => ["Grass / Poaceae" => $poaceae],
                "Others" => $others,
                "Tree" => [
                    "Alder" => $alder,
                    "Birch" => $birch,
                    "Cypress" => $cypress,
                    "Elm" => $elm,
                    "Hazel" => $hazel,
                    "Oak" => $oak,
                    "Pine" => $pine,
                    "Plane" => $plane,
                    "Poplar / Cottonwood" => $poplar
                ],
                "Weed" => [
                    "Chenopod" => $chenopod,
                    "Mugwort" => $mugwort,
                    "Nettle" => $nettle,
                    "Ragweed" => $ragweed
                ]
            ],
            "updatedAt" => "2021-08-29T13:00:00.000"
        ];
        return json_encode(["data"=>[$fakeData]]);
    }


    private function fakeForecastPollen(){

        $nameDay = new DisplayInfoPollen();
        $today =  $nameDay->getNameDay( date('N', time()));
        $tomorrow = $nameDay->getNameDay( date('N', time() + 86400));
        $afterTomorrow = $nameDay->getNameDay( date('N', time() + 2 * 86400));
        $fakeData = [
            "Alder" => [
                "day" => [$today, $tomorrow, $afterTomorrow],
                "min"   => [rand(1, 5), rand(3, 5), rand(1, 5)],
                "max"   => [rand(5, 10), rand(5, 10),rand(5, 10)]
            ],
            "Poaceae" => [          
                "min"   => [rand(0, 3),rand(0, 5),rand(0, 5)],
                "max"   => [rand(4, 7),rand(5, 10),rand(5, 10)]
            ],
            "Birch" => [
                "min"   => [rand(0, 5), rand(1, 5), rand(1, 5)],
                "max"   => [rand(5, 10), rand(5, 10), rand(5, 10)]
            ],
            "Cypress" => [
                "min"   => [rand(1, 5), rand(0, 5), rand(0, 5)],
                "max"   => [rand(5, 10), rand(5, 10), rand(5, 10)]
            ],
            "Elm" => [
                "min"   => [rand(1, 5), rand(0, 5), rand(0, 5)],
                "max"   => [rand(5, 10),    rand(5, 10), rand(5, 10)]
            ],
            "Hazel" => [
                "min"   => [rand(2, 5), rand(1, 5), rand(0, 5)],
                "max"   => [ rand(5, 10), rand(5, 15), rand(5, 10)]
            ],
            "Oak" => [
                "min"   => [rand(1, 5), rand(1, 5), rand(1, 5)],
                "max"   => [rand(5, 10), rand(5, 10), rand(5, 10)]
            ],
            "Pine" => [
                "min"   => [ rand(0, 5), rand(0, 5), rand(0, 5)],
                "max"   => [rand(5, 10), rand(5, 10), rand(5, 10)]
            ],
            "Plane" => [
                "min"   => [rand(0, 5), rand(0, 5), rand(0, 5)],
                "max"   => [rand(5, 10), rand(5, 20), rand(5, 10)]
            ],
            "Poplar" => [
                "min"   => [rand(0, 5), rand(0, 5), rand(0, 5)],
                "max"   => [rand(5, 10), rand(5, 10), rand(5, 10)]
            ],
            "Chenopod" => [ 
                "min" => [rand(1, 5), rand(1, 5), rand(3, 5)],
                "max" => [rand(5, 10), rand(5, 10), rand(5, 20)]
            ],
            "Mugwort" => [
                "min" => [rand(1, 5), rand(1, 5), rand(1, 5)],
                "max" => [rand(5, 10), rand(5, 10), rand(5, 10)]
            ],
            "Nettle" => [
                "min" => [rand(3, 10), rand(6, 10), rand(4, 10)],
                "max" => [rand(10, 50), rand(10, 50), rand(10, 50)]
            ],
            "Ragweed" => [
                "min" => [rand(1, 5), rand(1, 5), rand(2, 5)],
                "max" => [rand(5, 10), rand(5, 10), rand(5, 10)]
            ],
            "Others" => [
                "min" => [rand(1, 5), rand(1, 5), rand(3, 5)],
                "max" => [rand(5, 10), rand(5, 10), rand(5, 15)]
            ],
            "Grass" => [
                "min" => [rand(1, 50), rand(1, 50), rand(1, 50)],
                "max" => [rand(50, 100), rand(50, 100), rand(50, 100)]
            ],
            "Tree" => [
                "min" => [rand(1, 50), rand(1, 50), rand(1, 50)],
                "max" => [rand(50, 100), rand(50, 100), rand(50, 100)]
            ],
            "Weed" => [
                "min" => [rand(1, 50), rand(1, 50), rand(1, 50)],
                "max" => [rand(50, 100), rand(50, 100), rand(50, 100)]
            ],
            
        ];
        return $fakeData;
    }
}
