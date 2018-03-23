<header class="topBox">
    <h1>科目管理</h1>
</header>
<nav class="mainNav">
    <a class="btn" href="javascript:history.back()">返回</a>
</nav>
<article class="mainBox">
    <header><h2>数据集更新</h2></header>
    <form action="/admin/xmlParser/xmlParserController.php?action=updateCourseCodeTable" method="post" enctype="multipart/form-data">
        <section>
            <input id="file" name="file" onchange="onFileChange(this)" type="file" hidden/>
            <label id = "fileLabel" style="font-size:12px;cursor:pointer;text-align: center;padding:8px 20px;border-radius:3px;background-color:#333;color:white;border:none" for="file">添加XML文件</label>
        </section>
        <footer class="buttonBox">
            <button id="submit" type="submit" value="提交" class="btn" onclick="return validateForm(this)">提交</button>
        </footer>
    </form>
</article>
<script>
    function onFileChange(fileInput){
        var file = fileInput.files[0];
        var fileLabel = document.getElementById("fileLabel");
        fileLabel.textContent = file.name;
    }
    function validateForm(button){
        var fileInput = document.getElementById("file");
        if (fileInput.files.length == 0){
            window.alert("请选择要上传的文件");
            return false;
        }
        if (!fileInput.files[0].type.includes("xml")){
            window.alert("Please upload XML file only");
            return false;
        }
        button.style.display = "none";
        return true;
    }
</script>