<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/22
 * Time: 22:49
 */
if (isset($_GET['dir'])) {
    $dir = $_GET['dir'];
    if (is_dir($dir)) {
        $data=dir_info($dir);
    }else{
        $data="<span style='color:red'>该目录不存在！</span>";
    }
}else {
    $dir = '';
    $data="";
}
function dir_info($dir){
    $data='';
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            if($file != ".." && $file != "."){
                if(is_dir($dir."/".$file)) { //区分文件夹和文件
                    $data.="<img src='img/dir.ico'>".$file."&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href='dir.php?dir=".dirname($dir)."'>上级目录</a>&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href='dir.php?dir=".$dir."/".$file."'>打开目录</a>&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href='hello.php?d=".$dir."/".$file."' target='_black'>下载目录所有内容</a><br>";
                }else{
                    $data.="<img src='img/file.ico'>".$file."&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href='dir.php?dir=".dirname($dir)."'>上级目录</a>&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href='hello.php?d=".$dir."/".$file."' target='_black'>下载文件</a><br>";
                }
            }
        }
        closedir($dh);
    }
    return $data;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
</head>
<body>
<span>当前目录：</span><input type="text" id="list" readonly name="list" value="<?php echo $dir; ?>" size=100><a href='dir.php?dir=<?php echo dirname($dir);?>'>上级目录</a>
<br>
<a href="hello.php?list=<?php echo $dir;?>">从当前文件夹查找</a>
<br>
<span>目录内容：</span>
<div id='result'><?php echo $data;?></div>
</body>
</html>
