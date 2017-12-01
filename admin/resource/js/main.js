/**
 * 递归获取分类列表
 * @param $obj  将下拉框插入的位置
 * @param url    json接口
 * @param isRecursive    递归次数
 * @param id     根目录id
 * @param level  当前
 * PS:代码是我写的没错, 但不代表下次看到它的时候我自己还能读懂.
 */
function associateSelect($obj, url, isRecursive, fn, id, level) {
    id = id == null ? 0 : id;
    level = level == null ? 0 : level;
    isRecursive = isRecursive == null ? 1 : isRecursive;


    if (level == 0) {
        $select = $("<select name='classId' id='classId' class='input input-select input-size30'><option  value='0'>读取数据中....</option></select>").appendTo($obj);
    } else {
        $span = $('<span class="loading">加载中...</span>').appendTo($obj);
    }
    if (fn) {
        fn($select)
    }

    $.post(url, {'id': id}, function (json) {
        var json = eval("(" + json + ")");
        if (level != 0) {
            $span.remove();
        }
        if (json.code == 1) {
            var result = json.result;

            if (level == 0) {
                $select.find("option").eq(0).html("请选择...");
            } else {
                $select = $("<select name='classId' id='classId' class='input input-select input-size30'><option>请选择...</option></select>").appendTo($obj);
            }

            for (var i = 0; i < result.length; i++) {
                $("<option value='" + result[i]['id'] + "'>" + result[i]['title'] + "</option>").appendTo($select);
            }

            if (level < isRecursive) {
                $select.change(function () {
                    $(this).nextAll("select").remove();
                    var secondId = $(this).find("option:selected").val();
                    if(secondId==0){return false};
                    associateSelect($obj, url, isRecursive, fn, secondId, level + 1);
                });
            }
        }else if(json.code ==0)
        {
            $select.find("option").eq(0).html("");
        }
    });
}


/**
 * 输入框提示
 * @param $element    输入框的jquery对象
 * @param txt       要提示的文字
 */
function inputMessage($element, txt) {
    var type = $element.attr("type");
    if (type == "password") {
        $element.attr("type", "text");
    }
    $element.val(txt);
    $element.focus(function () {
        if (type == "password") {
            $element.attr("type", "password");
        }
        if ($(this).val() == txt) {
            $(this).val("");
        }
    }).blur(function () {
        if ($(this).val() == "") {
            if (type == "password") {
                $element.attr("type", "text");
            }
            $(this).val(txt)
        }
    });
}

// if($("#CourseClass").length>0){inputMessage($("#password"),"密码");}



$(document).ready(function () {

//---------------------------------------------------------------------------------------------------------------------------------------
    /**
     * 删除按钮确认
     */
    $(".deleteBtn").each(function(){
        $(this).click(function(e){
            return confirm('确认删除吗?');
        })
    })

//---------------------------------------------------------------------------------------------------------------------------------------
    /**
     * checkbox全选控制
     * @param $c  控制按钮的jquery对象
     * @param $cBox 需要被全选的jquery对象
     */
    function selectAllCheckBox($c, $cBox) {
        if ($c.length > 0) {
            var tf = true;
            $c.click(function () {

                tf = $(this).prop("checked") ? true : false;
                $cBox.each(function () {
                    $(this).prop("checked", tf);
                })
            })
        }
    }

    selectAllCheckBox($("#cBoxAll"), $(".cBox"));


//---------------------------------------------------------------------------------------------------------------------------------------
    /**
     * 设置下拉菜单默认选中项
     */
    $(".selectDefault").each(function () {
        var val = $(this).attr("defvalue");
        var option = $(this).find("option");
        option.each(function(){
            if($(this).val()==val){
                $(this).attr("selected", "selected");
            }
        })
    })


//---------------------------------------------------------------------------------------------------------------------------------------

    if ($("#classSelectRecursive").length > 0) {
        associateSelect($("#classSelectRecursive"), "/admin/questionnaire/questionnaireController.php?action=getJsonListClass", 1);
    }

    if ($("#classSelect").length > 0) {
        associateSelect($("#classSelect"), "/admin/questionnaire/questionnaireController.php?action=getJsonListClass", 0);
    }


//---------------------------------------------------------------------------------------------------------------------------------------
})


//---------------------------------------------------------------------------------------------------------------------------------------
function PopWindow() {
    if ($("#popWindow").length == 0) {
        $('<div id="popBg"></div><div id="popWindow"><article></article><i id="popCloseBtn" class="icon-font icon-font-close"></i></div>').appendTo('body');
    }
    var _this = this;
    this.speed = 600;
    this.$popBg = $("#popBg");
    this.$popWindow = $("#popWindow");
    this.$popArticle = $("#popWindow article");
    this.$popCloseBtn = $("#popCloseBtn");
    this.$window = $(window);

    this.y = 0;
    this.documentHeight = $(document).height();
    this.time =null;
    $(window).scroll(function(){
        clearTimeout(_this.time);
        _this.time = setTimeout(function(){_this.calculate();},200);

    });



}
//显示弹窗动画
PopWindow.prototype.show = function () {
    var _this = this;
    this.$popBg.height(_this.documentHeight).show();
    this.calculate();
    this.$popWindow.show().animate({'top':_this.y+'px'},_this.speed);
}
//关闭弹窗动画
PopWindow.prototype.close = function (fn) {
    if (fn) {
        fn();
    }
    var _this = this;
    this.$popBg.hide();
   // this.$popWindow.hide();
    this.$popWindow.animate({'top':'-700px'},_this.speed);
    this.$popCloseBtn.unbind('click');
    this.$popArticle.html("");
}
PopWindow.prototype.calculate = function(){
    var _this = this;
    this.y = Math.floor(_this.$window.height()*0.15 + _this.$window.scrollTop());
}
//调用弹窗方法 showText
PopWindow.prototype.showText = function (text) {
    this.$popArticle.html(text);
    this.show();
}
//调用弹窗方法 showLoadFile
PopWindow.prototype.showLoadFile = function (url, fn) {
    var _this = this;
    this.$popArticle.load(url);
    this.show();
    this.$popCloseBtn.click(function () {
        _this.close(fn)
    })
}

// $a = new PopWindow();
// $a.showLoadFile("/admin/questionnaire/snippet/formAddClass.html");



