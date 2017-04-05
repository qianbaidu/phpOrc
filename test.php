<?php

include ('Valite.php');
//$imgUrl = 'http://aso100.com/account/getVerifyCodeImage?14774919001814';
$valite = new Valite();

$imgs = glob('./imgs2/*');

foreach($imgs as $k => $v){
    $type = explode('.',$v)[2];
    if($type == 'png'){

        $valite->setImage($v);
        $valite->getHec();
        $ert = $valite->run();
        echo "<div style='padding: 5px;float: left;'>";
        print_r($ert);
        echo '<br><img src="'. $v .'"><br>';
        echo '</div>';
    }
}
//die;
//$valite->setImage('t40.png');
//$valite->getHec();
//$ert = $valite->run();
////$ert = "1234";
//print_r($ert);
//echo '<br><img src="t40.png"><br>';

?>