/**
* 加载科目父类列表, 复制粘贴下面html代码到form页面， course_code_parent_id为列表父类ID，course_code_child_id为列表子类ID，用于自动选择已选项
* <div id="courseCodeDiv" data-parent-id="<?php echo BasicTool::get('course_code_parent_id') ?>" data-child-id="<?php echo BasicTool::get('course_code_child_id') ?>"></div>
* @param $obj jquery object courseCodeDiv
*/
function loadCourseCodeParentSelect($obj) {
    $select = $("<div><label>科目分类<i>*</i></label><select name='course_code_parent_id' id='courseCodeParentSelect' class='input input-select input-size30' defvalue=\"<?php echo BasicTool::get('course_code_parent_id') ?>\"><option value=\"-1\">请选择...</option></select></div><div id='courseCodeChildDiv' style=\"display:none;\"><label>科目子分类<i>*</i></label><select name='course_code_child_id' id='courseCodeChildSelect' class='input input-select input-size30' defvalue=\"<?php echo BasicTool::get('course_code_child_id') ?>\"><option value=\"-1\">请选择...</option></select></div>").appendTo($obj);

    $('#courseCodeParentSelect').on('change', function() {
        if (this.value>0) {
            $("#courseCodeChildDiv").show();
            updateChildCourseCodeByParentId(this.value);
        } else {
            $("#courseCodeChildDiv").hide();
        }
    })

    $.ajax({
        url: "/admin/courseCode/courseCodeController.php?action=getListOfParentCourseCodeWithJson",
        type:"POST",
        processData: false,
        contentType: false,
        dataType:"json",
        success:function(json){
            if (json.code == 1) {
                var options = "";
                json.result.forEach((obj)=>{
                    options += `<option value="`+obj.id+`">`+obj.title+`</option>`;
                })
                $("#courseCodeParentSelect").append(options);
                var parentId = $obj.attr("data-parent-id");
                var childId = $obj.attr("data-child-id");
                if (parentId != null && childId != null) {
                    $("#courseCodeParentSelect").val(parentId);
                    $("#courseCodeChildDiv").show();
                    updateChildCourseCodeByParentId(parentId,(success)=>{
                        if(success) {
                            $('#courseCodeChildSelect').val(childId);
                        }
                    });

                }
            }else {
                console.error("Fail to get parent course code data.");
            }
        }
    });
}

/**
* 通过科目父类ID加载科目子类列表
* @param id 科目父类ID
* @param cb callback function，true为成功，false为失败
*/
function updateChildCourseCodeByParentId(id,cb) {
    $('#courseCodeChildSelect').find('option:not(:first)').remove();
    $.ajax({
        url: "/admin/courseCode/courseCodeController.php?action=getListOfChildCourseCodeByParentIdWithJson&course_code_parent_id="+id,
        type:"POST",
        processData: false,
        contentType: false,
        dataType:"json",
        success:function(json){
            if (json.code == 1) {
                var options = "";
                json.result.forEach((obj)=>{
                    options += `<option value="`+obj.id+`">`+obj.title+`</option>`;
                })
                $("#courseCodeChildSelect").append(options);
                cb(true);
            }else {
                console.error("Fail to get child course code data.");
                cb(false);
            }
        }
    });
}


$(document).ready(function () {
    if ($("#courseCodeDiv").length > 0) {
        loadCourseCodeParentSelect($("#courseCodeDiv"));
    }

});
