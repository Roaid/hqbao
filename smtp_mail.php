<?php
/*
如果转载请注明处出，您可以对此代码进行一定的修改和优化以达到您需要的目的

*  名称：用Socket发送电子邮件
*  描述：本类实现了直接使用需要验证的SMTP服务器直接发送邮件，参考文章《用Socket发送电子邮件》作者：limodou
*        此文章比较早，他是用不用验证SMTP服务器发送邮件，现在基本上SMTP服务器都需要验证了，所以这个文章里的类
不是很大！同时参考了RFC 1869]和PHP手册！！和上文还有不同的是我用的不是fsockopen()函数
具体你自己看吧！！我刚刚测试通过了，很爽！！
其实把这个类再改写一下就可以直接发送带附件的邮件了，期待。。。我过几天给大家写出来！
我刚刚调试通过，如果你不恶意去捉弄这个程序，他还是很听话的，过些日子我会对他完善加上发送附件的功能！！

即使你的虚拟主机不支持MAIL函数，现在也不怕了！快快试试这个吧！

*  作者：小露珠3.3 QQ：6550382
*  日期：2003-09-18

请尊重别人的劳动成功，请保留此版权信息，谢谢！
作者：小露珠3.3  ，欢迎和我联系交流PHP QQ6550382
MAIL：*****************

扬帆修正一点东西：在代码中已经用注释注明，本代码现在向163发信没问题～
*/
set_time_limit(120);
class smtp_mail
{
 var $host;          //主机
 var $port;          //端口 一般为25
 var $user;          //SMTP认证的帐号
 var $pass;          //认证密码
 var $debug = false;   //是否显示和服务器会话信息？
 var $conn;
 var $result_str;      //结果
 var $in;          //客户机发送的命令
 var $from;          //源信箱
 var $to;          //目标信箱
 var $subject;         //主题
 var $body;          //内容
 function smtp_mail($host,$port,$user,$pass,$debug=false)
 {
  $this->host   = $host;
  $this->port   = $port;
  $this->user   = base64_encode($user);
  $this->pass   = base64_encode($pass);
  $this->debug  = $debug;
  $this->socket = socket_create (AF_INET, SOCK_STREAM, SOL_TCP);  //具体用法请参考手册
  if($this->socket)
  {
   $this->result_str  =  "创建SOCKET:".socket_strerror(socket_last_error());
   $this->debug_show($this->result_str);
  }
  else
  {
   exit("初始化失败，请检查您的网络连接和参数");
  }
  $this->conn = socket_connect($this->socket,$this->host,$this->port);
  if($this->conn)
  {
   $this->result_str  =  "创建SOCKET连接:".socket_strerror(socket_last_error());
   $this->debug_show($this->result_str);
  }
  else
  {
   exit("初始化失败，请检查您的网络连接和参数");
  }
  $this->result_str = "服务器应答：<font color=#cc0000>".socket_read ($this->socket, 1024)."</font>";
  $this->debug_show($this->result_str);


 }
 function debug_show($str)
 {
  if($this->debug)
  {
   echo $str."<p>\r\n";
  }
 }
 function send($from,$to,$subject,$body)
 {
  if($from == "" || $to == "")
  {
   exit("请输入信箱地址");
  }
  if($subject == "") $sebject = "无标题";
  if($body    == "") $body    = "无内容";
  $this->from     =  $from;
  $this->to       =  $to;
  $this->subject  =  $subject;
  $this->body     =  $body;

       //扬帆修改部分代码
  $All          = "From:<".$this->from.">\r\n";
  $All          .= "To:<".$this->to.">\r\n";
  $All          .= "Subject:".$this->subject."\r\n\r\n";
  $All          .= $this->body;
  /*
  如过把$All的内容再加处理，就可以实现发送MIME邮件了
  不过还需要加很多程序
  */


  //以下是和服务器会话
  $this->in       =  "EHLO HELO\r\n";
  $this->docommand();

  $this->in       =  "AUTH LOGIN\r\n";
  $this->docommand();

  $this->in       =  $this->user."\r\n";
  $this->docommand();

  $this->in       =  $this->pass."\r\n";
  $this->docommand();

 // $this->in       =  "MAIL FROM:".$this->from."\r\n";
  $this->in       =  "MAIL FROM:<".$this->from.">\r\n";  //扬帆修改
  $this->docommand();

 // $this->in       =  "RCPT TO:".$this->to."\r\n";
  $this->in       =  "RCPT TO:<".$this->to.">\r\n";     //扬帆修改
  $this->docommand();

  $this->in       =  "DATA\r\n";
  $this->docommand();

     $this->in       =  $All."\r\n.\r\n";
  $this->docommand();

  $this->in       =  "QUIT\r\n";
  $this->docommand();

  //结束，关闭连接

 

 }
 function docommand()
 {
  socket_write ($this->socket, $this->in, strlen ($this->in));
  $this->debug_show("客户机命令：".$this->in);
  $this->result_str = "服务器应答：<font color=#cc0000>".socket_read ($this->socket, 1024)."</font>";
  $this->debug_show($this->result_str);
 }
}
?>
