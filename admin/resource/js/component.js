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
        dataType:"json"
    }).done(function(json){
        if (json.code == 1) {
            var options = "";
            json.result.forEach((obj)=>{
                options += `<option value="`+obj.id+`">`+obj.title+`</option>`;
            })
            $("#courseCodeParentSelect").append(options);
            var parentId = $obj.attr("data-parent-id");
            if (parentId != null) {
                $("#courseCodeParentSelect").val(parentId);
                $("#courseCodeChildDiv").show();
                var childId = $obj.attr("data-child-id");
                if (childId) {
                    // first time launch, clean the data-child-id attr value.
                    $obj.attr("data-child-id", undefined);
                }
                updateChildCourseCodeByParentId(parentId, childId);
            }
        }else {
            console.error("Fail to get parent course code data.");
        }
    }).fail(function(jqXHR){
        console.error("Fail to get parent course code data. " + jqXHR);
    });

    /**
     * 通过科目父类ID加载科目子类列表
     * @param id 科目父类ID
     * @param childId 科目子类ID
     */
    function updateChildCourseCodeByParentId(id,childId="-1") {
        $('#courseCodeChildSelect').find('option:not(:first)').remove();
        $.ajax({
            url: "/admin/courseCode/courseCodeController.php?action=getListOfChildCourseCodeByParentIdWithJson&course_code_parent_id="+id,
            type:"POST",
            processData: false,
            contentType: false,
            dataType:"json"
        }).done(function(json){
            if (json.code == 1) {
                var options = "";
                json.result.forEach((obj)=>{
                    options += `<option value="`+obj.id+`">`+obj.title+`</option>`;
                })
                $("#courseCodeChildSelect").append(options).val(childId);
            }else {
                console.error("Fail to get child course code data.");
            }
        }).fail(function(jqXHR){
            console.error("Fail to get child course code data. " + jqXHR);
        });
    }
}

/**
 * 注册course code 搜索框组件功能
 * @param $componentObj
 *
 * 使用方法: 复制下面代码到所需页面
 <div id="courseCodeInputComponent">
 <div>
 <label>课程类别 (例如:ADMS)</label>
 <input id="parentInput" class="input" type="text" list="parentCodeList" name="parentCode" value="">
 <datalist id="parentCodeList"></datalist>
 </div>
 <div>
 <label>课程代码 (例如:1000)</label>
 <input id="childInput" class="input" type="text" list="childCodeCodeList" name="childCode" value="">
 <datalist id="childCodeCodeList"></datalist>
 </div>
 </div>
 */
function registerCourseCodeInputComponent($componentObj) {
    let url = "/admin/courseCode/courseCodeController.php?action=getListOfParentCourseCodeWithJson";
    let $inputObj = $componentObj.find("#parentInput");
    let parentArr = [];
    fetch(url)
        .then(response => response.json())
        .then(json => {
            if (json.code == 1) {
                parentArr = json.result;
                let $dataListOption = "";
                json.result.forEach(item => {
                    $dataListOption += `<option label="" value="${item.title}" />`;
                })
                $componentObj.find("#parentCodeList").html($dataListOption);
            }
        })
        .catch(error => {
            alert(error + ".  出错位置: Course Coude Input Component");
        })

    $inputObj.on('input', () => {
        let val = $inputObj.val();
        $inputObj.val(val.toUpperCase());
    })

    $inputObj.blur(() => {
        $componentObj.find("#childCodeCodeList").html("");
        let parentResult = $inputObj.val();
        if (parentResult == "") return false;
        let selectedItem = parentArr.filter((item) => item.title == parentResult);
        if(selectedItem.length==0) return false;
        console.log(selectedItem);
        let url = "/admin/courseCode/courseCodeController.php?action=getListOfChildCourseCodeByParentIdWithJson&course_code_parent_id="+selectedItem[0].id;
        fetch(url)
            .then(response => response.json())
            .then(json => {
                if (json.code == 1) {
                    let $dataListOption = "";
                    json.result.forEach(item => {
                        $dataListOption += `<option label="" value="${item.title}" />`;
                    })
                    $componentObj.find("#childCodeCodeList").html($dataListOption);
                }
            })
            .catch(error => {
                alert(error + ".  出错位置: Course Coude Input Component");
            })
    })
}

/**
 * 注册professor 搜索框组件功能
 * @param $componentObj
 *
 * 使用方法: 复制下面代码到所需页面
 *
 <div id="professorInputComponent">
 <div>
 <label>教授</label>
 <input class="input" type="text" list="professorList" name="professorName" />
 <datalist id="professorList"></datalist>
 </div>
 </div>
 */
function registerProfessorInputComponent($componentObj) {
    url = "/admin/professor/professorController.php?action=getListOfProfessorWithJson&query=";

    let $inputObj = $componentObj.find("input");
    let queryWord = "";
    $inputObj.on('input', () => {
        queryWord = $inputObj.val();
        if (queryWord.length <= 6) {
            fetch(url + queryWord)
                .then(response => response.json())
                .then(json => {
                    if (json.code == 1) {
                        let $dataListOption = "";
                        let data = "";
                        json.result.forEach(item => {
                            data = `${item.firstname} ${item.lastname}`;
                            $dataListOption += `<option label="" value="${data}" />`;
                        })
                        $componentObj.find("datalist").html($dataListOption);
                        $inputObj.unbind('blur').blur(() => {
                            console.log(1);
                        })
                    }
                })
                .catch(error => {
                    alert(error + ".  出错位置: Course Coude Input Component");
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
