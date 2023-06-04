<?php
class NaiveBayes{
	
	private $samples, $labels, $input, $labelsSum, $lenSampleSub;
	
	public function __construct(){
		$this->samples = array();
		$this->labels = array();
		$this->labelsSum = array();
	}
	
	public function train($samples, $labels){
		// All multidimensional array samples must have the same size
		$size = -1;
		foreach($samples as $S){
			if(count($S) != $size && $size != -1){
				echo "Error : Uneven SAMPLES size <br>";
				return null;
			}
			$size = count($S);
		}
		
		// SAMPLES and labels must have the same size
		if(count($samples) != count($labels)){
			echo "Error : Uneven SAMPLES and labels size <br>";
			return null;
		}
		
		// labels should not be multidimensional array
		if (count($labels) != count($labels, COUNT_RECURSIVE)){
			echo "Error : labels should not be multidimensional <br>";
			return null;
		}
		
		$this->samples = $samples;
		$this->labels = $labels;
		
		// Sum of labels
		foreach($this->labels as $L){
			if(!array_key_exists($L, $this->labelsSum)){
				$this->labelsSum[$L] = 0;
			}
			$this->labelsSum[$L]++;
		}
		echo "Sum of labels is " . count($labels) . ": ";
		foreach ($this->labelsSum as $key => $value) {
			echo $key . ": " . $value . " ";
		}
		echo "<br><br>";
		
		$sampleSampleProb = array();
		$lenSamples = count($this->samples);
		// Calculate the probabilities of samples
		$this->lenSampleSub = count($this->samples[0]);
		for($col=0; $col<$this->lenSampleSub; $col++){
			for($row=0; $row<$lenSamples; $row++){
				$samplesProb = $col + 1 . "_" . $this->samples[$row][$col];
				if(!isset($this->$samplesProb)){
					$this->$samplesProb = array_fill_keys(array_keys($this->labelsSum), 0);
					array_push($sampleSampleProb, $samplesProb);
				}
				$this->$samplesProb[$this->labels[$row]]++;
			}
		}
		
		foreach($sampleSampleProb as $samplesProb){
			echo "Column ". $samplesProb . "&emsp;";
			// print_r($this->$samplesProb);
			foreach ($this->$samplesProb as $key => $value) {
				echo $key . ": " . $value . " ";
			}
			echo "<br>";

			foreach($this->$samplesProb as $key => $value){
				$this->$samplesProb[$key] /= $this->labelsSum[$key];
			}
			
			// print_r($this->$samplesProb);
			echo "Probability: ";
			foreach ($this->$samplesProb as $key => $value) {
				echo $key . ": " . $value . "   ";
			}
			echo "<br><br>";
		}
	}
	
	public function predict($input){
		// input should not be multidimensional array
		if (count($input) != count($input, COUNT_RECURSIVE)){
			echo "Error : input should not be multidimensional array <br>";
			return null;
		}
		
		// SAMPLES and input must have the same size
		if (count($this->samples) != count($this->samples, COUNT_RECURSIVE)){
			if($this->lenSampleSub != count($input)){
				echo "Error : Uneven SAMPLES and input size <br>";
				return null;
			}
		}
		else{
			if(count($input) != 1){
				echo "Error : Invalid input size <br>";
				return null;
			}
		}
		
		$this->input = $input;
		$lenInput = count($this->input);
		$probList = array();
		foreach($this->labelsSum as $key => $value){
			$prob = 1;
			for($i=0; $i<$lenInput; $i++){
				(count($this->samples) != count($this->samples, COUNT_RECURSIVE)) ?	$samplesProb = $i . "_" . $this->input[$i] : $samplesProb = $this->input[$i];
				if(isset($this->$samplesProb)){
					$prob *= $this->$samplesProb[$key];
				}
			}
			$prob *= $this->labelsSum[$key] / array_sum($this->labelsSum);
			$probList[$key] = $prob;
		}
		$highestProb = array_search(max($probList), $probList);
		// print_r($probList);
		echo "<br>";
		echo "Probability: ";
		foreach ($probList as $key => $value) {
			echo $key . ": " . $value . "   ";
		}
		return $highestProb;
	}
}
?>

