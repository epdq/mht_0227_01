<?php 
// bug_0914
$a  = '[{"DayId":0,"DaySort":"0,2,1","Route":[{"Route_Id":0,"Type":"5","Kind":"0","Title":"1","Isdo":"0","Address":"1","Image":"","TrafficType":1,"TrafficInfo":"","TimeStamp":1505358787},{"Route_Id":1,"Type":"5","Kind":"1","Title":"2","Isdo":"0","Address":"2","Image":"","TrafficType":1,"TrafficInfo":null,"TimeStamp":1505358797},{"Route_Id":2,"Type":"0","Site_Id":"1243","Title":"富比士杂志画廊","Isdo":"0","lat":"40.735033","lon":"-73.994734","Address":"62 5th Ave, New York, NY 10011美国","Image":"http://images.57us.com/p4/up/scenic/545/1497484783637.jpg","TrafficType":1,"TrafficInfo":"","TimeStamp":1505358795}]},{"DayId":1,"DaySort":"","Route":[]},{"DayId":2,"DaySort":"","Route":[]},{"DayId":3,"DaySort":"","Route":[]}]';

dd(json_decode($a, true));
