<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/templete/_mainHead.php";
?>
    <header class="topBox">
        <h1>首页</h1>
    </header>

    <article class="mainBox">
        <header>
            <h2>账号id</h2>
        </header>
        <section>
            <p>约克头条 2187, news1@atyorku.ca 654321</p>
            <p>吃遍多伦多 2364, news2@atyorku.ca 654321</p>
            <p>跟我一起旅行 2635, news3@atyorku.ca 654321</p>
            <p>每周话题 2374, news4@atyorku.ca 654321</p>
            <p>GPA+ 2278</p>
            <p>Mars书铺 2085</p>
            <p>智囊团：2105，2106，2107，2108，2109，2110，2111，2112，2113，2114</p>
        </section>
    </article>
    <article class="mainBox">
        <header>
            <h2>其他账号</h2>
        </header>
        <section>
            <p>百度网盘：mars95279527 / Abc159357!</p>
        </section>
    </article>

    <article class="mainBox">
        <header>
            <h2>功能组件</h2>
        </header>
        <section class="formBox">
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
            <div id="professorInputComponent">
                <div>
                    <label>教授</label>
                    <input class="input" type="text" list="professorList" name="professorName" />
                    <datalist id="professorList"></datalist>
                </div>
            </div>
        </section>
    </article>

    <article class="mainBox">
        <header>
            <h2>安全手册 - 用户权限配置参考列表</h2>
        </header>
        <section>
            <p>判断用户权限: $currentUser->isUserHasAuthority('FORUM_ADD') or
                BasicTool::throwException($currentUser->errorMsg);</p>
            <p>判断管理员权限: $currentUser->isUserHasAuthority('ADMIN') &&
                $currentUser->isUserHasAuthority('FORUM_DELETE');</p>
            <?php
            global $_AUT;
            foreach ($_AUT as $k => $v) {
                ?>
                <P><input name="authority[]" type="checkbox"><?php echo $k ?></P>
                <?php
            }
            ?>
        </section>
    </article>

    <article class="mainBox">
        <header>
            <h2>开发规范手册</h2>
        </header>
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="20%">数据库</th>
                    <th>说明</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>建表命名规范</td>
                    <td>每一张表都有带有一个字段 : id , 并将其设为主键 , 自增长</td>
                </tr>
                </tbody>
                <thead>
                <tr>
                    <th width="20%">开发规范</th>
                    <th>说明</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>index.php</td>
                    <td><p>index.php?s=XXX XXX代表要载入的代码片段</p>
                        <p>xxxController.php?action=XXX 用户提交form表单操作时, XXX代表操作行为. 例如
                            userController.php?action=addUser 添加一个新用户</p></td>
                </tr>
                <tr>
                    <td>controller.php</td>
                    <td>不能出现sql语法 , 所有方法必须在model里封装</td>
                </tr>
                </tbody>
            </table>
        </section>
    </article>
    <article class="mainBox">
        <header>
            <h2>方法列表</h2>
        </header>
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th colspan="3">UserModel.class</th>
                </tr>
                </thead>
                <tr>
                    <td>isLogin()</td>
                    <td>判断用户是否登录以及cookie是否合法</td>
                    <td>返回 bool</td>
                </tr>
                <tr>
                    <td>getAuthority($key)</td>
                    <td>查看当前用户是否有相应权限</td>
                    <td>getAuthority($key)<br>display|add|update|delete|god|user|course|guide|forum<br>如果有相应权限返回为真</td>
                </tr>
                <tr>
                    <td>getUserBlockState()</td>
                    <td>获取用户的限制状态</td>
                    <td>1正常0限制</td>
                </tr>
                <tr>
                    <td>getUserId()</td>
                    <td>获得当前用户id</td>
                    <td></td>
                </tr>
                <tr>
                    <td>getUserClassId()</td>
                    <td>获取当前用户所在组的id</td>
                    <td></td>
                </tr>
                <tr>
                    <td>getUserName()</td>
                    <td>获取当前用户的用户名</td>
                    <td></td>
                </tr>
                <tr>
                    <td>getUserAlias()</td>
                    <td>获得当前用户的别名</td>
                    <td></td>
                </tr>
                <tr>
                    <td>getUserIsAdmin()</td>
                    <td>获得当前用户的管理员身份标识</td>
                    <td>返回值: 1代表</td>
                </tr>
                <tr>
                    <td>getUserAuthorityTitle()</td>
                    <td>获得用户组别的头衔</td>
                    <td></td>
                </tr>
                <tr>
                    <td>isAdminLogin()</td>
                    <td>判断管理员是否登录以及cookie是否合法</td>
                    <td>返回 bool</td>
                </tr>
                <tr>
                    <td>login()</td>
                    <td>验证登录</td>
                    <td>login($name, $pwd,$usertype='user')</td>
                </tr>
                <tr>
                    <td>logout($url)</td>
                    <td></td>
                    <td>return bool</td>
                </tr>
                <thead>
                <tr>
                    <th colspan="3">BasicToll.class</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>post()</td>
                    <td>重新封装$_POST , 并对用户输入的进行安全处理</td>
                    <td>$title = BasicTool::post('title',"分类名不能为空");</td>
                </tr>
                <tr>
                    <td>get()</td>
                    <td>重新封装$_GET , 并对用户输入的进行安全处理</td>
                    <td>$action = BasicTool::get('cation',"分类名不能为空");</td>
                </tr>
                <tr>
                    <td>loadSnippet()</td>
                    <td></td>
                    <td>loadSnippet(文件名,自定义标题,默认引入的文件)</td>
                </tr>
                <tr>
                    <td>echoMessage()</td>
                    <td>输出一个提示页</td>
                    <td>BasicTool::echoMessage("操作成功", "/admin/administrator/");<br>BasicTool::echoMessage("操作成功",
                        "/admin/administrator/");
                    </td>
                </tr>
                <tr>
                    <td>jumpTo()</td>
                    <td>跳转到一个页面</td>
                    <td>BasicTool::jumpTo("/admin/");</td>
                </tr>
                <tr>
                <tr>
                    <td>echoJson()</td>
                    <td>封装一个json格式数据</td>
                    <td>BasicTool::echoJson(0, 'sql语句出错:' . $this->mysqli->error . $sql);</td>
                </tr>
                </tbody>
                <thead>
                <tr>
                    <th colspan="3">Model.class</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>$sqltool</td>
                    <td>每次new这个类的时候会自动存放一个SqlTool class的实例到此属性</td>
                    <td>$this->sqltool->getAffectedRows()</td>
                </tr>
                <tr>
                    <td>getListWithPage()</td>
                    <td>通过一个select查询语句返回一个二维数组,并且封装分页</td>
                    <td> getListWithPage($table,$sql,$pageCurrent=1, $pageSize=40)</td>
                </tr>
                <tr>
                    <td>echoPageList()</td>
                    <td>输出使用getListWithPage()方法时所封装的分页代码</td>
                    <td></td>
                </tr>
                <tr>
                    <td>getRowById()</td>
                    <td>根据id值从某张表内获得一条数据</td>
                    <td>$currentRow = $model->getRowById(表名,id的值);</td>
                </tr>
                <tr>
                    <td>addRow()</td>
                    <td>插入一条数据</td>
                    <td>addRow(表名,键值对数组)</td>
                </tr>
                <tr>
                    <td>updateRowById()</td>
                    <td>通过主键id修改一条数据</td>
                    <td>$model->updateRowById($table,$id,$arrKV)</td>
                </tr>
                <tr>
                    <td>realDeleteByFieldIn()</td>
                    <td>where field in () 语法真实删除一条/一组数据</td>
                    <td>$model->realDeleteByFieldIn($table, $field, $value, $debug=false)</td>
                </tr>
                <tr>
                    <td>realDeleteByFieldIn()</td>
                    <td>where field in () 语法真实删除一条/一组数据</td>
                    <td>$model->realDeleteByFieldIn($table, $field, $value, $debug=false)</td>
                </tr>
                <tr>
                    <td>isExistByFieldValue()</td>
                    <td>判断某个字段下的值是不是唯一</td>
                    <td>isExistByFieldValue($table,$field,$value)</td>
                </tr>
                </tbody>
                <thead>
                <tr>
                    <th colspan="3">SqlTool.class</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>$mysqli</td>
                    <td>数据库连接指针</td>
                    <td>$this->mysqli->query("set names utf8");</td>
                </tr>
                <tr>
                    <td>query()</td>
                    <td>向数据库发送一条sql语法, 如果有语法错误, 则终止程序运行并将错误报出</td>
                    <td>$this->sqltool->query($sql);</p>
                    </td>
                </tr>
                <tr>
                    <td>getRowBySql()</td>
                    <td>根据select查询语句, 将多行结果封装成一个一维数组返回</td>
                    <td>$row = $sqltool->getRowBySql($sql);</td>
                </tr>
                <tr>
                    <td>getListBySql()</td>
                    <td>根据select查询语句, 将多行结果封装成一个二维数组返回</td>
                    <td>addRow($arr,$table)</td>
                </tr>
                <tr>
                    <td>getCountByTable()</td>
                    <td>返回某张表中一共多少条数据</td>
                    <td>$count = $this->sqltool->getCountByTable('table');</td>
                </tr>
                <tr>
                    <td>getAffectedRows()</td>
                    <td>返回上一次数据库操作所影响的行数</td>
                    <td><p>if($this-&gt;sqltool-&gt;getAffectedRows()&gt;0) {</p>
                        <p>return true;</p>
                        <p>}</p></td>
                </tr>
                <tr>
                    <td>isExistBySql()</td>
                    <td>检查数据库中是否已经存在某值</td>
                    <td>isExist($sql)</td>
                </tr>
                </tbody>
            </table>
        </section>
    </article>

    <article class="mainBox">
        <form action="" method="post">
            <header>
                <h2>列表</h2>
            </header>
            <section>
                <table class="tab">
                    <thead>
                    <tr>
                        <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                        <th>标题</th>
                        <th>标题</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value=""></td>
                        <td><a href="">内容</a></td>
                        <td>内容</td>
                    </tr>
                    </tbody>
                </table>
            </section>
            <footer class="buttonBox">
                <input type="submit" value="删除" class="btn"><a class="btn" href="index.php?action=showFormAdd">添加</a>
            </footer>
        </form>
    </article>


    <article class="mainBox">
        <form action="" method="post">
            <header>
                <h2>表单</h2>
            </header>
            <section class="formBox">
                <div>
                    <label>文本框_普通</label>
                    <input class="input" placeholder="占位符" type="text" name="" value="">
                </div>
                <div>
                    <label>文本框_密码</label>
                    <input class="input" placeholder="占位符" type="password" name="" value="">
                </div>
                <div>
                    <label>文本框_邮箱</label>
                    <input class="input" placeholder="占位符" type="email" name="email" value="">
                </div>
                <div>
                    <label>下拉列表</label>
                    <select class="input input-select input-size50 selectDefault" name="authority" defvalue="默认值">
                        <option value="0">值</option>
                        <option value="默认值">这是你设置的默认值</option>
                    </select>
                </div>
                <div>
                    <label>文本域</label>
                    <textarea class="input input-textarea" placeholder="占位符" name="" value=""></textarea>
                </div>
                <div>
                    <input class="btn btn-center" type="submit" title="提交" value="提交">
                </div>
            </section>
        </form>
    </article>
    <article class="mainBox">
        <header>
            <h2>fetch</h2>
        </header>
        <section>
            <xmp>
fetch(`/admin/comment/commentController.php?action=addCommentWithJson`, {
    method: "POST",
    headers: {"Content-Type": "application/x-www-form-urlencoded"},
    body: `parent_id=0&receiver_id=6&section_name=guide&section_id=125&comment=新生微信群`,
    credentials:'same-origin'
}).then(response => response.json());
            </xmp>
        </section>
    </article>
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/templete/_mainFoot.php";
?>
