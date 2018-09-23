<?php
//require_once "zip_down.class.php";
require_once "zip.class.php";//引入压缩文件夹类
require_once "change_dir.inc.php";//引入相对路径转绝对路径
$dir=__DIR__;
$dir=str_replace("\\","/",$dir);
if(isset($_GET['list'])){
    $list=$_GET['list'];
}else{
    $list="/fastdfs/smb";
}
/*下载内容start*/
if(isset($_GET['d'])){
    $dir=$_GET['d'];
    if(is_dir($dir)){
        $save_zip=basename($dir).".zip";
        $down_file=$save_zip;
        $zip=new HZip;
        $zip::zipDir($dir,$save_zip);
    }else{
        $down_file=$dir;
    }

    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-Type: application/zip"); //zip格式的
    header('Content-disposition: attachment; filename='.basename($down_file)); //文件名
    header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件
    header('Content-Length:'.filesize($down_file)); //告诉浏览器，文件大小
    $res=readfile($down_file);
    unlink($down_file);//删除
    exit;
}
/*下载内容end*/
/*后台搜索实现start*/
if($_POST){
    $i=0;
    $j=0;
	$data=array();
	$dir=trim($_POST["list"]);
	$name=trim($_POST["name"]);
	if(!preg_match("/(^\/.*)|(:)/",$dir)){//判断是否为相对路径
	    $dir=url_to_absolute(__DIR__,$dir);//转为绝对路径
    }
	if(is_dir($dir)){
		$data['is_dir']=1;
		$data['dir']=find_files($dir,$name);
	}else{
		$data['is_dir']=0;
	}
	$data['i']=$i;
	$data['j']=$j;
	echo json_encode($data);
	exit;
}
/*后台搜索实现start*/
/*获取当前文件夹所有内容*/
function find_files($dir,$name) {
    $data='';
    global $i;
    global $j;
    if(@$handle = opendir($dir)) { //注意这里要加一个@，不然会有warning错误提示：）
        while(($file = readdir($handle)) !== false) {
            if($file != ".." && $file != ".") { //排除根目录；
            	$is_match=preg_match("/".$name."/", $file);
				if($is_match){
				    $file_out=preg_replace("/".$name."/","<span style='color:red'>".$name."</span>", $file);//红色显示匹配到的内容
                    if(is_dir($dir."/".$file)) { //区分文件夹和文件
                        $data.="<img src='img/dir.ico'>".$dir."/".$file_out."&nbsp;&nbsp;&nbsp;&nbsp;
                        <a href='dir.php?dir=".dirname($dir."/".$file)."' target='_black'>上级目录</a>&nbsp;&nbsp;&nbsp;&nbsp;
                        <a href='dir.php?dir=".$dir."/".$file."' target='_black'>打开目录</a>&nbsp;&nbsp;&nbsp;&nbsp;
                        <a href='?d=".$dir."/".$file."' target='_black'>下载目录所有内容</a><br>";
                    }else{
                        $data.="<img src='img/file.ico'>".$dir."/".$file_out."&nbsp;&nbsp;&nbsp;&nbsp;
                        <a href='dir.php?dir=".dirname($dir."/".$file)."' target='_black'>上级目录</a>&nbsp;&nbsp;&nbsp;&nbsp;
                        <a href='?d=".$dir."/".$file."' target='_black'>下载文件</a><br>";
                    }
					$j++;
				}
                if(is_dir($dir."/".$file)) { //如果是子文件夹，就进行递归
                    $data .= find_files($dir."/".$file,$name);
                } 
                $i++;
            }
        }
        closedir($handle);
        return $data;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
	<script>
		function search_file(){
			var list=$("#list").val();
			var name=$("#name").val();
			if(list==''){
				alert("目录为空！");
				return false;
			}
			if(name==''){
				alert("内容为空！");
				return false;
			}
			$.ajax({
				method:"post",
				url:"",
				data:{list:list,name:name},
				dataType:"json",
				beforeSend:function(){
					$("#result").html("查询中……");
				},
				success:function(data){
					var html='';
					if(data.is_dir==0){
						html="<span style='color:red'>该目录不存在！</span>";
					}else{
						if(data.dir==''){
							html="<span style='color:red'>未找到相关文件和文件夹！</span>";
						}else{
							html=data.dir;
						}
					}
					$("#result").html('共查询'+data.i+'个文件和文件夹<br>查询到'+data.j+'个相关内容<br>'+html);
				}
			});
		}
		function open_dir(){
		    if($("#list").val()==''){
		        var dir='<?php echo $dir?>';
            }else{
		        var dir=$("#list").val();
            }
            window.location.href="dir.php?dir="+dir;
        }
	</script>
</head>
<body>
<h1>文件查找下载</h1>
<span>目录：</span><input type="text" id="list" name="list" value="<?php echo $list;?>" size=30><button onclick="open_dir()" >打开目录</button>
	<br>
	<span>文件夹名或文件名：</span><input type="text" id="name" name="name" size=30>
	<input type="submit" onclick="search_file()" value="查找">
	<div id='result'></div>
</body>
</html>