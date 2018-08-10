<?php
namespace admin\institution;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class InstitutionModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = "institution";
    }

    // 机构类别
    const TYPE = ["university"=>1, "highSchool"=>2];

    /**
     * 添加一个机构
     * @param $title
     * @param $type
     * @param $coordinate
     * @param $termStartDates
     * @param $termEndDates
     * @return bool
     */
    public function addInstitution($title, $type, $coordinate, $termStartDates, $termEndDates)
    {
        $arr = [];
        $arr["title"] = $title;
        $arr["type"] = InstitutionModel::TYPE[$type];
        $arr["coordinate"] = $coordinate;
        $arr["term_start_dates"] = $termStartDates;
        $arr["term_end_dates"] = $termEndDates;
        return $this->addRow($this->table, $arr);
    }

    /**
     * 查询一个机构,返回一维键值数组
     *
     * @param $id
     * @return \一维关联数组
     */
    public function getInstitution($id)
    {
        $id = intval($id);
        $sql = "SELECT * from {$this->table} WHERE id = {$id}";
        $result = $this->sqltool->getRowBySql($sql);
        return $result;
    }

    /**
     * 获取全部机构信息
     * @param int $pageSize
     * @return array
     */
    public function getListOfInstitution($pageSize=20) {
      $sql = "SELECT * FROM {$this->table}";
      $countSql = "SELECT COUNT(*) FROM {$this->table}";
      return $this->getListWithPage($this->table, $sql, $countSql, $pageSize);
    }

    /**
     * 更改一个机构信息
     * @param $id
     * @param $title
     * @return bool
     */
    public function updateInstitution($id, $title, $type, $coordinate, $termStartDates, $termEndDates)
    {
        $arr = [];
        $arr["title"] = $title;
        $arr["type"] = $type;
        $arr["coordinate"] = $coordinate;
        $arr["term_start_dates"] = $termStartDates;
        $arr["term_end_dates"] = $termEndDates;
        return $this->updateRowById($this->table, $id, $arr);
    }

    /**
     * 删除一个机构
     * @param $id
     * @return bool|\mysqli_result
     */
    public function deleteInstitution($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = {$id}";
        return $this->sqltool->query($sql);
    }

    /**
     * 验证学校ID是否与当前登录用户的学校ID一致
     * @param int $id 学校ID
     * @return bool
     */
    public function authenticateInstitutionId($id) {
        $currentUser = new UserModel();
        if (!$currentUser->userId) {
            $this->errorMsg = "请先登录";
            return false;
        }
        if (!$currentUser->isUserHasAuthority("ADMIN") || !$currentUser->institutionId || intval($currentUser->institutionId) !== intval($id)) {
            $this->errorMsg = "学校ID不匹配";
            return false;
        }
        return true;
    }

    /**
     * 解析并获取当前学期截止日期
     * @param string $termEndingDates 学期所有截止日期 format example: '01-03,04-22,09-07' 顺序可有可无, 单数必须要有leading 0
     * @return string
     */
    public static function getCurrentTermEndingDate($termEndingDates) {
        $endingDates = explode(',', $termEndingDates);
        asort($endingDates);
        $currentDate = date('m-d');
        $result = "";
        foreach ($endingDates as $d) {
            if ($currentDate < $d) {
                $result = $d;
                break;
            }
        }
        if ($result === "") {
            $result = $endingDates[1];
        }
        return $result;
    }
}

abstract class InstitutionError {
    const INSTITUTION_ID_NOT_EXIST = "Institution Id does not exist";
}


?>
