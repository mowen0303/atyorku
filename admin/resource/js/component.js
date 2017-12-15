/**
 * 加载科目父类列表, 复制粘贴下面html代码到form页面， course_code_parent_id为列表父类ID，course_code_child_id为列表子类ID，用于自动选择已选项
 * <div id="courseCodeDiv" data-parent-id="<?php echo BasicTool::get('course_code_parent_id') ?>" data-child-id="<?php echo BasicTool::get('course_code_child_id') ?>"></div>
 * @param $obj jquery object courseCodeDiv
 */
function loadCourseCodeParentSelect($obj) {
    $select = $("<div><label>科目分类<i>*</i></label><select name='course_code_parent_id' id='courseCodeParentSelect' class='input input-select input-size30' defvalue=\"<?php echo BasicTool::get('course_code_parent_id') ?>\"><option value=\"-1\">请选择...</option></select></div><div id='courseCodeChildDiv' style=\"display:none;\"><label>科目子分类<i>*</i></label><select name='course_code_child_id' id='courseCodeChildSelect' class='input input-select input-size30' defvalue=\"<?php echo BasicTool::get('course_code_child_id') ?>\"><option value=\"-1\">请选择...</option></select></div>").appendTo($obj);

    $('#courseCodeParentSelect').on('change', function () {
        if (this.value > 0) {
            $("#courseCodeChildDiv").show();
            updateChildCourseCodeByParentId(this.value);
        } else {
            $("#courseCodeChildDiv").hide();
        }
    })

    $.ajax({
        url: "/admin/courseCode/courseCodeController.php?action=getListOfParentCourseCodeWithJson",
        type: "POST",
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (json) {
            if (json.code == 1) {
                var options = "";
                json.result.forEach((obj) => {
                    options += `<option value="` + obj.id + `">` + obj.title + `</option>`;
                })
                $("#courseCodeParentSelect").append(options);
                var parentId = $obj.attr("data-parent-id");
                var childId = $obj.attr("data-child-id");
                if (parentId != null && childId != null) {
                    $("#courseCodeParentSelect").val(parentId);
                    $("#courseCodeChildDiv").show();
                    updateChildCourseCodeByParentId(parentId, (success) => {
                        if (success) {
                            $('#courseCodeChildSelect').val(childId);
                        }
                    });

                }
            } else {
                console.error("Fail to get parent course code data.");
            }
        }
    });

    /**
     * 通过科目父类ID加载科目子类列表
     * @param id 科目父类ID
     * @param cb callback function，true为成功，false为失败
     */
    function updateChildCourseCodeByParentId(id, cb) {
        $('#courseCodeChildSelect').find('option:not(:first)').remove();
        $.ajax({
            url: "/admin/courseCode/courseCodeController.php?action=getListOfChildCourseCodeByParentIdWithJson&course_code_parent_id=" + id,
            type: "POST",
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (json) {
                if (json.code == 1) {
                    var options = "";
                    json.result.forEach((obj) => {
                        options += `<option value="` + obj.id + `">` + obj.title + `</option>`;
                    })
                    $("#courseCodeChildSelect").append(options);
                    cb(true);
                } else {
                    console.error("Fail to get child course code data.");
                    cb(false);
                }
            }
        });
    }
}


function registerCourseCodeInputComponent($obj) {
    let url = "/admin/courseCode/courseCodeController.php?action=getListOfParentCourseCodeWithJson";


}

function registerProfessorInputComponent($componentObj){
    url ="/admin/professor/professorController.php?action=getListOfProfessorWithJson&query=";

    $inputObj = $componentObj.find("input");
    queryWord = "";
    $inputObj.on('input',()=>{
        queryWord = $inputObj.val();
        if(queryWord.length<=3){
            fetch(url+queryWord)
            .then(response => response.json())
            .then(json => {
                if (json.code == 1) {
                    let dataListOption = "";
                    json.result.forEach(item=>{
                        dataListOption+=`<option label="" value="${item.firstname} ${item.middlename} ${item.lastname}" />`;
                    })
                    $componentObj.find("datalist").html(dataListOption);
                    $inputObj.unbind('blur').blur(()=>{
                        console.log(1);
                    })
                }
            })
            .catch(error=>{
                alert(error+".  出错位置: Course Coude Input Component");
            })
        }
    })

}




$(document).ready(function () {
    //课程分类select
    if ($("#courseCodeDiv").length > 0) {
        loadCourseCodeParentSelect($("#courseCodeDiv"));
    }

    //课程分类填写带搜索框
    if ($("#courseCodeInputComponent").length > 0) {
        registerCourseCodeInputComponent($("#courseCodeInputComponent"));
    }

    //课程分类填写带搜索框
    if ($("#courseCodeInputComponent").length > 0) {
        registerProfessorInputComponent($("#professorInputComponent"));
    }

});
