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
        $this->ambeeApiKey = trim(config::byKey('apikeyAmbee', 'pollen'));
        $this->apiKey = trim(config::byKey('apikey', 'pollen'));

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

            log::add('pollen', 'debug', 'Info Curl httpResponse != 200 : ' . json_encode($curlInfo) . ' - Url : ' . json_encode($url));
        }
        $error = curl_error($curl);
        if ($error != '') {
            log::add('pollen', 'debug', 'Problem with API : ' . json_encode($error));
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
                log::add('pollen', 'debug', 'Ville récupéré par l\'API reverse geoloc: ' . $data[0]->name);
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
            "Chenopod", "Mugwort", "Nettle", "Ragweed", "Others"
        ];
        log::add('pollen', 'debug', 'getForecastPollen Methode Start');
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
        log::add('pollen', 'debug', 'Call Pollen laltest For longitude: ' . $longitude . ' / latitude: ' . $latitude);
        $url = "https://api.ambeedata.com/latest/pollen/by-lat-lng?lat=" . $latitude . "&lng=" . $longitude;
        log::add('pollen', 'debug', 'URL Pollen Latest : ' . $url);
        $response = $this->curlApi($url, $this->ambeeApiKey, 'ambee');

        if ($response[2] == '429') {
            message::add('Ambee', __('Quota journalier données pollen dépassé', __FILE__));
            log::add('pollen', 'debug', 'Quota journalier données pollen dépassé');

        } else  if ($response[2] == '401') {
            throw new Exception('Api Key is unauthorized');

        } else if ($response[2] == '200') {
            $data = json_decode($response[0]);
            if (property_exists($data, 'data')) {
                log::add('pollen', 'debug', 'Pollen latest for Longitude: ' . $longitude . ' & Latitude: ' . $latitude);
                log::add('pollen', 'debug', 'Data Ambee latest : ' . json_encode($data));
                return $data;
            }

        } else if ($response[2] == '403') {
            message::add('Ambee', __('Votre clef Ambee n\'a plus de permission', __FILE__));
            log::add('pollen', 'debug', 'Votre clef Ambee n\'a plus de permission, vous pouvez basculer sur une formule payante');

        } else {
            throw new Exception('No data pollen server response - Http code : ' . $response[2]);
        }
    }



    /**
     * Appel Forecast Pollen Getambee
     */
    public function callApiForecastPollen($longitude, $latitude)
    {

        $longitude = (float)trim(round($longitude, 4));
        $latitude =  (float)trim(round($latitude, 4));
        log::add('pollen', 'debug', 'Call API Forecast Pollen for Longitude: ' . $longitude . ' & Latitude: ' . $latitude);
        $url = "https://api.ambeedata.com/forecast/pollen/by-lat-lng?lat=" . $latitude . "&lng=" . $longitude;
        log::add('pollen', 'debug', 'URL Forecast Pollen  : ' . $url);
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
                log::add('pollen', 'debug', 'Data Pollen Forecast not available !!');
            } else if (property_exists($data, 'data')) {
                log::add('pollen', 'debug', 'Data Pollen Forecast : ' . json_encode($response));
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
                $nameDay = new DisplayInfo();
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
                }
            }
        }
        return $newTabAqiDay;
    }


}
