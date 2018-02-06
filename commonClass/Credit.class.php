<?php
//积分奖励配置
class Credit {

    public static $dailyCredit = [
        [description=>"每日积分领取", credit=>"1"],
        [description=>"每日积分领取", credit=>"2"],
        [description=>"每日积分领取", credit=>"3"],
        [description=>"每日积分领取", credit=>"4"],
        [description=>"每日积分领取", credit=>"5"],
    ];

    public static $addCourseQuestion = [description=>"在问答系统中发布问题", credit=>"2"];

    public static $deleteCourseQuestion = [description=>"删除了在问答系统中发布的问题", credit=>"-2"];

    public static $addCourseSolution = [description=>"在问答系统中发布答案", credit=>"2"];

    public static $addBook = [description=>"在资料市场中发布资料", credit=>"2"];

    public static $addCourseRating = [description=>"在课评系统中发布课评", credit=>"10"];


}
?>
