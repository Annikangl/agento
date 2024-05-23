<?php
    /*
output format:
{"code":1,"fname":"http://test/pdf-create/tmp/1697667820_011ad496741b47b4b3947373287a6233.pdf","error":""}
    
    */
    ini_set('memory_limit', '1024M');
    
    set_time_limit(120); #120 сек должно хватить...
 

    define("PATH_TMP", dirname(__FILE__)."/tmp/"); 
    if (!is_dir(PATH_TMP)){
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
    $default_font =$fontname2;
       
    
 $need_file_del = [];
       
    $data_top = date("d.m.y");
    
    $curl_opt = [
            CURLOPT_ENCODING => "gzip",
            CURLOPT_HEADER =>0,
            CURLOPT_FAILONERROR =>0,
            CURLOPT_FOLLOWLOCATION =>1,
            CURLOPT_VERBOSE => false,
            CURLOPT_RETURNTRANSFER =>1,
            CURLOPT_TIMEOUT=>25,
            CURLOPT_CONNECTTIMEOUT=>25,
            CURLOPT_SSL_VERIFYPEER=>false,                        
            CURLOPT_USERAGENT=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36'
    ];      
    
    $translator = new Translator();
    $count_load = $translator->TranslatorAdd(dirname(__FILE__) . '/lang/property.txt');    
    
    
    if (isset($_POST) && $_POST!=[]){
        $source = $_POST["link"];
        
      #  $name = $_POST["name"];
       # $slogan = $_POST["slogan"];
       # $phone = $_POST["phone"];
       # $whatsapp = $_POST["whaapp"];

       # $icon = $_POST["photo"];
        
        if ($_POST["lang"]=='ru'){

            $translator->default_lang='ru';
        
        }

        #$translator->Translate(
        
        #
        # get photo....
        #
  
    /*
        $images = Curl::exec($icon,  $curl_opt, [], '', []);
        
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
        }*/
        
        if (trim($source) ==''){
                echo json_encode(
                    array(
                        "code" =>-1,
                        "fname" =>'',
                        "error" =>'Error: empty links fields...',
                        "title"=>''
                    )
                );
                exit;
        }        
    }else{

        if ($default_lang=='ru'){
    
          #  $name = 'Тестовый Запуск';        
         #   $slogan = 'Ваш надежный специалист по недвижимости... тут еще какой-то текст не очень длинный';        
        }else{
           # $name = 'Field For FIO';        
           # $slogan = 'Your trusted real estate specialist... here`s some another text...';
        }
       # $phone ='+7 000 000 00 00';
       # $whatsapp ='https://wa.me/1234567890';
        
        $source = "https://www.propertyfinder.ae/en/plp/buy/townhouse-for-sale-dubai-jumeirah-village-triangle-mediterranean-townhouse-10688490.html";
        
      #  $icon = dirname(__FILE__)."/no_photo.jpg";
    }
            

             
     
#
#   загрузка страницы....
#   
    $proxy = new Proxy_webshare(PROXY_SOURCE, true); # источник / true = перемешать
    
# пробуем 2 раза получить если нет - то ошибка...    
    $try_count = 0;
    
    $title_answer ='';
    
    while ($title_answer == '') {

            $get_page = Curl::exec($source, $curl_opt, [], '', $proxy->current_proxy);
  #          echo "$source ".strlen($get_page["response"])." \n";
 #           file_put_contents('1.txt',$get_page["response"]);
 #           exit;
            
            if (strpos($get_page["response"], '<script id="__NEXT_DATA__" type="application/json">') > 0) {
                break;
            } else {               
                if (!$proxy->Proxy_next()) {
                       echo json_encode(
                            array(
                                "code" =>-1,
                                "fname" =>'',
                                "error" =>'Error: need more proxy...',
                                "title"=>''
                            )
                        );
                        exit;
                }
            }
            $try_count++;
            if ($try_count>15) {
                echo json_encode(
                    array(
                        "code" =>-1,
                        "fname" =>'',
                        "error" =>'Error: Can`t get this pages or is not a ads page...',
                        "title"=>''
                    )
                );
                exit;       
            }
    }
    
    #file_put_contents('1.txt',$get_page["response"]);
    
    
    
    $jsonn = explode('<script id="__NEXT_DATA__" type="application/json">',$get_page["response"]);
    $jsonn = $jsonn[1];
    $jsonn = explode('</script>',$jsonn);
    $jsonn = $jsonn[0];
    
    /*
    #window.propertyfinder.settings.search
    if (strpos($jsonn,'tags: ')>0){
        $jsonn = explode('tags: ',$jsonn);
        $jsonn = trim($jsonn[0]);    
        $jsonn = substr($jsonn, 0,-1);    
    }else{
        $jsonn = explode(' };',$jsonn);
        $jsonn = trim($jsonn[0]);    
        $jsonn = substr($jsonn, 0,-1);
    }*/
    
    
    
   # echo ($jsonn);
    #exit;
    
    $jsonn = json_decode($jsonn, true);
    
    /*
    $jsonn2 = explode('<script type="application/ld+json">',$get_page["response"]);
    $jsonn2 =$jsonn2[1];
    $jsonn2 = explode('</script>',$jsonn2);
    $jsonn2 = trim($jsonn2[0]);
    $jsonn2 = json_decode($jsonn2, true);
    */
    #echo $jsonn2;
    #exit;
    
    # 1- путь
    # jsonn_tmp_path - путь....    
    #print_r($jsonn);
   #exit;
   # print_r($jsonn);
 #   exit;
    $jsonn = $jsonn["props"]["pageProps"]["propertyResult"];
    
   # print_r($jsonn);
   # exit;
    
    $title = $jsonn["property"]["title"];
    #$title = str_replace('，',', ',$title);
    
    
    if ($jsonn["property"]["price"]["period"]=='sell'){
        $jsonn["property"]["price"]["period"]='';
        $property_category = 'Sell';
    }else{
        $property_category = 'Rent';
    }
    
    
    if (isset($_POST["price"]) && trim($_POST["price"])!=''){
        $price = "AED ".trim($_POST["price"]);
    }else{
        $price =  number_format($jsonn["property"]["price"]["value"],0," ",",");
        
        if ($jsonn["property"]["price"]["period"]!=''){
            $price = "AED ".$price." / ".$translator->Translate($jsonn["property"]["price"]["period"]);    
        }else{
            $price = "AED ".$price;
        }
        
        
    }     
    
    /*if (isset($jsonn["property"]["data"]["rentFrequency"])){
        $price = $price." ".$translator->Translate($jsonn["property"]["data"]["rentFrequency"]);
    }*/
 
    $descr ='';
    if (isset($_POST["hide_description"]) && $_POST["hide_description"] == true ){
        $descr ='';
    }else{
        $descr = $jsonn["property"]["description"];
        $descr = str_replace("\n","<br>",$descr);
        $descr = str_replace("<br><br>","<br>",$descr);
    } 
    
     if (isset($_POST["description"]) && trim($_POST["description"])!=''){
        $descr =$_POST["description"];
        $descr = str_replace("\n","<br>",$descr);
     }else{
        $descr = $translator->TranslateAPI($descr);
     }    
        
#
# Типы опций:
# Location 
# кол-во кроватей / комнат / площадь
# доп. опции из раздела...

 

$options = [];

#$locations = [];

/*foreach ($jsonn["property"]["data"]["location"] as $one_loc){    
    $locations[] = $one_loc["name"];
}
$locations = array_reverse($locations);
*/
/*
$property_type='';
$property_category='';
if (isset($jsonn["included"])){
    foreach ($jsonn["included"] as $one){
    
        if ($one["type"]=='property_type'){
            $property_type = $translator->Translate(trim($one["attributes"]["name"]));
            $property_type = str_replace("&amp;","&",$property_type);
        }
        if ($one["type"]=='property_category'){
            $property_category = $one["attributes"]["name"];
        }   
        if ($one["type"]=='property_image'){
            $all_img[] = ["url"=>$one["links"]["full_screen"]];
        }   
        
    }
}*/
$all_img = [];

$all_img = $jsonn["property"]["images"]["property"];

#$locations = implode(", ",$locations);
#$locations =[];
#foreach ($jsonn["property"]["location_tree"] as $one_tre){
#    $locations[] = $one_tre["name"];
#}

#$locations = implode(", ",$locations);

$locations = $jsonn["property"]["location"]["full_name"];


$options[]="<b>".$translator->Translate("Location")."</b>: ".$locations;



if ($jsonn["property"]["property_type"] !=''){
    $options[]="<b>".$translator->Translate("Type")."</b>: ".$translator->Translate(trim($jsonn["property"]["property_type"]));
}

if ($property_category!=''){
  $options[]="<b>".$translator->Translate("Purpose")."</b>: ".$translator->Translate($property_category);   
}



if ($jsonn["property"]["bedrooms"]!=''){
    $options[]="<b>".$translator->Translate("Bedrooms")."</b>: ".$jsonn["property"]["bedrooms"];
}

if ($jsonn["property"]["bathrooms"]>0){
$options[]="<b>".$translator->Translate("Bathrooms")."</b>: ".$jsonn["property"]["bathrooms"];
}


$options[]="<b>".$translator->Translate("Property size").":</b> ".number_format($jsonn["property"]["size"]["value"],0," ",",")." ".$translator->Translate($jsonn["property"]["size"]["unit"]);


   

/*
$options[]="<b>".$translator->Translate("Furnishing")."</b>: ".$translator->Translate($jsonn["property"]["data"]["furnishingStatus"]);
*/


 

#
# дополнительные опции:
#   
$all_add_opts = [];

if (strpos($get_page["response"],'Amenities</p>')>0){
    #
    # convert mass svg to files...
    #
    $all_svg = explode('Amenities</p>', $get_page["response"]);
    #unset($all_svg[0]);
    
    $all_svg = explode('Description</p>', $all_svg[1]);
    $all_svg =$all_svg[0];
    
    $all_svg = explode('<svg width="24" height="24" viewBox="0 0 24 24"', $all_svg);
    unset($all_svg[0]);
    
    foreach ($all_svg as $one_svg){
        $one_svg_tmp = explode('</svg>',$one_svg);
        $one_svg_tmp =$one_svg_tmp[0];
        $one_svg_tmp = '<svg width="24" height="24" viewBox="0 0 24 24"'.$one_svg_tmp.'</svg>';
        
        $title_ = explode('<p class="', $one_svg);               
        $title_ = trim($title_[1]);
        $title_ = explode('</p>', $title_);               
        $title_ = trim($title_[0]);
        $title_ = explode('">', $title_);               
        $title_ = trim($title_[1]);
        $title_ = str_replace('&#x27;',"'",$title_);
        
        
       # echo $title." ".$one_svg."\n";
        
        $one_svg_tmp = str_replace('2000/svg" ','2000/svg" width="20" height="18" ',$one_svg_tmp);
        $one_svg_tmp = str_replace('xmlns="http://www.w3.org/2000/svg"','',$one_svg_tmp);


        
        $path = PATH_TMP.md5($title_).".svg";
        file_put_contents($path, $one_svg_tmp);

        $all_add_opts[] = [
            "img_data"=>$path,
            "title"=> $translator->Translate($title_)
        ];
       # break;
    }
    
  
   
}

 
 
#
# все фото...
#
$photos = [];
$first_photo = '';

 
 
 $scan_now = [];
 $proxy_now = [];
foreach ($all_img as $one){
    
    $path = PATH_TMP.md5($one["full"]).".jpeg";
    $path = PATH_TMP.md5($one["full"]).".jpeg";
    
    $scan_now[$path] = $one["full"];
    $proxy_now[$path] = $proxy->GetRandomProxy();
}


$res = Curl::Get_data_multi($scan_now, $curl_opt, $proxy_now);

$first_photo ='';
$first_photo_down = 0;

$is_vertical = 0;
$first_3 = [];

foreach ($res as $path => $one_res) {


    # skipp bad picture...
    if (strlen($one_res)<=1000){
        unset($res[$path]);
        continue;
    }
    
    file_put_contents($path, $one_res);
        
 if ($first_photo==''){
            
       $first_photo = $path;
        $first_photo = str_replace(".","__.",$first_photo);
        
#        echo "path =$path\n";
#        echo "first_photo =$first_photo\n";
#        exit;
        
        $image = new Thumbs($path);
        
        $img_x = $image->width;
        $img_y = $image->height;
        
        if ($img_x/$img_y>1.375 || $img_x/$img_y<1.125){
            # обрезаем первое фото...
            if ($img_x/$img_y>1.375){
                $max_x = intval($img_y*1.25);
                $image->thumb($max_x-1, $img_y-1);
            }elseif ($img_x/$img_y<1.125){
                $max_y = intval($img_x/1.25);
                $image->thumb($img_x-1, $max_y-1);  
            }
            
            $image->save($first_photo);

        }else{
            $image->save($first_photo);
        }
        $need_file_del [] =$first_photo;

      }else{
        if (count($first_3)<3){
            $first_3[] = $path;
        }
      }
    
    $image = new Thumbs($path);
        #$image->cut(640, 490);
        $img_x = $image->width;
        $img_y = $image->height;

       
        
        if ($img_y!=0 && $img_x>$img_y){
            #$photos["vert"][] = $path;
            $all_photo[] = ["url"=>$path,"type"=>"gor"];
           if ($img_x/$img_y>1.55){
                $max_x = intval($img_y*1.41);
                $image->thumb($max_x-1, $img_y-1);
            }elseif ($img_x/$img_y<1.27){
                $max_y = intval($img_x/1.41);
                $image->thumb($img_x-1, $max_y-1);  
            }           
        }else{
            #$photos["gor"][] = $path;
            $all_photo[] = ["url"=>$path,"type"=>"vert"];
            if ($img_x/$img_y>0.777071){
                $max_x = intval($img_y*0.707071);
                $image->thumb($max_x-1, $img_y-1);
            }elseif ($img_x/$img_y<0.637071){
                $max_y = intval($img_x/0.707071);
                $image->thumb($img_x-1, $max_y-1);  
            }
                        
        } 
        
        $image->save();   

    
    $need_file_del [] =$path;
    
    
}

if ($first_photo==''){
    $first_photo = dirname(__FILE__).'/no-photo.png';
}

if (count($first_3)<3){
    for ($i=count($first_3);$i<3;$i++){
        $first_3[] = dirname(__FILE__).'/no-photo.png';
    }
}
  
#
# создаем PDF
#
   
class MYPDF extends TCPDF {
    //Page header
    public function Header() {
        // Logo
      $this->Rect(0, 0, $this->getPageWidth(), $this->getPageHeight(), 
                   'DF', "",  array(224, 207, 184));      
    } 
}  
        
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);


// set margins
$pdf->setMargins(10, 5, 10);


// set auto page breaks
$pdf->setAutoPageBreak(TRUE, 20);

// set image scale factor
#$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

 # фон сверху...
 # фон сверху...
    $pdf->AddPage();    

  /*  $pdf->SetFillColor(46, 46, 46);
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
    $pdf->SetTextColor(0,0,0);
    $pdf->SetFont($default_font,'B',16);
    
    $y = $pdf->GetY();
 
    
  ###  #$pdf->MultiCell(140,10, $translator->TranslateAPI($title),0,'L','',true);
 
    $pdf->MultiCell(120, 10,   $translator->TranslateAPI($title), 0, 'L', 0, 0, '', '', true, 0, false, true, 20, 'M'); 
   
    #$pdf->SetXY(160,64);
    #$pdf->MultiCell(150,10,$price ,0,'L','',true);
       # цена
    $pdf->SetXY(140,10);
    
    $pdf->setFont($default_font, 'B', 16);
    $pdf->writeHTMLCell(65, 10, '', '', $price, 0, 0, 0, true, '', true);
          
 
    
 
    $pdf->SetFont($default_font,'',14);

    $i=0;
    
    $pdf->SetXY(115,30);
    $y = $pdf->GetY();
    
foreach ($options as $one){    
    $pdf->SetXY(115,$y+1);
    #$pdf->WriteHTML($one,  true, 0, true, 0);
    
    $pdf->writeHTMLCell(0, 0, '', '', $one, 0, 1, 0, true, '', true);
    $y = $pdf->GetY();
    $i++;
}
#$pdf->SetXY(115,$y+1);
#$pdf->writeHTMLCell(0, 0, '', '', "first = ".$first_photo_down, 0, 1, 0, true, '', true);

   
$pdf->Image($first_photo,10,30, 100,80, '','',true,true);

    $num_img =0; 
    
    foreach ($first_3 as $one_img){
        $pdf->Image($one_img,10+$num_img*34,112, 32, 24, '','',true,false);
        $num_img++;
    }

    #
    # описалово
    $pdf->SetFont($default_font,'',14);
   
    $pdf->SetXY(10,140);

    $pdf->Ln(1);
    
    $pdf->SetFont($default_font,'B',14);
    $pdf->MultiCell(0,0, $translator->Translate("Information"),0,'L','',true);

   
    
    $pdf->SetFont($default_font,'',11);
    $pdf->WriteHTML($descr, true, false, true, false, '');
###    $pdf->WriteHTML($descr, true, false, true, false, '');
    
         
    
    
    
    
    $y = $pdf->GetY();   
    
    if ($all_add_opts!=[]){        
        $pdf->Ln();
        $y = $pdf->GetY();     
        if ($y>200){
            $pdf->AddPage();
            $y = $pdf->GetY();
        }
        
        
        $pdf->SetFont($default_font,'B',14);
        $pdf->MultiCell(0,10, $translator->Translate("Amenities"),0,'L','',true);
         
        $pdf->SetFont($default_font,'',11);
        
        $y = $pdf->GetY();
        
        foreach ($all_add_opts as $one_opts){
        
            $pdf->ImageSVG($one_opts["img_data"], 25+$num*37, $y, $w='', $h=13,  '', $align='', $palign='', $border=0, $fitonpage=true);
       
            $pdf->MultiCell(35,15, $one_opts["title"],  0, 'C', 0, 0, 15+$num*37, $y+18, true);
            
            
            $num++;
            if ($num>4) {
                 $y = $pdf->GetY()+18;
                 if ($y>245){
                    if ($one_opts==end($all_add_opts)){
                        break;
                    }
                     
                     $pdf->AddPage();
                     $y = 10;
                 }
                 $num=0;
            }
        }
        $pdf->Ln(22);
        $y = $pdf->GetY();   
      #  $pdf->MultiCell(28,15, "y = ".$y,  0, 'С', 0, 0, 20+$num*30, $y+18, true);      
    }else{
        $pdf->Ln(1);
        $y = $pdf->GetY();                
    }

      if (isset($_POST["display_source_link"]) && trim($_POST["display_source_link"])== true ){   
       $pdf->setAutoPageBreak(FALSE, 0);
       
       $pdf->SetFont($default_font,'',7);
       $pdf->SetXY(0,275);
        $pdf->SetTextColor(204,204,204);   
       #$pdf->Cell(0, 0,"Ссылка на источник: ".str_replace("https://","https:// ",$source)."." ,0,0,'C',false, "https://krisha.kz/");
       $pdf->Cell(0, 0,$translator->Translate("Source link:"). "https://www.propertyfinder.ae/" ,0,0,'C',false, "https://www.propertyfinder.ae/");
    } 
    
    
    
    
    $num = 0;    
    $row = 0;
    
       $pdf->setPrintFooter(false);
    $pdf->SetAutoPageBreak(false, 0);
    
   if ($all_photo!=[]){
    foreach ($all_photo as $key=>$one){
        $row++;        
        #$pdf->Image($one,20,25, 170,240, '', '', '', false, 300, '', false, false, 0, "CM", false, false); 
        if ($one["type"]=='vert'){
            $pdf->AddPage("P");
            $pdf->Image($one["url"], 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
        }else{
            $pdf->AddPage("L");
            $pdf->Image($one["url"], 0, 0, 297, 210, '', '', '', false, 300, '', false, false, 0);
        }

 
    }    
 
   }
 
     
// move pointer to last page
$pdf->lastPage();

$f_name = (time())."_".md5($name).".pdf";

#$pdf->Output( PATH_TMP.$f_name, 'I');
$pdf->Output( PATH_TMP.$f_name, 'F');
 

foreach ($need_file_del as $one){
    unlink($one);
}




echo json_encode(
                    array(
                        "code" =>1,
                        "fname" => HOST_NAME.$f_name,
                        "error" =>'',
                        "title"=>$title
                    )
                );


   
exit;
  
?>