<?php

namespace app\controllers;

use Yii;
use app\models\Category;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class CategoryController extends Controller
{
    /**
     * {@inheritdoc}
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
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {

        return $this->render('index');
    }

    /**
     * Displays a single Category model.
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
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Category();
        $id = Yii::$app->request->get('id');
        $categoryId=($id != null) ? $id : 0;
        

        if ( ! empty(Yii::$app->request->post('Category'))) 
        {
            $post            = Yii::$app->request->post('Category');
            $model->name     = $post['name'];
            $model->position = $post['position'];
            $parent_id       = $post['parentId'];

            if (empty($parent_id))
                $model->makeRoot();
            else
            {
                $parent = Category::findOne($parent_id);
                $model->appendTo($parent);
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
                'model' => $model,
                'parentId' => $categoryId
            ]);
    }
    
    /**
     * Updates an existing Category model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ( ! empty(Yii::$app->request->post('Category'))) 
        {
            $post            = Yii::$app->request->post('Category');

            $model->name     = $post['name'];
            $model->position = $post['position'];
            $parent_id       = $post['parentId'];
if ($model->save())            
            {
                if (empty($parent_id))
                {
                    if ( ! $model->isRoot())
                        $model->makeRoot();
                }
                else // move node to other root 
                {
                    if ($model->id != $parent_id)
                    {
                        $parent = Category::findOne($parent_id);
                        $model->appendTo($parent);
                    }
                }

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }
     /**
     * Deletes an existing Category model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->isRoot())
            $model->deleteWithChildren();
        else 
            $model->delete();

        return $this->redirect(['index']);
    }
    
    
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested category does not exist.');
        }
    }

}
