
<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */


$this->title = Yii::t('app', 'Categories');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="category-index">
    <h1><?= Html::encode($this->title) ?></h1>
   
     <p>
        <?= Html::button('create', ['class' => 'btn btn-success','onclick' => 'generate(0)']) ?>
    </p>
    <input type="hidden" name="parent_id" id="parent_id" value="0"/>
    
</div>
<script>
    function generate(parent)
    {
        var parent_id=$("#parent_id").val();
        $.ajax({
            url: '<?php echo Url::to(['category/create']) ?>',
           type: 'post',
           data: {parent: parent_id },
           success: function (data) {
               result=JSON.parse(data);
               generateHtml(result);
              $("#parent_id").val(result.id);
              $(".categoryId").prop('checked',false);
              

           }

      });
    }
    function generateHtml(model)
    {
        var staticHtml="<span class=categoryParent><input type=checkbox class='categoryId' onclick=assignCategoryId("+model.id+") value="+model.id+"/> "+model.name+" </span><br/>";
    $(".category-index").append(staticHtml);
    }
    
    function assignCategoryId(parentId)
    {
      $("#parent_id").val(parentId);  
    }
        
</script>
