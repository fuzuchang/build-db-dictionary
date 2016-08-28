<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="author" content="keke-QianKe"/>
<title>数据库工具 v1.0</title>

<link rel="stylesheet"
	href="http://cdn.bootcss.com/twitter-bootstrap/3.0.3/css/bootstrap.min.css">
	<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->

</head>
<body>
	<div class="container">
		<div class="page-header">
			<h1>数据库工具 v1.0</h1>
		</div>
		<div class="row" style="margin-top:180px;">
			<form class="form-horizontal" role="form" action="examples/dbUpdate.php" method="post" id="updatePage">
				
				<div class="form-group">
					<div class="col-sm-4 col-sm-offset-4">
						<a href="examples/dbDoc.php" class="btn btn-primary btn-lg btn-block" role="button">生成数据库字典</a>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-4 col-sm-offset-4">
						<a href="examples/dbStruct.php" class="btn btn-info btn-lg btn-block" role="button">生成数据库结构</a>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-4 col-sm-offset-4">
						<input type="submit" name="upgrade" class="btn btn-success btn-lg btn-block" data-loading-text="正在升级,请稍后..." value="开始升级">
					</div>
				</div>
				
				<div class="form-group">
					<div class="col-sm-4 col-sm-offset-4">
					<div class="checkbox">
				          <label>
							<input type="checkbox" name="droptable" value="true">删除多余的表
						  </label>
						  <span class="label label-danger">勾选前请先备份你的数据库</span>
				      </div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-4 col-sm-offset-4">
						<div class="checkbox">
				        <label>
				          <input type="checkbox" name="dropfield" value="true">删除多余的字段
				        </label>
				        <span class="label label-danger">勾选前请先备份你的数据库</span>
				      </div>
						
					</div>
				</div>
				
			</form>
		</div>
	</div>
</body>
</html>