<?php
/* Smarty version 3.1.30, created on 2017-08-05 23:33:12
  from "D:\phpStudy\WWW\KUAIXUEAPP\Index\Tpl\Index\index.html" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_5985e5389268c6_39072167',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '029bfbaa200f878db99551a13c10bd7b4c2ad9e9' => 
    array (
      0 => 'D:\\phpStudy\\WWW\\KUAIXUEAPP\\Index\\Tpl\\Index\\index.html',
      1 => 1501947186,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5985e5389268c6_39072167 (Smarty_Internal_Template $_smarty_tpl) {
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<?php echo $_smarty_tpl->tpl_vars['var']->value;?>

<form action="" method="post">
	<label>
		昵称:<input type="text" name="username" id="" />
	</label>
	<label>
		内容:<input type="text" name="email" id="" />
	</label>
	<input type="submit" value="提交"/>
</form>
</body>
</html><?php }
}
