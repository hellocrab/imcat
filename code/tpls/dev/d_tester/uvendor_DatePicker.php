<?php (!defined('RUN_MODE')) && die('No Init');?>
<script src="<?php echo PATH_VENDUI; ?>/My97DatePicker/WdatePicker.js"></script>

<p>
  <input class="Wdate" type="text" onclick="WdatePicker()">
  <font color=red>&lt;- 点我弹出日期控件</font></p>
<p>更多demo请访问官方主页<a href="http://www.my97.net">http://www.my97.net</a><br>
  <br>
</p>
<h1>请务必仔细阅读下面的文字</h1>
<pre>
注意:此版本为 4.8 Beta2 build 20111221


*** 更新内容:  ---------------------------------------------------------------------
[新增]preload预载选项
[增强]验证功能可被关闭errDealMode=-1
[修改]调整周算法模式,新增weekMethod属性
[修改]去除My97DatePicker.htm
[修改]position改成相对坐标(原来为绝对坐标)
[修正]跨域错误问题
[修正]onchange不能触发的问题
[修正]兼容Safari5
[修正]&lt;script&gt;空标签时的错误
[修正]平面模式下的几个偶发问题
[修正]双月日历下跨年选择出错的问题
[修正]修正复杂iframe下,弹出位置偏移的问题(很偶发的情况)


*** 使用方法:  ---------------------------------------------------------------------

1. 去官方网站看看,你当前下载的是否是最新的版本,很多bug都是因为使用的不是最新版本造成的
   官方主页:<a href="http://www.my97.net" target="_blank">http://www.my97.net</a>
2. 将My97DatePicker整个目录包,放入您的项目的相应目录下
  My97DatePicker目录下各文件的作用:
  1.1 My97DatePicker目录是一个整体,不可破坏里面的目录结构,也不可对里面的文件改名,可以改目录名
  1.2 各目录及文件的用途:
    WdatePicker.js 配置文件,在调用的地方仅需使用该文件,可多个共存,以xx_WdatePicker.js方式命名
    calendar.js 日期库主文件,无需引入
    目录lang 存放语言文件,你可以根据需要清理或添加语言文件
    目录skin 存放皮肤的相关文件,你可以根据需要清理或添加皮肤文件包
3. 您可以根据您自己的需要,删除不必要的皮肤和语言文件
4. 您可以根据您自己的需要,添加新的皮肤包
   皮肤中心地址:<a href="http://www.my97.net/dp/skin.asp" target="_blank">http://www.my97.net/dp/skin.asp</a>
5. 详细阅读在线演示和使用说明,大部分问题都可以通过这里解决,请细看
   在线演示:<a href="http://www.my97.net/dp/demo/" target="_blank">http://www.my97.net/dp/demo/</a>
6. 如果遇到无法解决的问题
   请先参考:<a href="http://www.my97.net/dp/support.asp" target="_blank">http://www.my97.net/dp/support.asp</a>
7. 如果遇到问题,而技术支持页面无法解决的
   您可以通过技术支持页面中提供的联系方式联系我,注意:问问题时,一定要附上相关的HTML代码和详细的错误信息
8. 您有什么意见或建议,你可以通过技术支持页面中提供的联系方式联系我
9. 如果您对日期控件的许可协议有兴趣,您可以访问:<a href="http://www.my97.net/dp/license.asp">http://www.my97.net/dp/license.asp</a>
10.最后祝大家项目顺利,月月加薪!


*** 官方主页 ---------------------------------------------------------------------
<a href="http://www.my97.net" target="_blank">http://www.my97.net</a>
在线演示和使用说明
<a href="http://www.my97.net/dp/demo/" target="_blank">http://www.my97.net/dp/demo/</a>
皮肤中心:
<a href="http://www.my97.net/dp/skin.asp" target="_blank">http://www.my97.net/dp/skin.asp</a>
许可协议
<a href="http://www.my97.net/dp/license.asp">http://www.my97.net/dp/license.asp</a>  
源代码:
<a href="http://www.my97.net/dp/source.asp" target="_blank">http://www.my97.net/dp/source.asp</a>
技术支持页面
<a href="http://www.my97.net/dp/support.asp" target="_blank">http://www.my97.net/dp/support.asp</a></pre>
