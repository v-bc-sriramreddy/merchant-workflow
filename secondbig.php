<?php

$values = array(9,1,2,3,8,6,2);

$length = count($values);

$max = $values[0];
$secondMax = $values[0];
for($i=1;$i<$length;$i++)
{
  if ($max < $values[$i])
  {
      $max = $values[$i];
  }
  
  if ($secondMax < $values[$i] && $values[$i]!=$max){
      $secondMax = $values[$i];
  }

}

echo $max;
echo "<br>";
echo $secondMax;

?>