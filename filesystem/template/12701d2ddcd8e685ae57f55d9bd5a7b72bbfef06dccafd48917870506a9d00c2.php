<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>news</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.staticfile.org/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdn.staticfile.org/popper.js/1.15.0/umd/popper.min.js"></script>
    <script src="https://cdn.staticfile.org/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/4.3.1/css/bootstrap.min.css">
    <link href="http://localhost//template/component/page/css/pager.css" rel="stylesheet">
</head>
<body>
<?php foreach($page['data'] as $items): ?>
<div>
    <?php echo $items['brand_name']; ?>
</div>
<?php endforeach; ?>

<div>
    <ul class="pagination pager">
        <?php $request=make('request');; ?>
        <?php $params=$request->all(); ?>
        <?php if(count($page['page_list'])>0):?>
        <?php $params['page']=1;; ?>
        <li class="page-item"><a class="page-link" href="<?php echo make_method_static('common','http_url_build',$params) ?>">首页</a></li>
        <?php endif; ?>
        <?php if(isset($page['pre_page'])):?>
        <?php $params['page']=$page['pre_page'];; ?>
        <li class="page-item"><a class="page-link" href="<?php echo make_method_static('common','http_url_build',$params) ?>"><img class="pager-go" src="http://localhost//template/component/page/../../asset/icon-picture/left-narrow.png" alt=""></a></li>
        <?php endif; ?>
        <?php foreach($page['page_list'] as $item): ?>
        <?php if($item==$page['current_page']):?>
        <?php $params['page']=$item;; ?>
        <li class="page-item active"><a class="page-link" href="<?php echo make_method_static('common','http_url_build',$params) ?>"><?php echo $item; ?></a></li>
         <?php else: ?>
        <?php $params['page']=$item;$url_item=$request->get_full_url(false).'?'.http_build_query($params);; ?>
        <li class="page-item"><a class="page-link" href="<?php echo make_method_static('common','http_url_build',$params) ?>"><?php echo $item; ?></a></li>
        <?php endif; ?>
        <?php endforeach; ?>
        <?php if(isset($page['next_page'])):?>
        <?php $params['page']=$page['next_page'];$url_item=$request->get_full_url(false).'?'.http_build_query($params); ?>
        <li class="page-item"><a class="page-link" href="<?php echo make_method_static('common','http_url_build',$params) ?>"><img class="pager-go" src="http://localhost//template/component/page/../../asset/icon-picture/right-narrow.png" alt=""></a></li>
        <?php endif; ?>
        <?php if(count($page['page_list'])>0):?>
        <?php $params['page']=$page['page_total'];$url_item=$request->get_full_url(false).'?'.http_build_query($params); ?>
        <li class="page-item"><a class="page-link" href="<?php echo make_method_static('common','http_url_build',$params) ?>">末页</a></li>
        <?php endif; ?>
    </ul>
</div>

</body>
</html>