<?php

define('WORD_WIDTH',8);
define('WORD_HIGHT',10);
define('OFFSET_X',15);
define('OFFSET_Y',10);
define('WORD_SPACING',9);

class valite
{
	public function setImage($Image)
	{
		$this->ImagePath = $Image;
	}
	public function getData()
	{
		return $data;
	}
	public function getResult()
	{
		return $DataArray;
	}
	public function getHec()
	{
		//$res = imagecreatefromjpeg($this->ImagePath);
		$res = imagecreatefrompng($this->ImagePath);
		$size = getimagesize($this->ImagePath);

		$data = array();
		$colorMap = array();

		for($i=0;$i<4;++$i)
		{
			$x = ($i*(WORD_WIDTH+WORD_SPACING))+OFFSET_X;
			$y = OFFSET_Y;
			for($h = $y; $h < (OFFSET_Y+WORD_HIGHT); ++ $h)
			{
				for($w = $x; $w < ($x+WORD_WIDTH); ++$w)
				{

					$rgb = imagecolorat($res,$w,$h);

					$rgbarray = imagecolorsforindex($res, $rgb);
					$key = $rgbarray['red'] .'_' . $rgbarray['green'] .'_' . $rgbarray['blue']  ;
					if(isset($colorMap[$key])){
						$colorMap[$key] ++ ;
					}else{
						$colorMap[$key] = 0;
					}
				}
			}

		}
		arsort($colorMap);

		foreach($colorMap as $k => $v){
			$temp = explode('_',$k);
			if($temp[0] > 200 || $temp[1] > 200 || $temp[2] > 200){
				continue;
			}
			$fontColor = $k;
			break;
		}
		$fontColor = explode('_',$fontColor);



		for($i=0; $i < $size[1]; ++$i) 		//宽度像素位
		{
			for($j=0; $j < $size[0]; ++$j)  //长度像素位
			{
				$rgb = imagecolorat($res,$j,$i);

				$rgbarray = imagecolorsforindex($res, $rgb);

				if($rgbarray['red'] ==  $fontColor[0] && $rgbarray['green'] == $fontColor[1]
				&& $rgbarray['blue'] == $fontColor[2])
				{
					$data[$i][$j]=1;
				}else{
					$data[$i][$j]=0;
				}
			}
		}


		for($i=0; $i < $size[1]; ++$i)
		{
			for($j=0; $j < $size[0]; ++$j)
			{
				$num = 0;
				if($data[$i][$j] == 1)
				{
					// 上
					if(isset($data[$i-1][$j])){
						$num = $num + $data[$i-1][$j];
					}
					// 下
					if(isset($data[$i+1][$j])){
						$num = $num + $data[$i+1][$j];
					}
					// 左
					if(isset($data[$i][$j-1])){
						$num = $num + $data[$i][$j-1];
					}
					// 右
					if(isset($data[$i][$j+1])){
						$num = $num + $data[$i][$j+1];
					}
					// 上左
					if(isset($data[$i-1][$j-1])){
						$num = $num + $data[$i-1][$j-1];
					}
					// 上右
					if(isset($data[$i-1][$j+1])){
						$num = $num + $data[$i-1][$j+1];
					}
					// 下左
					if(isset($data[$i+1][$j-1])){
						$num = $num + $data[$i+1][$j-1];
					}
					// 下右
					if(isset($data[$i+1][$j+1])){
						$num = $num + $data[$i+1][$j+1];
					}
				}
				if($num == 0){
					$data[$i][$j] = 0;
				}
			}
		}


		$this->DataArray = $data;
		$this->ImageSize = $size;
	}
	public function run()
	{
		$result="";
		// 做成字符串
		$data = array("","","","");
		for($i=0;$i<4;++$i)
		{
			$x = ($i*(WORD_WIDTH+WORD_SPACING))+OFFSET_X;
			$y = OFFSET_Y;
			for($h = $y; $h < (OFFSET_Y+WORD_HIGHT); ++ $h)
			{
				for($w = $x; $w < ($x+WORD_WIDTH); ++$w)
				{
					$data[$i].=$this->DataArray[$h][$w];
//					echo $this->DataArray[$h][$w] == 1 ? "<span style='color: red;'>1</span>" : 0 ;
//					echo " ";
				}
//				 echo "<br />";
			}
			
		}


		// 进行关键字匹配
		foreach($data as $numKey => $numString)
		{
			$max=0.0;
			$num = 0;
			foreach($this->Keys as $key => $value)
			{
				$percent=0.0;
				similar_text($value, $numString,$percent);
				if(intval($percent) > $max)
				{
					$max = $percent;
					$num = $key;
					if(intval($percent) > 95)
						break;
				}
			}
			$result.=$num;
		}
//		$this->Draw();
//var_dump($data);
		$this->data = $result;
		// 查找最佳匹配数字
		return $result;
	}

	public function Draw()
	{
		for($i=0; $i<$this->ImageSize[1]; ++$i)
		{
	        for($j=0; $j<$this->ImageSize[0]; ++$j)
		    {
			    echo $this->DataArray[$i][$j] = $this->DataArray[$i][$j] == 1 ?  1 : '<span style="visibility: hidden">0</span>';
	        }
		    echo "\n";
		}
	}
	public function __construct()
	{
        //匹配字库
		$this->Keys = array(
			'0' => '00011000001111000110011011000011110000111100001111000011011001100011110000011000',
			'1' => '00011000001110000111100000011000000110000001100000011000000110000001100001111110',
			'2' => '00011110001100110110000100000001000000110000011000001101000110000011000001111111',
			'3' => '00111110011000110000000100000011000011100000001100000001000000010110001100111110',
			'4' => '00000110000011100001111000110110011001101100011011111111000001100000011000000110',
			'5' => '01111111011000000110000001101110011100110000000100000001011000010011001100011110',
			'6' => '00011110001100110110000101100000011011100111001101100001011000010011001100011110',
			'7' => '11111111000000110000001100000110000011000001100000110000011000001100000011000000',
			//'8' => '00111101011001101100001101100110001111000110011011100111111010110110111000111100',
			'8' => '00011110001100110110000100110011000111100011001101100001011000010011001100011110',
			'9' => '00111100011001101100001111000011011001110011101100000011010000110110011000111100',
	);
	}
	protected $ImagePath;
	protected $DataArray;
	protected $ImageSize;
	protected $data;
	protected $Keys;
	protected $NumStringArray;

}
?>