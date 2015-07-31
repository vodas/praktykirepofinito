<?php

namespace app\charts;

include_once(dirname(__FILE__) . '/LabCharts.php');

class LabChartsBar extends LabCharts {
    
    public function __construct () {
        $this->_type = 'bvs';
        $this->_colors = 'D9351C|FAAC02|79D81C|2A9DF0|02AA9D';
        $this->_size = '300x150';
    }
    protected function dataToString ($data) {
        $str = 't:';
        $max = max($data);
        foreach ($data as $value) {
            $newValue = round($value / $max * 100, 3);
            $str .= $newValue . ',';
        }
        return substr($str, 0, -1);
    }
    public function setLabels ($labels) {
        $this->_labelsBar = $labels;
    }
    public function setBarStyles ($width, $spaceBar = 10) {
        $this->_stylesBar = $width . ',' . $spaceBar . ',10';
    }
    public function setAxis ($range) {
		$max = max($this->baseData);
		if (!$max)
			return;
		$newRange = round($range / $max *100, 3);
		$strLabels = '0:';
		$strPosition = '0';
		for ($a = 0; $a <= $max; $a += $range) {
			$strLabels .= '|' . $a;
		}
		for ($a = 0; $a <= 100; $a += $newRange) {
			$strPosition .= ',' . $a;
		}
		$this->_axis = 'y';
		$this->_axisRange = $strLabels;
		$this->_axisPosition = $strPosition;
	}
	public function setGrids ($range) {
		$max = max($this->baseData);
		if (!$max)
			return;
		$newRange = round($range / $max *100, 3);
		$this->_grids = '0,' . $newRange;
	}
    
}

?>
