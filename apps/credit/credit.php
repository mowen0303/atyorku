<!DOCTYPE html>
<html lang="en">
<head>
<title>查看积分</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
	body {
 	 margin: 0;
 	 display: flex;
 	 width: 100%;
 	 height: 100%;
 	 background-color: #F6F9FF;
	}

	.header {
		background-color: #F44336;
		padding: 20px;
 		text-align: center;
		width: 100%;
		height: 200px;
	}

	.info {
		background-color: #FFF;
		height: 180px;
		width: 94%;
		margin-left: 3%;
		margin-right: 3%;
		border-radius: 10px;
		position: absolute;
		top: 20%;
		box-shadow: 0 3px 0 #EDF4FE;
	}

	.image {
		height: 100px;
		width: 100px;
		border-radius: 50%;
		background-color: white;
		position: absolute;
		top: -35%;
		right: 10%;
		margin:10px auto;
	}


</style>
</head>
<body>

<div class="header">
	<p id='name' style="size: 5; color:white; margin-top: 0; justify-content: center;"></p>
</div>

<div class="info">
	<p style="color: grey; margin-left: 5%;">总积分</p>
	<h1 id='creditText' style="margin-left: 5%; margin-top: 1%;"></h1>
	<img id="imgSrc" class="image" src="" />
</div>


<script type="text/javascript">

	var userId = window.location.hash.substr(1);

	fetch('http://www.atyorku.ca/admin/user/userController.php?action=getRowOfUserBasicInfoWithJson&userId=' + userId,  {
    method: "POST",
    headers: {"Content-Type": "application/x-www-form-urlencoded"},
    credentials:'same-origin'
})
	.then(response => response.json())
	.then(data => {
		if(data.code == 1){
			const totalCredit = document.getElementById("creditText");
			totalCredit.innerHTML = data.result.credit;

			const name = document.getElementById("name");
			name.innerHTML = data.result.alias;

			const showImg = document.getElementById("imgSrc").src = "http://www.atyorku.ca" + data.result.img;
		}else{
			alert(data.message);
		}
	})
</script>

</body>
</html>