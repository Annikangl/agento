<?php
/*
output format:
{"code":1,"fname":"http://test/pdf-create/tmp/1697667820_011ad496741b47b4b3947373287a6233.pdf","error":""}

*/
ini_set('memory_limit', '1024M');

set_time_limit(120); #120 сек должно хватить...


define("PATH_TMP", dirname(__FILE__) . "/tmp/");
if (!is_dir(PATH_TMP)) {
    mkdir(PATH_TMP);
}

# dialog_msg
global $dialog_msg;
global $default_lang;

#
$default_lang = 'en';

include 'pdf_config.php';

include 'class/Proxy.php';
include 'class/Curl.php';
include 'class/thumbs.php';
include 'class/Translator.php';

include 'class/tcpdf/examples/tcpdf_include.php';


$fontname = TCPDF_FONTS::addTTFfont('font/SF-Bold.ttf', 'TrueTypeUnicode', '', 32);
$fontname2 = TCPDF_FONTS::addTTFfont('font/SF.ttf', 'TrueTypeUnicode', '', 32);
$default_font = $fontname2;


$need_file_del = [];

$data_top = date("d.m.y");

$curl_opt = [
    CURLOPT_ENCODING => "gzip",
    CURLOPT_HEADER => 0,
    CURLOPT_FAILONERROR => 0,
    CURLOPT_FOLLOWLOCATION => 1,
    CURLOPT_VERBOSE => false,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_TIMEOUT => 25,
    CURLOPT_CONNECTTIMEOUT => 25,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36'
];

$translator = new Translator();
$count_load = $translator->TranslatorAdd(dirname(__FILE__) . '/lang/dubizzle.txt');


if (isset($_POST) && $_POST != []) {

    if ($_POST["lang"] == 'ru') {

        #  $default_font = 'freeserif';

        $translator->default_lang = 'ru';

    }

    #$translator->Translate(

    $source = $_POST["link"];

    #   $name = $_POST["name"];
    #   $slogan = $_POST["slogan"];
    #   $phone = $_POST["phone"];
    #   $whatsapp = $_POST["whaapp"];

    #  $icon = $_POST["photo"];
    #
    # get photo....
    #


    /*  $images = Curl::exec($icon,  $curl_opt, [], '', []);

      if (strlen($images["response"])<=200 || strpos($images["response"], "403")>0
      || strpos($images["response"], "404")>0
      ){
          $icon = dirname(__FILE__)."/no_photo.jpg";
      }else{
          #
          #   конвертация фото
          #


          file_put_contents(PATH_TMP."".md5($icon).".jpg", $images["response"]);

          $image = new Thumbs(PATH_TMP."".md5($icon).".jpg");
          $image->cut(100, 120);
          $image->save();

          $icon = PATH_TMP."".md5($icon).".jpg";

          $need_file_del [] =$icon;
      }
      */
    if (trim($source) == '') {
        echo json_encode(
            array(
                "code" => -1,
                "fname" => '',
                "error" => 'Error: empty links fields...',
                "title" => ''
            )
        );
        exit;
    }
} else {

    if ($default_lang == 'ru') {

        #   $name = 'Тестовый Запуск';
        #    $slogan = 'Ваш надежный специалист по недвижимости... тут еще какой-то текст не очень длинный';
    } else {
        #   $name = 'Field For FIO';
        #  $slogan = 'Your trusted real estate specialist... here`s some another text...';
    }
    # $phone ='+7 000 000 00 00';
    # $whatsapp ='https://wa.me/1234567890';

    $source = "https://dubai.dubizzle.com/property-for-rent/residential/apartmentflat/2023/10/21/direct-from-landlord-flexible-payment-neat-2-144/";

    $icon = dirname(__FILE__) . "/no_photo.jpg";
}


#
#   загрузка страницы....
#
$proxy = new Proxy_webshare(PROXY_SOURCE, true); # источник / true = перемешать

# пробуем 2 раза получить если нет - то ошибка...
$try_count = 0;

$title_answer = '';

while ($title_answer == '') {

    $get_page = Curl::exec($source, $curl_opt, [], '', $proxy->current_proxy);
#            echo "$source ".strlen($get_page["response"])." \n";
    if (strpos($get_page["response"], '<h2 class="MuiTypography-root MuiTypography-body1 ') > 0) {
        break;
    } else {
        if (!$proxy->Proxy_next()) {
            echo json_encode(
                array(
                    "code" => -1,
                    "fname" => '',
                    "error" => 'Error: need more proxy...',
                    "title" => ''
                )
            );
            exit;
        }
    }
    $try_count++;
    if ($try_count > 15) {
        echo json_encode(
            array(
                "code" => -1,
                "fname" => '',
                "error" => 'Error: Can`t get this pages or is not a ads page...',
                "title" => ''
            )
        );
        exit;
    }
}

$jsonn = explode('<script id="__NEXT_DATA__" type="application/json">', $get_page["response"]);
$jsonn = $jsonn[1];
$jsonn = explode('</script>', $jsonn);
$jsonn = $jsonn[0];

#file_put_contents('1.txt', $jsonn);

$jsonn = json_decode($jsonn, true);

# find needed part of the record
foreach ($jsonn["props"]["pageProps"]["reduxWrapperActionsGIPP"] as $one) {
    if ($one["type"] == "listings/detailPropertyRequest/fulfilled") {
        break;
    }
}

$title = trim($one["payload"]["name"]["en"]);

if (isset($_POST["price"]) && trim($_POST["price"]) != '') {
    $price = "AED " . number_format(trim($_POST["price"]), 0, " ", ",");
} else {
    $price = "AED " . number_format($one["payload"]["ad_ops"]["price_aed"], 0, " ", ",");
}


if (isset($_POST["hide_description"]) && $_POST["hide_description"] == true) {
    $descr = '';
} else {
    $descr = trim($one["payload"]["description"]["en"]);
}

if (isset($_POST["description"]) && trim($_POST["description"]) != '') {
    $descr = $_POST["description"];
    $descr = str_replace("\n", "<br>", $descr);
} else {
    $descr = $translator->TranslateAPI($descr);
}


#
# Типы опций:
# Location
# кол-во кроватей / комнат / площадь
# доп. опции из раздела...


$options = [];

$options[] = "<b>" . $translator->Translate("Location") . "</b>: " . $one["payload"]["ad_ops"]["loc"];


foreach ($one["payload"]["property_info"] as $one_opt) {
    $name_ = $one_opt["label"]["en"];

    if ($name_ == 'Updated') continue;

    $value = $translator->Translate($one_opt["value"]["en"]);
    $options[] = "<b>" . $translator->Translate($name_) . "</b>: " . $value;
}

#print_r($one["payload"]["summary"]);
#exit;
foreach ($one["payload"]["summary"] as $one_opt) {
    $name_ = trim($one_opt["label"]["en"]);

    if ($name_ == 'Updated') continue;

    $value = $translator->Translate(trim($one_opt["value"]["en"]));

    if ($name_ == 'Size') {
        $value = $value . " " . $translator->Translate("sqft");
    }

    $options[] = "<b>" . $translator->Translate($name_) . "</b>: " . $value;
}


#
# дополнительные опции:
#
$all_add_opts = [];

if (strpos($get_page["response"], 'Amenities</h2>') > 0) {
    $all_opts = explode('<div class="MuiGrid-root MuiGrid-item MuiGrid-grid-xs-4 MuiGrid-grid-md-2', $get_page["response"]);
    unset($all_opts[0]);

    foreach ($all_opts as $opts) {
        $imgs = explode('src="', $opts);
        $imgs = $imgs[1];
        $imgs = explode('"', $imgs);
        $imgs = $imgs[0];
        $titl = explode('data-testid="', $opts);
        $titl = $titl[2];
        $titl = explode('"', $titl);
        $titl = $titl[0];

        #$images = Curl::exec($icon,  $curl_opt, [], '', []);
        $path = PATH_TMP . md5($imgs) . ".svg";


        if (is_file($path)) {
            $data_svg = file_get_contents($path);
        } else {
            if (is_file($path)) {

            } else {
                $get_svg = Curl::exec($imgs, $curl_opt, [], '', $proxy->current_proxy);

                $get_svg = str_replace('stroke=', 'fill-opacity="0" stroke=', $get_svg);
                #$image->saveJPG($path_after);
                #$jpegBinaryString = Svg::make($get_img)->toJpeg();
                file_put_contents($path, $get_svg["response"]);

            }
        }
        $all_add_opts[] = [
            "img_data" => $path,
            "title" => $translator->Translate(trim(str_replace("&amp;", "&", $titl)))
        ];
    }
}
#
#print_r($all_add_opts);
#exit;


#
# все фото...
#
$photos = [];
$first_photo = '';

$scan_now = [];
$proxy_now = [];


foreach ($one["payload"]["photos"] as $one) {


    $path = PATH_TMP . md5($one["main"]) . ".jpeg";

    $scan_now[$path] = $one["main"];
    $proxy_now[$path] = $proxy->GetRandomProxy();
}


$res = Curl::Get_data_multi($scan_now, $curl_opt, $proxy_now);

$first_photo = '';
$first_photo_down = 0;

$is_vertical = 0;
$first_3 = [];

foreach ($res as $path => $one_res) {


    # skipp bad picture...
    if (strlen($one_res) <= 1000) {
        unset($res[$path]);
        continue;
    }

    file_put_contents($path, $one_res);

    if ($first_photo == '') {

        $first_photo = $path;
        $first_photo = str_replace(".", "__.", $first_photo);

#        echo "path =$path\n";
#        echo "first_photo =$first_photo\n";
#        exit;

        $image = new Thumbs($path);

        $img_x = $image->width;
        $img_y = $image->height;

        if ($img_x / $img_y > 1.375 || $img_x / $img_y < 1.125) {
            # обрезаем первое фото...
            if ($img_x / $img_y > 1.375) {
                $max_x = intval($img_y * 1.25);
                $image->thumb($max_x - 1, $img_y - 1);
            } elseif ($img_x / $img_y < 1.125) {
                $max_y = intval($img_x / 1.25);
                $image->thumb($img_x - 1, $max_y - 1);
            }

            $image->save($first_photo);

        } else {
            $image->save($first_photo);
        }
        $need_file_del [] = $first_photo;

    } else {
        if (count($first_3) < 3) {
            $first_3[] = $path;
        }
    }

    $image = new Thumbs($path);
    #$image->cut(640, 490);
    $img_x = $image->width;
    $img_y = $image->height;


    if ($img_y != 0 && $img_x > $img_y) {
        #$photos["vert"][] = $path;
        $all_photo[] = ["url" => $path, "type" => "gor"];
        if ($img_x / $img_y > 1.55) {
            $max_x = intval($img_y * 1.41);
            $image->thumb($max_x - 1, $img_y - 1);
        } elseif ($img_x / $img_y < 1.27) {
            $max_y = intval($img_x / 1.41);
            $image->thumb($img_x - 1, $max_y - 1);
        }
    } else {
        #$photos["gor"][] = $path;
        $all_photo[] = ["url" => $path, "type" => "vert"];
        if ($img_x / $img_y > 0.777071) {
            $max_x = intval($img_y * 0.707071);
            $image->thumb($max_x - 1, $img_y - 1);
        } elseif ($img_x / $img_y < 0.637071) {
            $max_y = intval($img_x / 0.707071);
            $image->thumb($img_x - 1, $max_y - 1);
        }

    }

    $image->save();


    $need_file_del [] = $path;

}

if ($first_photo == '') {
    $first_photo = dirname(__FILE__) . '/no-photo.png';
}

if (count($first_3) < 3) {
    for ($i = count($first_3); $i < 3; $i++) {
        $first_3[] = dirname(__FILE__) . '/no-photo.png';
    }
}

#
# создаем PDF
#

class MYPDF extends TCPDF
{
    //Page header
    public function Header()
    {
        // Logo
        $this->Rect(0, 0, $this->getPageWidth(), $this->getPageHeight(),
            'DF', "", array(224, 207, 184));
    }
}

#$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


$pdf->setPrintHeader(false);
$pdf->setPrintFooter(true);


// set margins
$pdf->setMargins(10, 5, 10);


// set auto page breaks
$pdf->setAutoPageBreak(TRUE, 20);

// set image scale factor
#$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

# фон сверху...
$pdf->AddPage();
/*
    $pdf->SetFillColor(46, 46, 46);
    $pdf->Rect(0,0,220,60, "F");

    $pdf->SetTextColor(255,255,255);
    $pdf->SetFont($default_font,'B',22);

    $pdf->SetX(10);
    $pdf->Cell(0,0,$translator->Translate("Commercial offer"),0,0,'L',true);


   $pdf->SetFont($default_font,'',16);
    $pdf->SetXY(170,7);
    $pdf->Cell(0, 0,$data_top,0,0,'R',true);

    $pdf->Image($icon,10,22, 25,30);

    $pdf->Ln(14);
    $pdf->SetFont($default_font,'B',18);
    $pdf->SetX(40);
    $pdf->Cell(30,0, $name,0,0,'L',true);

    $pdf->SetFont($default_font,'',14);

    $pdf->SetXY(140,22);
    $pdf->SetFont($default_font,'',14);

   # $pdf->Write(5, $translator->Translate("Contacts").":", '');
    $pdf->Cell(0, 0,$translator->Translate("Contacts").":",0,0,'R',true);

    $pdf->SetFont($default_font,'',14);
    $pdf->SetXY(140,29);

    $pdf->Cell(0, 0,$phone,0,0,'R',true, "tel:".$phone);


    $pdf->SetFont($default_font,'',12);
    $pdf->SetXY(40,30);
    $pdf->MultiCell(95,8, $slogan,0,'L','',true);



if (isset($_POST["socials"])){
        $step_icon = 0;
        if (isset($_POST["socials"]["whaapp"]) && $_POST["socials"]["whaapp"]!=''){
            $pdf->Image(dirname(__FILE__).'/viber.png',40,43, 10,0,'PNG',$_POST["socials"]["whaapp"], '', true, 150, '', false, false, 1, false, false, false);
            $step_icon++;
        }
        if (isset($_POST["socials"]["website"]) && $_POST["socials"]["website"]!=''){
            $pdf->Image(dirname(__FILE__).'/website.png',$step_icon*12+40,43, 10,0,'PNG',$_POST["socials"]["website"], '', true, 150, '', false, false, 1, false, false, false);
            $step_icon++;
        }
        if (isset($_POST["socials"]["instagram"]) && $_POST["socials"]["instagram"]!=''){
            $pdf->Image(dirname(__FILE__).'/insta.png',$step_icon*12+40,43, 10,0,'PNG',$_POST["socials"]["instagram"], '', true, 150, '', false, false, 1, false, false, false);
            $step_icon++;
        }

    }

    // Line break
    $pdf->Ln(15);

    $pdf->SetXY(10,55);
    */
# заголовок объявления
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont($default_font, 'B', 16);

$y = $pdf->GetY();


#$pdf->MultiCell(140,10, $translator->TranslateAPI($title),0,'L','',true);

$pdf->MultiCell(120, 10, $translator->TranslateAPI($title), 0, 'L', 0, 0, '', '', true, 0, false, true, 20, 'M');

#$pdf->SetXY(160,64);
#$pdf->MultiCell(150,10,$price ,0,'L','',true);
# цена
$pdf->SetXY(140, 10);

$pdf->setFont($default_font, 'B', 16);
$pdf->writeHTMLCell(55, 10, '', '', $price, 0, 0, 0, true, '', true);


/*$i=0;
$y = $pdf->GetY()+14;
$pdf->SetXY(115,110);
*/
$pdf->SetFont($default_font, '', 14);

$i = 0;

$pdf->SetXY(115, 30);
$y = $pdf->GetY();

foreach ($options as $one) {
    $pdf->SetXY(115, $y + 1);
    #$pdf->WriteHTML($one,  true, 0, true, 0);

    $pdf->writeHTMLCell(0, 0, '', '', $one, 0, 1, 0, true, '', true);
    $y = $pdf->GetY();
    $i++;
}

$pdf->Image($first_photo, 10, 30, 100, 80, '', '', true, true);

$num_img = 0;

foreach ($first_3 as $one_img) {
    $pdf->Image($one_img, 10 + $num_img * 34, 112, 32, 24, '', '', true, false);
    $num_img++;
}

#
# описалово
# $pdf->SetFont($default_font,'',14);

$pdf->SetXY(10, 140);

$pdf->Ln(1);

$pdf->SetFont($default_font, 'B', 14);
$pdf->MultiCell(0, 0, $translator->Translate("Information"), 0, 'L', '', true);

$pdf->Ln(3);

$pdf->SetFont($default_font, '', 11);
$pdf->WriteHTML($descr, true, false, true, false, '');


#$pdf->SetFont($default_font,'',12);
#$pdf->WriteHTML($translator->TranslateAPI($descr));


if ($all_add_opts != []) {
    $pdf->Ln(3);
    $y = $pdf->GetY();
    if ($y > 200) {
        $pdf->AddPage();
        $y = $pdf->GetY();
    }


    $pdf->SetFont($default_font, 'B', 14);
    $pdf->MultiCell(0, 10, $translator->Translate("Amenities"), 0, 'L', '', true);

    $num = 0;

    $pdf->SetFont($default_font, '', 14);

    $y = $pdf->GetY();

    foreach ($all_add_opts as $one_opts) {

        $pdf->ImageSVG($one_opts["img_data"], 25 + $num * 37, $y, $w = '', $h = 13, '', $align = '', $palign = '', $border = 0, $fitonpage = true);

        $pdf->MultiCell(35, 15, $one_opts["title"], 0, 'C', 0, 0, 15 + $num * 37, $y + 18, true);


        $num++;
        if ($num > 4) {
            $y = $pdf->GetY() + 18;
            if ($y > 245) {
                if ($one_opts == end($all_add_opts)) {
                    break;
                }

                $pdf->AddPage();
                $y = 10;
            }
            $num = 0;
        }
    }
    $pdf->Ln(20);
    #  $y = $pdf->GetY();
    #  $pdf->MultiCell(28,15, "y = ".$y,  0, 'С', 0, 0, 20+$num*30, $y+18, true);
} else {
    $pdf->Ln(5);
    $y = $pdf->GetY();
}

if (isset($_POST["display_source_link"]) && trim($_POST["display_source_link"]) == true) {
    $pdf->setAutoPageBreak(FALSE, 0);

    $pdf->SetFont($default_font, '', 7);
    $pdf->SetXY(0, 275);
    $pdf->SetTextColor(204, 204, 204);
    #$pdf->Cell(0, 0,"Ссылка на источник: ".str_replace("https://","https:// ",$source)."." ,0,0,'C',false, "https://krisha.kz/");
    $pdf->Cell(0, 0, $translator->Translate("Source link:") . "https://www.dubizzle.com/", 0, 0, 'C', false, "https://www.dubizzle.com/");
}


$num = 0;
$row = 0;

$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(false, 0);

if ($all_photo != []) {
    foreach ($all_photo as $key => $one) {
        $row++;
        #$pdf->Image($one,20,25, 170,240, '', '', '', false, 300, '', false, false, 0, "CM", false, false);
        if ($one["type"] == 'vert') {
            $pdf->AddPage("P");
            $pdf->Image($one["url"], 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
        } else {
            $pdf->AddPage("L");
            $pdf->Image($one["url"], 0, 0, 297, 210, '', '', '', false, 300, '', false, false, 0);
        }


    }

}


// move pointer to last page
$pdf->lastPage();

$f_name = (time()) . "_" . md5($name) . ".pdf";

#$pdf->Output( PATH_TMP.$f_name, 'I');
$pdf->Output(PATH_TMP . $f_name, 'F');


foreach ($need_file_del as $one) {
    unlink($one);
}


#exit;

echo json_encode(
    array(
        "code" => 1,
        "fname" => HOST_NAME . $f_name,
        "error" => '',
        "title" => $title
    )
);


exit;

?>
