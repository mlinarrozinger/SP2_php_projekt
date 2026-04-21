

<!DOCTYPE html>
<html>
   <head>
      <title>Prva spletna stran</title>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
   </head>
   <body>
      <p>Pozdravljen, svet!</p>
      <p>To je spletna stran.<br />Naša prva!</p><br />
      Pozdravljeni v letu <?php echo date("Y"); ?>.

      <p>To je prvi odstavek.</p>
   <p>Tukaj pa je drugi odstavek.<br />To je druga vrstica istega odstavka.</p>

   <?php
   		echo "Peter!<br />";
   		echo "<a href=\"https://www.primer.si/\">Kliknite tukaj.</a><br />";
   		0
   ?>
   
   
   <img src="https://upload.wikimedia.org/wikipedia/commons/3/3f/Fronalpstock_big.jpg" alt="Testna slika" width="300"/><br />
  
   <img src="images/testna_slika.png" alt="Lokalna slika" width="300" /><br />

   <p>
      Tole je že tretji odstavek. Ta odstavek vsebuje nekaj,<br />
      kar izpišemo s pomočjo PHP kode:<br />
       <?php  
       		echo "Vsota števil 5 in 10 je: <br /> ";
       		echo 5+10;
       ?>
   </p>
   </body>
</html>