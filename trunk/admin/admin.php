<?php
include "./config.php";
session_start();

 
switch ($do){
case "login":
	login();
	break;
case "logout":
	logout();
	break;
case "manage":
	manage($manga,$chapter);
	break;
case "resize":
	resize_chapter($manga,$chapter);
	echo '<meta http-equiv="refresh" content="0; URL=admin.php?do=manage&manga='.$manga.'&chapter='.$chapter.'">'; die;
	break;
case "massresize":
	mass_resize($manga);
	break;
case "addmanga":
	//mass_resize($manga);
	break;
case "buildsite":
	mksite();
	break;
case "scrach":
	from_scrach();
	break;
default:
   index();
}
   
function draw_page($page,$label,$h,$w){
   
$dat.= '<html><head><title>'.TITLE.'</title><link rel="stylesheet" type="text/css" href="admin.css" ></head><body>';
$dat.= '<table height="100%" width="100%"><tr><td align="center">';
if (isset($_SESSION['eror'])){$dat.= '<div class="error">'.$_SESSION['eror'].'</div>';unset($_SESSION['eror']);}
$dat.= '<table class="tab" cellspacing="0"><tr><td class="label">'.$label.'</td><td width="1px" class="buttons">';
if ($_SESSION['user']==md5(USER_NAME) AND $_SESSION['pass']==md5(PASSWORD)){$dat.= '<a class="logout" href="admin.php?do=logout"><img src="logout.jpg" border="0" alt=""></a>';}
$dat.= '</td></tr><tr><td colspan="2" height="'.$h.'" width="'.$w.'" style="overflow: auto;">';
$dat.= '<table border="0" height="100%" width="100%" class="cont"><tr>';
if ($_SESSION['user']==md5(USER_NAME) AND $_SESSION['pass']==md5(PASSWORD)){$dat.='<td  class="menu" valign="top" width="1">';$dat.= make_menu();$dat.= '</td>';}
$dat.= '<td align="center">';
$dat.= $_SESSION['path'];
//$dat.= '<div style="height:490px;overflow:auto;" align="center">';
$dat.= $page;
//$dat.= '</div>';
$dat.= '</td></tr></table>';
$dat.= '</td></tr></table>';
$dat.= '</td></tr></table>'; 
$dat.= '</body></html>';
echo $dat;
}
    
function index(){
isloged();
$content='Admin Panell' ;
draw_page($content, "Main","500", "900");
}
 
function login(){
if ($_POST['submit']){
if ($_POST['username']==USER_NAME AND $_POST['pass']==PASSWORD){
$_SESSION['user']=md5($_POST['username']);
$_SESSION['pass']=md5($_POST['pass']);
}else{$_SESSION['eror']="Wrong Username or Password";}
echo '<meta http-equiv="refresh" content="0; URL=admin.php">';
 die;}
$content.='<form class="login" action="admin.php?do=login" method="post"><br>Username<br><input type="text" name="username" value=""><br><br>Password<br><input type="password" name="pass" value=""><br><br><input type="submit" name="submit" value="Login"></form>';
draw_page($content, "Login", "180", "200");
}

function logout(){
if (!session_destroy()){$_SESSION['eror']="Could not Logout<br>Please close your browser to force a logout";}
$_SESSION['eror']="Loging out successfull!";
echo '<meta http-equiv="refresh" content="0; URL=admin.php">'; die;}

function isloged(){
if ($_SESSION['user']!=md5(USER_NAME) AND $_SESSION['pass']!=md5(PASSWORD)){echo '<meta http-equiv="refresh" content="0; URL=admin.php?do=login">'; die;}
}

function make_menu(){
isloged();
$menu=  '<table>';
$menu.= '<tr><td>Manga</td></tr>';
$menu.= '<tr><td>&nbsp;&nbsp;<a href="admin.php?do=manage">Manage</a></td></tr>';
$menu.= '<tr><td><a href="admin.php?do=buildsite">Build Site</a></td></tr>';
$menu.= '</table>';
return $menu;
}

function manage($manga,$chapter){
isloged();
if (!isset($manga)){
$contend=get_dir_content("data");
foreach($contend as $v1){
$data.='<a href="admin.php?do=manage&manga='.$v1.'">'.$v1.'</a><br>';
$data2='<a href="admin.php?do=addmanga">Add Manga</a><br><br><a href="admin.php?do=scrach">Start all over</a>';
}
}else{
if (!isset($chapter)){
$m="data/".$manga."/data";
$contend=get_dir_content($m);
$data.='<b>'.$manga.'</b><br><br>';
foreach($contend as $v1){
$data.='<a href="admin.php?do=manage&manga='.$manga.'&chapter='.$v1.'">'.$v1.'</a><br>';
}
$data2='<a href="admin.php?do=make&stuff=chapter">Add Chapter</a><br><br><a href="admin.php?do=del?stuff=chapter">Delete Chapter</a><br><br><a href="admin.php?do=massresize&manga='.$manga.'">Mass Resize</a>';
}else{
$m="data/".$manga."/data/".$chapter;
$contend=get_dir_content($m);
$data.='<a href="admin.php?do=manage&manga='.$manga.'">'.$manga.'</a>->'.$chapter.'<br>';
$data.='<table border="0">';
$data.='<tr><td>Name</td><td width="100px">Resized image</td><td width="100px">Thumbnail</td></tr>';
foreach($contend as $v1){
$data.='<tr><td>'.$v1.'</td>';
if (file_exists('../data/'.$manga.'/thumb/'.$chapter.'/'.$v1.'')){$t='green';}else{$t='red';}
if (file_exists('../data/'.$manga.'/small/'.$chapter.'/'.$v1.'')){$s='green';}else{$s='red';}
$data.='<td style="background:'.$s.';">&nbsp;</td>';
$data.='<td style="background:'.$t.';">&nbsp;</td>';
$data.='</tr>'; 
}
$data2='<br><a href="admin.php?do=resize&manga='.$manga.'&chapter='.$chapter.'">resize images</a><br><br>';
$data.='</table>';
}
}
$data.='</td><td class="sidemenu" valign="top">';
$data.=$data2;
$data.='</td>';
draw_page($data, "Manage", "500", "900");
}

function get_dir_content($dir){
$content=array();
$handle = opendir("../".$dir);
while (false !== ($file = readdir($handle))) {
if ($file!="." AND $file!=".."){
$content[]=$file;
}
}
closedir($handle);
natsort($content);
return $content;
}

function make_dir($dir){
if ($handle = opendir($dir)) {
while (false !== ($file = readdir($handle))) {
if ($file =="data"){$data;}
if ($file =="thumb"){$thumb;}
if ($file =="narmal"){$normal;}
}
if (is_set($data)){
if (!is_set($thumb)){$thumb_dir=$dir."/thumb"; mkdir($thumb_dir, 0777);}
if (!is_set($normal)){$noram_dir=$dir."/normal"; mkdir($normal_dir, 0777);}
}
closedir($handle);
}
}

function add_manga($dir){
if ($handle = opendir($dir)) {
while (false !== ($file = readdir($handle))) {
if ($file =="data"){$data;}
if ($file =="thumb"){$thumb;}
if ($file =="narmal"){$normal;}
}
if (is_set($data)){
if (!is_set($thumb)){$thumb_dir=$dir."/thumb"; mkdir($thumb_dir, 0777);}
if (!is_set($normal)){$noram_dir=$dir."/normal"; mkdir($normal_dir, 0777);}
}
closedir($handle);
}
}

function resize_chapter($manga,$chapter){
isloged();
if (isset($manga) AND isset($chapter)){
$dir="data/".$manga;
$datadir= $dir."/data/".$chapter;
$thmbdir= "../".$dir."/thumb/".$chapter;
$smalldir= "../".$dir."/small/".$chapter;
//echo $dir1."<br>";
$contend=get_dir_content($datadir);
foreach($contend as $v1){
$dir3="../".$datadir."/".$v1;
if (is_img($dir3)){
echo $v1."<br>";
set_time_limit(20);
thumb($dir3, 0, 150, $thmbdir, $v1);
set_time_limit(20);
thumb($dir3, 500, 0, $smalldir, $v1);
//die;
flush();
}
}
}
}

function is_img($file){
if (!is_dir($file)){
$size = getimagesize($file);
if (!$size[0]||!$size[1]||!$size[2]||!$size[3]){return false;}else{return true;}
}
}

function thumb($file, $W, $H, $thumb_dir, $img_name){
if (!is_dir($thumb_dir)){mkdir($thumb_dir, 0777);}
$size = getimagesize($file);
$w=$size[0];
$h=$size[1];
if ($H=="0"){
if ($size[0]>$size[1]){$W=$W*2;}
$newHeight=ceil(($size[1]*$W)/$size[0]);
$newWidth = $W;
}else if ($W=="0"){ 
$newWidth=ceil(($size[0]*$H)/$size[1]);
$newHeight = $H;
}else{

if ($size[0]>$W OR $size[1]>$H){
if ($size[0]>$size[1]){
$newHeight=ceil(($size[1]*$W)/$size[0]);
$newWidth = $W;
}else{
$newWidth=ceil(($size[0]*$H)/$size[1]);
$newHeight = $H;
}
}
}

switch ($size[2]){
case 1:
$original = imagecreatefromgif($file); break;
case 2:
$original = imagecreatefromjpeg($file);break;
case 3:
$original = imagecreatefrompng($file);break;
}
$tempImg = imagecreatetruecolor($newWidth, $newHeight);
imagecopyresampled($tempImg, $original, 0, 0, 0, 0, $newWidth, $newHeight, $size[0], $size[1]);
switch ($size[2]){
case 1:
imagegif($tempImg, "$thumb_dir/$img_name", 100);break;
case 2:
imagejpeg($tempImg, "$thumb_dir/$img_name", 100);break;
case 3:
imagejpeg($tempImg, "$thumb_dir/$img_name", 100);break;
//imagepng($tempImg, "$thumb_dir/$img_name", 100);break;
}
}

function mkchapsite($manga,$chapter){
if (isset($manga) AND isset($chapter)){
$files = array();
$chaps = array();
$dir="data/".$manga;
$datadir= $dir."/small/".$chapter;
//echo $dir1."<br>";
$handle=get_dir_content($datadir);
foreach($handle as $file){
//echo $file."<br>";
$files[]= $file;
}

$chapdir= $dir."/data/";
$handle2=get_dir_content($chapdir);
foreach($handle2 as $file2){
//echo $file."<br>";
$chaps[]= $file2;
}

foreach ($chaps as $key => $value){if ($value == $chapter){$c=$key;}}

$ccount= count($chaps)-1;
$pc=$c-1;
$nc=$c+1;



natsort($files);
$fil = implode("','",$files);
natsort($chaps);
$cha = implode("','",$chaps);
//echo $fil;
//echo $cha;
//die;

$fdata='';
$fdata.='<html>'."\n";
$fdata.='<html>'."\n";
$fdata.='<head>'."\n";
$fdata.='<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">'."\n";
$fdata.='<title></title>'."\n";
$fdata.=''."\n";
$fdata.='<script language="Javascript" type="text/javascript">'."\n";
$fdata.='<!--'."\n";
$fdata.='imagenames=new Array(\''.$fil.'\');'."\n";
$fdata.='chapternames=new Array(\''.$cha.'\');'."\n";
$fdata.='manga="'.$manga.'";'."\n";
$fdata.='chapter="'.$chapter.'";'."\n";
$fdata.=''."\n";
$fdata.='n=0;'."\n";
$fdata.='picP= new Image();'."\n";
$fdata.='picP.src="../../data/"+manga+"/small/"+chapter+"/"+imagenames[0];'."\n";
$fdata.='picN= new Image(); '."\n";
$fdata.='picN.src="../../data/"+manga+"/small/"+chapter+"/"+imagenames[1];'."\n";
$fdata.='picL= new Image(); '."\n";
$fdata.='picL.src="../../admin/loading1.jpg";'."\n";
$fdata.=''."\n";
$fdata.=''."\n";
$fdata.='function previousPage(){'."\n";
$fdata.='document.getElementById("page").src = picP.src;'."\n";
$fdata.='if (n>0){n--;'."\n";
$fdata.='window.scrollTo(0,0);'."\n";
$fdata.='preload(n);'."\n";
$fdata.='}'."\n";
$fdata.='}'."\n";
$fdata.=''."\n";
$fdata.='function nextPage(){'."\n";
$fdata.='document.getElementById("page").src = picN.src;'."\n";
$fdata.='t=imagenames.length-1;'."\n";
$fdata.='if (n<t){n++;'."\n";
$fdata.='window.scrollTo(0,0);'."\n";
$fdata.='preload(n);'."\n";
$fdata.='}'."\n";
$fdata.='}'."\n";
$fdata.=''."\n";
$fdata.='function preload(p){'."\n";
$fdata.='t=imagenames.length-1'."\n";
$fdata.='if (p>0){pp=p-1;}'."\n";
$fdata.='if (p<t){np=p+1;}'."\n";
$fdata.='picP.src="../../data/"+manga+"/small/"+chapter+"/"+imagenames[pp];'."\n";
$fdata.='picN.src="../../data/"+manga+"/small/"+chapter+"/"+imagenames[np];'."\n";
$fdata.=''."\n";
$fdata.='}'."\n";
$fdata.=''."\n";
$fdata.='function thb(){'."\n";
$fdata.='txt="";'."\n";
$fdata.='y=1;'."\n";
$fdata.='for (i=0;i<imagenames.length;i++){'."\n";
$fdata.='txt=txt+"<a href=\'javascript:Page("+i+");\'><img src=\'../../data/"+manga+"/thumb/"+chapter+"/"+imagenames[i]+"\' border=\'0\'></a>&nbsp;&nbsp;&nbsp;&nbsp;";'."\n";
$fdata.='if(y>3){txt=txt+"<br><br>";y=0;}else{y++;}'."\n";
$fdata.='}'."\n";
$fdata.='document.getElementById("thmb").innerHTML= txt;'."\n";
$fdata.='document.getElementById("page").src = "../../data/"+manga+"/small/"+chapter+"/"+imagenames[0];'."\n";
$fdata.=''."\n";
$fdata.='}'."\n";
$fdata.=''."\n";
$fdata.='function show(){'."\n";
$fdata.='obj=document.getElementById("thmb").style;'."\n";
$fdata.='obj.display = obj.display? "":"block";'."\n";
$fdata.='setTimeout("window.scrollBy(0,150)",500);'."\n";
$fdata.='}'."\n";
$fdata.=''."\n";
$fdata.='function Page(p){'."\n";
$fdata.='document.getElementById("page").src = picL.src;'."\n";
$fdata.='window.scrollTo(0,0);'."\n";
$fdata.='n=p;'."\n";
$fdata.='document.getElementById("page").src = "../../data/"+manga+"/small/"+chapter+"/"+imagenames[n];'."\n";
$fdata.='preload(n);'."\n";
$fdata.='}'."\n";
$fdata.='-->'."\n";
$fdata.='</script>'."\n";
$fdata.='<link rel="stylesheet" type="text/css" href="../viewer.css" />'."\n";
$fdata.='</head>'."\n";
$fdata.='<body onload="thb();">'."\n";
$fdata.='<div id="chapmenul">';
if ($c>0){$fdata.='<a href="'.$chaps[$pc].'.html">Предишна Глава</a>';}
$fdata.='</div><div class="chapmenuc"><a href="index.html">Индекс</a></div><div id="chapmenur">';
if ($c<$ccount){$fdata.='<a href="'.$chaps[$nc].'.html">Следваща Глава</a>';}
$fdata.='</div>'."\n";
$fdata.='<center><br>'."\n";
$fdata.='<a href="javascript:previousPage();">Предишна страница</a>'."\n";
$fdata.='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'."\n";
$fdata.='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'."\n";
$fdata.='<a href="javascript:nextPage();">Следваща страница</a>'."\n";
$fdata.='<br>'."\n";
$fdata.='<img id="page" src="../../admin/loading1.jpg">'."\n";
$fdata.='<br>'."\n";
$fdata.='<a href="javascript:previousPage();">Предишна страница</a>'."\n";
$fdata.='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'."\n";
$fdata.='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'."\n";
$fdata.='<a href="javascript:nextPage();">Следваща страница</a>'."\n";
$fdata.='<br><br><a href="javascript:show();">Thumbnails</a><br>'."\n";
$fdata.='<div id="thmb">'."\n";
$fdata.='</div>'."\n";
$fdata.='</center>'."\n";
$fdata.='</body>'."\n";
$fdata.='</html>'."\n";

$mdir="../manga/".$manga;
if (!is_dir($mdir)){mkdir($mdir, 0777);}
$File = "../manga/".$manga."/".$chapter.".html";
$Handle = fopen($File, 'w');
fwrite($Handle, $fdata);
fclose($Handle); 
}
}

function mksite(){
isloged();
$data='';
$manga=get_dir_content("data");
foreach($manga as $v1){
$data.='<b>'.$v1.'</b><br>';
$chapter=get_dir_content("data/".$v1."/small");
foreach($chapter as $v2){
$data.='&nbsp;&nbsp;'.$v2.'<br>';
mkchapsite($v1,$v2);
}
}
draw_page($data, "Build Site","500", "900");
}

function mass_resize($manga){
isloged();
$chapter=get_dir_content("data/".$manga."/data");
foreach($chapter as $v2){
echo $v2;
flush();
resize_chapter($manga,$v2);
}
}

function from_scrach(){
isloged();
$manga=get_dir_content("data/");
foreach($manga as $v2){
echo $v2;
flush();
mass_resize($v2);
}
}


?>