CKEDITOR.editorConfig = function( config )
{
         config.language = 'zh-cn'; //设置界面显示语言为中文
         config.uiColor = '#ddd'; //设置UI界面颜色
         config.skin = 'kama'; //设置界面显示样式
         config.height = 400;//设置编辑器的高度
		 
         config.toolbar = [ //设置编辑器的可用功能，Full是全部功能
							['Source','-','Preview','-','Templates','-','Print'],
							['RemoveFormat'],
							['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
							['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
							['Link','Unlink','Anchor'],
							['Image','Flash'],
							['Styles','Format','Font','FontSize'],
							['TextColor','BGColor']
						];
         config.disableNativeSpellChecker = false ;//提速，禁用拼写检查
         config.scayt_autoStartup = false;//提速，禁用拼写检查      
         //下面的配置是调用CKFinder插件实现文件上传管理的
         config.filebrowserBrowseUrl      = 'ckfinder/ckfinder.html';
         config.filebrowserImageBrowseUrl = 'ckfinder/ckfinder.html?Type=Images';
         config.filebrowserFlashBrowseUrl = 'ckfinder/ckfinder.html?Type=Flash';
         config.filebrowserUploadUrl      = 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files';
         config.filebrowserImageUploadUrl = 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images';
         config.filebrowserFlashUploadUrl = 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash';
};
