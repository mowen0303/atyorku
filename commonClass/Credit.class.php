<?php
//积分奖励配置
class Credit {

    public static $dailyCredit = [
        [description=>"连续领取第1天", credit=>"+0.5"],
        [description=>"连续领取第2天", credit=>"+0.6"],
        [description=>"连续领取第3天", credit=>"+0.7"],
        [description=>"连续领取第4天", credit=>"+1.0"],
        [description=>"连续领取第5天及以上", credit=>"+1.5"],
    ];

    public static $addCourseQuestion = [description=>"在问答系统中发布问题", credit=>"1"];

    public static $deleteCourseQuestion = [description=>"删除了在问答系统中发布的问题", credit=>"1"];

    public static $addCourseSolution = [description=>"在问答系统中发布答案", credit=>"1"];

    public static $addBook = [description=>"在资料市场中发布资料", credit=>"1"];

    public static $addCourseRating = [
        3=>[description=>"你的课评被评为:有用课评", credit=>"+1"],
        5=>[description=>"你的课评被评为:优秀课评", credit=>"+3"],
    ];

}
?>
