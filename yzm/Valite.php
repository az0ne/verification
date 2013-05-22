<?php

define('WORD_WIDTH',9);
define('WORD_HIGHT',13);
define('OFFSET_X',7);
define('OFFSET_Y',3);
define('WORD_SPACING',4);
define('GRAIN_MAX_VALUE',1);

class valite {
	protected $ImagePath;
	protected $DataArray;
	protected $ImageSize;
	protected $data;
	protected $Keys;
	protected $NumStringArray;
	protected $RGBarray;
	protected $dirx = array(0, 0, -1, 1, 1, 1, -1, -1);
	protected $diry = array(1, -1, 0, 0, -1, 1, -1, 1);
	
	
	public function setImage($Image)
	{
		$this->ImagePath = $Image;
	}
	public function getData()
	{
		return $this->data;
	}
	public function getResult()
	{
		return $this->DataArray;
	}
	public function getSize() {
		return $this->ImageSize;
	}
	
	public function in($x, $y) {
		return 0 <= $x && 0 <= $y && $x < $this->ImageSize[1] && $y < $this->ImageSize[0]; 
	}
	 
	public function getGrainValue($x, $y) {
		$res = 0;
		for ($i = 0; $i < 8; $i++) {
			$nx = $x + $this->dirx[$i];
			$ny = $y + $this->diry[$i];
			if ($this->in($nx, $ny) == true) {
				
			 	if ($this->DataArray[$nx][$ny] == 1)
					$res++;
			}
		}
		return $res;
	}
	
	public function getSquareValue($xs, $ys, $xe, $ye) {
		$res = 0;
		for ($i = $xs; $i <= $xe; $i++) 
			for ($j = $ys; $j <= $ye; $j++) 
				if ($this->DataArray[$i][$j] == 1)
					$res++;
		return $res;
	}
	
	public function removeGrain($times) {
	 	//$times = GRAIN_MAX_VALUE;
	 	while ($times != 0) {
			for ($i = 0; $i < $this->ImageSize[1]; $i++) 
				for ($j = 0; $j < $this->ImageSize[0]; $j++) 
					if ($this->DataArray[$i][$j] == 1) {
						$tmp = $this->getGrainValue($i, $j);
						if ($tmp <= $times)
							$this->DataArray[$i][$j] = 0;
						}
			$times--;
	 	}
	}
	
	public function dfs($x, $y,& $color, & $up, & $down, & $left, & $right, $num) {
		$left = min($left, $y);
		$right = max($right, $y);
		$up = min($up, $x);
		$down = max($down, $x);
		$color[$x][$y] = $num;
		for ($i = 0; $i < 8; $i++) {
			$nx = $x + $this->dirx[$i];
			$ny = $y + $this->diry[$i];
			if ($this->in($nx, $ny) == true) {
				//if ($nx == 8) 
					//echo $nx . " " . $ny . " " . $color[$nx] . "\n" ;
				if ($this->DataArray[$nx][$ny] != 0 && $color[$nx][$ny] == 0) {
					$this->dfs($nx, $ny, $color, $up, $down, $left, $right, $num);
				}
			}
		}
	}
	
	public function getHec() {
		$res = imagecreatefromjpeg($this->ImagePath);
		$size = getimagesize($this->ImagePath);
		$data = array();
		for($i=0; $i < $size[1]; ++$i)
		{
			for($j=0; $j < $size[0]; ++$j)
			{
				$rgb = imagecolorat($res,$j,$i);
				$rgbarray = imagecolorsforindex($res, $rgb);
				if($rgbarray['red'] > 130 && ( $rgbarray['green']<50
						|| $rgbarray['blue'] < 50)){
					//$data[$i][$j]=$rgbarray['red'];// * 10000 + $rgbarray['green'] * 100 + $rgbarray['blue'];
					$data[$i][$j] = 1;
				}else{
					$data[$i][$j] = 0;
				}
			}
		}
		$this->DataArray = $data;
		$this->ImageSize = $size;
		//$this->Draw();
		//echo "========================\n";
	}
	
	public function getBlock($color, $up, $down, $left, $right) {
		$res = array();
		$str = "";
		$n = $down - $up + 1;
		$m = $right - $left + 1;
		$sum = $n * $m;
		$cnt = 0;
		for ($i = 0; $i < $n; $i++) 
			for ($j = 0; $j < $m; $j++){
				if ($this->DataArray[$i + $up][$j + $left] == 1)
					$cnt++;
				$str .=  $this->DataArray[$i + $up][$j + $left];
			}
		$res['n'] = $n;
		$res['m'] = $m;
		$res['percent'] = 100.0 * $cnt / $sum;
		$res['data'] = $str;
		$res['pixelNum'] = $cnt;
		return $res;
	}
	public function gao() {
	    $flag = 1;
		$grainValue = GRAIN_MAX_VALUE;
		
		while ($grainValue <= 3 && $flag == 1) {
			$this->getHec();
			$this->removeGrain($grainValue);
			$res = array();
			$color = array();
			$up = array(); $down = array(); $left = array(); $right = array();
			$block = array();
			$num = 0;
			$size = $this->ImageSize;
		
			for ($i = 0; $i < $size[1]; $i++) {
				$color[$i] = array();
				for ($j = 0; $j < $size[0]; $j++) {
					$color[$i][$j] = 0;
				}
			//echo $i . "\n";
			}
		//$this->Draw();
		//echo "==============\n";
			for ($j = 0; $j < $size[0]; $j++) {
				for ($i = 0; $i < $size[1]; $i++) {
					//echo $i . " " . $j . "\n";
					if ($color[$i][$j] == 0 && $this->DataArray[$i][$j] == 1) {
					//echo $i . " " . $j . "\n";
						$num++;
						$up[$num] = $down[$num] = $i;
						$left[$num] = $right[$num] = $j;
						$this->dfs($i, $j, $color, $up[$num], $down[$num], $left[$num], $right[$num], $num);
						$block[$num] = $this->getBlock($color, $up[$num], $down[$num], $left[$num], $right[$num]);
						while ($block[$num]['m'] > 20 && $grainValue > 2) {
							$num++;
							$up[$num] = $up[$num - 1];
							$down[$num] = $down[$num - 1];
							$left[$num] = $left[$num - 1] + 16;
							$right[$num] = $right[$num - 1];
							$block[$num] = $this->getBlock($color, $up[$num], $down[$num], $left[$num], $right[$num]);
							//echo $block[$num]['m'] .  "\n";
						}
					}
				}
			}	
			//echo $num . "\n";
			//$str = "";
			//for ($i = 1; $i <= $num; $i++) {
			//echo $block[$i]['n'] . " " . $block[$i]['m'] . "\n" . $block[$i]['percent'] . "\n";
			//}
			if (count($block) == 4)
				$flag = 0;
			else $grainValue++;
			//echo $grainValue . " " . count($block) . "\n";
			//$this->Draw();
		}
		return $block;
	}
	
	public function Draw()
	{
		for($i=0; $i<$this->ImageSize[1]; ++$i)
		{
	        for($j=0; $j<$this->ImageSize[0]; ++$j)
		    {
			    echo $this->DataArray[$i][$j];
	        }
		    echo "\n";
		}
	}
	
	public function DrawFlie() {
		$file = fopen("test.txt","w");
		$res = "";
		for($i=0; $i<$this->ImageSize[1]; ++$i) {
	        for($j=0; $j<$this->ImageSize[0]; ++$j) {
			    $res = $res . $this->DataArray[$i][$j] . " ";
	        }
		    $res = $res. "\n";
		}
		fwrite($file,$res);
		fclose($file);
	}
	
	
	public function __construct() {
		
	}
}
?>