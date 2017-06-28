<?php
/*
���ת����ע�������������ԶԴ˴������һ�����޸ĺ��Ż��Դﵽ����Ҫ��Ŀ��

*  ���ƣ���Socket���͵����ʼ�
*  ����������ʵ����ֱ��ʹ����Ҫ��֤��SMTP������ֱ�ӷ����ʼ����ο����¡���Socket���͵����ʼ������ߣ�limodou
*        �����±Ƚ��磬�����ò�����֤SMTP�����������ʼ������ڻ�����SMTP����������Ҫ��֤�ˣ�����������������
���Ǻܴ�ͬʱ�ο���RFC 1869]��PHP�ֲᣡ�������Ļ��в�ͬ�������õĲ���fsockopen()����
�������Լ����ɣ����Ҹող���ͨ���ˣ���ˬ����
��ʵ��������ٸ�дһ�¾Ϳ���ֱ�ӷ��ʹ��������ʼ��ˣ��ڴ��������ҹ���������д������
�Ҹոյ���ͨ��������㲻����ȥ׽Ū������������Ǻ������ģ���Щ�����һ�������Ƽ��Ϸ��͸����Ĺ��ܣ���

��ʹ�������������֧��MAIL����������Ҳ�����ˣ������������ɣ�

*  ���ߣ�С¶��3.3 QQ��6550382
*  ���ڣ�2003-09-18

�����ر��˵��Ͷ��ɹ����뱣���˰�Ȩ��Ϣ��лл��
���ߣ�С¶��3.3  ����ӭ������ϵ����PHP QQ6550382
MAIL��*****************

�﷫����һ�㶫�����ڴ������Ѿ���ע��ע����������������163����û���⡫
*/
set_time_limit(120);
class smtp_mail
{
 var $host;          //����
 var $port;          //�˿� һ��Ϊ25
 var $user;          //SMTP��֤���ʺ�
 var $pass;          //��֤����
 var $debug = false;   //�Ƿ���ʾ�ͷ������Ự��Ϣ��
 var $conn;
 var $result_str;      //���
 var $in;          //�ͻ������͵�����
 var $from;          //Դ����
 var $to;          //Ŀ������
 var $subject;         //����
 var $body;          //����
 function smtp_mail($host,$port,$user,$pass,$debug=false)
 {
  $this->host   = $host;
  $this->port   = $port;
  $this->user   = base64_encode($user);
  $this->pass   = base64_encode($pass);
  $this->debug  = $debug;
  $this->socket = socket_create (AF_INET, SOCK_STREAM, SOL_TCP);  //�����÷���ο��ֲ�
  if($this->socket)
  {
   $this->result_str  =  "����SOCKET:".socket_strerror(socket_last_error());
   $this->debug_show($this->result_str);
  }
  else
  {
   exit("��ʼ��ʧ�ܣ����������������ӺͲ���");
  }
  $this->conn = socket_connect($this->socket,$this->host,$this->port);
  if($this->conn)
  {
   $this->result_str  =  "����SOCKET����:".socket_strerror(socket_last_error());
   $this->debug_show($this->result_str);
  }
  else
  {
   exit("��ʼ��ʧ�ܣ����������������ӺͲ���");
  }
  $this->result_str = "������Ӧ��<font color=#cc0000>".socket_read ($this->socket, 1024)."</font>";
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
   exit("�����������ַ");
  }
  if($subject == "") $sebject = "�ޱ���";
  if($body    == "") $body    = "������";
  $this->from     =  $from;
  $this->to       =  $to;
  $this->subject  =  $subject;
  $this->body     =  $body;

       //�﷫�޸Ĳ��ִ���
  $All          = "From:<".$this->from.">\r\n";
  $All          .= "To:<".$this->to.">\r\n";
  $All          .= "Subject:".$this->subject."\r\n\r\n";
  $All          .= $this->body;
  /*
  �����$All�������ټӴ����Ϳ���ʵ�ַ���MIME�ʼ���
  ��������Ҫ�Ӻܶ����
  */


  //�����Ǻͷ������Ự
  $this->in       =  "EHLO HELO\r\n";
  $this->docommand();

  $this->in       =  "AUTH LOGIN\r\n";
  $this->docommand();

  $this->in       =  $this->user."\r\n";
  $this->docommand();

  $this->in       =  $this->pass."\r\n";
  $this->docommand();

 // $this->in       =  "MAIL FROM:".$this->from."\r\n";
  $this->in       =  "MAIL FROM:<".$this->from.">\r\n";  //�﷫�޸�
  $this->docommand();

 // $this->in       =  "RCPT TO:".$this->to."\r\n";
  $this->in       =  "RCPT TO:<".$this->to.">\r\n";     //�﷫�޸�
  $this->docommand();

  $this->in       =  "DATA\r\n";
  $this->docommand();

     $this->in       =  $All."\r\n.\r\n";
  $this->docommand();

  $this->in       =  "QUIT\r\n";
  $this->docommand();

  //�������ر�����

 

 }
 function docommand()
 {
  socket_write ($this->socket, $this->in, strlen ($this->in));
  $this->debug_show("�ͻ������".$this->in);
  $this->result_str = "������Ӧ��<font color=#cc0000>".socket_read ($this->socket, 1024)."</font>";
  $this->debug_show($this->result_str);
 }
}
?>
