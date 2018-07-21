<?php 

$keyword = $_GET['keyword'];
//$returndata=$keyword."中文测试3";
$hst = new test();
$returndata = $hst->responseMsg($keyword);
echo json_encode($returndata, JSON_UNESCAPED_UNICODE);
//echo $returndata;
//echo "hst1233444";
class test
{
    private function setNote($lx1,$lx2,$noteID,$time)
    {
    
        $mysqli = new mysqli('*************', '*********', '*********', '*******');
        if ($mysqli->connect_error) {return "连接数据库失败";}
        //$strsql=sprintf("UPDATE `lou%s` SET zt='%s' WHERE zt='0';",$noteID,$zt);
        
        $strsql=sprintf("INSERT INTO `%s` (`id`,`date`,`%s`) VALUES ('001','%s','%s');",$lx1,$lx2,$time,$noteID);		       
        if( mysqli_query($mysqli,$strsql)) { $mysqli->close();return "插入成功"; } 
        else{ $mysqli->close(); return "指令执行错误"; }
    }
    //用于修改井盖状态 
    private function setNote2($noteID,$noteID2,$time)
    {
    
        $mysqli = new mysqli('*************', '*********', '*********', '*******');
        if ($mysqli->connect_error) {return "连接数据库失败";}
        $strsql=sprintf("UPDATE `井盖状态` SET moved='%s',damged='%s',date='%s' WHERE id='001';",$noteID,$noteID2,$time);
		//return $strsql;
        if($noteID=="1"||$noteID2=="1")
        {
            $key="";
            if($noteID=="1") $key=$key."井盖被移动了-";
            if($noteID2=="1")  $key=$key."井盖被损坏了-"; 
            $time2= str_split( $time , 2 );
            $key=$key.$time2[0]."月".$time2[1]."日".$time2[2].":".$time2[3].":".$time2[4];
            $this->sendmail($key);
        }   
         if( mysqli_query($mysqli,$strsql) )
        {
   			$mysqli->close();
		    return $key."修改表成功";
        } 
        else
        {
            $mysqli->close();
            return "指令执行错误";
        }
    }
    //用于修改井盖位置
    private function setNote3($noteID,$noteID2,$time)
    {
        $mysqli = new mysqli('*************', '*********', '*********', '*******');
        if ($mysqli->connect_error) {return "连接数据库失败";}
        $strsql=sprintf("UPDATE `井盖位置` SET weidu='%s',jingdu='%s',date='%s' WHERE id='001';",$noteID,$noteID2,$time);
		//return $strsql;
        
         if( mysqli_query($mysqli,$strsql) )
        {
   			$mysqli->close();
		    return "修改表成功";
        } 
        else
        {
            $mysqli->close();
            return "指令执行错误3";
        }
      
    }
    
    //查询数据库
    private function getNote($noteID,$lx)
    {
    
        $mysqli = new mysqli('*************', '*********', '*********', '*******');
         if ($mysqli->connect_error) {
            return "错误";
        }
        $strsql=sprintf("SELECT * FROM `%s`",$noteID);
        $result="";
        $str1=mysqli_query($mysqli,$strsql);
        if( mysqli_num_rows($str1) > 0 )
        {
            while($row = mysqli_fetch_assoc($str1))
            {
                $time= str_split( $row["date"] , 2 );
                $result=$result.$time[0]."月".$time[1]."日".$time[2].":".$time[3].":".$time[4]."-".$row[$lx]."#";
			}
            $mysqli->close();
            return $result;
        } 
        else
        {
            $mysqli->close();
            return "当前表为空";
        }
    }
    
    //专门用于查询井盖状态
    private function getNote2($noteID)
    {
    
        $mysqli = new mysqli('*************', '*********', '*********', '*******');
         if ($mysqli->connect_error) {
            return "错误";
        }
        $strsql=sprintf("SELECT * FROM `%s`",$noteID);
        $result="井盖编号：001<br>";
        $str1=mysqli_query($mysqli,$strsql);
        if( mysqli_num_rows($str1) > 0 )
        {
            while($row = mysqli_fetch_assoc($str1))
            {
                //$result=$result.$row["moved"]." ".$row["damged"]." ".$row["date"]."<br>";
                if( $row["moved"] == "0"){ $result=$result."没有被移动<br>"; }
                else { $result=$result."被移动了！<br>";}                
                if( $row["damged"] == "0"){ $result=$result."没有被损坏<br>"; }
                else { $result=$result."被损坏了！<br>";}
                $time= str_split( $row["date"] , 2 );
                $result=$result."更新时间：".$time[0]."月".$time[1]."日".$time[2].":".$time[3].":".$time[4]."<br>";
			}
            $mysqli->close();
            return $result;
        } 
        else
        {
            $mysqli->close();
            return "当前表为空";
        }
    }
    
    //专门用于查询井盖位置
    private function getNote3($noteID)
    {
    
        $mysqli = new mysqli('*************', '*********', '*********', '*******');
         if ($mysqli->connect_error) {
            return "错误";
        }
        $strsql=sprintf("SELECT * FROM `%s`",$noteID);
        $result="";
        $str1=mysqli_query($mysqli,$strsql);
        if( mysqli_num_rows($str1) > 0 )
        {
            while($row = mysqli_fetch_assoc($str1))
            {
                $result=$result.$row["weidu"]."-".$row["jingdu"];
                $time= str_split( $row["date"] , 2 );
                $result=$result."-".$time[0]."月".$time[1]."日".$time[2].":".$time[3].":".$time[4];
			}
            $mysqli->close();
            return $result;
        } 
        else
        {
            $mysqli->close();
            return "当前表为空";
        }
    }
    
    //删除，暂时用不到的功能。。。
	private function clearNote($noteID)
	{
	    $mysqli = new mysqli('*************', '*********', '*********', '*******');
        if ($mysqli->connect_error) 
		{
            return "发生未知错误";
        }		
		$strsql=sprintf("DELETE FROM `lou1` WHERE zt<>'0'");
		mysqli_query($mysqli,$strsql);
		$strsql=sprintf("DELETE FROM `lou2` WHERE zt<>'0'");
		mysqli_query($mysqli,$strsql);
		return "成功";
    }
    
    //发送邮件
    public function sendmail($note)
    {
         $mail=new SaeMail();
         //$note="12345";
         $ret = $mail->quickSend("*******","井盖消息",$note,"*******","*******");
         return "";
    }
	public function responseMsg($keyword)
    {
            // moved/0/0/0713162830   moved/井盖移动（0表示没有移动，1表示被移动了）/井盖损坏(0表示没有损坏，1表示被损坏了)/日期，
            //gas/123/0713164523 gas/气体/日期，
            //water/123/0713164612  water/水位/日期，temperature/123/0713164641 temperature/温度/日期
			// gps/纬度/经度/日期
            if(preg_match("/^moved/",$keyword))
            {    
                //$contentStr = date("Y-m-d H:i:s",time());               
                $arr = explode("/",$keyword);
                $contentStr = $this->setNote2($arr[1],$arr[2],$arr[3]);
                return $contentStr;
            }
             elseif(preg_match("/^gas/",$keyword) )
            {

                //$contentStr = date("Y-m-d H:i:s",time());               
                $arr = explode("/",$keyword);
				$contentStr = $this->setNote("井盖气体","gas",$arr[1],$arr[2]);
                return $contentStr;
                //return $arr[2];
            }
			elseif( preg_match("/^water/",$keyword))
			{   
                //$contentStr = date("Y-m-d H:i:s",time());               
                $arr = explode("/",$keyword);
				$contentStr = $this->setNote("井盖水位","water",$arr[1],$arr[2]);
                return $contentStr;
			}
			elseif( preg_match("/^temperature/",$keyword))
			{   
                //$contentStr = date("Y-m-d H:i:s",time());               
                $arr = explode("/",$keyword);
				$contentStr = $this->setNote("井盖温度","temp",$arr[1],$arr[2]);
                return $contentStr;
			}
            
            elseif( $keyword == "井盖移动情况")
			{   
                //$contentStr = date("Y-m-d H:i:s",time());               
                $arr = explode("/",$keyword);
				$contentStr = $this->getNote2("井盖状态");
                return $contentStr;
			}
            elseif( $keyword == "gpsrq")
			{   
                //$contentStr = date("Y-m-d H:i:s",time());               
                //$arr = explode("/",$keyword);
				$contentStr = $this->getNote3("井盖位置");
                return $contentStr;
			}
            elseif( preg_match("/^gps/",$keyword))
		    {   
                //$contentStr = date("Y-m-d H:i:s",time());               
                $arr = explode("/",$keyword);
				$contentStr = $this->setNote3($arr[1],$arr[2],$arr[3]);
                return $contentStr;
			}
            elseif( $keyword == "井盖温度情况" )
			{   
                //$contentStr = date("Y-m-d H:i:s",time());               
                $arr = explode("/",$keyword);
				$contentStr = $this->getNote("井盖温度","temp");
                return $contentStr;
			}
            elseif( $keyword == "井盖水位情况" )
			{   
                //$contentStr = date("Y-m-d H:i:s",time());               
                $arr = explode("/",$keyword);
				$contentStr = $this->getNote("井盖水位","water");
                return $contentStr;
			}
            elseif( $keyword == "井盖气体情况"||$keyword == "testtesthst"  )
			{   
                //$contentStr = date("Y-m-d H:i:s",time());               
                $arr = explode("/",$keyword);
				$contentStr = $this->getNote("井盖气体","gas");
                return $contentStr;
			}
            else 
			{
				$contentStr = "输入错误";
                
               return $contentStr;

			}
    }
}

?>