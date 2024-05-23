<?php

ini_set('error_reporting', E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

ini_set('memory_limit', '2048M');
set_time_limit(0);

# признак текущего скрапера...    для логера и таскера
define("SCRAPER_NAME", "propertyfinder.ae");

# max execution time... Если больше этого времени - закончим работу.
define("MAX_WORK_TIME", 3600 * 8);

# включить перевод? если нет - то пишет как есть, если да заменяет 2а поля по шаблонам.
#define("ACTIVE_TRANSLATE", true);

# максимум потоков за цикл. Для скана фоток
define("MAX_THREAD", 15);

# Для скана страниц
define("MAX_THREAD_PAGE", 5);


# сколько раз позиция должна не встретится чтобы ее пропустить.
define("MAX_ERROR_SKIPP", 3);

include 'class/Timer2.php';
include 'class/Task_run.php';
include 'class/Loger.php';
include 'class/Curl.php';
include 'class/Proxy.php';
#if (ACTIVE_TRANSLATE) include 'class/Translator.php';
include 'config.php';
include 'class/meekrodb.2.4.class.php';


try {
    DB::$dbName = DB_NAME;
    DB::$user = DB_USER;
    DB::$password = DB_PWD;
    DB::$host = DB_HOST;

    DB::query("SET CHARACTER SET utf8;");
} catch (Exception $e) {
    echo 'Error message ', $e->getMessage(), "\n";
}

#DB::$logfile = 'querylog.txt';

#try {
$run = new Property();
#} catch (Exception $e) {
#    echo 'Error message ',  $e->getMessage(), "\n";
#}


class Property
{

    var $new_task;
    var $key_api;
    var $count_update, $count_add;

    function __construct()
    {

        #
        TIMER::start(0);

        LOGER_::LogStart(SCRAPER_NAME, "combo");

        LOGER_::msg("Connect to DB - ok", "INFO");
        /*
                if (ACTIVE_TRANSLATE) {
                    # загурзка переводов/замены...
                    # update task status..
                    LOGER_::msg("Load translator...", "INFO");
                    $translator = new Translator();
                    $count_load = $translator->TranslatorAdd(dirname(__FILE__) . '/lang/dubizle_com_brand_models.txt');
                    LOGER_::msg("Add strings: [$count_load]", "INFO");
                    #echo " - Ioniq5 = ".$translator->Translate("Ioniq5")."\n";
                }
        */
        # блокируем двойной запуск одного скрапера
        $this->new_task = new Task_run(SCRAPER_NAME, LOGER_::return_log_path());
        if ($this->new_task->task_status == false) {
            throw new ParserException("Double task run... Current stop...", $this->new_task);
        }

        # update task status..
        $this->new_task->Task_update(0, "Load proxy list...");

        $proxy = new Proxy_webshare(PROXY_SOURCE, true);    # источник / true = перемешать

        if (!$proxy->Proxy_next()) {
            throw new ParserException("Need more proxy. Can't pasring more...", $this->new_task);
        }

        LOGER_::msg("Load proxy - [" . count($proxy->all_proxy) . "]", "INFO");

        # is active?
        $this->CheckIsParserActive();

        # получаем key для парсинга...
        $this->key_api = $this->GetKey($proxy);


        #
        # parsing data
        #

        #
        # для всех активных устанавливаем update_flag в 1. Чтобы потом понять которые нужно или удалить или деактивировать.
        # пока стоит деактиваация...
        DB::update('catalog_property', ['update_flag' => 1], "active_flag=1");


        # type = 1 buy, type = 2 rent
        # лимит на парсинг 80К страницу
        # разбиваем парсинг по областям
        # параметр "c"
        $type_deals = [
            "buy" => 1,
            "rent" => 2
        ];


        # параметр "l"
        $location = [
            "Dubai" => "1", #dubai
            "Abu Dhabi" => "6", #Abu Dhabi
            "Sharjah" => "4", #Sharja
            "Ras Al Khaimah" => "3", #Ras AI Khaimah
            "Ajman" => "5", #ajman
            "Fujairah" => "7", #Fujarih
            "Al Ain" => "8", #al ain
        ];

        # параметр "t"
        $property_type = [
            "Apartment" => "1",   #Apartment
            "Villa" => "35", #Vila
            "Townhouse" => "22", #Townhouse
            "Penthaouse" => "20", #Penthaouse
#        "Compound"=>"42", #Compound
#        "Duplex"=>"24", #Duplex
#        "Full floor"=>"18", #Full floor
#        "Half floor"=>"29", #Half floor
#        "Whole Building"=>"10", #Whole Building
#        "Land"=>"5", #Land
#        "Bulk sale unit"=>"30", #Bulk sale unit
#        "Bungalow"=>"31", #Bungalow
#        "Hotel & Hotel appartment"=>"45", #Hotel & Hotel appartment
        ];

        $header = array(
            'Accept: */*',
            'Accept-Encoding: gzip, deflate, br',
            'Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
            'Connection: keep-alive',
            'Content-Type: application/x-www-form-urlencoded',
            'Sec-Ch-Ua: "Not A(Brand";v="99", "Google Chrome";v="121", "Chromium";v="121"',
            'Sec-Ch-Ua-Mobile: ?0',
            'Sec-Ch-Ua-Platform: "Windows"',
            'Sec-Fetch-Dest: empty',
            'Sec-Fetch-Mode: cors',
            'Sec-Fetch-Site: cross-site'
        );

        $curl_opt = [
            CURLOPT_ENCODING => "gzip",
            CURLOPT_HEADER => 0,
#            CURLOPT_FAILONERROR => 0,
            #           CURLOPT_FOLLOWLOCATION => 1,
#            CURLOPT_VERBOSE => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36"
        ];

        $this->count_add = 0;
        $this->count_update = 0;

        #  for ($type=1;$type<=2;$type++){
        foreach ($type_deals as $name_deals => $one_deals) {
            foreach ($location as $name_loc => $one_loc) {
                foreach ($property_type as $name_type => $one_type) {


                    $page = 1;

                    #while ($page==1 || $page<=$page_count){
                    # is active task?
                    $this->CheckIsParserActive();

#                    $url='https://www.propertyfinder.ae/_next/data/'.$this->key_api.'/en/search.json?l='.$one_loc.'&c='.$one_deals.'&t='.$one_type.'&fu=0&v=1&ob=mr&page='.$page;
                    $url = 'https://www.propertyfinder.ae/en/search?l=' . $one_loc . '&c=' . $one_deals . '&t=' . $one_type . '&fu=0&rp=y&ob=mr&page=' . $page;

                    $get_json = $this->GetLoopPage($url, $curl_opt, $header, '', $proxy);

#                    file_put_contents('exam.json', $get_json["response"]);
                    #                   exit;

                    /*if (strlen($get_json["response"])>2 &&
                        strlen($get_json["response"])<=11000){

                        file_put_contents('exam.json', $get_json["response"]);
                        echo "Error...";
                        exit;

                    }*/


                    $get_json = explode('<script id="__NEXT_DATA__" type="application/json">', $get_json["response"]);
                    $get_json = $get_json[1];
                    $get_json = explode('</script>', $get_json);
                    $get_json = $get_json[0];

                    $get_json = json_decode($get_json, true);
                    $get_json = $get_json["props"];

                    if ($page == 1) {
                        $page_count = $get_json["pageProps"]["searchResult"]["meta"]["page_count"];
                    }

                    $this->new_task->Task_update(0, "Scaning [$name_deals][$name_loc][$name_type] count=[" . $page . "/" . $page_count . "]");
                    LOGER_::msg("Scaning [$name_deals][$name_loc][$name_type] count=[" . $page . "/" . $page_count . "]", "INFO");

                    $page++;

                    #  continue;

                    #
                    # Update current pages...
                    #
                    $this->Update_records($get_json, $proxy, $name_loc, $name_deals);

                    #
                    # мульти сканирование
                    #
                    $all_links = [];
                    if ($page_count > 1) {
                        for ($i = 2; $i <= $page_count; $i++) {
                            #$all_links [] = 'https://www.propertyfinder.ae/_next/data/'.$this->key_api.'/en/search.json?l='.$one_loc.'&c='.$one_deals.'&t='.$one_type.'&fu=0&v=1&ob=mr&page='.$i;
                            $all_links [] = 'https://www.propertyfinder.ae/en/search?l=' . $one_loc . '&c=' . $one_deals . '&t=' . $one_type . '&fu=0&rp=y&ob=mr&page=' . $i;
                        }

                        $this->CheckIsParserActive();

                        $cur_links_go = [];
                        $scan_now = [];
                        $proxy_now = [];
                        $bad_link = [];
                        while ($all_links != []) {
                            $cur_link = array_pop($all_links);
                            $scan_now[$cur_link] = $cur_link;
                            $proxy_now[$cur_link] = $proxy->current_proxy;

                            # is active task?
                            $this->CheckIsParserActive();

                            if (count($scan_now) >= MAX_THREAD_PAGE || $all_links == []) {
                                # get data...
                                $res = Curl::Get_data_multi($scan_now, $curl_opt, $proxy_now);

                                $have_error = 0;
                                foreach ($res as $key_id => $one_res) {
                                    # $json = json_decode($one_res, true, 512, JSON_BIGINT_AS_STRING);

#                                    if (((json_decode($one_res, true) != false && strlen($one_res) > 10) ||
#                                                json_decode($one_res, true)===[]) &&
#                                                $one_res !='{"notFound":true}') {

                                    if (strlen($one_res) > 5000 &&
                                        strpos($one_res, '<script id="__NEXT_DATA__" type="application/json">') > 0) {
                                        #
                                        # Update current pages...
                                        #
                                        $get_json = explode('<script id="__NEXT_DATA__" type="application/json">', $one_res);
                                        $get_json = $get_json[1];
                                        $get_json = explode('</script>', $get_json);
                                        $get_json = $get_json[0];

                                        $get_json = json_decode($get_json, true);
                                        $get_json = $get_json["props"];


                                        $this->new_task->Task_update(0, "Scaning [$name_deals][$name_loc][$name_type] [$key_id] count=[" . $page . "/" . $page_count . "]");
                                        LOGER_::msg("Scaning [$name_deals][$name_loc][$name_type] [$key_id] count=[" . $page . "/" . $page_count . "]", "INFO");

                                        $this->Update_records($get_json, $proxy, $name_loc, $name_deals);

                                        $page++;

                                        #break;
                                    } else {
                                        LOGER_::msg("Can't get json answer... [$key_id] Try other proxy.");
                                        $have_error = 1;

                                        if (isset($bad_link[$key_id])) {
                                            LOGER_::msg("!!!!Have a problem pages [$key_id].");
                                        } else {
                                            $all_links[] = $key_id;
                                        }

                                        # метим проблемные страницы... Больше 1но повтора не делаем.
                                        # возможна ситцация что крайняя страница исчезнет
                                        # чтобы не было зацикливания парсера.
                                        $bad_link[$key_id] = 1;
                                    }

                                }
                                /* if ($have_error){
                                     if (!$proxy->Proxy_next()) {
                                         throw new ParserException("Need more proxy. Can't pasring more...", $this->new_task);
                                     }else{
                                         # что-то слетело, возможно изменился ключ...
                                         # получаем заново ключ...
                                         #
                                         $key_api_tmp = $this->GetKey($proxy);


                                         #меняем ключ на новый в текущих путях сканирования...
                                         $all_links_tmp = [];
                                         foreach ($all_links as $keys=>$one_link){
                                             $new_path = str_replace($this->key_api,$key_api_tmp,$one_link);
                                             $all_links_tmp[$new_path]=$new_path;
                                         }

                                         #$url = str_replace($this->key_api,$key_api_tmp,$url);
                                         $all_links = $all_links_tmp;
                                         # меняем тек. ключ на новый
                                         $this->key_api = $key_api_tmp;
                                     }
                                 }*/
                                $scan_now = [];
                                $proxy_now = [];
                                # exit;
                            }
                        }

                    }


                    #  if ($page>$page_count){
                    #      break;
                    #  }

                    #          if ($page>1){
                    #              break;
                    #          }
                    #    }
                }
            }
        }
        #
        # add images
        #

        $itter = 0;

        while ($itter == 0 || $count_bad > 0) {
            $count_bad = $this->AddImages($proxy, $curl_opt);
            $itter++;

            # не больше 5 попыток... Остальное в след. раз
            if ($itter > 5) break;
        }


        #
        # все что не нашли для update + 1 ошибку...
        #
        $counts_deactive = 0;
        $all_bad_position = DB::query("SELECT id,miss_try_count FROM catalog_property WHERE update_flag=1");
        foreach ($all_bad_position as $one) {
            if ($one["miss_try_count"] >= MAX_ERROR_SKIPP) {
                DB::update('catalog_property', [
                    'active_flag' => 0,
                    'update_flag' => 0,
                    'miss_try_count' => 0
                ], "id=%i", $one["id"]);
                $counts_deactive++;
            } else {
                $new_count = $one["miss_try_count"] + 1;
                DB::update('catalog_property', [
                    'update_flag' => 0,
                    'miss_try_count' => $new_count
                ], "id=%i", $one["id"]);
            }
        }


        #  DB::update('catalog_dubizzle', ['active_flag' => 0, 'update_flag' => 0, 'data_last_update' => time()], "update_flag=1");

        #  $stats_remove = DB::affectedRows();

        LOGER_::msg("Deactivate records [$counts_deactive]", "INFO");

        ## или удаление...
        /* $row = DB::query("SELECT * FROM catalog_dubizzle WHERE update_flag=1");

         if ($row){
             foreach ($row as $one){
                 DB::query("DELETE FROM catalog_dubizzle WHERE id=%i", $one["id"]);
                 DB::query("DELETE FROM catalog_dubizzle_img WHERE id=%i", $one["id"]);
                 LOGER_::msg("Delete record [".$one["id"]."]","INFO");
             }
         }
         */


        # убираем блокировку  / finish...

        $this->new_task->Task_end("Add=" . $this->count_add . " Update=" . $this->count_update . " Remove=$counts_deactive");

        LOGER_::msg("Add=" . $this->count_add . " Update=" . $this->count_update . " Remove=$counts_deactive", "INFO");
        LOGER_::msg(" === end scraping ===");

    }

    function AddImages2($proxy, $curl_opt)
    {


    }

    # detect images for current DB
    function AddImages($proxy, $curl_opt)
    {
        #
        # сканируем фото...
        #

        $bad_count = 0;

        $all_images = DB::query("SELECT id FROM catalog_property WHERE active_flag=1 and get_images=1");

        $this->new_task->Task_update(0, "Need scaning photo =[" . count($all_images) . "]");
        LOGER_::msg("Need scaning photo =[" . count($all_images) . "]", "INFO");


        $scan_now = [];
        $proxy_now = [];

        $cur_photo_num = 0;

        foreach ($all_images as $one) {
            $cur_photo_num++;

            #$url='https://www.propertyfinder.ae/api/pwa/property/images/v2?propertyId='.$one["id"].'&imageType=new_small&locale=en';
            $url = 'https://www.propertyfinder.ae/api/pwa/property/images/v2?propertyId=' . $one["id"] . '&imageType=full_screen&locale=en';
            $scan_now[$one["id"]] = $url;
            $proxy_now[$one["id"]] = $proxy->GetRandomProxy();

            # is active task?
            $this->CheckIsParserActive();

            if (count($scan_now) >= MAX_THREAD || $one == end($all_images)) {
                # get data...
                $res = Curl::Get_data_multi($scan_now, $curl_opt, $proxy_now);

                # file_put_contents('img.json', json_encode($res));
                # exit;

                foreach ($res as $key_id => $one_res) {
                    $json = json_decode($one_res, true, 512, JSON_BIGINT_AS_STRING);


                    if (!$json && $one_res != '[]') {
                        LOGER_::msg("Skipp images for [$key_id ] [" . $proxy_now[$key_id]["ip"] . ":" . $proxy_now[$key_id]["port"] . "]  [" . $key_id . "] - can't get json answer...", "ERROR");
                        # LOGER_::msg("Skipp record ".$one_res, "ERROR");
                        # exit;
                        $bad_count++;

                    } else {
                        # есть страницы где нет фоток...
                        if ($json != []) {
                            foreach ($json as $one_img) {
                                DB::insert('catalog_property_img', [
                                    'id' => $key_id,
                                    'path' => $one_img["link"],
                                    'type' => $one_img["classificationLabel"],
                                ]);
                            }
                        }

                        LOGER_::msg("Add images for [$key_id ] [" . $proxy_now[$key_id]["ip"] . ":" . $proxy_now[$key_id]["port"] . "]  [" . $key_id . "]. count_images = " . count($json), "INFO");

                        # скидываем флаг что фотки нужно сканить...
                        DB::update('catalog_property', [
                            'get_images' => 0
                        ], "id=%i", $key_id);

                    }
                }
                $this->new_task->Task_update(0, "Scaning photo =[" . $cur_photo_num . " / " . count($all_images) . "]");
                LOGER_::msg("Scaning photo =[" . $cur_photo_num . " / " . count($all_images) . "]", "INFO");

                $scan_now = [];
                $proxy_now = [];
            }
        }
        return $bad_count;
    }


    # chek time limit... and is active current task?
    function CheckIsParserActive()
    {

        if (MAX_WORK_TIME < TIMER::get()) {
            throw new ParserException("Time limit is up " . TIMER::get() . " > " . MAX_WORK_TIME, $this->new_task);
        }
        if (!$this->new_task->Task_checker()) {
            throw new ParserException("Someone stopped this parser...", $this->new_task);
        }
    }

    # запрашиваем страницу пока не получим ответ (это JSON, и не 0 размер) или закончаться прокси.
    function GetLoopPage($url, $curl_opt, $header, $post_request, $proxy)
    {


        while (1) {
            # is active / time limit
            $this->CheckIsParserActive();

            $get_page = Curl::exec(
                $url,
                $curl_opt,
                $header,
                $post_request,
                $proxy->current_proxy
            );

            LOGER_::msg("Get page " . $proxy->current_proxy["ip"] . ":" . $proxy->current_proxy["port"] . "  size = [" . strlen($get_page["response"]) . "]", "INFO");


            if (strlen($get_page["response"]) > 5000 && strpos($get_page["response"], '<script id="__NEXT_DATA__" type="application/json">') > 0) {
                break;
            } else {
                LOGER_::msg("Can't get json answer... Try other proxy.");
                if (!$proxy->Proxy_next()) {
                    throw new ParserException("Need more proxy. Can't pasring more...", $this->new_task);
                } else {
                    # что-то слетело, возможно изменился ключ...
                    # получаем заново ключ...
                    #
                    $key_api_tmp = $this->GetKey($proxy);


                    #меняем ключ на новый в тек пути сканирования...
                    $url = str_replace($this->key_api, $key_api_tmp, $url);

                    # меняем тек. ключ на новый
                    $this->key_api = $key_api_tmp;
                }
            }
        }


        return $get_page;
    }

    function Update_records($get_json, $proxy, $name_loc, $name_deals)
    {
#
# data process...
#
        foreach ($get_json["pageProps"]["searchResult"]["properties"] as $one_prop) {

            $id = $one_prop["id"];

            $row = DB::queryFirstRow("SELECT *
              FROM catalog_property
              WHERE id=%i LIMIT 1", $id);


            $property_city = '';
            $property_tower = '';
            $property_community = '';
            $property_subcommunity = '';

            foreach ($one_prop["location_tree"] as $one_loc_tmp) {
                switch ($one_loc_tmp["type"]) {
                    case "CITY":
                        $property_city = $one_loc_tmp["name"];
                        break;
                    case "COMMUNITY":
                        $property_community = $one_loc_tmp["name"];
                        break;
                    case "SUBCOMMUNITY":
                        $property_subcommunity = $one_loc_tmp["name"];
                        break;
                    case "TOWER":
                        $property_tower = $one_loc_tmp["name"];
                        break;

                }
            }


            if ($one_prop["completion_status"] == 'completed' ||
                $one_prop["completion_status"] == 'completed_primary') {
                $one_prop["completion_status"] = 'ready';
            }
            if ($one_prop["completion_status"] == 'off_plan' ||
                $one_prop["completion_status"] == 'off_plan_primary') {
                $one_prop["completion_status"] = 'off_plan';
            }

            if ($one_prop["price"]["period"] == 'sell') {
                $one_prop["price"]["period"] = '';
            }


            #update current records...
            if ($row) {
                $this->count_update++;
                DB::update('catalog_property', [
                    'active_flag' => 1,
                    'update_flag' => 0,
                    'data_last_update' => time(),
                    'miss_try_count' => 0, # 3 ошибки - тогда отключаем...

                    'source' => $one_prop["share_url"],
                    'listed_date' => strtotime($one_prop["listed_date"]),
                    'title' => $one_prop["title"],
                    'price' => $one_prop["price"]["value"],
                    'period' => $one_prop["price"]["period"],

                    'main_photo' => $one_prop["images"]["0"]["medium"],


                    'property_type' => $one_prop["property_type"],
                    'full_location_path' => $one_prop["location"]["full_name"],

                    'bedrooms' => $one_prop["bedrooms"],
                    'bathrooms' => $one_prop["bathrooms"],

                    'size_sqft' => $one_prop["size"]["value"],
                    'size_m2' => $one_prop["size"]["value"] / 10.764,

                    'geo_lat' => $one_prop["location"]["coordinates"]["lat"],
                    'geo_lon' => $one_prop["location"]["coordinates"]["lon"],

                    'property_city' => $property_city,
                    'property_tower' => $property_tower,
                    'property_community' => $property_community,
                    'property_subcommunity' => $property_subcommunity,

                    'reference' => $one_prop["reference"],
                    'description' => $one_prop["description"],

                    'property_city' => $name_loc,
                    'deal_type' => $name_deals,
                    'amenity_names' => implode(", ", $one_prop["amenity_names"]),
                    'completion_type' => $one_prop["completion_status"],
                    'furnished' => $one_prop["furnished"],
                    'rera' => $one_prop["rera"]

                ], "id=%i", $id);


                LOGER_::msg("[$id] [" . $proxy->current_proxy["ip"] . ":" . $proxy->current_proxy["port"] . "] Update record  price = " . $one_prop["price"]["value"], "INFO");

                #
                # add images list...
                #

            } else {

                $this->count_add++;
                DB::insert('catalog_property', [
                    'id' => $id,
                    'active_flag' => 1,
                    'update_flag' => 0,
                    'data_add' => time(),
                    'data_last_update' => time(),
                    'miss_try_count' => 0, # 3 ошибки - тогда отключаем...

                    'source' => $one_prop["share_url"],
                    'listed_date' => strtotime($one_prop["listed_date"]),
                    'title' => $one_prop["title"],
                    'price' => $one_prop["price"]["value"],
                    'period' => $one_prop["price"]["period"],

                    'main_photo' => $one_prop["images"]["0"]["medium"],


                    'property_type' => $one_prop["property_type"],
                    'full_location_path' => $one_prop["location"]["full_name"],

                    'bedrooms' => $one_prop["bedrooms"],
                    'bathrooms' => $one_prop["bathrooms"],

                    'size_sqft' => $one_prop["size"]["value"],
                    'size_m2' => $one_prop["size"]["value"] / 10.764,

                    'geo_lat' => $one_prop["location"]["coordinates"]["lat"],
                    'geo_lon' => $one_prop["location"]["coordinates"]["lon"],

                    'property_city' => $property_city,
                    'property_tower' => $property_tower,
                    'property_community' => $property_community,
                    'property_subcommunity' => $property_subcommunity,

                    'reference' => $one_prop["reference"],
                    'description' => $one_prop["description"],

                    'property_city' => $name_loc,
                    'deal_type' => $name_deals,
                    'amenity_names' => implode(", ", $one_prop["amenity_names"]),
                    'completion_type' => $one_prop["completion_status"],
                    'furnished' => $one_prop["furnished"],
                    'get_images' => 1,    # нужно остканить фотки...
                    'rera' => $one_prop["rera"]

                ]);
                LOGER_::msg("[$id] [" . $proxy->current_proxy["ip"] . ":" . $proxy->current_proxy["port"] . "] Add record  price = " . $one_prop["price"]["value"], "INFO");
            }

        }
        return true;
    }




#
# получаем key для парсинга...
#
    function GetKey($proxy)
    {

        # Начинаем парсить
        #

        $this->new_task->Task_update(0, "Get api key...");


        LOGER_::msg(" === start scraping === ", "INFO");

        $curl_opt = [
            CURLOPT_ENCODING => "gzip",
            CURLOPT_HEADER => 0,
            CURLOPT_FAILONERROR => 0,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_VERBOSE => false,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 30
        ];

        $url_key = "https://www.propertyfinder.ae/";

        $api_key = '';
        while ($api_key == '') {

            $get_page = Curl::exec($url_key, $curl_opt, [], '', $proxy->current_proxy);
            LOGER_::msg("Get page " . $proxy->current_proxy["ip"] . ":" . $proxy->current_proxy["port"] . " [$url_key] size = [" . strlen($get_page["response"]) . "]", "INFO");

            # is active?
            $this->CheckIsParserActive();

            if (strpos($get_page["response"], '"buildId":"') > 0) {
                $api_key = explode('"buildId":"', $get_page["response"]);
                $api_key = $api_key[1];
                $api_key = explode('"', $api_key);
                $api_key = $api_key[0];
                break;
            } else {
                LOGER_::msg("Can't detect api_key... Try other proxy.");
                if (!$proxy->Proxy_next()) {
                    throw new ParserException("Need more proxy. Can't pasring more...", $this->new_task);
                }
            }
        }

        LOGER_::msg(" detect api_key = $api_key");

        return $api_key;
    }

    # сравнение строки и массива строк...
    function check_by_str($str, $vars)
    {
        $str = mb_strtolower($str, "UTF8");

        $vars = explode(", ", $vars);
        $good_lines = [];
        foreach ($vars as $one) {
            if (strpos((" " . $str . " "), ($one)) !== FALSE) {
                #echo "yes: $str [$one]\n";
                return true;
            }
        }

        return false;
    }
}

class ParserException extends Exception
{
    function __construct($message = '', $task)
    {
        parent::__construct($message);
        LOGER_::msg($message, "ERROR");
        $task->Task_finish_error($message);

        #
        # some msg with problem info...
        # Scraper is down and don't complete the task...
        exit;
    }

}
