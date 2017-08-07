<?php

namespace backend\modules\course\controllers;

use Yii;
use common\models\ResetSchedule;
use common\models\search\SearchResetSchedule;
use common\models\Module;
use common\models\Unit;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * AutoresetController implements the CRUD actions for ResetSchedule model.
 */
class AutoresetController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all ResetSchedule models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchResetSchedule();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		
		$modules = ArrayHelper::map
		(
			Module::find()
			->where(['program.company_id'=>\Yii::$app->user->identity->c_id])
			->innerJoinWith(['program'])->orderBy('title')->all(), 'module_id',function($model, 	$defaultValue) {
					return $model->title.' ['.$model->program->title.']';
				}
		);
		$lessons = ArrayHelper::map
		(
			Unit::find()
			->where(['program.company_id'=>\Yii::$app->user->identity->c_id])
			->innerJoinWith(['module','module.program as program'])->orderBy('title')->all(), 'unit_id',function($model, $defaultValue) {
					return $model->title.' ['.$model->module->title.']';
				}
		);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			'modules' => $modules,
			'lessons' => $lessons
        ]);
    }

    /**
     * Displays a single ResetSchedule model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ResetSchedule model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ResetSchedule();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->s_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ResetSchedule model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->s_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ResetSchedule model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ResetSchedule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ResetSchedule the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ResetSchedule::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
