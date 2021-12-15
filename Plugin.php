<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * Bangumi 追番列表
 *
 * @package BangumiList
 * @author smoe
 * @version 1.0.0
 * @link https://smoe.cc
 */
class BangumiList_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx = array('BangumiList_Plugin', 'replace');
        Helper::addRoute("route_BangumiList", "/BangumiList", "BangumiList_Action", 'action');
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate()
    {
        Helper::removeRoute("route_BangumiList");
    }

    public static function personalConfig(Typecho_Widget_Helper_Form $form) {}

    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        /**表单设置 */
        $userID = new Typecho_Widget_Helper_Form_Element_Text('userID', NULL, NULL, _t('输入你的 Bangumi 用户 ID'), _t('用户 ID 在个人主页中查询，是 "你的用户名 @" 后面的那串数字'));
        $userID->input->setAttribute('class', 'mini');
        $form->addInput($userID);
        $appID = new Typecho_Widget_Helper_Form_Element_Text('appID', NULL, NULL, _t('输入你的 app_id'), _t('app_id 在开发者平台中申请，https://bangumi.tv/dev/app'));
        $appID->input->setAttribute('class', 'mini1');
        $form->addInput($appID);
        $hasCache = new Typecho_Widget_Helper_Form_Element_Radio('hasCache', array('1' => _t('开启'), '0' => _t('关闭')), '0', _t('开启缓存'), _t('开启缓存需保证插件根目录可写'));
        $form->addInput($hasCache);
        $cacheTime = new Typecho_Widget_Helper_Form_Element_Text('cacheTime', NULL, '86400', _t('缓存过期时间'), _t('设置缓存过期时间，单位为秒，默认为一天'));
        $form->addInput($cacheTime);
    }

    public static function replace($content, $widget, $lastResult)
    {
        $content = empty($lastResult) ? $content : $lastResult;
        if (strpos($content, '[bangumi]') !== false) {
            $content = str_replace("[bangumi]", self::output(), $content);
        }
        return $content;
    }

    public static function output()
    {
        //$cssPath = Helper::options()->pluginUrl . '/BangumiList/loading.css';
        return '<style>.article>div{width:50%;}.bangumi_loading{width:150px;height:150px;border-radius:50%;perspective:800px;margin:20px auto}.inner{position:absolute;box-sizing:border-box;width:100%;height:100%;border-radius:50%}.inner.one{left:0;top:0;animation:rotate-one 1s linear infinite;border-bottom:5px solid #ff73b3}.inner.two{right:0;top:0;animation:rotate-two 1s linear infinite;border-right:5px solid #ff73b3}.inner.three{right:0;bottom:0;animation:rotate-three 1s linear infinite;border-top:5px solid #ff73b3}@keyframes rotate-one{0%{transform:rotateX(35deg) rotateY(-45deg) rotateZ(0deg)}100%{transform:rotateX(35deg) rotateY(-45deg) rotateZ(360deg)}}@keyframes rotate-two{0%{transform:rotateX(50deg) rotateY(10deg) rotateZ(0deg)}100%{transform:rotateX(50deg) rotateY(10deg) rotateZ(360deg)}}@keyframes rotate-three{0%{transform:rotateX(35deg) rotateY(55deg) rotateZ(0deg)}100%{transform:rotateX(35deg) rotateY(55deg) rotateZ(360deg)}}.bangumiList{margin:20px 0}.bangumi_loading_text{text-align:center;font-size:16px;font-weight:600;color:#c7254e}.Bangumi ul{display:inline-block;vertical-align:top;margin:10px 20px 10px 0}.Bangumi ul img{width:88px;height:132px;object-fit:cover;padding:2px;box-shadow: 0 1px 5px #aaa;}.Bangumi ul p{width:88px;display:block;margin-block-start:1em;margin-block-end:1em;margin-inline-start:0;margin-inline-end:0;overflow-wrap:break-word}</style>' .
            '<div id="Bangumi" class="Bangumi">
        	    <div>
                    <div class="bangumi_loading">
                        <div class="inner one"></div>
                        <div class="inner two"></div>
                        <div class="inner three"></div>
                        </div>
                    <div class="bangumi_loading_text">追番列表加载中...</div>
                </div>
            </div>' . "
		<script>
		var xhrbgm = new XMLHttpRequest();
        xhrbgm.open('GET', '/BangumiList', true);
        xhrbgm.send();
        xhrbgm.onreadystatechange = function (e) {
          if (xhrbgm.readyState == 4 && xhrbgm.status == 200) {
            Bangumi.innerHTML = '';
            Bangumi.insertAdjacentHTML('beforeend', xhrbgm.responseText);
            //console.log(xhrbgm.responseText);
          }else{
            Bangumi.insertAdjacentHTML('beforeend', '<p>追番列表加载失败</p>');
          }
        };
		</script>";
    }
}
