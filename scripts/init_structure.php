<?php
/// !!! Что нужно сдеать и доделать
// доделать создание TVs
// приделать к prepertySets нужные ассоциации (сниппеты)

include('config.core.php');

if (!defined('MODX_CORE_PATH')) {
    define('MODX_CORE_PATH', '/path/to/core/');
}
if (!defined('MODX_CONFIG_KEY')) {
    define('MODX_CONFIG_KEY', 'config');
}
 
require_once( MODX_CORE_PATH . 'model/modx/modx.class.php');
$modx = new modx();
$modx->initialize('mgr');
$modx->getService('error', 'error.modError');


$resources = array(
                   1=>array('pagetitle'=>'О компании','alias'=>'about','content'=>'<p>Пара слов о компании</p>')
                   
                   
                   ,2=>array('pagetitle'=>'Новости','alias'=>'news','content'=>'<div class="news">[[!getPage@News]]</div>
<div class="paging">
<ul class="pageList">
  [[!+page.nav]]
</ul>
</div>','richtext'=>false)
                   
                   ,3=>array('pagetitle'=>'Контакты','alias'=>'contacts','content'=>'<p>Контакты, а дальше форма.</p> [[$FormFeedback]]')
                                      
                   );  
// inset news ojects
foreach ($resources as $id => $props) {
    echo "Create resource  - $id ".$props['pagetitle']."\n";
        
    $r['published'] = 'on';    
    $r['template'] = 1;
    $r['parent'] = 0;        
            
        
    $r = array_merge($r,$props);
    
    $response = $modx->runProcessor('resource/create', $r);    


    if ($id == 2) {
      $object = $response->getObject();
      $news_id =  $object['id'];
    }
}
    

echo "News id IS - ".$news_id . "\n";

// create base chunks _header _footer
echo "Create _header and _footer chunks \n";
$chunk_header = $modx->newObject('modChunk');
$chunk_header->set('name','_header');
$chunk_header->set('description','Шапка');
$chunk_header->save();

$chunk_footer = $modx->newObject('modChunk');
$chunk_footer->set('name','_footer');
$chunk_footer->set('description','Подвал');
$chunk_footer->save();


// created email chunks
$chunk_email = $modx->newObject('modChunk');
$chunk_email->set('name','EmailForm');
$chunk_email->set('description','Стандартная форма обратной связи');
$chunk_email->set('content',"<h2>Письмо с сайта [[++site_name]]</h2>
<p><strong>Имя</strong> [[+name]]</p>
<p><strong>Email</strong> [[+email]]</p>
<p><strong>Телефон</strong> [[+phone]]</p>
<p><strong>Комментарии</strong> [[+comments]]</p>");
$chunk_email->save();


// create template main
echo "Create tpl for main page \n";
$tpl = $modx->newObject('modTemplate');
$tpl->set('templatename','Главная');
$tpl->set('description','Шаблон для главной страницы');
$tpl->set('content',"[[\$_header]]\n\n[[\$_footer]]");
$tpl->save();

// rename main page and set tempalte = 2
echo "Rename main page \n";
$r = $modx->getObject('modResource',1);
$r->set('pagetitle','Главная');
$r->set('template',2);
$r->save();

//and inner template
echo "Rename inner template \n";
$tpl = $modx->getObject('modTemplate',1);
$tpl->set('templatename','Типовая внутренняя');
$tpl->set('description','Подходит под большинство внутренних страниц');
$tpl->set('content',"[[\$_header]]\n\n[[\$_footer]]");
$tpl->save(); 


// create tv and assign to template
// как нить потом





// news structure
$news = array(
              array('pagetitle'=>'«We Are Young» получила «Грэмми» в номинации «Песня года»','introtext'=>'Композиция «We Are Young» группы fun. стала лауреатом премии «Грэмми» в номинации «Песня года». Об этом объявили на 55-й церемонии вручения музыкальной премии, которая проходит 10 февраля в Лос-Анджелесе. Премию присудили также певице Адель в категории «Лучшее сольное исполнение поп-композиции».','content'=>'<p>Композиция «We Are Young» группы fun. стала лауреатом премии «Грэмми» в номинации «Песня года». Об этом объявили на 55-й церемонии вручения музыкальной премии, которая проходит 10 февраля в Лос-Анджелесе. Премию присудили также певице Адель в категории «Лучшее сольное исполнение поп-композиции».</p>')              
              ,array('pagetitle'=>'Ликующее большинство','introtext'=>'В Москве учредили организацию для борьбы с либералами, ювенальной юстицией и образовательными стандартами: репортаж «Ленты.ру»','content'=>'<p>В Москве учредили организацию для борьбы с либералами, ювенальной юстицией и образовательными стандартами: репортаж «Ленты.ру»</p>')              
              ,array('pagetitle'=>'«Не могу питаться святым духом»','introtext'=>'Басманный суд Москвы отправил Сергея Удальцова под домашний арест','content'=>'<p>Басманный суд Москвы отправил Сергея Удальцова под домашний арест: репортаж «Ленты.ру»</p>')
              ,array('pagetitle'=>'«Суд лишил народ права на восстание»','introtext'=>'Квачкову дали 13 лет за подготовку переворота','content'=>'<p>Квачкову дали 13 лет за подготовку переворота: репортаж «Ленты.ру»</p>')              
              ,array('pagetitle'=>'Дигидрогена монооксид','introtext'=>'Краткая история заблуждений о воде','content'=>'<p>Лукашенко велел увеличить производство «пальцем пиханной» колбасы</p>')              
              );

foreach ($news as $index => $props) {
    echo "Create resource  - ".$props['pagetitle']."\n"; 
        
    $rn['published'] = 'on';
    $rn['template'] = 1;
    $rn['parent'] = $news_id;    
    $rn['alias'] = 'news-'.($index+1);
    
    
    $rn = array_merge($rn,$props);
    $response = $modx->runProcessor('resource/create', $rn);  
    
    if ($response->isError()) {  /* An error occurred */
      if ($response->hasFieldErrors()) {
          $fieldErrors = $response->getAllErrors();
          $errorMessage = implode("\n", $fieldErrors);
      } else {
          $errorMessage = 'An error occurred: ' . $response->getMessage();
      }
      echo $errorMessage;
  }  
       
    
}


// chunks for news
echo "Create news chunks \n";
$chunk_news = $modx->newObject('modChunk');
$chunk_news->set('name','NewsRow');
$chunk_news->set('description','Новость на странице новостей');
$chunk_news->set('content',"<div class='item'>
    <span>[[+publishedon:rudate]]</span> <a href='[[~[[+id]]]]'>[[+pagetitle]]</a>    
  </div>");
$chunk_news->save();

$chunk_news_side = $modx->newObject('modChunk');
$chunk_news_side->set('name','NewsSideRow');
$chunk_news_side->set('description','Новость на главной странице');
$chunk_news_side->set('content',"<div class='item'>
   <h3><a href='[[~[[+id]]]]'>[[+pagetitle]]</a> <span>[[+publishedon:rudate]]</span></h3>
   <p><a href='[[~[[+id]]]]'><img src='[[+tv.preview:phpthumbof=`w=270&h=175&zc=1&q=95`]]' alt='[[+pagetitle]]' /></a> [[+introtext]]</p>
  </div>");
$chunk_news_side->save(); 


// property for news in main and inner pages
echo "Create News modPropertySet \n";
$ps = $modx->newObject('modPropertySet');
$ps->set('name','News');
$ps->setProperties(array('element'=>'getResources','includeTVs'=>1,'limit'=>1,'pageFirstTpl'=>' ','pageLastTpl'=>' ','parents'=>3,'tpl'=>'NewsRow'));
$ps->save();

$pss = $modx->newObject('modPropertySet');
$pss->set('name','NewsSide');
$pss->setProperties(array('limit'=>1,'parents'=>3,'tpl'=>'NewsSideRow'));
$pss->save();



// created propertySet for Breadcrumbs
$ps_breads = $modx->newObject('modPropertySet');
$ps_breads->set('name','Breads');
$ps_breads->setProperties(array('crumbSeparator'=>'<li> » </li>','homeCrumbTitle'=>'[[++site_name]]'));
$ps_breads->save();



// create tv 
$tv_preview = $modx->newObject('modTemplateVar');
$tv_preview->set('name','preview');
$tv_preview->set('type','image');
$tv_preview->set('caption','Картинка');
$tv_preview->save();


// create SEO tvs
$tv_title = $modx->newObject('modTemplateVar');
$tv_title->set('name','seo_title');
$tv_title->set('type','text');
$tv_title->set('caption','Заголовок страницы');
$tv_title->save();

$tv_meta_keywords = $modx->newObject('modTemplateVar');
$tv_meta_keywords->set('name','seo_keywords');
$tv_meta_keywords->set('type','text');
$tv_meta_keywords->set('caption','Meta keywords');
$tv_meta_keywords->save();

$tv_meta_description = $modx->newObject('modTemplateVar');
$tv_meta_description->set('name','seo_description');
$tv_meta_description->set('type','text');
$tv_meta_description->set('caption','META description');
$tv_meta_description->save();


echo "Delete self file\n";
unlink(__FILE__);