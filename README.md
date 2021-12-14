# Typecho 追番列表插件

#### 项目介绍
Bangumi 追番列表插件 Typecho 版

使用短代码 `[bangumi]` 在任何位置插入你的 Bangumi 追番列表

插件效果见此处：https://smoe.cc/bangumi

![screenshot](https://smoe.cc/usr/uploads/2021/12/2220208987.webp)

#### 使用方法：

  1. 在 [Release](https://github.com/BapiGso/BangumiList/releases) 中下载此插件的最新版，上传至网站的/usr/plugins/目录下；
  2. 在 [bgm.tv](https://bgm.tv/)注册一个账号并且在[开发者平台](https://bgm.tv/dev/app)创建一个新应用，记下appid
  3. 启用该插件，正确填写相关信息。
  4. 在你想展示的位置插入短代码 `[bangumi]`
  
  
#### 项目说明
 - 此项目copy自[@ShadowySpirits](https://github.com/ShadowySpirits/BangumiList)因原作者转到了Hugo且Bangumi的Api有了一些更新所以开了个仓库，在此感谢[原作者](https://github.com/ShadowySpirits)和Bangumi提供的[Api](https://github.com/bangumi/api)
 - 将原项目使用的Jquery.ajax更换为原生ajax，现在没有使用Jquery的网站能更方便的引入了！
 - 因Bangumi Api的限制最多只能查询25条信息，代码中已将看过和在看合并为了看过
