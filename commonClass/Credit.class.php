<?php
//积分奖励配置
class Credit {

    public static $dailyCredit = [
        [description=>"连续领取积分第1天", credit=>"1"],
        [description=>"连续领取积分第2天", credit=>"2"],
        [description=>"连续领取积分第3天", credit=>"3"],
        [description=>"连续领取积分第4天", credit=>"4"],
        [description=>"连续领取积分>=5天", credit=>"5"],
    ];

    public static $addCourseQuestion = [description=>"在问答系统中发布问题", credit=>"6"];

    public static $deleteCourseQuestion = [description=>"删除了在问答系统中发布的问题", credit=>"-6"];

    public static $addCourseSolution = [description=>"在问答系统中发布答案", credit=>"6"];

    public static $addBook = [description=>"在资料市场中发布资料", credit=>"6"];

    public static $addCourseRating = [
        3=>[description=>"你的课评被评为:有用课评", credit=>"6"],
        5=>[description=>"你的课评被评为:优秀课评", credit=>"10"],
    ];

}
?>
