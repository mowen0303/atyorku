/**
*灯箱插件
 */

(function($) {
	/**
	 * $ is an alias to jQuery object  /别名对象
	 *
	 */
	$.fn.lightBox = function(settings) {
		//配置的灯箱插件 
		settings = jQuery.extend({
			// 配置相关变量
			overlayBgColor: 		'#000',		// 背景颜色
			overlayOpacity:			0.8,		// 不透明值
			// 配置与导航
			fixedNavigation:		false,		// （布尔）导航（next和prev按钮）是否固定显示。
			// 配置相关的图像
			imageLoading:			'img/lightbox-ico-loading.gif',		// （字符串）加载图标的图像路径
			imageBtnPrev:			'img/lightbox-btn-prev.gif',			// （字符串）上一页按钮的图像路径
			imageBtnNext:			'img/lightbox-btn-next.gif',			// （字符串）下一个按钮的图像路径
			imageBtnClose:			'img/lightbox-btn-close.gif',		// （字符串）关闭按钮的图像路径
			imageBlank:				'img/lightbox-blank.gif',			// （字符串）一个像素的空白图像路径
			// 配置相关的集装箱图像框
			containerBorderSize:	10,			// （整数）如果你调整填充样式的容器，#lightbox-container-image-box，您将需要更新此值
			containerResizeSpeed:	400,		//（整数）指定时间容器的图像调整。这些数毫秒。400是默认。
			// 配置相关的文本标题。例如：2或8。你可以改变“图像”和“文本。
			txtImage:				'Image',	// （字符串）指定文本的“形象”
			txtOf:					'of',		// （字符串）指定文本”
			// 配置相关的键盘导航
			keyToClose:				'c',		// （字符串）（C =关闭）信关闭的jQuery灯箱接口。除了这封信，字母X和SCAPE关键是使用。
			keyToPrev:				'p',		// （字符串）（P =上一张）给显示上一张图片
			keyToNext:				'n',		// （字符串）（N =下一张）信显示下一张图片。
			// 以任何方式改变这些变量
			imageArray:				[],
			activeImage:			0
		},settings);
		//缓存所有匹配元素的jQuery对象
		var jQueryMatchedObj = this; // This, 在这种情况下，请参阅jQuery对象
		/**
		 *初始化插件调用启动功能
		 *
		 * @return boolean false
		 */
		function _initialize() {
			_start(this,jQueryMatchedObj); // This,在这种情况下，用户有点击的对象（链接）
			return false; // 避免以下的链接的浏览器
		}
		/**
		*  缓存所有匹配元素的jQuery对象
		*  开始的jQuery灯箱插件
		* @参数对象objClicked whick用户点击的对象（链接）
		* @参数对象jQueryMatchedObj所有匹配的元素的jQuery对象
		 */
		function _start(objClicked,jQueryMatchedObj) {
			// Hime some elements to avoid conflict with overlay in IE. These elements appear above the overlay.
			$('embed, object, select').css({ 'visibility' : 'hidden' });
			// Call the function to create the markup structure; style some elements; assign events in some elements.
			_set_interface();
			// Unset total images in imageArray
			settings.imageArray.length = 0;
			// Unset image active information
			settings.activeImage = 0;
			// We have an image set? Or just an image? Let磗 see it.
			if ( jQueryMatchedObj.length == 1 ) {
				settings.imageArray.push(new Array(objClicked.getAttribute('href'),objClicked.getAttribute('title')));
			} else {
				// Add an Array (as many as we have), with href and title atributes, inside the Array that storage the images references		
				for ( var i = 0; i < jQueryMatchedObj.length; i++ ) {
					settings.imageArray.push(new Array(jQueryMatchedObj[i].getAttribute('href'),jQueryMatchedObj[i].getAttribute('title')));
				}
			}
			while ( settings.imageArray[settings.activeImage][0] != objClicked.getAttribute('href') ) {
				settings.activeImage++;
			}
			// Call the function that prepares image exibition
			_set_image_to_view();
		}
		/**
		 * Create the jQuery lightBox plugin interface
		 *
		 * The HTML markup will be like that:
			<div id="jquery-overlay"></div>
			<div id="jquery-lightbox">
				<div id="lightbox-container-image-box">
					<div id="lightbox-container-image">
						<img src="../fotos/XX.jpg" id="lightbox-image">
						<div id="lightbox-nav">
							<a href="#" id="lightbox-nav-btnPrev"></a>
							<a href="#" id="lightbox-nav-btnNext"></a>
						</div>
						<div id="lightbox-loading">
							<a href="#" id="lightbox-loading-link">
								<img src="../images/lightbox-ico-loading.gif">
							</a>
						</div>
					</div>
				</div>
				<div id="lightbox-container-image-data-box">
					<div id="lightbox-container-image-data">
						<div id="lightbox-image-details">
							<span id="lightbox-image-details-caption"></span>
							<span id="lightbox-image-details-currentNumber"></span>
						</div>
						<div id="lightbox-secNav">
							<a href="#" id="lightbox-secNav-btnClose">
								<img src="../images/lightbox-btn-close.gif">
							</a>
						</div>
					</div>
				</div>
			</div>
		 *
		 */
		function _set_interface() {
			// 应用到body标签的HTML标记
			$('body').append('<div id="jquery-overlay"></div><div id="jquery-lightbox"><div id="lightbox-container-image-box"><div id="lightbox-container-image"><img id="lightbox-image"><div style="" id="lightbox-nav"><a href="#" id="lightbox-nav-btnPrev"></a><a href="#" id="lightbox-nav-btnNext"></a></div><div id="lightbox-loading"><a href="#" id="lightbox-loading-link"><img src="' + settings.imageLoading + '"></a></div></div></div><div id="lightbox-container-image-data-box"><div id="lightbox-container-image-data"><div id="lightbox-image-details"><span id="lightbox-image-details-caption"></span><span id="lightbox-image-details-currentNumber"></span></div><div id="lightbox-secNav"><a href="#" id="lightbox-secNav-btnClose"><img src="' + settings.imageBtnClose + '"></a></div></div></div></div>');	
			// Get page sizes
			var arrPageSizes = ___getPageSize();
			// 样式覆盖，并显示它
			$('#jquery-overlay').css({
				backgroundColor:	settings.overlayBgColor,
				opacity:			settings.overlayOpacity,
				width:				arrPageSizes[0],
				height:				arrPageSizes[1]
			}).fadeIn();
			// Get page scroll
			var arrPageScroll = ___getPageScroll();
			// Calculate top and left offset for the jquery-lightbox div object and show it
			$('#jquery-lightbox').css({
				top:	arrPageScroll[1] + (arrPageSizes[3] / 10),
				left:	arrPageScroll[0]
			}).show();
			// Assigning click events in elements to close overlay
			$('#jquery-overlay,#jquery-lightbox').click(function() {
				_finish();									
			});
			// Assign the _finish function to lightbox-loading-link and lightbox-secNav-btnClose objects
			$('#lightbox-loading-link,#lightbox-secNav-btnClose').click(function() {
				_finish();
				return false;
			});
			// If window was resized, calculate the new overlay dimensions
			$(window).resize(function() {
				// Get page sizes
				var arrPageSizes = ___getPageSize();
				// Style overlay and show it
				$('#jquery-overlay').css({
					width:		arrPageSizes[0],
					height:		arrPageSizes[1]
				});
				// Get page scroll
				var arrPageScroll = ___getPageScroll();
				// Calculate top and left offset for the jquery-lightbox div object and show it
				$('#jquery-lightbox').css({
					top:	arrPageScroll[1] + (arrPageSizes[3] / 10),
					left:	arrPageScroll[0]
				});
			});
		}
		/**
		 * 准备图像展览；做一个图像磗加载计算它磗大小
		 *
		 */
		function _set_image_to_view() { // show the loading
			// Show the loading
			$('#lightbox-loading').show();
			if ( settings.fixedNavigation ) {
				$('#lightbox-image,#lightbox-container-image-data-box,#lightbox-image-details-currentNumber').hide();
			} else {
				// Hide some elements
				$('#lightbox-image,#lightbox-nav,#lightbox-nav-btnPrev,#lightbox-nav-btnNext,#lightbox-container-image-data-box,#lightbox-image-details-currentNumber').hide();
			}
			// 图像预压过程
			var objImagePreloader = new Image();
			objImagePreloader.onload = function() {
				$('#lightbox-image').attr('src',settings.imageArray[settings.activeImage][0]);
				//Perfomance效果的图像容器的大小调整
				_resize_container_image_box(objImagePreloader.width,objImagePreloader.height);
				//	明确的onLoad，IE浏览器的行为与GIF动画irratically另有
				objImagePreloader.onload=function(){};
			};
			objImagePreloader.src = settings.imageArray[settings.activeImage][0];
		};
		/**
		 Perfomance效果的图像容器的大小调整IT
			*
			*@参数整数intImageWidth形象磗的宽度，将显示
			* @参数整数intImageHeight图像磗的高度，将显示ed
		 */
		function _resize_container_image_box(intImageWidth,intImageHeight) {
			// Get current width and height
			var intCurrentWidth = $('#lightbox-container-image-box').width();
			var intCurrentHeight = $('#lightbox-container-image-box').height();
			// Get the width and height of the selected image plus the padding
			var intWidth = (intImageWidth + (settings.containerBorderSize * 2)); // Plus the image磗 width and the left and right padding value
			var intHeight = (intImageHeight + (settings.containerBorderSize * 2)); // Plus the image磗 height and the left and right padding value
			// Diferences
			var intDiffW = intCurrentWidth - intWidth;
			var intDiffH = intCurrentHeight - intHeight;
			// Perfomance the effect
			$('#lightbox-container-image-box').animate({ width: intWidth, height: intHeight },settings.containerResizeSpeed,function() { _show_image(); });
			if ( ( intDiffW == 0 ) && ( intDiffH == 0 ) ) {
				if ( $.browser.msie ) {
					___pause(250);
				} else {
					___pause(100);	
				}
			} 
			$('#lightbox-container-image-data-box').css({ width: intImageWidth });
			$('#lightbox-nav-btnPrev,#lightbox-nav-btnNext').css({ height: intImageHeight + (settings.containerBorderSize * 2) });
		};
		/**
		 * 显示准备形象
		 *
		 */
		function _show_image() {
			$('#lightbox-loading').hide();
			$('#lightbox-image').fadeIn(function() {
				_show_image_data();
				_set_navigation();
			});
			_preload_neighbor_images();
		};
		/**
		 * 显示的图像信息
		 *
		 */
		function _show_image_data() {
			$('#lightbox-container-image-data-box').slideDown('fast');
			$('#lightbox-image-details-caption').hide();
			if ( settings.imageArray[settings.activeImage][1] ) {
				$('#lightbox-image-details-caption').html(settings.imageArray[settings.activeImage][1]).show();
			}
			// 如果我们有一个形象的树立，X图像显示“X”
			if ( settings.imageArray.length > 1 ) {
				$('#lightbox-image-details-currentNumber').html(settings.txtImage + ' ' + ( settings.activeImage + 1 ) + ' ' + settings.txtOf + ' ' + settings.imageArray.length).show();
			}		
		}
		/**
		 * 显示的按钮导航
		 *
		 */
		function _set_navigation() {
			$('#lightbox-nav').show();

			// 而在CSS文件中定义的这个配置，我们在这里定义。它磗需要IE浏览器。刚才。
			$('#lightbox-nav-btnPrev,#lightbox-nav-btnNext').css({ 'background' : 'transparent url(' + settings.imageBlank + ') no-repeat' });
			
			// 显示上一个按钮，如果不是在第一组的形象
			if ( settings.activeImage != 0 ) {
				if ( settings.fixedNavigation ) {
					$('#lightbox-nav-btnPrev').css({ 'background' : 'url(' + settings.imageBtnPrev + ') left 15% no-repeat' })
						.unbind()
						.bind('click',function() {
							settings.activeImage = settings.activeImage - 1;
							_set_image_to_view();
							return false;
						});
				} else {
					//下一步按钮显示的图像按钮
					$('#lightbox-nav-btnPrev').unbind().hover(function() {
						$(this).css({ 'background' : 'url(' + settings.imageBtnPrev + ') left 15% no-repeat' });
					},function() {
						$(this).css({ 'background' : 'transparent url(' + settings.imageBlank + ') no-repeat' });
					}).show().bind('click',function() {
						settings.activeImage = settings.activeImage - 1;
						_set_image_to_view();
						return false;
					});
				}
			}
			
			// 显示下一个按钮，如果不是在设定的最后形象
			if ( settings.activeImage != ( settings.imageArray.length -1 ) ) {
				if ( settings.fixedNavigation ) {
					$('#lightbox-nav-btnNext').css({ 'background' : 'url(' + settings.imageBtnNext + ') right 15% no-repeat' })
						.unbind()
						.bind('click',function() {
							settings.activeImage = settings.activeImage + 1;
							_set_image_to_view();
							return false;
						});
				} else {
					// 下一步按钮显示的图像按钮
					$('#lightbox-nav-btnNext').unbind().hover(function() {
						$(this).css({ 'background' : 'url(' + settings.imageBtnNext + ') right 15% no-repeat' });
					},function() {
						$(this).css({ 'background' : 'transparent url(' + settings.imageBlank + ') no-repeat' });
					}).show().bind('click',function() {
						settings.activeImage = settings.activeImage + 1;
						_set_image_to_view();
						return false;
					});
				}
			}
			// 启用键盘导航
			_enable_keyboard_navigation();
		}
		/**
		 *启用支持键盘导航
		 *
		 */
		function _enable_keyboard_navigation() {
			$(document).keydown(function(objEvent) {
				_keyboard_action(objEvent);
			});
		}
		/**
		 * 禁用支持键盘导航
		 *
		 */
		function _disable_keyboard_navigation() {
			$(document).unbind();
		}
		/**
		 * 执行的键盘操作
		 *
		 */
		function _keyboard_action(objEvent) {
			// To ie
			if ( objEvent == null ) {
				keycode = event.keyCode;
				escapeKey = 27;
			// To Mozilla
			} else {
				keycode = objEvent.keyCode;
				escapeKey = objEvent.DOM_VK_ESCAPE;
			}
			// Get the key in lower case form
			key = String.fromCharCode(keycode).toLowerCase();
			// Verify the keys to close the ligthBox
			if ( ( key == settings.keyToClose ) || ( key == 'x' ) || ( keycode == escapeKey ) ) {
				_finish();
			}
			// Verify the key to show the previous image
			if ( ( key == settings.keyToPrev ) || ( keycode == 37 ) ) {
				// If we磖e not showing the first image, call the previous
				if ( settings.activeImage != 0 ) {
					settings.activeImage = settings.activeImage - 1;
					_set_image_to_view();
					_disable_keyboard_navigation();
				}
			}
			// Verify the key to show the next image
			if ( ( key == settings.keyToNext ) || ( keycode == 39 ) ) {
				// If we磖e not showing the last image, call the next
				if ( settings.activeImage != ( settings.imageArray.length - 1 ) ) {
					settings.activeImage = settings.activeImage + 1;
					_set_image_to_view();
					_disable_keyboard_navigation();
				}
			}
		}
		/**
		 * 预压上一个和下一个图像显示
		 *
		 */
		function _preload_neighbor_images() {
			if ( (settings.imageArray.length -1) > settings.activeImage ) {
				objNext = new Image();
				objNext.src = settings.imageArray[settings.activeImage + 1][0];
			}
			if ( settings.activeImage > 0 ) {
				objPrev = new Image();
				objPrev.src = settings.imageArray[settings.activeImage -1][0];
			}
		}
		/**
		 * 卸下jQuery的灯箱插件的HTML标记
		 *
		 */
		function _finish() {
			$('#jquery-lightbox').remove();
			$('#jquery-overlay').fadeOut(function() { $('#jquery-overlay').remove(); });
			// Show some elements to avoid conflict with overlay in IE. These elements appear above the overlay.
			$('embed, object, select').css({ 'visibility' : 'visible' });
		}
		/**
		 /第三个功能
		* getpagesize的（）由quirksmode.com
		*
		* @返回的数组返回页面的宽度，高度和窗口宽度，高度数组
		 */
		function ___getPageSize() {
			var xScroll, yScroll;
			if (window.innerHeight && window.scrollMaxY) {	
				xScroll = window.innerWidth + window.scrollMaxX;
				yScroll = window.innerHeight + window.scrollMaxY;
			} else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
				xScroll = document.body.scrollWidth;
				yScroll = document.body.scrollHeight;
			} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
				xScroll = document.body.offsetWidth;
				yScroll = document.body.offsetHeight;
			}
			var windowWidth, windowHeight;
			if (self.innerHeight) {	// all except Explorer
				if(document.documentElement.clientWidth){
					windowWidth = document.documentElement.clientWidth; 
				} else {
					windowWidth = self.innerWidth;
				}
				windowHeight = self.innerHeight;
			} else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
				windowWidth = document.documentElement.clientWidth;
				windowHeight = document.documentElement.clientHeight;
			} else if (document.body) { // other Explorers
				windowWidth = document.body.clientWidth;
				windowHeight = document.body.clientHeight;
			}	
			// for small pages with total height less then height of the viewport
			if(yScroll < windowHeight){
				pageHeight = windowHeight;
			} else { 
				pageHeight = yScroll;
			}
			// for small pages with total width less then width of the viewport
			if(xScroll < windowWidth){	
				pageWidth = xScroll;		
			} else {
				pageWidth = windowWidth;
			}
			arrayPageSize = new Array(pageWidth,pageHeight,windowWidth,windowHeight);
			return arrayPageSize;
		};
		/**
		/第三个功能
		* getPageScroll（）由quirksmode.com
		*
		* @返回的数组X，Ÿ页滚动值的数组返回。
		 */
		function ___getPageScroll() {
			var xScroll, yScroll;
			if (self.pageYOffset) {
				yScroll = self.pageYOffset;
				xScroll = self.pageXOffset;
			} else if (document.documentElement && document.documentElement.scrollTop) {	 // Explorer 6 Strict
				yScroll = document.documentElement.scrollTop;
				xScroll = document.documentElement.scrollLeft;
			} else if (document.body) {// all other Explorers
				yScroll = document.body.scrollTop;
				xScroll = document.body.scrollLeft;	
			}
			arrayPageScroll = new Array(xScroll,yScroll);
			return arrayPageScroll;
		};
		 /**
		  * Stop the code execution from a escified time in milisecond
		  *
		  */
		 function ___pause(ms) {
			var date = new Date(); 
			curDate = null;
			do { var curDate = new Date(); }
			while ( curDate - date < ms);
		 };
		// Return the jQuery object for chaining. The unbind method is used to avoid click conflict when the plugin is called more than once
		return this.unbind('click').click(_initialize);
	};
})(jQuery); // Call and execute the function immediately passing the jQuery object