<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$professorModel = new admin\professor\ProfessorModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));

//============ Function with JSON ===============//

/**
 * JSON -  获取指定ID的教授
 * @param id 教授ID
 * http://www.atyorku.ca/admin/professor/professorController.php?action=getProfessorByIdWithJson&id=3
 */
function getProfessorByIdWithJson() {
    global $professorModel;
    try {
        $id = BasicTool::get("id","需要提供 Professor ID");
        $result = $professorModel->getProfessorById($id);
        if ($result) {
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "未找到该ID对应的教授");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * 通过提供的教授名称来获取一个教授ID， 如果没有该名称，新建一个教授返回ID
 * http://www.atyorku.ca/admin/professor/professorController.php?action=getProfessorIdByFullNameWithJson
 */
function getProfessorIdByFullNameWithJson() {
    getProfessorIdByFullName("json");
}

/**
 * JSON -  获取教授列表
 * @param query 模糊搜素关键词 (optional)
 * http://www.atyorku.ca/admin/professor/professorController.php?action=getListOfProfessorWithJson&query=andy
 */
function getListOfProfessorWithJson() {
    global $professorModel;
    try {
        $result = $professorModel->getListOfProfessor(BasicTool::get("query"),BasicTool::get("pageSize"));
        if ($result) {
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "获取教授列表失败");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * JSON -  教授热度 +1
 * @param id 教授 ID
 * http://www.atyorku.ca/admin/professor/professorController.php?action=incrementProfessorViewCountByIdWithJson&id=3
 */
function incrementProfessorViewCountByIdWithJson() {
    global $professorModel;
    try {
        $id = BasicTool::get("id","需要提供 Professor ID");
        $result = $professorModel->incrementProfessorViewCountById($id);
        if ($result) {
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "添加教授热度失败");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

// /**
// * JSON - 添加 Professor
// * @param name 要添加的 professor name
// * http://www.atyorku.ca/admin/professor/professorController.php?action=addProfessorWithJson&name=prof_name
// */
// public function addProfessorWithJson() {
//     modifyProfessor("json");
// }
//
// /**
// * JSON - 删除 Professor
// * @param id 要删除的 Professor ID
// * http://www.atyorku.ca/admin/professor/professorController.php?action=deleteProfessorWithJson&id=3
// */
// public function deleteProfessorWithJson() {
//     deleteProfessor("json");
// }

// =============== End Function with JSON ================= //

/**
* 修改或添加一个Course Code
* @param flag add | update
* @param title 新的或修改的Course Code 名称
* @param parent_id 父类科目ID
* @param id 需要修改的科目ID (flag=="update"时必填)
*/
function modifyProfessor($echoType = "normal") {
    global $professorModel;
    try {
        $flag = BasicTool::post("flag");
        $firstname = BasicTool::post("firstname","需要提供教授名");
        $lastname = BasicTool::post("lastname","需要提供教授姓");
        checkAuthority();
        $result = null;
        if ($flag == "add") {
            $result = $professorModel->addProfessor($firstname, $lastname);
            if ($result) {
                if ($echoType == "normal") {
                    BasicTool::echoMessage("添加成功","/admin/professor/index.php?listProfessor");
                } else {
                    BasicTool::echoJson(1, "添加成功", $result);
                }
            } else {
                BasicTool::throwException("添加教授失败。");
            }
        } else if ($flag == "update") {
            $id = BasicTool::post("id","需要提供要修改的 Professor ID");
            $result = $professorModel->updateProfessor($id, $firstname, $lastname);
            if ($result) {
                if ($echoType == "normal") {
                    BasicTool::echoMessage("修改成功","/admin/professor/index.php?listProfessor");
                } else {
                    BasicTool::echoJson(1, "修改成功", $result);
                }
            } else {
                BasicTool::throwException("修改教授失败。");
            }
        }
    } catch(Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }

}

/**
* 删除一个或多个科目
* @param id 数字或array 要删除的id
*/
function deleteProfessor($echoType = "normal") {
    global $professorModel;
    global $currentUser;
    try {
        checkAuthority();
        $id = BasicTool::post('id') or BasicTool::throwException("请指定被删除教授ID");
        $i = 0;
        if (is_array($id)) {
            foreach ($id as $v) {
                $i++;
                $professorModel->deleteProfessorById($v) or BasicTool::throwException("删除多个教授失败");
            }
        } else {
            $i++;
            $professorModel->deleteProfessorById($id) or BasicTool::throwException("删除1个教授失败");
        }
        if ($echoType == "normal") {
            BasicTool::echoMessage("成功删除{$i}个教授", $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(1, "成功删除{$i}个教授");
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}

function getProfessorIdByFullName($echoType = "normal") {
    global $professorModel;
    try {
        $profName = BasicTool::post('name') or BasicTool::throwException("教授名称不能为空");
        $id = $professorModel->getProfessorIdByFullName($profName);
        if ($id) {
            if ($echoType == "normal") {
                BasicTool::echoMessage("成功", $_SERVER['HTTP_REFERER']);
            } else {
                BasicTool::echoJson(1, "成功", $id);
            }
        } else {
            BasicTool::throwException($professorModel->errorMsg);
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}

function checkAuthority() {
    global $currentUser;
    if (!($currentUser->isUserHasAuthority('ADMIN'))) {
        BasicTool::throwException("无权限操作");
    }
}
