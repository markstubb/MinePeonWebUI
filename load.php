
<pre>
  <?php
   function ProcStats()
   {   
       $fp=fopen("/proc/stat","r");
       if(false===$fp)
               return false;
       $a=explode(' ',fgets($fp));
print_r($a) ;
       array_shift($a); //get rid of 'cpu'
       while(!$a[0])
           array_shift($a); //get rid of ' '
       var_dump($a);
       fclose($fp);
       return $a;
   }

   $a=ProcStats();
   sleep(5);
   $b=ProcStats();

  
   $total=array_sum($b)-array_sum($a);
  
   $loadavg = round(100* (($b[0]+$b[1]+$b[2]) - ($a[0]+$a[1]+$a[2])) / $total, 2); // user+nice+system
   $iowait= round(100* ($b[4] - $a[4])/$total,2);
?>
<p>
<?php
print_r($a) ;
?>
</pre>
</p>
<p>
<pre>
<?php
print_r($b) ;
?>
</pre>
</p>
<p>
<pre>
<?php
echo $iowait;
?>
</pre>
</p>