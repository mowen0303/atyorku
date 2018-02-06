<?php
//积分奖励配置
class Credit {
    public static $addCourseQuestion = [
        description=>"在问答系统中发布问题",
        credit=>"4"
    ];

    public static $deleteCourseQuestion = [
        description=>"删除了在问答系统中发布的问题",
        credit=>"-4"
    ];

    public static $addBook = [
        description=>"在资料市场中发布资料",
        credit=>"2"
    ];

    public static $addCourseRating = [
        description=>"在课评系统中发布课评",
        credit=>"10"
    ];

    public static $addCourseSolution = [
        description=>"在问答系统中发布答案",
        credit=>"4"
    ];
}
?>
