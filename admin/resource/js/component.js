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
    let url = "/admin/professor/professorController.php?action=getListOfProfessorWithJson&query=";
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

function registerCommentComponent($componentObj){
    let $commentObj = $componentObj.find("textarea");
    let $submitButton = $componentObj.find("#commentButton");
    let $commentListContainer = $("#commentListContainer");
    let $inputContainer = $('<div class="replyBox"><input type="text"><div class="postButton">回复</div></div>');
    let $inputObj = $inputContainer.find("input");
    let $replyButton = $inputContainer.find(".postButton");
    let $loadMoreButton = $componentObj.find("#loadMoreButton");
    let loadMoreButtonText =$loadMoreButton.html();
    let $commentCountNumber = $componentObj.find("#commentCountNumber");

    //post
    let section_name = $componentObj.attr("data-category");     //产品-表名
    let section_id = $componentObj.attr("data-production-id");  //产品-ID
    let receiver_id = "";                                       //接受者ID
    let parent_id = 0;                                         //评论父级ID，默认为0
    let comment = "";
    let page =1;
    let isLoading = false;
    let isAblePost = true;
    let isViewAllModel = false;
    //其他参数
    let $previousReplyButton = null;
    //初始化
    if($componentObj.attr('data-category')=="all"){
        $commentObj.attr({'disabled':true,'placeholder':'管理员模式已经开启，此模式下只能删除，不能添加评论'});
        isViewAllModel = true;
    }
    //获取留言
    loadingData();

    //增加留言
    $submitButton.click(function(){
        if(!isAblePost){
            alert("请先登录AtYorkU App");
            return false;
        }
        receiver_id = $componentObj.attr("data-receiver-id");
        parent_id = 0;
        comment = $commentObj.val();
        if(!comment){
            alert("说点什么吧...");
            return false;
        }
        addComment($(this),(json)=>{
            let row = json.result;
            let $comment = $(`<div class="commentContainer"><div class="parentComment"><div class="avatar" style="background-image: url('${row.img}')"></div><div class="comment"><div class="l1"><span>${row.alias}</span>：${row.comment}</div><div class="l2"><span>${row.time}</span><span class="functionBtn"><em data-comment-id="${row.id}" class="deleteButton">删除</em></span></div></div></div><div class="childComment"></div></div>`)
            registerDeleteButton($comment.find(".deleteButton"));
            $commentListContainer.prepend($comment);
            $commentObj.val("");
        });
    })

    //回复留言
    $replyButton.click(function(){
        if(!isAblePost){
            alert("请先登录AtYorkU App");
            return false;
        }
        comment = $inputObj.val();
        if(!comment){
            alert("说点什么吧...");
            return false;
        }
        let $childComment = $(this).parents(".commentContainer").find(".childComment");
        addComment($(this),(json)=>{
            let childRow = json.result;
            let $comment =  $(`<div class="comment"><div class="l1"><span>${childRow.alias}</span>：${childRow.comment}</div><div class="l2"><span>${childRow.time}</span></div></div>`);
            $childComment.append($comment);
        });
    })

    //加载更多
    $loadMoreButton.click(()=>{
        loadingData();
    })

    //增加留言
    function addComment($button,success){
        if(isViewAllModel){
            alert("管理员模式已经开启，此模式下只能删除，不能添加评论");
            return false;
        }
        if(isLoading || !isAblePost) return false;
        isLoading = true;
        let url = "/admin/comment/commentController.php?action=addCommentWithJson";
        let buttonText = $button.html();
        $button.addClass('loading').html("发布中...");
        let options = {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: `section_name=${section_name}&section_id=${section_id}&receiver_id=${receiver_id}&parent_id=${parent_id}&comment=${comment}&`,
            credentials: 'same-origin',
        }
        fetch(url, options)
            .then(response => response.json())
            .then(json=>{
                if(json.code==1){
                    success(json);
                    $previousReplyButton && $previousReplyButton.html("回复");
                    $inputContainer.hide();
                    $inputObj.val("");
                    $button.html(buttonText);
                    $commentCountNumber.html(parseInt($commentCountNumber.html())+1)
                }else{
                    alert(json.message);
                }
                $button.html(buttonText).removeClass('loading');
                isLoading=false;
            })
            .catch(error=>alert(error))
    }
    //加载评论数据
    function loadingData(){
        let url = `/admin/comment/commentController.php?action=getCommentsWithJson&section_name=${section_name}&section_id=${section_id}&page=${page}`;
        $loadMoreButton.html("评论加载中...");
        fetch(url,{credentials: 'same-origin'}).then(response => response.json())
            .then(json=>{
                if(json.code == 1){
                    json.result.forEach((row)=>{
                        let rowId= row.id;
                        //插入父级
                        let $commentContainer = $(`<div class="commentContainer"><div class="parentComment"><div class="avatar" style="background-image: url('${row.img}')"></div><div class="comment"><div class="l1"><span>${row.alias}</span>：${row.comment}</div><div class="l2"><span>${row.time}</span><span class="functionBtn"><em data-parent-id="${rowId}" data-uid="${row.uid}" class="replyButton">回复</em><em data-comment-id="${row.id}" class="deleteButton">删除</em></span></div></div></div><div class="childComment"></div></div>`);
                        if(json.secondResult.uid != row.uid){
                            json.secondResult.isAdmin == 1||$commentContainer.find(".deleteButton").remove();
                        }else{
                            $commentContainer.find(".replyButton").remove();
                        }
                        $commentContainer.appendTo($commentListContainer);
                        //插入子集
                        if(row.child_comment){
                            $childContainer = $commentContainer.find(".childComment");
                            row.child_comment.forEach((childRow)=>{
                                let $childComment = $(`<div class="comment"><div class="l1"><span>${childRow.alias}</span>：${childRow.comment}</div><div class="l2"><span>${childRow.time}</span><span class="functionBtn"><em data-parent-id="${rowId}" data-uid="${childRow.uid}" class="replyButton">回复</em><em data-comment-id="${childRow.id}" class="deleteButton">删除</em></span></div></div>`);
                                if(json.secondResult.uid != childRow.uid){
                                    json.secondResult.isAdmin == 1||$childComment.find(".deleteButton").remove();
                                }else{
                                    $childComment.find(".replyButton").remove();
                                }
                                $childComment.appendTo($childContainer)
                            })

                        }
                    })
                    //注册回复留言事件
                    $componentObj.find(".replyButton").each(function(){
                        registerReplyButton($(this));
                    })
                    $componentObj.find(".deleteButton").each(function(){
                        registerDeleteButton($(this));
                    })
                    page++;
                    if(page < json.secondResult.totalPage) $loadMoreButton.show();
                }else{
                    $loadMoreButton.remove();
                }
                if(!json.secondResult.uid){
                    $commentListContainer.find(".replyButton").each(function () {
                        $(this).remove();
                    })
                    $commentObj.attr({'disabled':true,'placeholder':"请登录后发表评论"});
                    isAblePost = false;
                }
                $loadMoreButton.html(loadMoreButtonText);
            })
            .catch(error=>alert(error))
    }

    //注册回复按钮
    function registerReplyButton($button){
        $button.click(function(){
            receiver_id = $(this).attr("data-uid");
            parent_id = $(this).attr("data-parent-id");
            if($previousReplyButton!=null && $previousReplyButton[0].isEqualNode($(this)[0])){
                if($(this).html() == "回复"){
                    $(this).html("收起");
                    $(this).parent().append($inputContainer);
                    $inputContainer.show();
                }else{
                    $(this).html("回复");
                    $inputContainer.hide();
                }
            }else{
                $(this).parent().append($inputContainer);
                $inputContainer.show();
                $(this).html("收起");
                $previousReplyButton!=null && $previousReplyButton.html("回复");
            }
            $previousReplyButton = $(this);
        })
    }
    //注册删除按钮
    function registerDeleteButton($button){
        $button.click(function(){
            if(!confirm("确定删除吗?")) return false;
            let commentId = $button.attr("data-comment-id");
            let url = `/admin/comment/commentController.php?action=deleteCommentByIdWithJson&commentId=${commentId}`;
            fetch(url, {credentials: 'same-origin'})
                .then(response => response.json())
                .then(json=>{
                    if(json.code==1){
                        if($button.parent().parent().parent().parent().hasClass("parentComment")){
                            $button.parents(".commentContainer").remove();
                        }else{
                            $button.parents(".comment").remove();
                        }
                        $commentCountNumber.html(parseInt($commentCountNumber.html())-1)
                    }else{
                        alert(json.message);
                    }
                })
                .catch(error=>alert(error))
        })
    }
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

    //课程分类填写带搜索框
    if ($("#commentComponent").length > 0) {
        registerCommentComponent($("#commentComponent"));
    }

});
