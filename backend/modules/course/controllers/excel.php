<?php
/********************************************************************
*********************************************************************
** --------------------------- PHPExcel -------------------------- **
*********************************************************************
*********************************************************************/
error_reporting(E_ALL);
/********************************************************************/
ini_set('display_errors', FALSE);
ini_set('display_startup_errors', FALSE);
date_default_timezone_set('Europe/London');
/********************************************************************/
define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
error_reporting(0);
$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
require_once($parse_uri[0] . 'wp-load.php');
/**************************************************************************/
global $wpdb;
/**************************************************************************/
$site_id = $_REQUEST['site_id'];

if( !$site_id ) {
	$site_id = 1;
};
switch_to_blog( $site_id );
/**************************************************************************/
$save_folder_path = $parse_uri[0].'wp-content/uploads/reports';
/**************************************************************************/
if ( file_exists( $save_folder_path.'/error_log' ) ) {
	unlink( $save_folder_path.'/error_log' );
};
/**************************************************************************/

/** Include PHPExcel */
require_once AL_CPP_EXTN_PATH . 'includes/plugins/PhpExcel/PHPExcel.php';

/**************************************************************************/
if( isset( $_REQUEST['cat_id'] ) ) {
	$shortcode = '[cpp_category_reports_array category_ids="'.$_REQUEST['cat_id'].'"]';
	$cpp_reports_array = maybe_unserialize( do_shortcode( $shortcode ) );
} else {
	$cpp_reports_array = maybe_unserialize(do_shortcode('[cpp_category_reports_array]'));
}
restore_current_blog();
$searched_by_user  = $cpp_reports_array['cats'][0]['name'];
/*
$searched_by_user  = ' Service Excellence, SDP, Enfield ';
*/
/**************************************************************************/
$RedColor	= 'c10001';
$GrayColor	= '81889a';
$AmberColor	= 'ffc000';
$GreenColor	= '008000';
/**************************************************************************/
/* echo '<pre>';
print_r($cpp_reports_array);
echo '</pre>'; */
/**************************************************************************/
// Create new PHPExcel object
/* echo date('H:i:s'), " Create new PHPExcel object", EOL; */
$objPHPExcel = new PHPExcel();

// Set document properties
/* echo date('H:i:s'), " Set document properties", EOL; */
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")->setLastModifiedBy("Maarten Balliauw")->setTitle("PHPExcel Test Document")->setSubject("PHPExcel Test Document")->setDescription("Test document for PHPExcel, generated using PHP classes.")->setKeywords("office PHPExcel php")->setCategory("Test result file");

/* $styleArray = array(
'font'  => array(
'bold'  => true,
'color' => array('rgb' => 'FF0000'),
'size'  => 15,
'name'  => 'Verdana'
)); */

// Set title for Excel
//$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(1);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(1);
$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(40);
$objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(40);
$objPHPExcel->getActiveSheet()->getRowDimension('6')->setRowHeight(35);
$objPHPExcel->getActiveSheet()->getRowDimension('7')->setRowHeight(30);
//$objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(50);

//$objPHPExcel->getActiveSheet()->setCellValue('I1', "The C.A.A.R\nThe Capability & Awareness Register");
$objPHPExcel->getActiveSheet()->getStyle('I1')->getAlignment()->setWrapText(true);


$sheet = $objPHPExcel->getActiveSheet();
$style = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$sty = array(
    'alignment' => array(
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP
    )
);
$sheet->getStyle("I1:M1")->applyFromArray($style);
$objPHPExcel->getActiveSheet()->getColumnDimension('I1')->setAutoSize(true);

$objPHPExcel->getActiveSheet()->getStyle('I1')->getAlignment()->setWrapText(true);
$objPHPExcel->setActiveSheetIndex(0)->mergeCells('I1:M2');

//Image in title
$objDrawing = new PHPExcel_Worksheet_Drawing();
$objDrawing->setName('Logo');
$objDrawing->setDescription('Logo');
/****************************************************************************/
switch_to_blog(1);
$upload_dir = wp_upload_dir();
restore_current_blog();
/****************************************************************************/
switch_to_blog( $site_id );
$site_right_client_img = get_option( 'site_right_client_img' );
$site_right_client_img = explode("/uploads/", $site_right_client_img);
$site_right_client_img = $upload_dir['basedir'].'/'.$site_right_client_img[1];
/****************************************************************************/
restore_current_blog();
switch_to_blog($site_id);
$site_left_client_img  = get_option( 'site_left_client_img' );
$site_left_client_img = explode("/uploads/", $site_left_client_img);
$site_left_client_img = $upload_dir['basedir'].'/'.$site_left_client_img[1];
restore_current_blog();
/****************************************************************************/

/****************************************************************************/
if( !$site_left_client_img ) {
	$site_left_client_img = AL_CPP_EXTN_PATH . 'assets/img/PhpExcel/officelogo-left.jpg'; 
};
if( !$site_right_client_img ) {
	$site_right_client_img = AL_CPP_EXTN_PATH . 'assets/img/PhpExcel/officelogo-right.jpg'; 
};
if (file_exists($site_right_client_img)) {
	$objDrawing->setPath($site_right_client_img); //setOffsetY has no effect
};
$objDrawing->setCoordinates('D1');
$objDrawing->setHeight(60); // logo height
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

$objDrawing = new PHPExcel_Worksheet_Drawing();
$objDrawing->setName('Logo');
$objDrawing->setDescription('Logo');
$logo = AL_CPP_EXTN_PATH . 'assets/img/PhpExcel/officelogo.jpg'; // Provide path to your logo file
if (file_exists($site_left_client_img)) {
	$objDrawing->setPath($site_left_client_img); //setOffsetY has no effect
};
$objDrawing->setCoordinates('O1');
$objDrawing->setHeight(60); // logo height
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());


//Display Category Name
$categoryname = $cpp_reports_array['cats'][0]['name'];
$sheet->getStyle("E3:I3")->applyFromArray($sty);
$objPHPExcel->setActiveSheetIndex(0)->mergeCells("E3:I3");
$objPHPExcel->getActiveSheet()->setCellValue('E3', "Program:" . $categoryname);
$objPHPExcel->getActiveSheet()->getStyle('E3')->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle("E3")->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("E3")->getFont()->setSize(10);

$objRichText = new PHPExcel_RichText();

$objBold = $objRichText->createTextRun('This report searched by : ');
$objBold->getFont()->setBold(true);

$objRichText->createText($searched_by_user);

$objPHPExcel->getActiveSheet()->getCell('E4')->setValue($objRichText);
$objPHPExcel->getActiveSheet()->getStyle('E4')->getAlignment()->setWrapText(false);
$objPHPExcel->getActiveSheet()->getStyle("E4")->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->getStyle('A6:AZ8')->getFont()->setSize(10);
//Color Coding
function cellColor($cells, $color) {
    global $objPHPExcel;
    
    $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
            'rgb' => $color
        )
    ));
}
/* cellColor('E5', $GreenColor);
cellColor('H5', $AmberColor);
cellColor('L5', $RedColor);
cellColor('O5', $GrayColor); */

$styleArray = array(
    'font' => array(
        'color' => array(
            'rgb' => '6d6d6d'
        ),
        'size' => 8
    )
);

/*
$objPHPExcel->getActiveSheet()->getCell('F5')->setValue('Green : Complete');
$objPHPExcel->getActiveSheet()->getStyle('F5')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getCell('I5')->setValue('Amber : In Progress');
$objPHPExcel->getActiveSheet()->getStyle('I5')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getCell('M5')->setValue('Red : Not Started');
$objPHPExcel->getActiveSheet()->getStyle('M5')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getCell('P5')->setValue('Grey : Not Applicable');
$objPHPExcel->getActiveSheet()->getStyle('P5')->applyFromArray($styleArray);
*/
/* $objPHPExcel->getActiveSheet()->getCell('F5')->setValue('Complete');
$objPHPExcel->getActiveSheet()->getStyle('F5')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getCell('I5')->setValue('In Progress');
$objPHPExcel->getActiveSheet()->getStyle('I5')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getCell('M5')->setValue('Not Started');
$objPHPExcel->getActiveSheet()->getStyle('M5')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getCell('P5')->setValue('Not Applicable');
$objPHPExcel->getActiveSheet()->getStyle('P5')->applyFromArray($styleArray); */


//Student Loop
$names = $cpp_reports_array['All_Students_Details'];

//$objPHPExcel->getActiveSheet()->fromArray($names, null, 'A9');
$worksheet = $objPHPExcel->getActiveSheet();
$student   = 8;
$mark      = 9;
foreach ($names as $row => $columns) {
    $columns = (array) $columns;
	switch_to_blog( $site_id );
	$cat_student_per_details = do_shortcode( "[cpp_overall_category_progress cat_id='".$_REQUEST['cat_id']."' user_id='".$columns['ID']."' ]" );
	$cat_student_per_details = maybe_unserialize( $cat_student_per_details );
	restore_current_blog();
	$columns['capability_percentage'] = $cat_student_per_details['result']['Percentage'];
	$capability_percentage = $columns['capability_percentage'];
	if( ( $capability_percentage < 100 ) && ( $capability_percentage > 95 ) ) {
		$split_color_val = 9;
	} else {
		$split_color_val = round ( ( $capability_percentage / 10 ), 0 );
	}
	if( get_current_blog_id() != 1 ) {
		/************************************************************************/
		switch_to_blog(1);
		/************************************************************************/
		$AL_CPP_EXTN_URL = plugin_dir_path( __FILE__ ).'range-images/ ';
		/************************************************************************/
		restore_current_blog();
		/************************************************************************/
	} else {
		/************************************************************************/
		$AL_CPP_EXTN_URL = plugin_dir_path( __FILE__ ).'range-images/ ';
		/************************************************************************/
	}
	$AL_CPP_EXTN_URL = str_replace('/', '\\', $AL_CPP_EXTN_URL);
	$AL_CPP_EXTN_URL = str_replace(' ', '', $AL_CPP_EXTN_URL);
	switch ($split_color_val) {
		case 1: 
			$logo = $AL_CPP_EXTN_URL . 'range1.png';
			break;
		case 2:
			$logo = $AL_CPP_EXTN_URL . 'range2.png';
			break;
		case 3:
			$logo = $AL_CPP_EXTN_URL . 'range3.png';
			break;
		case 4: 
			$logo = $AL_CPP_EXTN_URL . 'range4.png';
			break;
		case 5:
			$logo = $AL_CPP_EXTN_URL . 'range5.png';
			break;
		case 6:
			$logo = $AL_CPP_EXTN_URL . 'range6.png';
			break;
		case 7:
			$logo = $AL_CPP_EXTN_URL . 'range7.png';
			break;
		case 8:
			$logo = $AL_CPP_EXTN_URL . 'range8.png';
			break;
		case 9:
			$logo = $AL_CPP_EXTN_URL . 'range9.png';
			break;
		case 10:
			$logo = $AL_CPP_EXTN_URL . 'range1.png';
			break;
		default:
			$logo = $AL_CPP_EXTN_URL . 'range0.png';
	}
    $student += 1;
    $mark += 1;
    $merging = $row + $student;
    $perce   = $row + $mark;
	$objPHPExcel->getActiveSheet()->getStyle('A' . $merging . ':C' . $merging)->getFont()->setSize(10);
	$objPHPExcel->getActiveSheet()->getStyle('A' . $perce . ':C' . $perce)->getFont()->setSize(10);
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $merging . ':C' . $merging);
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $perce . ':C' . $perce);
    $worksheet->setCellValueByColumnAndRow('A9', $row + $student, $columns['display_name']);
    $worksheet->setCellValueByColumnAndRow('A10', $row + $mark, $capability_percentage);
    $sheet = $objPHPExcel->getActiveSheet();
    $style = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
        )
    );
    $sheet->getStyle("A" . $merging)->applyFromArray($style);
    $sheet->getStyle("A" . $mark)->applyFromArray($style);
    
    $objDrawing = new PHPExcel_Worksheet_Drawing();
    $objDrawing->setName('Logo');
    $objDrawing->setDescription('Logo');
	if (file_exists($logo)) {
		$objDrawing->setPath($logo); //setOffsetY has no effect
	};
    $objDrawing->setCoordinates('A' . $perce);
    $objDrawing->setHeight(25); // logo height
    $objDrawing->setWidth(155); // logo height
    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
}

//Table Loop
$cate = $cpp_reports_array['cats'][0]['courses'];

/* foreach($cate as $row => $columns) {
$objPHPExcel->setActiveSheetIndex(0)->mergeCells('E'.$merging.':J'.$merging);

} */

function cellsToMergeByColsRow($start = -1, $end = -1, $row = -1) {
    $merge = 'A1:A1';
    if ($start >= 0 && $end >= 0 && $row >= 0) {
        $start = PHPExcel_Cell::stringFromColumnIndex($start);
        $end   = PHPExcel_Cell::stringFromColumnIndex($end);
        $merge = "$start{$row}:$end{$row}";
    }
    return $merge;
}
function cellsToWidthByColsRow($start = -1, $row = -1) {
    $width = 'A';
    if ($start >= 0 && $row == -1) {
        $start = PHPExcel_Cell::stringFromColumnIndex($start);
        $width = "$start";
    }
    if ($start >= 0 && $row != -1) {
        $start = PHPExcel_Cell::stringFromColumnIndex($start);
        $width = "$start{$row}";
    }
    return $width;
}

$starting  = 4;
$ending    = 9;
$rownumber = 6;
$cat_count = count($cate);
if( !$cat_count ) {
	goto courses_not_present;
};
$max_length = 30;
for ($i = 0; $i < $cat_count; $i++) {
    
    $unit_count = count($cate[$i]['units']);
    if($unit_count){    
    $unit_all   = $cate[$i]['units'];
    $ending     = ($unit_count * 2) + $starting - 1;
    $objPHPExcel->getActiveSheet()->mergeCells(cellsToMergeByColsRow($starting, $ending, $rownumber));
	$objPHPExcel->getActiveSheet()->getStyle(cellsToWidthByColsRow($starting, $rownumber))->getAlignment()->setWrapText(true);//
    $worksheet->setCellValueByColumnAndRow($starting, $rownumber, $cate[$i]['course_name']);
    
    $sheet = $objPHPExcel->getActiveSheet();
    $style = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ),
		'font'  => array(
			'bold'  => true,
			'color' => array('rgb' => 'FFFFFF'),
		)
	);
    cellColor(cellsToWidthByColsRow($starting, $rownumber), '0070c0');
    $sheet->getStyle(cellsToWidthByColsRow($starting, $rownumber))->applyFromArray($style);
    
    $objPHPExcel->getActiveSheet()->getColumnDimension(cellsToWidthByColsRow($ending + 1))->setWidth(1);
    $unittitle = $starting;
    foreach ($unit_all as $unitname) {
		/***********************************************/
		$StudentUnitFillColumn	= $unittitle;
		$StudentUnitFillRow		= $rownumber + 1;
		/***********************************************/
        $objPHPExcel->getActiveSheet()->mergeCells(cellsToMergeByColsRow($unittitle, $unittitle + 1, $rownumber + 1));
        $worksheet->setCellValueByColumnAndRow($unittitle, $rownumber + 1, $unitname['name']);
		$new_length = strlen($unitname['name']);
		if($new_length > $max_length){
			$max_length = $new_length;
		}
		$objPHPExcel->getActiveSheet()->getStyle(cellsToWidthByColsRow($unittitle, $rownumber + 1))->getAlignment()->setWrapText(true);
        $worksheet->setCellValueByColumnAndRow($unittitle, $rownumber + 2, 'Aware');
        $worksheet->setCellValueByColumnAndRow($unittitle + 1, $rownumber + 2, 'Capable');
        cellColor(cellsToWidthByColsRow($unittitle, $rownumber + 1), 'b7dee8');
        cellColor(cellsToWidthByColsRow($unittitle, $rownumber + 2), 'dce6f1');
        cellColor(cellsToWidthByColsRow($unittitle + 1, $rownumber + 2), 'dce6f1');
		
		foreach( $unitname['students'] as $single_student ) {
			/********************************************************************************/
			if( $single_student['aware'] == 'green' ) {
				$AwareColor = $GreenColor;
			} else if( $single_student['aware'] == 'red' ) {
				$AwareColor = $RedColor;
			} else if( $single_student['aware'] == 'amber' ) {
				$AwareColor = $AmberColor;
			} else {
				$AwareColor = $GrayColor;
			}
			if( $single_student['capable'] == 'green' ) {
				$CapableColor = $GreenColor;
			} else if( $single_student['capable'] == 'red' ) {
				$CapableColor = $RedColor;
			} else if( $single_student['capable'] == 'amber' ) {
				$CapableColor = $AmberColor;
			} else {
				$CapableColor = $GrayColor;
			}
			/********************************************************************************/
			$StudentUnitFillRow = $StudentUnitFillRow + 2;
			/************************************* AWARE ************************************/
			cellColor( cellsToWidthByColsRow( $StudentUnitFillColumn, $StudentUnitFillRow ), $AwareColor );
			cellColor( cellsToWidthByColsRow( $StudentUnitFillColumn, $StudentUnitFillRow + 1 ), $AwareColor );
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells(cellsToWidthByColsRow( $StudentUnitFillColumn, $StudentUnitFillRow ).':'.cellsToWidthByColsRow( $StudentUnitFillColumn, $StudentUnitFillRow +1 ));
			/************************************* AWARE ************************************/
			
			/************************************ CAPABLE ***********************************/
			cellColor( cellsToWidthByColsRow( $StudentUnitFillColumn + 1, $StudentUnitFillRow ), $CapableColor );
			cellColor( cellsToWidthByColsRow( $StudentUnitFillColumn + 1, $StudentUnitFillRow + 1 ), $CapableColor );
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells(cellsToWidthByColsRow( $StudentUnitFillColumn + 1, $StudentUnitFillRow ).':'.cellsToWidthByColsRow( $StudentUnitFillColumn + 1, $StudentUnitFillRow + 1 ));
			/************************************ CAPABLE ***********************************/
			$FinalRowRun 	= $StudentUnitFillRow + 1;
			$FinalColumnRun = $StudentUnitFillColumn + 1;
		}
		
        $sheet = $objPHPExcel->getActiveSheet();
        $style = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $sheet->getStyle(cellsToWidthByColsRow($unittitle, $rownumber + 1))->applyFromArray($style);
        $sheet->getStyle(cellsToWidthByColsRow($unittitle, $rownumber + 2))->applyFromArray($style);
        $sheet->getStyle(cellsToWidthByColsRow($unittitle + 1, $rownumber + 2))->applyFromArray($style);
        $unittitle += 2;
    }
    $starting = $ending + 2;
    //$ending += 7;
    }
    
}
$objPHPExcel->getActiveSheet()->getRowDimension('7')->setRowHeight($max_length);
//Border color
$border_style = array(
    'borders' => array(
        'left' => array(
            'style' => PHPExcel_Style_Border::BORDER_THICK,
            'color' => array(
                'rgb' => 'ffffff'
            )
        ),
        'right' => array(
            'style' => PHPExcel_Style_Border::BORDER_THICK,
            'color' => array(
                'rgb' => 'ffffff'
            )
        ),
        'top' => array(
            'style' => PHPExcel_Style_Border::BORDER_THICK,
            'color' => array(
                'rgb' => 'ffffff'
            )
        ),
        'bottom' => array(
            'style' => PHPExcel_Style_Border::BORDER_THICK,
            'color' => array(
                'rgb' => 'ffffff'
            )
        ),
    )
);
$sheet        = $objPHPExcel->getActiveSheet();

/*******************************************************************/
/* ------------- Add Border To Whole Tests Columns --------------- */
/*******************************************************************/
$WholeTestLastBlock = cellsToWidthByColsRow( $FinalColumnRun, $FinalRowRun );
$styleArray = array(
	'borders' => array(
		'allborders' => array(
			'style' => PHPExcel_Style_Border::BORDER_THICK,
			'color' => array('rgb' => 'ffffff'),
		),
	),
);
$sheet->getStyle( 'E8:'.$WholeTestLastBlock )->applyFromArray($styleArray);
$sheet->getStyle( 'E7:'.$WholeTestLastBlock )->applyFromArray($styleArray);
/* $sheet->getStyle('A1:'.$WholeTestLastBlock)->getAlignment()->setIndent(4); */
/*******************************************************************/

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Save Excel 2007 file
$callStartTime = microtime(true);

$objWriter   = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
$callEndTime = microtime(true);
$callTime    = $callEndTime - $callStartTime;

/* echo date('H:i:s'), " File written to ", str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME)), EOL;
echo 'Call time to write Workbook was ', sprintf('%.4f', $callTime), " seconds", EOL;
// Echo memory usage
echo date('H:i:s'), ' Current memory usage: ', (memory_get_usage(true) / 1024 / 1024), " MB", EOL; */


/***********************************************************/
if ( !file_exists( $save_folder_path ) ) {
    mkdir( $save_folder_path, 0777, true );
};
/***********************************************************/
if ( file_exists( $save_folder_path.'/Program-'.str_replace('+', '_', urlencode($categoryname) ).date('y-m-d').'-Assessment-Report.xls' ) ) {
	unlink( $save_folder_path.'/Program-'.str_replace('+', '_', urlencode($categoryname) ).date('y-m-d').'-Assessment-Report.xls' );
};
/***********************************************************/
/* // Save Excel 95 file */
$callStartTime = microtime(true);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save( $save_folder_path.'/Program-'.str_replace('+', '_', urlencode($categoryname) ).date('y-m-d').'-Assessment-Report.xls' );
/***********************************************************/
switch_to_blog(1);
$upload_dir = wp_upload_dir();
restore_current_blog();
/***********************************************************/
if( $cat_count ) {
	$file_url = $upload_dir['baseurl'].'/reports/Program-'.str_replace('+', '_', urlencode($categoryname) ).date('y-m-d').'-Assessment-Report.xls';
	$data = array( 'message'=> '1', 'url'=> $file_url );
} else {
	courses_not_present:
	$data = array( 'message'=> '2', 'url'=> '#' );
}
echo json_encode($data);
/***********************************************************/
