<?php

namespace backend\modules\course\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;

use common\models\Program;
use common\models\Company;
use common\models\UserProfile;
use common\models\Division;
use common\models\State;
use common\models\Location;
use common\models\Role;

class ExportController extends Controller
{	
	
	public function actionExport(){
		
		
		$program = Program::findOne(\Yii::$app->request->post()['p_id']);
		$company = Company::findOne(\Yii::$app->request->post()['c_id']);
		
		//////////////////get users searched////////////////////////////
		$param = unserialize(\Yii::$app->request->post()['params']);
		$searched_by_user = '';
		//var_dump($params);die;
		if($param){
			//print_r($params);die;
			$query = UserProfile::find();
			$dataProvider = new ActiveDataProvider([
				'query' => $query,
					'pagination' => [
						'pageSize' => 0,
					],
			]);	
			$query->joinWith(['user']);
			$query->andFilterWhere(['user.c_id'=>\Yii::$app->user->identity->c_id]);			
			//if any of the user parametr is filled,then search for that users
			//$query = User::find()->where(['c_id' =>Yii::$app->user->identity->c_id]);			
 			if(isset($param['user']) && $param['user'] !='')
				$query->andFilterWhere(['user_id'=>$param['user']]);			
 			if(isset($param['state']) && $param['state'] !=''){
				$query->andFilterWhere(['state'=>$param['state']]);
				$state = State::findOne($param['state']);
				$searched_by_user .= " State:".$state->name;
			}
				
			if(isset($param['role']) && $param['role'] !=''){
				$query->andFilterWhere(['role'=>$param['role']]);
				$role = Role::findOne($param['role']);
				$searched_by_user .= " Role:".$role->title;
			}
				
			if(isset($param['location']) && $param['location'] !=''){
				$query->andFilterWhere(['location'=>$param['location']]);
				$location = Location::findOne($param['location']);
				$searched_by_user .= " Location:".$location->name;
			}
				
			if(isset($param['division']) && $param['division'] !=''){
				$query->andFilterWhere(['division'=>$param['division']]); 
				$division = Division::findOne($param['division']);
				$searched_by_user .= " Division:".$division->title;
			}
				
			if(isset($param['firstname']) && $param['firstname'] !=''){
				$query->andFilterWhere(['like', 'firstname',$param['firstname']]);
				$searched_by_user .= " Firstname:".$param['firstname'];
			}
				
			if(isset($param['lastname']) && $param['lastname'] !=''){
				$query->andFilterWhere(['like', 'lastname', $param['lastname']]);	
				$searched_by_user .= " Lastname:".$param['lastname'];
			}
				
			//$query->andFilterWhere(['division'=>$param['division']]); 
			$users = $dataProvider->models;
			$filtered_users = [];
			foreach($users as $user){
				$filtered_users[] = $user->user_id;
			}			
		}else{
			$users = UserProfile::find()->joinWith(['user'])->andFilterWhere(['user.c_id'=>\Yii::$app->user->identity->c_id])->all();
			$filtered_users = [];
			foreach($users as $user){
				$filtered_users[] = $user->user_id;
			}	
		}
		//////////////////////////////////////////////////////////
		
		$RedColor	= 'c10001';
		$GrayColor	= '81889a';
		$AmberColor	= 'ffc000';
		$GreenColor	= '008000';

		$objPHPExcel = new \PHPExcel();	
		$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")->setLastModifiedBy("Maarten Balliauw")->setTitle("PHPExcel Test Document")->setSubject("PHPExcel Test Document")->setDescription("Test document for PHPExcel, generated using PHP classes.")->setKeywords("office PHPExcel php")->setCategory("Test result file");
		$styleArray = array(
			  'borders' => array(
				  'allborders' => array(
					'style' => \PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb' => 'ffffff'),
				  )
			  )
		  );
		$objPHPExcel->getDefaultStyle()->applyFromArray($styleArray);	
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(1);
		$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(40);
		$objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(40);
		$objPHPExcel->getActiveSheet()->getRowDimension('6')->setRowHeight(35);
		$objPHPExcel->getActiveSheet()->getRowDimension('7')->setRowHeight(30);
		
		//$objPHPExcel->getActiveSheet()->setCellValue('I1', "");
		$objPHPExcel->getActiveSheet()->getStyle('I1')->getAlignment()->setWrapText(true);
		$sheet = $objPHPExcel->getActiveSheet();
		$alignment = new \PHPExcel_Style_Alignment();
		$style = array(
			'alignment' => array(
				'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);
		$sty = array(
			'alignment' => array(
				'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP
			)
		);
		$sheet->getStyle("I1:M1")->applyFromArray($style);
		//$objPHPExcel->getActiveSheet()->getColumnDimension('I1')->setAutoSize(true);

		$objPHPExcel->getActiveSheet()->getStyle('I1')->getAlignment()->setWrapText(true);
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('I1:M2');

		//Image in title
		$objDrawing = new \PHPExcel_Worksheet_Drawing();
		$objDrawing->setName('Logo');
		$objDrawing->setDescription('Logo');
		$objDrawing->setPath(\Yii::$app->basePath.'/web/img/CAAR-Logo2.png');
		
		$objDrawing->setCoordinates('D1');
		$objDrawing->setHeight(60); // logo height
		$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

		$objDrawing = new \PHPExcel_Worksheet_Drawing();
		$objDrawing->setName('Logo');
		$objDrawing->setDescription('Logo');
		if ($company->logo != '' && file_exists(\Yii::$app->basePath.'/web/'.$company->logo)) {
			$objDrawing->setPath(\Yii::$app->basePath.'/web/'.$company->logo); //setOffsetY has no effect
		}else{
			$objDrawing->setPath(\Yii::$app->basePath.'/web/img/default_logo.jpg');
		};
		$objDrawing->setCoordinates('O1');
		$objDrawing->setHeight(60); 
		$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
		//if file exists
		//Display program title
		$categoryname = $program->title;
		$sheet->getStyle("E3:I3")->applyFromArray($sty);
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells("E3:I3");
		$objPHPExcel->getActiveSheet()->setCellValue('E3', "Program:" . $categoryname);
		$objPHPExcel->getActiveSheet()->getStyle('E3')->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle("E3")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle("E3")->getFont()->setSize(10);
		
		//searched by
		//$searched_by_user = implode(',',$param);
		$objRichText = new \PHPExcel_RichText();
		$objBold = $objRichText->createTextRun('This report searched by : ');
		$objBold->getFont()->setBold(true);
		$objRichText->createText($searched_by_user);
		$objPHPExcel->getActiveSheet()->getCell('E4')->setValue($objRichText);
		
		$objPHPExcel->getActiveSheet()->getStyle('E4')->getAlignment()->setWrapText(false);
		$objPHPExcel->getActiveSheet()->getStyle("E4")->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('A6:AZ8')->getFont()->setSize(10);	

		$styleArray = array(
			'font' => array(
				'color' => array(
					'rgb' => '6d6d6d'
				),
				'size' => 8
			)
		);		
		$enrollments = $program->programEnrollments;
		$worksheet = $objPHPExcel->getActiveSheet();
		$student   = 8;
		$mark      = 9;
		$row = 0;
		foreach($enrollments as $key=>$enrollment){

			if(in_array($enrollment->user_id,$filtered_users))
			{	
			$capability_percentage = $enrollment->user->getProgramProgress($program->program_id);
			if( ( $capability_percentage < 100 ) && ( $capability_percentage > 95 ) ) {
				$split_color_val = 9;
			} else {
				$split_color_val = round ( ( $capability_percentage / 10 ), 0 );
			}
			switch ($split_color_val) {
				case 1: 
					$logo = \Yii::$app->basePath.'/web/img/excel/range1.png';
					break;
				case 2:
					$logo = \Yii::$app->basePath.'/web/img/excel/range2.png';
					break;
				case 3:
					$logo = \Yii::$app->basePath.'/web/img/excel/range3.png';
					break;
				case 4: 
					$logo = \Yii::$app->basePath.'/web/img/excel/range4.png';
					break;
				case 5:
					$logo = \Yii::$app->basePath.'/web/img/excel/range5.png';
					break;
				case 6:
					$logo = \Yii::$app->basePath.'/web/img/excel/range6.png';
					break;
				case 7:
					$logo = \Yii::$app->basePath.'/web/img/excel/range7.png';
					break;
				case 8:
					$logo = \Yii::$app->basePath.'/web/img/excel/range8.png';
					break;
				case 9:
					$logo = \Yii::$app->basePath.'/web/img/excel/range9.png';
					break;
				case 10:
					$logo = \Yii::$app->basePath.'/web/img/excel/range1.png';
					break;
				default:
					$logo = \Yii::$app->basePath.'/web/img/excel/range0.png';
			}
			$student += 1;
			$mark += 1;
			$merging = $row + $student;
			$perce   = $row + $mark;
			$objPHPExcel->getActiveSheet()->getStyle('A' . $merging . ':C' . $merging)->getFont()->setSize(10);
			$objPHPExcel->getActiveSheet()->getStyle('A' . $perce . ':C' . $perce)->getFont()->setSize(10);
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $merging . ':C' . $merging);
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $perce . ':C' . $perce);
			if($enrollment->userProfile)
				$worksheet->setCellValueByColumnAndRow('A9', $row + $student, $enrollment->userProfile->fullname);
			else
				$worksheet->setCellValueByColumnAndRow('A9', $row + $student, $enrollment->user->username);
			$worksheet->setCellValueByColumnAndRow('A10', $row + $mark, $capability_percentage);
			$sheet = $objPHPExcel->getActiveSheet();
			$style = array(
				'alignment' => array(
					'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
				)
			);
			$sheet->getStyle("A" . $merging)->applyFromArray($style);
			$sheet->getStyle("A" . $mark)->applyFromArray($style);
			
			$objDrawing = new \PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Logo');
			$objDrawing->setDescription('Logo');
			if (file_exists($logo)) {
				$objDrawing->setPath($logo); //setOffsetY has no effect
			};
			$objDrawing->setCoordinates('A' . $perce);
			$objDrawing->setHeight(25); // logo height
			$objDrawing->setWidth(155); // logo height
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
			$row = $row+1;
			}
		}
		
		//modules and unit section
		$starting  = 4;
		$ending    = 9;
		$rownumber = 6;
		$max_length = 30;
		$modules = $program->publishedModules;
		foreach($modules as $module){

			$units = $module->publishedUnits;
			$unit_count = count($units);
			if($unit_count){    
				$ending     = ($unit_count * 2) + $starting - 1;
				$objPHPExcel->getActiveSheet()->mergeCells($this->cellsToMergeByColsRow($starting, $ending, $rownumber));
				$objPHPExcel->getActiveSheet()->getStyle($this->cellsToWidthByColsRow($starting, $rownumber))->getAlignment()->setWrapText(true);//
				$worksheet->setCellValueByColumnAndRow($starting, $rownumber, $module->title);				
				$sheet = $objPHPExcel->getActiveSheet();
				$style = array(
					'alignment' => array(
						'horizontal' =>  \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
						'vertical' =>  \PHPExcel_Style_Alignment::VERTICAL_CENTER
					),
					'font'  => array(
						'bold'  => true,
						'color' => array('rgb' => 'FFFFFF'),
					)
				);
				$this->cellColor($objPHPExcel,$this->cellsToWidthByColsRow($starting, $rownumber), '0070c0');
				$sheet->getStyle($this->cellsToWidthByColsRow($starting, $rownumber))->applyFromArray($style);
				
				$objPHPExcel->getActiveSheet()->getColumnDimension($this->cellsToWidthByColsRow($ending + 1))->setWidth(1);
				$unittitle = $starting;
				foreach($units as $unit){

					/***********************************************/
					$StudentUnitFillColumn	= $unittitle;
					$StudentUnitFillRow		= $rownumber + 1;
					/***********************************************/	
					$style = array(
						'alignment' => array(
							'horizontal' =>  \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							'vertical' =>  \PHPExcel_Style_Alignment::VERTICAL_CENTER
						),
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
						)
					);
					$sheet->getStyle($this->cellsToWidthByColsRow($unittitle, $rownumber +1))->applyFromArray($style);
					$objPHPExcel->getActiveSheet()->mergeCells($this->cellsToMergeByColsRow($unittitle, $unittitle + 1, $rownumber + 1));
					$worksheet->setCellValueByColumnAndRow($unittitle, $rownumber + 1, $unit->title);
					$new_length = strlen($unit->title);
					if($new_length > $max_length){
						$max_length = $new_length;
					}
					$objPHPExcel->getActiveSheet()->getStyle($this->cellsToWidthByColsRow($unittitle, $rownumber + 1))->getAlignment()->setWrapText(true);
					$worksheet->setCellValueByColumnAndRow($unittitle, $rownumber + 2, 'Aware');
					$worksheet->setCellValueByColumnAndRow($unittitle + 1, $rownumber + 2, 'Capable');
					$this->cellColor($objPHPExcel,$this->cellsToWidthByColsRow($unittitle, $rownumber + 1), 'b7dee8');
					$this->cellColor($objPHPExcel,$this->cellsToWidthByColsRow($unittitle, $rownumber + 2), 'dce6f1');
					$this->cellColor($objPHPExcel,$this->cellsToWidthByColsRow($unittitle + 1, $rownumber + 2), 'dce6f1');	

					//students 
					foreach($enrollments as $enrollment){

						if(in_array($enrollment->user_id,$filtered_users))
						{
						/********************************************************************************/
						//get unit progress
						$progress = $enrollment->user->getUnitProgress($unit->unit_id);
						if( $progress['ap'] == 'green' ) {
							$AwareColor = $GreenColor;
						} else if( $progress['ap'] == 'red' ) {
							$AwareColor = $RedColor;
						} else if( $progress['ap'] == 'amber' ) {
							$AwareColor = $AmberColor;
						} else {
							$AwareColor = $GrayColor;
						}
						if( $progress['cp'] == 'green' ) {
							$CapableColor = $GreenColor;
						} else if( $progress['cp'] == 'red' ) {
							$CapableColor = $RedColor;
						} else if( $progress['cp'] == 'amber' ) {
							$CapableColor = $AmberColor;
						} else {
							$CapableColor = $GrayColor;
						}
						/********************************************************************************/
						$StudentUnitFillRow = $StudentUnitFillRow + 2;
						/************************************* AWARE ************************************/
						$this->cellColor( $objPHPExcel,$this->cellsToWidthByColsRow( $StudentUnitFillColumn, $StudentUnitFillRow ), $AwareColor );
						$this->cellColor( $objPHPExcel,$this->cellsToWidthByColsRow( $StudentUnitFillColumn, $StudentUnitFillRow + 1 ), $AwareColor );
						$objPHPExcel->setActiveSheetIndex(0)->mergeCells($this->cellsToWidthByColsRow( $StudentUnitFillColumn, $StudentUnitFillRow ).':'.$this->cellsToWidthByColsRow( $StudentUnitFillColumn, $StudentUnitFillRow +1 ));
						/************************************* AWARE ************************************/
						
						/************************************ CAPABLE ***********************************/
						$this->cellColor( $objPHPExcel,$this->cellsToWidthByColsRow( $StudentUnitFillColumn + 1, $StudentUnitFillRow ), $CapableColor );
						$this->cellColor( $objPHPExcel,$this->cellsToWidthByColsRow( $StudentUnitFillColumn + 1, $StudentUnitFillRow + 1 ), $CapableColor );
						$objPHPExcel->setActiveSheetIndex(0)->mergeCells($this->cellsToWidthByColsRow( $StudentUnitFillColumn + 1, $StudentUnitFillRow ).':'.$this->cellsToWidthByColsRow( $StudentUnitFillColumn + 1, $StudentUnitFillRow + 1 ));
						/************************************ CAPABLE ***********************************/
						$FinalRowRun 	= $StudentUnitFillRow + 1;
						$FinalColumnRun = $StudentUnitFillColumn + 1;
						}
					}
					//////////
					$sheet->getStyle($this->cellsToWidthByColsRow($unittitle, $rownumber + 1))->applyFromArray($style);
					$sheet->getStyle($this->cellsToWidthByColsRow($unittitle, $rownumber + 2))->applyFromArray($style);
					$sheet->getStyle($this->cellsToWidthByColsRow($unittitle + 1, $rownumber + 2))->applyFromArray($style);
					$unittitle += 2;					
				}
			$starting = $ending + 2;
			}
		}
		$objPHPExcel->getActiveSheet()->getRowDimension('7')->setRowHeight($max_length);
		//Border color
		$border_style = array(
			'borders' => array(
				'allborders' => array(
					'style' => \PHPExcel_Style_Border::BORDER_THICK,
					'color' => array('rgb' => 'ffffff'),
				),
			),
		);
		$sheet        = $objPHPExcel->getActiveSheet();

		/*******************************************************************/
		/* ------------- Add Border To Whole Tests Columns --------------- */
		/*******************************************************************/
		$WholeTestLastBlock = $this->cellsToWidthByColsRow( $FinalColumnRun, $FinalRowRun );
		$styleArray = array(
			'borders' => array(
				'allborders' => array(
					'style' => \PHPExcel_Style_Border::BORDER_THICK,
					'color' => array('rgb' => 'ffffff'),
				),
			),
		);
		$sheet->getStyle()->applyFromArray($border_style);
		$sheet->getStyle( 'E8:'.$WholeTestLastBlock )->applyFromArray($styleArray);
		$sheet->getStyle( 'E7:'.$WholeTestLastBlock )->applyFromArray($styleArray);
		
		/* $sheet->getStyle('A1:'.$WholeTestLastBlock)->getAlignment()->setIndent(4); */
		/*******************************************************************/	
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Simple');


		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);		
		//save sheet
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		//$categoryname = str_replace('&','and',$categoryname);
		$categoryname = $program->program_id;
		$objWriter->save( \Yii::$app->basePath.'/web/uploads/Program-'.str_replace('+', '_', urlencode($categoryname) ).date('y-m-d').'-Assessment-Report.xls' );
		$file_url = \Yii::$app->homeurl.'uploads/Program-'.str_replace('+', '_', urlencode($categoryname) ).date('y-m-d').'-Assessment-Report.xls';
		return $this->redirect($file_url);
	}
	
	public function cellColor(&$objPHPExcel,$cells, $color) {

		$objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
			'type' => \PHPExcel_Style_Fill::FILL_SOLID,
			'startcolor' => array(
				'rgb' => $color
			)
		));
	}
	
	public function cellsToMergeByColsRow($start = -1, $end = -1, $row = -1) {
		$merge = 'A1:A1';
		if ($start >= 0 && $end >= 0 && $row >= 0) {
			$start = \PHPExcel_Cell::stringFromColumnIndex($start);
			$end   = \PHPExcel_Cell::stringFromColumnIndex($end);
			$merge = "$start{$row}:$end{$row}";
		}
		return $merge;
	}
	
	public function cellsToWidthByColsRow($start = -1, $row = -1) {
		$width = 'A';
		if ($start >= 0 && $row == -1) {
			$start = \PHPExcel_Cell::stringFromColumnIndex($start);
			$width = "$start";
		}
		if ($start >= 0 && $row != -1) {
			$start = \PHPExcel_Cell::stringFromColumnIndex($start);
			$width = "$start{$row}";
		}
		return $width;
	}
}