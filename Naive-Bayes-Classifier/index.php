<?php

include('NaiveBayes.php');
/*
$SAMPLES = [[5, 1, 1], [1, 5, 1], [1, 2, 5]];
$LABELS = ['a', 'b', 'c'];

$TEST = [3, 1, 1];
*/
/*
$SAMPLES = [0, 1, 2, 1, 1];
$LABELS = ['Good', 'Bad', 'Good', 'Bad', 'Good'];
$TEST = [1];
*/

// $SAMPLES = [[0, 0, 0], [0, 1, 1], [1, 1, 0], [1, 0, 1], [1, 1, 1]];
// $LABELS = ['Good', 'Bad', 'Good', 'Bad', 'Good'];
// $TEST = [0, 0, 1];


function read_data_file($filename) {
    $SAMPLES = array();
    $LABELS = array();
    $file = fopen($filename, "r");
    if ($file) {
        while (($line = fgets($file)) !== false) {
            $line = trim($line);
            $data = explode(",", $line);
            $label = array_pop($data);
            array_push($SAMPLES, $data);
            array_push($LABELS, $label);
        }
        fclose($file);
    }
    return array($SAMPLES, $LABELS);
}

function read_test_file($filename) {
	$file = fopen($filename, "r");
	if ($file) {
		$data = explode(",", fgets($file));
		fclose($file);
	}
	return $data;
}


// $SAMPLES = [['Sunny', 'Hot', 'High', 'Weak'], ['Sunny', 'Hot', 'High', 'Strong'], ['Overcast', 'Hot', 'High', 'Weak']
// 			, ['Rain', 'Mild', 'High', 'Weak'], ['Rain', 'Cool', 'Normal', 'Weak'], ['Rain', 'Cool', 'Normal', 'Strong']
// 			, ['Overcast', 'Cool', 'Normal', 'Strong'], ['Sunny', 'Mild', 'High', 'Weak'], ['Sunny', 'Cool', 'Normal', 'Weak']
// 			, ['Rain', 'Mild', 'Normal', 'Weak'], ['Sunny', 'Mild', 'Normal', 'Strong'], ['Overcast', 'Mild', 'High', 'Strong']
// 			, ['Overcast', 'Hot', 'Normal', 'Weak'], ['Rain', 'Mild', 'High', 'Strong']];
// $LABELS = ['No', 'No', 'Yes', 'Yes', 'Yes', 'No', 'Yes', 'No', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'No'];

// $TEST = ['Sunny', 'Cool', 'High', 'Strong'];

$filename = "dataBayes.txt";

// Gọi hàm để đọc dữ liệu từ file và chuyển đổi thành mảng
$data = read_data_file($filename);

// Lấy kết quả trả về
$SAMPLES = $data[0];
$LABELS = $data[1];

$testfile = "testBayes.txt";
$data_test = read_test_file($testfile);
$TEST = $data_test;

$NB = new NaiveBayes ();
$NB->train($SAMPLES, $LABELS);
$PREDICTED = $NB->predict($TEST);
echo "<br>";
echo "Result: " . $PREDICTED;
?>