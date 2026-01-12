<?  if (!empty($_GET["gate"])){
    date_default_timezone_set('Asia/Hong_Kong');
    $date = empty($_GET["date"]) ? date("Y-m-d") : $_GET["date"];
    $url = "https://www.hongkongairport.com/flightinfo-rest/rest/flights?date=".$date."&arrival=false&lang=zh_HK&cargo=false";
    $content = file_get_contents($url);
    $decode = json_decode($content, true);

    foreach ($decode as $json) {
      foreach ($json["list"] as $item) {
        if ($item["gate"] == $_GET["gate"] && !str_contains($item["status"], "啟航")){
          $error = "";

          $time = $item["time"];
          echo $item["time"].',';
          
          foreach ($item["flight"] as $flight_no){
            echo $flight_no["no"].',';
          }

          foreach ($item["destination"] as $dest){
            $dest_zh = $dest_en = $dest;
            $airport_code = "https://ewlricw.rf.gd/fidp/dest.json";
            $aptc = file_get_contents($airport_code);
            $data = json_decode($aptc, true);
            if ($data['active'] == true){
              foreach ($data['dest'] as $apt) {
                if ($apt['code'] == $dest_zh) {
                  $dest_zh = $apt['zh_HK'];
                  $dest_en = $apt['en'];
                  break;
                }
              }
            }
            echo $dest_zh.',';
            echo $dest_en.',';
          }

          if (str_contains($item["status"], "最後")){
            echo $item["status"].",";
          } else {
            echo $item["status"].",";
          }

          if (str_contains($item["status"], "最後")){
            echo "Final Call";
          } else if (str_contains($item["status"], "預備")){
            echo "Boarding Soon";
          } else if (str_contains($item["status"], "現正")){
            echo "Now Boarding";
          } else if (str_contains($item["status"], "預計")){
            echo str_replace('預計', 'Est at ', $item["status"]);
          } else if (str_contains($item["status"], "啟航")){
            echo str_replace('啟航', 'Dep ', $item["status"]);
          } else if (str_contains($item["status"], "截止")){
            echo "Gate Closed";
          }  else if (str_contains($item["status"], "取消")){
            echo "Cancelled";
          }  else if (str_contains($item["status"], "延誤")){
            echo "Delayed";
          } else {
            echo $item["status"];
          }
          break;
        }
      }
    }
  } else {
    echo "Invalid Inputs";
    exit(-1);
  }

  if (empty($time)){
    if (empty($_GET["date"])){
      $tmr = new DateTime('tomorrow');
      header("Location: ?gate=".$_GET["gate"]."&date=".$tmr->format('Y-m-d'));
    } else {
      $error = "No Recent Flight";
    }
    echo $error;
  }
?>
