<?php
//$bookModel = new \admin\book\BookModel();
$bookCategoryModel = new \admin\bookCategory\BookCategoryModel();
$flag = BasicTool::get("flag");
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?listBook">返回</a>
</nav>
<article class="mainBox">
    <form action="bookController.php?action=modifyBook" method="post">
        <input name="flag" value="<?php echo $flag?>" type="hidden">
        <input name="f_id" value="<?php echo BasicTool::get('f_id')?>" type="hidden">
        <header>
            <h2><?php echo $flag=="update"?"修改二手书":"添加二手书"; ?></h2>
        </header>
        <section class="formBox">
            <div>
                <label>标题<i>*</i></label>
                <input class="input" type="text" name="name" value="<?php echo BasicTool::get('f_name') ?>">
            </div>
            <div>
                <label>所属分类<i>*</i></label>
                <select class="input input-select input-size50 selectDefault" name="book_category_id" defvalue="<?php echo BasicTool::get('f_book_category_id') ?>">
                    <?php
                        $arrOfCategory = $bookCategoryModel->getListOfBookCategory(100);
                        foreach($arrOfCategory as $rowOfCategory){
                            echo '<option value="'.$rowOfCategory['id'].'">'.$rowOfCategory['name'].'</option>';
                        }
                    ?>
                </select>
            </div>
            <div>
                <label>价格<i>*</i></label>
                <input class="input" type="number" name="price" min="0.0" step="0.01" value="<?php echo (float)BasicTool::get('f_price') ?>">
            </div>
            <div>
                <label>描述</label>
                <textarea class="input input-textarea" name="description"><?php echo BasicTool::get('f_description') ?></textarea>
            </div>
        </section>
        <footer class="submitBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>
</article>
