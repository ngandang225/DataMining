<?php

// $data_item = [
//     ["id"=>1, "item"=>"iPhone;Samsung;Lenovo"],
//     ["id"=>2, "item"=>"Nokia;iPhone;Samsung;LG;Lenovo"],
//     ["id"=>3, "item"=>"Nokia;Samsung"],
//     ["id"=>4, "item"=>"iPhone;Samsung;Nokia"],
//     ["id"=>5, "item"=>"Lenovo"],
//     ["id"=>6, "item"=>"Samsung;LG"],
//     ["id"=>7, "item"=>"Samsung;Lenovo"]
// ];

function read_data($filename) {
    $file = fopen($filename, "r");
    $data_item = array();
    
    $index = 1;

    if ($file) {
        while (($line = fgets($file)) !== false) {
            $line = trim ($line);
            $data = explode(" ", $line);
            $item = implode(";", $data);
            array_push($data_item, array("id" => $index, "item" => $item));
            $index++;
        }
    }
    fclose($file);
    return $data_item;
}

$data_item = read_data("dataApriori.txt");


$minSupport = 2;

//Lấy từng phần tử mảng, chuyển mảng đầu vào thành Deep array để xử lý

$arr = [];
for ($i = 0; $i < count($data_item); $i++){
    $ar = [];
    $val = explode(";", $data_item[$i]['item']);
    for ($j = 0; $j < count($val); $j++){
        $ar[] = $val[$j];
    }
    array_push($arr, $ar);
}
// $arr = [
//     ["iPhone", "Samsung", "Lenovo"],
//     ["Nokia","iPhone","Samsung","LG","Lenovo"],
//     ["Nokia", "Samsung"],
//     ["iPhone", "Samsung", "Nokia"],
//     ["Lenovo"],
//     ["Samsung", "LG"],
//     ["Samsung", "Lenovo"],
// ]

//frequency
$frequency_item = frequencyItem($arr);
$elimination_item = eliminationItem($frequency_item, $minSupport);

// print_r($frequency_item);
// echo ("<br>");
// print_r($elimination_item);
// echo ("<br>");

do {
    $pair_item = pairItem($elimination_item);
    $frequency_item = frequencyPairItem($pair_item, $arr);
    $elimination_item = eliminationItem($frequency_item, $minSupport);
} while ($elimination_item == $frequency_item);

//Đếm số lần item xuất hiện 
function frequencyItem($data){
    $arr = [];
    for ($i = 0; $i < count($data); $i++){
        $count = array_count_values($data[$i]);
        foreach ($count as $key => $val) {
            if (array_key_exists($key, $arr)) {
                $arr[$key] += 1;
            } else {
                $arr[$key] = 1;
            }
        }
    }
    return $arr;
}

//Loại bỏ item nhỏ hơn minSupport
function eliminationItem($data, $min) {
    $arr = [];
    foreach ($data as $key => $v) {
        if ($v >= $min) {
            $arr[$key] = $v;
        }
    }
    return $arr;
}

//Ghép cặp item
function pairItem($data){
    $n = 0;
    $arr = [];
    foreach ($data as $key1 => $v1) {
        $m = 1;
        foreach ($data as $key2 => $v2) {
            $str = explode("_", $key2);
            for ($i = 0; $i < count($str); $i++) {
                if (!strstr($key1, $str[$i])) {
                    if ($m > $n + 1 && count($data) > $n + 1) {
                        $arr[$key1 . "_" . $str[$i]] = 0;
                    }
                }
            }
            $m++;
        }
        $n++;
    }
    return $arr;
}
//Số lần xuất hiện của cặp item
function frequencyPairItem($data_pair, $data){
    $arr = $data_pair;
    foreach ($data_pair as $key => $k) {
        for ($i = 0; $i < count($data); $i++) {
            $kk = explode("_", $key);
            $jm = 0;
            
            for ($k = 0; $k < count($kk); $k++) {
                for ($l = 0; $l < count($data[$i]); $l++) {
                    if ($data[$i][$l] == $kk[$k]) {
                        $jm += 1;
                        break;
                    }
                }
            }
            if ($jm > count($kk) - 1) {
                $arr[$key] += 1;
            }
        }
    }
    return $arr;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apriori</title>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>
<body style="padding: 40px;">
    <h3 class="text-left">Algorithm Apriori</h3>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <!-- note here -->
                <div class="panel-body">Known</div>
                <div class="panel-footer">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="text-center">Id</th>
                                    <th colspan="5" class="text-center">Item</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                for ($i = 0; $i < count($data_item); $i++) {
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $data_item[$i]['id']?></td>
                                        <td><?= $data_item[$i]['item']?></td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- note here -->
            </div>
        </div>
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">Question</div>
                <div class="panel-footer">
                How to identify the pattern or rule that if one item is chosen, there is a likelihood of choosing the other items.
            </div>
        </div>
        <div class="col-md-12">
            <div class="panel panel-default">
                <form action="" method="post">
                    <div class="panel-body">Solution <button type="submit" name="submit">Process</button></div>
                </form>
                <?php if (isset($_POST['submit'])) {?>
                    <div class="panel-footer">
                        <b>Iteration 1 (Calculating the initial frequency of itemsets:)</b>
                        <div class="table-responsive">
                            <table class="table table-bordered">>
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th width="50%">Frequency</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $frequency_item = frequencyItem($arr);
                                    foreach($frequency_item as $k => $v) {
                                    ?>
                                        <tr>
                                            <td><?= $k ?></td>
                                            <td><?= $v ?></td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <span>Elimination iteration 1 (Removing items that do not meet the minimum support threshold) So that it becomes:</span>
                        <div class="table-responsive">
                            <table class="table table-bordered">>
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th width="50%">Frequency</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $elimination_item = eliminationItem($frequency_item, $minSupport);
                                    foreach($elimination_item as $k => $v) {
                                    ?>
                                        <tr>
                                            <td><?= $k ?></td>
                                            <td><?= $v ?></td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                        $iteration = 2;
                        do {
                        ?>
                            <b>Iteration <?php echo $iteration; ?> (Calculating the initial frequency of itemsets):</b>
                            <div class="table-responsive">
                                <table class="table table-bordered">>
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th width="50%">Frequency</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $pair_item = pairItem($elimination_item);
                                        $frequency_pair_item = frequencyPairItem($pair_item, $arr);
                                        foreach($frequency_pair_item as $key => $val) {
                                            $ex = explode("_", $key);
                                            $item = "";
                                            $v = "";
                                            for ($k = 0; $k < count($ex); $k++) {
                                                if ($k !== count($ex) - 1) {
                                                    $item .= "," . $ex[$k];
                                                } else {
                                                    $v = $ex[$k];
                                                }
                                            }
                                            $association_rules[] = array("item" => substr($item, 1), "val" => $v, "sc" => $val);
                                        ?>
                                            <tr>
                                                <td><?= $key ?></td>
                                                <td><?= $val ?></td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <span>
                            Iterative elimination <?php echo $iteration; ?> (Discard items that do not meet the minimum support value) So that it becomes:
                            </span>
                            <div class="table-responsive">
                            <table class="table table-bordered">>
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th width="50%">Frequency</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $elimination_pair_item = eliminationItem($frequency_pair_item, $minSupport);
                                    foreach($elimination_pair_item as $k => $v) {
                                    ?>
                                        <tr>
                                            <td><?= $k ?></td>
                                            <td><?= $v ?></td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                            $iteration++;
                        } while ($elimination_pair_item == $frequency_pair_item);
                        ?>
                        <b>Since there are no more frequencies to be eliminated, the iteration is stopped.</b><br>
                        <b>Calculate support and confidence:</b><br>
                        <?php
                        for ($i = 0; $i < count($association_rules); $i++) {
                            $x = 0;
                            echo $i + 1 . " Confident value, ";
                            echo $association_rules[$i]['item']."=>". $association_rules[$i]['val'] . "=";
                            $ex = explode(",", $association_rules[$i]['item']);
                            for ($l = 0; $l < count($arr); $l++) {
                                $count = 0;
                                for ($k = 0; $k < count($ex); $k++) {
                                    for ($j = 0; $j <count($arr[$l]); $j++) {
                                        if ($arr[$l][$j] == $ex[$k]) {
                                            $count += 1;
                                        }
                                    }
                                }
                                if (count($ex) == $count) {
                                    $x += 1;
                                }
                            }
                            // Define minConfident = 40%
                            $minConfident = 40;
                            $confident = (floatval($association_rules[$i]["sc"])/floatval($x)) * 100;
                            // Gán key c = confident
                            $association_rules[$i]["c"] = number_format($confident, 2, ".", ";");
                            echo $association_rules[$i]["sc"] . "/" . $x . "=" . number_format(floatval($association_rules[$i]["sc"])
                             / floatval($x), 2, ".", ";") . "=" . number_format($confident, 0, ".", ";") . "%";
                            if ($confident < $minConfident){
                                echo " => Reject";
                            }
                            echo "<br>";
                        }
                        ?>
                        <b>Based on the Apriori algorithm, the obtained association rules are as follows:</b><br>
                        <?php
                        for ($i = 0; $i < count($association_rules); $i++) {
                            if ($association_rules[$i]["c"] >= $minConfident){
                                echo $i + 1 . ". if " . $association_rules[$i]['item'] . " so " 
                                . $association_rules[$i]['val'] . "<br>";
                            }
                        }
                        ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
</html>