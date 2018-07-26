<?php

session_start();
if(isset($_GET["print"])){
print_r($_SESSION);
}
echo "<hr>";
$cart  = array();

if(isset($_GET["text"])){
	$temp = $_GET["text"];
	/* Provjeravanje da li je ADD komanda */
	if(strpos($temp, "ADD") !== false){
		
		$temparray = explode(" ", $temp);
		/* pri explode provjeravamo broj stringova razdvojenih razmakom kako bi provjerili da li je komanda za dodavanje u inventory ili u cart */
		if(sizeof($temparray)==5){
		$id=$temparray[1]-1;
		$id2=$temparray[1];
		$idexists="0";
		if(isset($_SESSION["inventory"])){
		/*provjera da li već postoji proizvod sa istim id-om u inventory*/
		foreach ($_SESSION["inventory"] as $red){
			if($red["id"]==$id2){
				$idexists="1";}
		}}
		if($idexists=="0"){
		$temp2 = array("id"=>$temparray[1], "name"=>$temparray[2], "quantity"=>$temparray[3], "price"=>$temparray[4]);
		if (empty($_SESSION["inventory"])) {
    		$_SESSION["inventory"][0] = $temp2;
		}else{
			array_push($_SESSION["inventory"],$temp2);
		}
	}else if($idexists=="1"){
		$_SESSION["inventory"][$id]["quantity"]+=$temparray[3];
	}
	/*ovdje se nalazi provjera broja stringova za cart*/
	}else if(sizeof($temparray)==3){
		$id=$temparray[1]-1;
		$id2=$temparray[1];
		$idexists="0";
		/*provjera da li već postoji proizvod sa istim id-om u cart*/
		foreach ($_SESSION["cart"] as $red){
			if($red["id"]==$id2){
				$idexists="1";}
		}
		/*Ako je količina proizvoda veća ili jednaka u invenotry nego što se pokušava dodati*/
		if($_SESSION["inventory"][$id]["quantity"]>=$temparray[2] && $idexists=="0"){
		$temp3 = array("id"=>$_SESSION["inventory"][$id]["id"], "name"=>$_SESSION["inventory"][$id]["name"], "quantity"=>$temparray[2], "price"=>$_SESSION["inventory"][$id]["price"]);
		if (empty($_SESSION["cart"])) {
    		$_SESSION["cart"][0] = $temp3;
		}else{
			array_push($_SESSION["cart"],$temp3);
		}
		}else if($_SESSION["inventory"][$id]["quantity"]>=$temparray[2]+$_SESSION["cart"][$id]["quantity"] && $idexists=="1"){
		$_SESSION["cart"][$id]["quantity"]+=$temparray[2];

		}else{
		echo "Only ".$_SESSION["inventory"][$id]["quantity"]." of ".$_SESSION["inventory"][$id]["name"]." left.";
	}
	}
}	


			/* printanje računa i čišćenje košarice*/
			if(strpos($temp, "CHECKOUT") !== false){
				$total=0;
				foreach ($_SESSION["cart"] as $red) {
					
					$id3=$red["id"]-1;
					$_SESSION["inventory"][$id3]["quantity"]-=$red["quantity"];
					$total += $red["quantity"]*$red["price"];
					echo $red["name"]." ".$red["quantity"]." x ".$red["price"]." = ".$red["quantity"]*$red["price"]."<br>";
				}
				echo "TOTAL = ".$total;
				$temp4=array();
				$_SESSION["cart"]=$temp4;
			}

				/* brisanje proizvoda iz košarice */

				if(strpos($temp, "REMOVE") !== false){
					$temparray = explode(" ", $temp);
					$id=$temparray[1]-1;
					$id2=$temparray[1];
					$idexists="0";
					foreach ($_SESSION["cart"] as $red){
						if($red["id"]==$id2){
							$idexists="1";
						}
					}
					if($idexists=="1"){
						if($_SESSION["cart"][$id]["quantity"]<=$temparray[2]){
							unset($_SESSION["cart"][$id]);
						}else{
						$_SESSION["cart"][$id]["quantity"]-=$temparray[2];
					}
					}else if($idexists=="0"){
						echo "No such item in cart.";
					}
				}
		}
?>

<form action="#" method="get">
<input type="text" name="text" placeholder=""></input><br/>
<input type="submit" name="submit" value="Submit"></input>
</form>


<form action="#" method="get">
<input type="hidden" name="print" value="1"></input><br/>
<input type="submit" name="submit" value="Print inventory"></input>
</form>

<p>App is made by given instructions.<br>
Commands:<br>
<strong>ADD <i>id name quantity price<i/></strong> - adds item to inventory list, if id exists adds the quantity values, but must be in this form.<br>
<strong>ADD <i>id quantity<i/></strong> - adds item to the shopping cart, if id exists add the quantity values, also if there isn't enough of the items in the inventory it will print a message.<br>
<strong>CHECKOUT</strong> - prints receipt and subtracts item quantity values from inventory.<br>
<strong>REMOVE <i>id quantity<i/></strong> - removes item from shopping cart, by quantity value or the whole item if the given quantity is equal or greater than in the shopping cart. <br>
<strong>END</strong> - not implemented.
